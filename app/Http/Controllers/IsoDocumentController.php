<?php

namespace App\Http\Controllers;

use App\Models\IsoTicketDocument;
use App\Models\IsoTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
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
            $q->where('id', '=',  $search)
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
                'specific_type' => $document['specificType']
            ]);
        }
        return redirect()->route('iso.document')->with('success', 'Ticket created successfully!');
    }
    public function submitToIDC(IsoTicket $ticket){
        // Auth check
        $this->authTicketCheck($ticket);

        // Update ticket Status
        $ticket->update([
            'status' => 'submitted_to_idc'
        ]);
        return redirect()->route('iso.document')->with('success','Ticket submitted to IDC successfully!');
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

        // Email the Document Handler (Ticket Creator)
        Mail::to($ticket->creator->email)
            ->send(new TicketStatusChanged($ticket, $oldStatus, $changedBy));

        // Email all IDC Admins
        $idcAdmins = User::where('role', 'IDC Admin')->get();

        foreach ($idcAdmins as $admin){
            Mail::to($admin->email)
            ->send(new TicketStatusChanged($ticket,$oldStatus, $changedBy));
        }

        // TODO: Ask if they want to add save notes to comments

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

        // check if any document is on_hold
        $hasOnHold = $documents->contains(function($doc){
            return $doc->status === 'on_hold';
        });

        if($hasOnHold){
            $ticket->status = 'on_hold';
            $ticket->save();
            return;
        }

        // Check if all documents are approved
        $allApproved = $documents->every(function($doc){
            return $doc->status === 'approved';
        });

        if ($allApproved && $documents->count() > 0){
            $ticket->status = 'approved';
            $ticket->save();
            return;
        }
    }
}