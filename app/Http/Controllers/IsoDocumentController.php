<?php

namespace App\Http\Controllers;

use App\Models\IsoMasterDocument;
use App\Models\IsoTicketDocument;
use App\Models\IsoTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TicketStatusChanged;
use App\Mail\DocumentStatusUpdated;

class IsoDocumentController extends Controller 
{
    public function authTicketCheck(IsoTicket $ticket){
        // Auth check: Make sure ticket belongs to the current user
        if($ticket->created_by !== auth()->id()){
            abort(403, 'Unauthorized action.');
        }

        // Auth Check: Only allow edit on pending tickets
        if($ticket->status !=='pending'){
            abort(403, 'Cannot edit tickets that are not pending.');
        }
    }
    public function loadDocument(Request $request){
        // Make the query - get the tickets for the current user
        $query = IsoTicket::where('created_by', auth()->id());

        // Status from URL (?status=something)
        $statusFilter = $request->query('status');
        
        $search = $request->query('search');

        // If a status filter was provided AND it's not 'all'
        if($statusFilter && $statusFilter !== 'all'){
            $query->where('status', $statusFilter);
        }

        $this->searchFilter($query, $search);

        // Execute the query with document count and ordering
        $tickets = $query->with(['documents.revisingMasterDocument', 'creator.user'])
            ->withCount('documents')
            ->orderBy('created_at','desc')
            ->get();

            
        // Pass tickets to the view and the current filter to the view
        return view('iso.document', compact('tickets', 'statusFilter', 'search'));
    }

