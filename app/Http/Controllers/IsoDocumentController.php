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
use App\Mail\TicketStatusChanged;

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
        $tickets = $query->with('documents', 'creator.user')
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

    public function editDocument(IsoTicket $ticket){
        // Auth Check
        $this->authTicketCheck($ticket);

        // Load the ticket with its documents relationship
        $ticket -> load('documents');

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

        // Update the ticket
        $ticket->update([
            'originating_section' => $validated['originating_section'],
            'sharepoint_link'=> $validated['sharepoint_link'],
            'message_to_idc'=>$validated['message_to_idc']
        ]);

        // Delete all existing documents for the current ticket
        $ticket->documents()->delete();

        // Recreate documents with the new list
        foreach ($documents as $document){
            IsoTicketDocument::create([
                'ticket_id' => $ticket->id,
                'document_code' => $document['code'],
                'document_title'=> $document['title'],
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
        
        $ticket = IsoTicket::create([
            'originating_section'=> $validated['originating_section'],
            'sharepoint_link' => $validated['sharepoint_link'],
            'message_to_idc' => $validated['message_to_idc'],
            'status' => 'pending',
            'created_by' => auth()->id()
        ]);
        
        // Save each document
        foreach($documents as $document){
            IsoTicketDocument::create([
                'ticket_id'=> $ticket->id,
                'document_code' => $document['code'],
                'document_title'=> $document['title'],
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
        $query = IsoTicket::with(['documents', 'creator.user']);

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

        $document = IsoTicketDocument::findorFail($documentId);
        $document->status = $request->status;

        // Auto-set registered_at when approved
        if($request->status === 'approved'){
            $document->registered_at = now();
        }

        $document->save();

        // Check if we need to auto-update each ticket status
        $ticket = $document->ticket;
        $this->recalculateTicketStatus($ticket);

        $ticket->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Document status updated',
            'ticket_status' => $ticket->status
        ]);
    }

    public function recalculateTicketStatus($ticket){
        $documents = $ticket->documents;
        $oldStatus = $ticket->status;

        // check if any document is on_hold
        $hasOnHold = $documents->contains(function($doc){
            return $doc->status === 'on_hold';
        });

        if($hasOnHold){
            $ticket->status = 'on_hold';
            $ticket->save();

            if($oldStatus !== 'on_hold'){
                $this->sendTicketStatusNotification(
                    $ticket,
                    $oldStatus,
                    'IDC Admin - ' . auth()->user()->name
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
            if($oldStatus !== 'approved'){
                $this->sendTicketStatusNotification(
                    $ticket,
                    $oldStatus,
                    'IDC Admin - ' . auth()->user()->name
                );
            }
            return;
        }
    }

    private function sendTicketStatusNotification($ticket, $oldStatus, $changedBy){
        // Email the Document Handler (Ticket owner)
        Mail::to($ticket->creator->email)
            ->send(new TicketStatusChanged($ticket, $oldStatus, $changedBy));
        // Delay for the free trial of Mailtrap
        usleep(500000);

        // Email all the IDC Admins
        $idcAdmins = User::where('role', 'IDC Admin')->get();
        foreach ($idcAdmins as $admin){
            Mail::to($admin->email)
                ->send(new TicketStatusChanged($ticket, $oldStatus, $changedBy));
            // Delay for the free trial of Mailtrap
            usleep(500000);
        }
    }

    // ==================================
    // Reset Ticket Function
    // ==================================
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
            ->select('id', 'document_code', 'document_title', 'source_type', 'specific_type')
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
                            'superseded_at'=> now(),
                        ]);
                        $originalDoc->timestamps = true; //This too

                        $masterDoc = IsoMasterDocument::create([
                            'document_code' => $originalDoc->document_code,
                            'document_title' => $document->document_title,
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
                            'superseded_at' => now()
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
                ->with('msg','Ticket #'.$ticket->id . ' has been registered successfully!');
        } catch (\Exception $e){
            \Log::error('Register ticket failed: '. $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to register ticket. Please try again');
        }
    }
}