    public function searchFilter($query, $search){
        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
            // Search in ticket fields
            $q->where('ticket_number', 'like',  "%$search%")
                ->orWhere('originating_section', 'like', "%{$search}%")
            //Search in related documents
                ->orWhereHas('documents', function($docQuery) use ($search) {
                        $docQuery->where('document_code', 'like', "%{$search}%")
                        ->orWhere('document_title', 'like', "%{$search}%");
                });
            });
        }
    }

    public function checkDocumentCode(Request $request){
        $request -> validate([
            'document_code' => 'required|string'
        ]);
        $code = trim($request->document_code);
        $existsInMaster = IsoMasterDocument::where('document_code', $code)->exists();
        $existsInActiveTickets = IsoTicketDocument::where('document_code', $code)
            ->where('classification', 'addition')
            ->whereHas('ticket', function($q) {
                $q->whereIn('status', ['pending', 'submitted_to_idc', 'with_qmr', 'on_hold']);
            })
            ->exists();
        $exists = $existsInMaster || $existsInActiveTickets;
        return response()->json(['exists' => $exists]);
    }

    public function editDocument(IsoTicket $ticket){
        // Auth Check
        $this->authTicketCheck($ticket);

        // Load the ticket with its documents relationship
        $ticket -> load(['documents' => function($query){
            $query->select(
                'id',
                'ticket_id',
                'document_code',
                'document_title',
                'classification',
                'source_type',
                'specific_type',
                'status',
                'revising_master_document_id'
            )->with('revisingMasterDocument');
        }]);

        // Return the ticket data as JSON for the model
        return response()->json($ticket);
    }

    public function updateDocument(Request $request, IsoTicket $ticket){
        // Auth Check
        $this->authTicketCheck($ticket);

        // Validate incoming data
        $validated = $request->validate([
            'originating_section' => 'required |string|max:255',
            'sharepoint_link' => 'required|url',
            'message_to_idc'=> 'required|string',
            'documents' => 'required|json'
        ]);

        // Parse the documents JSON
        $documents = json_decode($validated['documents'], true);



        // Check for document code uniqueness and active ticket duplicates
        foreach ($documents as $doc) {
            $code = trim($doc['code']);

            // 1. Check if this document code is already in use by another active ticket (regardless of classification)
            $existsInActiveTickets = IsoTicketDocument::where('document_code', $code)
                ->whereHas('ticket', function($q) use ($ticket) {
                    $q->whereIn('status', ['pending', 'submitted_to_idc', 'with_qmr', 'on_hold'])
                      ->where('id', '!=', $ticket->id);
                })
                ->exists();

            if ($existsInActiveTickets) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "The document code '{$code}' is already in use by another active ticket. Multiple concurrent tickets cannot process the same document code.");
            }

            // 2. If it is a new addition, check if it already exists in the Master Document List
            if ($doc['classification'] === 'addition') {
                $existsInMaster = IsoMasterDocument::where('document_code', $code)->exists();
                if ($existsInMaster) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "The document code '{$code}' is already registered in the Master Document List. New documents must have a unique code.");
                }
            }
        }

        // Update the ticket
        $ticket->update([
            'originating_section' => $validated['originating_section'],
            'sharepoint_link'=> $validated['sharepoint_link'],
            'message_to_idc'=>$validated['message_to_idc']
        ]);

        // Delete all existing documents for the current ticket
        $ticket->documents()->delete();

        $isIdcAdmin = in_array(auth()->user()->role ?? '', ['IDC Admin', 'SuperAdmin']);

        // Recreate documents with the new list
        foreach ($documents as $document){
            $docTitle = trim($document['title'] ?? '');

            // For deletions, title MUST be the master document's original title
            if ($document['classification'] === 'deletion' && !empty($document['revisingMasterId'])) {
                $masterDoc = IsoMasterDocument::find($document['revisingMasterId']);
                if ($masterDoc) {
                    $docTitle = $masterDoc->document_title;
                }
            } elseif ($document['classification'] === 'revision' && !empty($document['revisingMasterId'])) {
                $masterDoc = IsoMasterDocument::find($document['revisingMasterId']);
                if ($masterDoc) {
                    if (!$isIdcAdmin) {
                        // Regular employee: enforce title from master (staged revision title if set, else original title)
                        $docTitle = (!empty($masterDoc->revised_document_title) && trim($masterDoc->revised_document_title) !== '')
                            ? trim($masterDoc->revised_document_title)
                            : $masterDoc->document_title;
                    }
                }
            }

            IsoTicketDocument::create([
                'ticket_id' => $ticket->id,
                'document_code' => $document['code'],
                'document_title'=> $docTitle,
                'classification'=> $document['classification'],
                'source_type'=> $document['source'],
                'specific_type'=> $document['specificType'],
                'status' => 'pending',
                'revising_master_document_id' => $document['revisingMasterId'] ?? null,
            ]);
        }

        return redirect()->route('iso.document')->with('success', 'Ticket updated successfully!');
    }

    public function destroyDocument(IsoTicket $ticket){
        // Check if ticket belongs to current user
        if ($ticket-> created_by !== auth()-> id()){
            abort(403, 'Unauthorized action.');
        }

        // Auth check: Only allow deleting pending tickets
        if ($ticket-> status !== 'pending'){
            abort(403, 'Cannot delete tickets that are not pending.');
        }

        // Delete related documents first (Foreign Key Constraint)
        $ticket->documents()->delete();

        // Delete the ticket itself
        $ticket->delete();

        return redirect()->route('iso.document')->with('success', 'Ticket deleted successfully!');
    }

    public function storeDocument(Request $request){
        $validated = $request ->validate([
            'originating_section'=> 'required|string|max:255',
            'sharepoint_link' => 'required|url',
            'message_to_idc' => 'required|string',
            'documents' => 'required|json'

        ]);



        // Parsing the JSON documents
        $documents = json_decode($validated['documents'], true);

        // Check for document code uniqueness and active ticket duplicates
        foreach ($documents as $doc) {
            $code = trim($doc['code']);

            // 1. Check if this document code is already in use by another active ticket (regardless of classification)
            $existsInActiveTickets = IsoTicketDocument::where('document_code', $code)
                ->whereHas('ticket', function($q) {
                    $q->whereIn('status', ['pending', 'submitted_to_idc', 'with_qmr', 'on_hold']);
                })
                ->exists();

            if ($existsInActiveTickets) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "The document code '{$code}' is already in use by another active ticket. Multiple concurrent tickets cannot process the same document code.");
            }

            // 2. If it is a new addition, check if it already exists in the Master Document List
            if ($doc['classification'] === 'addition') {
                $existsInMaster = IsoMasterDocument::where('document_code', $code)->exists();
                if ($existsInMaster) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "The document code '{$code}' is already registered in the Master Document List. New documents must have a unique code.");
                }
            }
        }
        
        $ticket = IsoTicket::create([
            'originating_section'=> $validated['originating_section'],
            'sharepoint_link' => $validated['sharepoint_link'],
            'message_to_idc' => $validated['message_to_idc'],
            'status' => 'pending',
            'created_by' => auth()->id()
        ]);
        
        $isIdcAdmin = in_array(auth()->user()->role ?? '', ['IDC Admin', 'SuperAdmin']);
        
        // Save each document
        foreach($documents as $document){
            $docTitle = trim($document['title'] ?? '');

            // For deletions, title MUST be the master document's original title
            if ($document['classification'] === 'deletion' && !empty($document['revisingMasterId'])) {
                $masterDoc = IsoMasterDocument::find($document['revisingMasterId']);
                if ($masterDoc) {
                    $docTitle = $masterDoc->document_title;
                }
            } elseif ($document['classification'] === 'revision' && !empty($document['revisingMasterId'])) {
                $masterDoc = IsoMasterDocument::find($document['revisingMasterId']);
                if ($masterDoc) {
                    if (!$isIdcAdmin) {
                        // Regular employee: enforce title from master (staged revision title if set, else original title)
                        $docTitle = (!empty($masterDoc->revised_document_title) && trim($masterDoc->revised_document_title) !== '')
                            ? trim($masterDoc->revised_document_title)
                            : $masterDoc->document_title;
                    }
                }
            }

            IsoTicketDocument::create([
                'ticket_id'=> $ticket->id,
                'document_code' => $document['code'],
                'document_title'=> $docTitle,
                'classification' => $document['classification'],
                'source_type' => $document['source'],
                'specific_type' => $document['specificType'],
                'status' => 'pending',
                'revising_master_document_id' => $document['revisingMasterId'] ?? null,
            ]);
        }
        return redirect()->route('iso.document')->with('success', 'Ticket created successfully!');
    }
    public function submitToIDC(IsoTicket $ticket){
        // Auth check
        $this->authTicketCheck($ticket);
        
        // Update the statuses of the documents to submitted to idc
        $ticket->documents()->update(['status' => 'submitted_to_idc']);

        // Testing Feature: Generate ticketing number
        $ticketNumber = $this->generateTicketNumber();

        // Save old status for email
        $oldStatus = $ticket->status;

        // Update ticket Status
        $ticket->update([
            'status' => 'submitted_to_idc',
            'ticket_number' => $ticketNumber,
            'submitted_at' => now()
        ]);
        // This is null at first because no one technically 'updated' it.
        $changedBy = null;
        // Email the IDC Document Handlers
        $this->sendTicketStatusNotification($ticket, $oldStatus, $changedBy);
        return redirect()->route('iso.document')->with('success','Ticket #' . $ticketNumber . ' submitted to IDC successfully!');
    }

    private function generateTicketNumber(){
        $year = date('Y');

        // get the last ticket number for this year
        $lastTicket = IsoTicket::whereYear('submitted_at', $year)
            ->whereNotNull('ticket_number')
            ->orderBy('ticket_number', 'desc')
            ->first();
        if($lastTicket && preg_match('/(\d+)$/', $lastTicket->ticket_number, $matches)){
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'ISO-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function loadIdcDashboard(Request $request){
        // Get status filter
        $statusFilter = $request->query('status','submitted_to_idc');

        // Must define search here instead of the extracted method
        $search = $request->query('search');

        // Start of Calculating Document Counts by status
        $statusCounts = IsoTicket::where('status', '!=', 'pending')
            ->selectRaw('status, SUM(
                (SELECT COUNT(*)
                FROM iso_ticket_documents
                WHERE iso_ticket_documents.ticket_id = iso_tickets.id)
                ) as document_count')
            ->groupBy('status')
            ->pluck('document_count', 'status')
            ->toArray();

        // Calculate total for "All Tickets" (all non-pending)
        $statusCounts['all'] = array_sum($statusCounts);

        // Make sure all statuses have a count (even if 0)
        $statusCounts = array_merge([
            'submitted_to_idc' => 0,
            'with_qmr' => 0,
            'approved' => 0,
            'on_hold' => 0,
            'all' => 0
        ], $statusCounts);

        // Start building the query - IDC should see all the tickets
        $query = IsoTicket::with(['documents.revisingMasterDocument', 'creator.user']);

        // Build query
        if ($statusFilter !== 'all'){
            $query->where('status', $statusFilter);
        } else{
            // When showing "All Tickets", exclude pending ones
            // IDC Should never see the pending tickets
            $query->where('status', '!=', 'pending');
        }

        // Apply search if present
        $this->searchFilter($query, $search);

        // Exclude 'pending' tickets (IDC only sees submitted ones)
        $query->where('status', '!=', 'pending');

        // Order by newest first and get results
        $tickets = $query->withCount('documents')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('iso.idc-dashboard', compact('tickets', 'statusFilter', 'search', 'statusCounts'));
    }

    /**
     * Update ticket status (IDC action)
     */
    public function updateTicketStatus(Request $request, IsoTicket $ticket){
        // Validate incoming data
        $validated = $request->validate([
            'status' => ['required', 'in:submitted_to_idc,with_qmr'],
            'notes' => ['nullable', 'string', 'max:1000']
        ]);

        // Store old status before updating
        $oldStatus = $ticket->status;
        $newStatus = $validated['status'];

        $ticket->status = $newStatus;
        $ticket->save();

        // Update all documents to match the ticket status
        $ticket->documents()->update([
            'status'=> $newStatus
        ]);

        // Get IDC member who made the change
        $changedBy = auth()->user()->name;

        $this->sendTicketStatusNotification($ticket,$oldStatus,$changedBy);

        return redirect()->route('iso.idc.dashboard')
            ->with('msg','Ticket Status updated successfully!');
    }

    // =====================================
    // Updating the Document Statuses
    // =====================================
    public function updateDocumentStatus(Request $request, $documentId){
        $request->validate([
            'status' => 'required|in:approved,on_hold'
        ]);

        $document = IsoTicketDocument::findOrFail($documentId);
        $oldDocStatus = $document->status;

        // Auto-set registered_at when approved
        if($request->status === 'approved'){
            $document->registered_at = now();
        }

        $document->status = $request->status;
        $document->save();

        // Check if we need to auto-update each ticket status
        $ticket = $document->ticket;
        $oldTicketStatus = $ticket->status;

        // Recalculate ticket status without sending ticket-level notification immediately
        $this->recalculateTicketStatus($ticket, false);

        $ticket->refresh();

        $changedBy = 'IDC Admin - ' . (auth()->user()->name ?? 'Administrator');

        // Notification routing:
        // 1. If the ticket just became fully approved (all documents approved), send the complete ticket approval notification.
        // 2. Otherwise (partial approval or document on-hold), send the document-specific update notification.
        if ($ticket->status === 'approved' && $oldTicketStatus !== 'approved') {
            $this->sendTicketStatusNotification($ticket, $oldTicketStatus, $changedBy);
        } else {
            $this->sendDocumentStatusNotification($document, $oldDocStatus, $changedBy);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document status updated',
            'ticket_status' => $ticket->status
        ]);
    }

    public function recalculateTicketStatus($ticket, $sendNotification = true){
        $documents = $ticket->documents;
        $oldStatus = $ticket->status;

        // check if any document is on_hold
        $hasOnHold = $documents->contains(function($doc){
            return $doc->status === 'on_hold';
        });

        if($hasOnHold){
            $ticket->status = 'on_hold';
            $ticket->save();

            if($sendNotification && $oldStatus !== 'on_hold'){
                $this->sendTicketStatusNotification(
                    $ticket,
                    $oldStatus,
                    'IDC Admin - ' . (auth()->user()->name ?? 'Administrator')
                );
            }
            return;
        }

        // Check if all documents are approved
        $allApproved = $documents->every(function($doc){
            return $doc->status === 'approved';
        });

        if ($allApproved && $documents->count() > 0){
            $ticket->status = 'approved';
            $ticket->save();
            if($sendNotification && $oldStatus !== 'approved'){
                $this->sendTicketStatusNotification(
                    $ticket,
                    $oldStatus,
                    'IDC Admin - ' . (auth()->user()->name ?? 'Administrator')
                );
            }
            return;
        }

        // If previously on_hold but no documents are on_hold anymore, restore to submitted_to_idc
        if ($oldStatus === 'on_hold') {
            $ticket->status = 'submitted_to_idc';
            $ticket->save();

            if($sendNotification && $oldStatus !== 'submitted_to_idc'){
                $this->sendTicketStatusNotification(
                    $ticket,
                    $oldStatus,
                    'IDC Admin - ' . (auth()->user()->name ?? 'Administrator')
                );
            }
            return;
        }
    }

    private function sendTicketStatusNotification($ticket, $oldStatus, $changedBy){
        if (!$ticket) {
            return;
        }

        $sentEmails = [];

        // 1. Email the Document Handler (Ticket owner) if available
        if ($ticket->creator && !empty($ticket->creator->email)) {
            $creatorEmail = trim($ticket->creator->email);
            try {
                Mail::to($creatorEmail)
                    ->send(new TicketStatusChanged($ticket, $oldStatus, $changedBy));
                $sentEmails[] = strtolower($creatorEmail);
            } catch (\Throwable $e) {
                Log::error("Failed to send TicketStatusChanged email to ticket creator ({$creatorEmail}): " . $e->getMessage());
            }
        }

        // 2. Email all IDC Admins individually so one failure never blocks others
        try {
            $idcAdmins = User::where('role', 'IDC Admin')->get();
            foreach ($idcAdmins as $admin){
                $adminEmail = trim($admin->email ?? '');
                if (!empty($adminEmail) && !in_array(strtolower($adminEmail), $sentEmails)) {
                    try {
                        Mail::to($adminEmail)
                            ->send(new TicketStatusChanged($ticket, $oldStatus, $changedBy));
                        $sentEmails[] = strtolower($adminEmail);
                    } catch (\Throwable $e) {
                        Log::error("Failed to send TicketStatusChanged email to IDC Admin ({$adminEmail}): " . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("Failed to retrieve or email IDC Admins in sendTicketStatusNotification: " . $e->getMessage());
        }
    }

    private function sendDocumentStatusNotification(IsoTicketDocument $document, $oldDocStatus, $changedBy){
        $ticket = $document->ticket;
        if (!$ticket) {
            return;
        }

        $sentEmails = [];

        // 1. Email the Document Handler (Ticket creator) with partial status update
        if ($ticket->creator && !empty($ticket->creator->email)) {
            $creatorEmail = trim($ticket->creator->email);
            try {
                Mail::to($creatorEmail)
                    ->send(new DocumentStatusUpdated($document, $ticket, $oldDocStatus, $changedBy));
                $sentEmails[] = strtolower($creatorEmail);
            } catch (\Throwable $e) {
                Log::error("Failed to send DocumentStatusUpdated email to ticket creator ({$creatorEmail}): " . $e->getMessage());
            }
        }

        // 2. Also notify IDC Admins so they are aware of partial document status updates
        try {
            $idcAdmins = User::where('role', 'IDC Admin')->get();
            foreach ($idcAdmins as $admin){
                $adminEmail = trim($admin->email ?? '');
                if (!empty($adminEmail) && !in_array(strtolower($adminEmail), $sentEmails)) {
                    try {
                        Mail::to($adminEmail)
                            ->send(new DocumentStatusUpdated($document, $ticket, $oldDocStatus, $changedBy));
                        $sentEmails[] = strtolower($adminEmail);
                    } catch (\Throwable $e) {
                        Log::error("Failed to send DocumentStatusUpdated email to IDC Admin ({$adminEmail}): " . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("Failed to retrieve or email IDC Admins in sendDocumentStatusNotification: " . $e->getMessage());
        }
    }

    // ==================================
    // Delete Ticket Permanently (Hard Delete)
    // ==================================
    public function deleteTicket(IsoTicket $ticket){
        // Auth check: only IDC Admin or SuperAdmin
        $userRole = auth()->user()->role;
        if($userRole !== 'IDC Admin' && $userRole !== 'SuperAdmin'){
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Prevent deleting registered tickets
        if($ticket->is_registered){
            return response()->json(['success' => false, 'message' => 'Cannot delete a registered ticket.'], 403);
        }

        try{
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Delete child documents first
            $ticket->documents()->delete();

            // Hard delete the ticket itself
            $ticket->delete();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return response()->json(['success' => true, 'message' => 'Ticket permanently deleted.']);
        } catch(\Exception $e){
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            \Log::error('Delete ticket failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete ticket.'], 500);
        }
    }

    // ==================================
    // Remove Individual Document from Ticket
    // ==================================
    public function removeTicketDocument(IsoTicket $ticket, $documentId){
        // Auth check: only IDC Admin, SuperAdmin, or the ticket creator
        $user = auth()->user();
        $isIdcAdmin = in_array($user->role, ['IDC Admin', 'SuperAdmin']);
        $isOwner = ($ticket->created_by === $user->id);

        if (!$isIdcAdmin && !$isOwner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Prevent modifying registered tickets
        if ($ticket->is_registered) {
            return response()->json(['success' => false, 'message' => 'Cannot remove documents from a registered ticket.'], 403);
        }

        // Find document belonging to this ticket
        $document = $ticket->documents()->where('id', $documentId)->first();
        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found in this ticket.'], 404);
        }

        // Ensure ticket has at least one document
        if ($ticket->documents()->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'A ticket must contain at least one document. To remove the entire ticket, please delete the ticket.'
            ], 422);
        }

        try {
            $document->delete();

            // Recalculate ticket status based on remaining documents
            $this->recalculateTicketStatus($ticket);
            $ticket->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Document removed successfully.',
                'remaining_count' => $ticket->documents()->count(),
                'ticket_status' => $ticket->status
            ]);
        } catch (\Exception $e) {
            \Log::error('Remove document from ticket failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to remove document. Please try again.'], 500);
        }
    }

    // ==================================
    // Update Ticket & Document Details by IDC Admin
    // ==================================
    public function updateTicketDetailsByAdmin(Request $request, IsoTicket $ticket){
        // Auth check: only IDC Admin or SuperAdmin
        $userRole = auth()->user()->role;
        if (!in_array($userRole, ['IDC Admin', 'SuperAdmin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Prevent modifying registered tickets
        if ($ticket->is_registered) {
            return response()->json(['success' => false, 'message' => 'Cannot modify a registered ticket.'], 403);
        }

        $validated = $request->validate([
            'originating_section' => 'nullable|string|max:255',
            'documents'           => 'required',
        ]);

        $documents = is_array($validated['documents']) 
            ? $validated['documents'] 
            : json_decode($validated['documents'], true);

        if (!is_array($documents) || count($documents) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'A ticket must contain at least one document.'
            ], 422);
        }

        // Validate each document item
        foreach ($documents as $index => $doc) {
            $code = trim($doc['document_code'] ?? $doc['code'] ?? '');
            $title = trim($doc['document_title'] ?? $doc['title'] ?? '');
            $classification = strtolower(trim($doc['classification'] ?? ''));

            if (empty($code)) {
                return response()->json(['success' => false, 'message' => "Document #" . ($index + 1) . " requires a Document Code."], 422);
            }
            if (empty($title)) {
                return response()->json(['success' => false, 'message' => "Document #" . ($index + 1) . " requires a Document Title."], 422);
            }
            if (!in_array($classification, ['addition', 'revision', 'deletion'])) {
                return response()->json(['success' => false, 'message' => "Invalid modification type on document #" . ($index + 1) . "."], 422);
            }
        }

        try {
            DB::beginTransaction();

            // 1. Update originating section if provided (SharePoint link & message remain untouched)
            if (!empty($validated['originating_section'])) {
                $ticket->update([
                    'originating_section' => trim($validated['originating_section'])
                ]);
            }

            // 2. Track retained document IDs
            $retainedIds = [];

            foreach ($documents as $doc) {
                $docId = $doc['id'] ?? null;
                $code = trim($doc['document_code'] ?? $doc['code'] ?? '');
                $title = trim($doc['document_title'] ?? $doc['title'] ?? '');
                $classification = strtolower(trim($doc['classification'] ?? ''));
                $sourceType = trim($doc['source_type'] ?? $doc['source'] ?? 'forms');
                $specificType = isset($doc['specific_type']) ? trim($doc['specific_type']) : ($doc['specificType'] ?? null);

                if ($docId && $ticketDoc = $ticket->documents()->where('id', $docId)->first()) {
                    $updateData = [
                        'document_code'  => $code,
                        'document_title' => $title,
                        'classification' => $classification,
                        'source_type'    => $sourceType,
                        'specific_type'  => $specificType ?: null,
                    ];
                    if (isset($doc['revising_master_document_id']) || isset($doc['revisingMasterId'])) {
                        $updateData['revising_master_document_id'] = $doc['revising_master_document_id'] ?? $doc['revisingMasterId'] ?? null;
                    }
                    $ticketDoc->update($updateData);
                    $retainedIds[] = $ticketDoc->id;
                } else {
                    $newDoc = $ticket->documents()->create([
                        'document_code'  => $code,
                        'document_title' => $title,
                        'classification' => $classification,
                        'source_type'    => $sourceType,
                        'specific_type'  => $specificType ?: null,
                        'status'         => 'pending',
                        'revising_master_document_id' => $doc['revising_master_document_id'] ?? $doc['revisingMasterId'] ?? null,
                    ]);
                    $retainedIds[] = $newDoc->id;
                }
            }

            // 3. Remove any documents not in the retained list
            $ticket->documents()->whereNotIn('id', $retainedIds)->delete();

            // 4. Recalculate ticket status
            $this->recalculateTicketStatus($ticket);

            DB::commit();

            $ticket->refresh();
            $ticket->load('documents.revisingMasterDocument');

            return response()->json([
                'success' => true,
                'message' => 'Ticket and document details updated successfully.',
                'ticket'  => $ticket,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update ticket details by admin failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update ticket details. Please try again.'], 500);
        }
    }

    public function resetSystem(Request $request){
        // Double check if user is IDC Admin
        $userRole = auth()->user()->role;
        if($userRole !== 'IDC Admin' && $userRole !== 'SuperAdmin'){
            return redirect()->back()->with('error', 'Unathorized Action');
        }
        try{
            // Disable Foreign key check first before deleting
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Truncate delets all records and resets auto-increment
            IsoTicketDocument::truncate(); //truncate the child first before the parent
            IsoTicket::truncate();
            
            // Turn the foreign key check back on
            DB::statement('SET FOREIGN_KEY_CHECKS=1');


            return redirect()->route('iso.idc.dashboard')
                ->with('msg', 'Ticketing system has been reset successfully!');
        } catch (\Exception $e){
            // Turn the foreign key check back on here as well just in case
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            \Log::error('Reset system failed: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to reset system. Please check logs');
        }
    }
    // ==================================
    // Getting Documents based on the office
    // ==================================
    public function getDocumentsByOffice(Request $request){
        $office = $request->query('office');

        if(!$office){
            return response()->json([]);
        }

        $documents = IsoMasterDocument::where('originating_section', $office)
            ->where('status', 'Active')
            ->select('id', 'document_code', 'document_title', 'revised_document_title', 'source_type', 'specific_type')
            ->orderBy('document_code')
            ->get();
            
        return response()->json($documents);
    }

    // ==================================
    // ISO Document management system (Registry)
    // ==================================
    public function registerTicket(IsoTicket $ticket){
        // Check if user is authorized
        $userRole = auth()->user()->role;
        $allowedRoles = ['IDC Admin', 'SuperAdmin'];

        if(!in_array($userRole, $allowedRoles)){
            return redirect()->back()->with('error', 'Uauthorized Action');
        }

        // Check if ticket is approved
        if($ticket->status !== 'approved'){
            return redirect()->back()->with('error', 'Only approved tickets can be registered');
        }

        // Check if already registered
        if($ticket->is_registered){
            return redirect()->back()->with('error','This ticket is already registered.');
        }

        try{
            DB::transaction(function() use($ticket){
                // Mark ticket as registered
                $ticket->is_registered = true;
                $ticket->save();

                // Process each document in the ticket
                foreach($ticket->documents as $document){
                    if($document->classification === 'addition'){
                        $masterDoc = IsoMasterDocument::create([
                            'document_code' => $document->document_code,
                            'document_title' => $document->document_title,
                            'source_type' => $document->source_type,
                            'specific_type' => $document->specific_type,
                            'originating_section' => $ticket->originating_section,
                            'current_revision' => 0,
                            'is_original' => 1,
                            'original_document_id' => null,
                            'status' => 'Active',
                            'registered_at' => $document->registered_at,
                            'source' => 'ticket',
                        ]);
                    } elseif ($document->classification === 'revision'){
                        $originalDoc = IsoMasterDocument::find($document->revising_master_document_id);
                        if(!$originalDoc){
                            throw new \Exception("Could not find the Original Document to revise (ID: {$document->revising_master_document_id})");
                        }

                        $originalDoc->timestamps = false; // Debug: Remove this if ever
                        $originalDoc->update([
                            'status' => 'Superseded',
                            'superseded_at'=> $document->registered_at,
                            'revised_document_title' => null,
                        ]);
                        $originalDoc->timestamps = true; //This too

                        $masterDoc = IsoMasterDocument::create([
                            'document_code' => $originalDoc->document_code,
                            'document_title' => $document->document_title,
                            'revised_document_title' => null,
                            'source_type' => $document->source_type,
                            'specific_type' => $document->specific_type,
                            'originating_section' => $ticket->originating_section,

                            'current_revision' => $originalDoc->current_revision + 1,
                            'is_original' => false,
                            'original_document_id' => $originalDoc->is_original
                                ? $originalDoc->id
                                : $originalDoc->original_document_id,
                            'status' => 'Active',
                            'registered_at' => $document->registered_at,
                            'source' => 'ticket',
                        ]);
                    } elseif ($document->classification === 'deletion'){
                        $docToDelete = IsoMasterDocument::find($document->revising_master_document_id);

                        if(!$docToDelete){
                            throw new \Exception("Document to delete not found (ID: {$document->revising_master_document_id})");
                        }

                        $docToDelete->timestamps = false;
                        $docToDelete->update([
                            'status' => 'Superseded',
                            'superseded_at' => $document->registered_at,
                        ]);
                        $docToDelete->timestamps = true;

                        // Create deletion record for audit trail
                        $masterDoc = IsoMasterDocument::create([
                            'document_code' => $docToDelete->document_code,
                            'document_title' => $docToDelete->document_title,
                            'source_type' => $docToDelete->source_type,
                            'specific_type' => $docToDelete->specific_type,
                            'originating_section' => $ticket->originating_section,
                            'current_revision' => $docToDelete->current_revision,
                            'is_original' => false,
                            'original_document_id' => $docToDelete->original_document_id ?? $docToDelete->id,
                            'status' => 'Deleted',
                            'registered_at' => $document->registered_at,
                            'deleted_at' => now(),
                            'source' => 'ticket',
                        ]);
                    }
                    $document->update([
                        'master_document_id' => $masterDoc->id,
                    ]);
                }

            });

            return redirect()->route('iso.idc.dashboard')
                ->with('msg','Ticket no:'.$ticket->ticket_number . ' has been registered successfully!');
        } catch (\Exception $e){
            \Log::error('Register ticket failed: '. $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to register ticket. Please try again');
        }
    }
}