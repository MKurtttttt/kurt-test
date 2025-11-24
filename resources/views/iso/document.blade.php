<x-app-layout>
@php
function getStatusColor($status){
    $colors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'submitted_to_idc' => 'bg-blue-100 text-blue-800',
        'with_qmr' => 'bg-purple-100 text-purple-800',
        'approved'=> 'bg-green-100 text-green-800',
        'on_hold' => 'bg-red-100 text-red-800'
    ];
    return $colors[$status] ?? 'bg-gray-100 text-gray-800';
}
@endphp

<div class="min-h-screen">
<div class="container mx-auto">
    <div class="con-box">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> 
        </div>
        @endif
        @if (session('msg'))
            <div class="w-full bg-green-600 text-white rounded-xl px-4 py-2 mb-4" id ="msg">
                {{ session('msg') }}
            </div>
        @endif
        @if(session('error'))
            <div class="w-full bg-red-600 text-white rounded-xl px-4 py-2 mb-4" id="error-msg">
                {{ session('error') }}
            </div>
        @endif

        <div class="w-[95%] px-4 flex my-4 items-center">
            <img src="{{ asset('images/logos/school/soc_logo.png') }}" class="w-[100px] h-[100px] mr-2"/>
            <div class="w-full flex flex-col justify-center">
                <h1 class="text-[1.5rem] font-bold leading-tight">ISO Document Management System</h1>
                <span class="text-gray-500 text-sm"> Document Modification / Creation Notice (DMCN) Tracking</span>
            </div>
        </div>
        <hr class="w-full opacity-100">

        <!-- Status Tabs -->
         <div class="w-full flex">
            <a href="{{ route('iso.document', ['status' => 'all']) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'all' ? 'active_link' : '' }}">
                My Tickets
            </a>
            <a href="{{ route('iso.document', ['status' => 'submitted_to_idc']) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'submitted_to_idc' ? 'active_link' : '' }}">
                Submitted to IDC
            </a>
            <a href="{{ route('iso.document', ['status' => 'with_qmr']) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'with_qmr' ? 'active_link' : '' }}">
                With QMR
            </a>
            <a href="{{ route('iso.document', ['status' => 'approved']) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'approved' ? 'active_link' : '' }}">
                Approved
            </a>
            <a href="{{ route('iso.document', ['status' => 'on_hold']) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'on_hold' ? 'active_link' : '' }}">
                On-hold
            </a>
         </div>
         <hr class="mb-2 opacity-90 w-full">

        <!-- My Tickets Table -->
        <div id="mytickets" class="w-full flex flex-col border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            @if(count($tickets) > 0)
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Ticket ID</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Originating Section</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Documents</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr class="border-b hover:bg-gray-50 cursor-pointer">
                                <td class="px-4 py-3 text-sm font-mono text-blue-600">
                                    #{{ $ticket->id }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $ticket->originating_section }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                        {{ $ticket->documents_count }} document(s)
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-block px-2 py-1 rounded text-xs {{ getStatusColor($ticket->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $ticket->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <button 
                                            class="view-details-btn text-blue-600 hover:text-blue-800 text-sm font-semibold"
                                            data-ticket-id='{{ $ticket->id }}'
                                            data-ticket-section='{{ $ticket->originating_section }}'
                                            data-ticket-status='{{ $ticket->status }}'
                                            data-ticket-created='{{ $ticket->created_at->format('M d, Y h:i A') }}'
                                            data-ticket-creator='{{ $ticket->creator->name ?? 'Unknown' }}'
                                            data-ticket-sharepoint='{{ $ticket->sharepoint_link }}'
                                            data-ticket-message='{{ $ticket->message_to_idc }}'
                                            data-ticket-documents='@json($ticket->documents)'
                                        >
                                            View Details
                                        </button>
                                        <!-- Edit Button - Only show for pending tickets -->
                                         @if ($ticket-> status === 'pending')
                                            <button onclick="openEditModal({{ $ticket->id }})"
                                                class="bg-yellow-500 hover:bg-yellow-600 text-gray px-3 py-1 rounded text-sm">
                                                Edit
                                            </button>
                                            <!-- Delete Buton -->
                                            <button onclick="confirmDelete({{ $ticket->id }})"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                                Delete
                                            </button>
                                         @endif
                                    </div>
                                </td> 
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="w-full flex items-center justify-center text-sm text-gray-400 pt-4 pb-12">
                    <span class="italic">No tickets found.</span>
                </div>
            @endif
        </div>

        <!-- Submitted to IDC table -->
           <div id="submitted" class="w-full flex-col border border-gray-200 rounded-lg shadow-sm overflow-hidden inactive_link">
                <div class="w-full flex items-center justify-center text-sm text-gray-400 pt-4 pb-12">
                    <span class="italic">No tickets with QMR.</span>
                </div>
           </div>

        <!-- Submitted to QMR table -->
        <div id="qmr" class="w-full flex flex-col border border-gray-200 rounded-lg shadow-sm overflow-hidden inactive_link">
            <div class="w-full flex items-center justify-center text-sm text-gray-400 pt-4 pb-12">
                <span class="italic">No tickets with QMR.</span>
            </div>
        </div>
        <!-- Approved Table -->
            <div id="onhold" class="w-full flex flex-col border border-gray-200 rounded-lg shadow-sm overflow-hidden inactive_link">
            <div class="w-full flex items-center justify-center text-sm text-gray-400 pt-4 pb-12">
                <span class="italic">No tickets on-hold</span>
            </div>
            </div>

        <!-- Action buttons -->
        <section class="py-4 w-full flex justify-center items-center space-x-4">
        <button id="create_ticket_btn" class="flex items-center justify-center bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600">
            <img src="{{ asset('images/icons/add.png') }}" alt="Add Icon" class="w-6 h-6 mr-6"/>
            <span>Create new Ticket</span>
        </button>
        </section>
    </div>
</div>
</div>

<!-- Modal for creating new Ticket -->
<div id="ticket_modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="text-xl font-bold">Create New DMCN Ticket</h2>
            <button id="close_modal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        
        <form id="ticket_form" action="{{ route('iso.document.store') }}" method="POST" class="modal-body">
            @csrf
            <!-- Originating Section -->
            <div class="form-group">
                <label class="form-label">Originating Section (Department) <span class="text-red-500">*</span></label>
                <input type="text" name="originating_section" class="form-input" placeholder="e.g., Human Resources, IT Department" required>
            </div>

            <hr class="my-4">

            <!-- Add Document Section -->
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <h3 class="font-semibold mb-3 text-gray-700">Add Documents to Ticket.</h3>
                <div class="grid grid-cols-2 gap-3">
                    <!-- Document Code -->
                    <div class="form-group mb-2">
                        <label class="form-label text-sm">Document Code</label>
                        <input type="text" id="doc_code" class="form-input" placeholder="Enter Document Code">
                    </div>
                    <!-- Document Title -->
                    <div class="form-group mb-2">
                        <label class="form-label text-sm">Document Title</label>
                        <input type="text" id="doc_title" class="form-input" placeholder="Enter Document Title">
                    </div>
                    <!-- Classification -->
                    <div class="form-group mb-2">
                        <label class="form-label text-sm">Classification</label>
                        <select id="doc_classification" class="form-input">
                            <option value="">Select...</option>
                            <option value="revision">For revision</option>
                            <option value="addition">Addition</option>
                            <option value="deletion">Deletion</option>
                        </select>
                    </div>

                    <!-- Source Document Type -->
                    <div class="form-group mb-2">
                        <label class="form-label text-sm">Source Document</label>
                        <select id="doc_source" class="form-input">
                            <option value="">Select...</option>
                            <option value="eoms">EOMS Manual</option>
                            <option value="procedures">Procedures</option>
                            <option value="forms">Forms</option>
                            <option value="records">Records Management Manual</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                </div>
                <!-- Specific Type (conditional) -->
                <div id="specific_type_section" class="form-group mb-2" style="display: none;">
                    <label class="form-label text-sm">Specific Type</label>
                    <select id="doc_specific_type" class="form-input">
                        <option value="">Select...</option>
                    </select>
                </div>

                <button type="button" id="add_document_btn" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 mt-2">
                    + Add Document to List
                </button>
            </div>

            <!-- Documents List Table -->
            <div class="mb-4">
                <h3 class="font-semibold mb-2 text-gray-700">Documents in this Ticket (<span id="doc_count">0</span>)</h3>
                <div class="border border-gray-300 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Code</th>
                                <th class="px-3 py-2 text-left">Title</th>
                                <th class="px-3 py-2 text-left">Classification</th>
                                <th class="px-3 py-2 text-left">Source</th>
                                <th class="px-3 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="documents_list">
                            <tr id="empty_row">
                                <td colspan="5" class="text-center py-4 text-gray-400 italic">No documents added yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Hidden input to store documents JSON-->
            <input type="hidden" name="documents" id="documents_json">

            <hr class="my-4">

            <!-- SharePoint Folder Link -->
            <div class="form-group">
                <label class="form-label">SharePoint Folder Link <span class="text-red-500">*</span></label>
                <input type="url" name="sharepoint_link" class="form-input" placeholder="https://hau.sharepoint.com/..." required>
                <small class="text-gray-500">Provide the link to the SharePoint folder containing the DMCN and actual document</small>
            </div>

            <!-- Email Message to IDC -->
            <div class="form-group">
                <label class="form-label">Message to IDC <span class="text-red-500">*</span></label>
                <textarea name="message_to_idc" class="form-input" rows="4" placeholder="Write a message to the Institutional Document Controller..." required></textarea>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded hover:bg-green-700 font-semibold">
                    Submit Ticket to IDC
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Viewing Ticket Details -->
<div id="details_modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="text-xl font-bold">Ticket Details - <span id="detail_ticket_id"></span></h2>
            <button id="close_details_modal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        
        <div class="modal-body">
            <!-- Ticket Information -->
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <h3 class="font-semibold mb-3 text-gray-700">Ticket Information</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm text-gray-600">Originating Section:</label>
                        <p class="font-medium" id="detail_section"></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Status:</label>
                        <p id="detail_status"></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Created:</label>
                        <p class="font-medium" id="detail_created"></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Created By:</label>
                        <p class="font-medium" id="detail_creator"></p>
                    </div>
                </div>
            </div>

            <!-- SharePoint Link -->
            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                <label class="text-sm text-gray-600 block mb-2">SharePoint Folder:</label>
                <a id="detail_sharepoint" href="#" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium break-all">
                    <!-- Link will be populated here -->
                </a>
            </div>

            <!-- Documents List -->
            <div class="mb-4">
                <h3 class="font-semibold mb-2 text-gray-700">Documents in this Ticket</h3>
                <div class="border border-gray-300 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Code</th>
                                <th class="px-3 py-2 text-left">Title</th>
                                <th class="px-3 py-2 text-left">Classification</th>
                                <th class="px-3 py-2 text-left">Source</th>
                            </tr>
                        </thead>
                        <tbody id="detail_documents_list">
                            <!-- Documents will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Message to IDC -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <label class="text-sm text-gray-600 block mb-2">Message to IDC:</label>
                <p class="text-gray-800 whitespace-pre-wrap" id="detail_message"></p>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- EDIT TICKET MODAL -->
<!-- ============================================ -->
<div id="edit_modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-scroll m-4">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Edit Ticket</h2>
            <button id="close_edit_modal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form id="edit_ticket_form" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Originating Section -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Originating Section <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="edit_originating_section" 
                           name="originating_section"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <!-- SharePoint Link -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        SharePoint Link <span class="text-red-500">*</span>
                    </label>
                    <input type="url" 
                           id="edit_sharepoint_link" 
                           name="sharepoint_link"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="https://..."
                           required>
                </div>

                <!-- Message to IDC -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Message to IDC <span class="text-red-500">*</span>
                    </label>
                    <textarea id="edit_message_to_idc" 
                              name="message_to_idc"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              required></textarea>
                </div>

                <!-- Documents Section -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Documents <span class="text-red-500">*</span>
                        <span class="text-gray-500 text-xs ml-2">(Total: <span id="edit_doc_count">0</span>)</span>
                    </label>

                    <!-- Add Document Form -->
                    <div class="bg-gray-50 p-4 rounded-md mb-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Document Code</label>
                                <input type="text" id="edit_doc_code" class="w-full px-2 py-1 border rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Document Title</label>
                                <input type="text" id="edit_doc_title" class="w-full px-2 py-1 border rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Classification</label>
                                <select id="edit_doc_classification" class="w-full px-2 py-1 border rounded text-sm">
                                    <option value="">Select...</option>
                                    <option value="revision">Revision</option>
                                    <option value="addition">Addition</option>
                                    <option value="deletion">Deletion</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Source Type</label>
                                <select id="edit_doc_source" class="w-full px-2 py-1 border rounded text-sm">
                                    <option value="">Select...</option>
                                    <option value="eoms">EOMS Manual</option>
                                    <option value="procedures">Procedures</option>
                                    <option value="forms">Forms</option>
                                    <option value="records">Records Management</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                        </div>
                        <div id="edit_specific_type_section" style="display: none;" class="mb-3">
                            <label class="block text-xs text-gray-600 mb-1">Specific Type</label>
                            <select id="edit_doc_specific_type" class="w-full px-2 py-1 border rounded text-sm">
                                <option value="">Select...</option>
                            </select>
                        </div>
                        <button type="button" id="edit_add_document_btn" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                            Add Document
                        </button>
                    </div>

                    <!-- Documents List Table -->
                    <div class="border rounded-md overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left">Code</th>
                                    <th class="px-3 py-2 text-left">Title</th>
                                    <th class="px-3 py-2 text-left">Class</th>
                                    <th class="px-3 py-2 text-left">Source</th>
                                    <th class="px-3 py-2 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="edit_documents_list">
                                <tr id="edit_empty_row">
                                    <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                                        No documents added yet
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Hidden input for documents JSON -->
                <input type="hidden" id="edit_documents_json" name="documents" value="[]">

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" id="cancel_edit_btn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="delete_form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
</x-app-layout>

<style>
    .container { 
        width: 100%;
        display: flex; 
        justify-content: center;
        padding: 2rem 0;
    }
    
    .con-box { 
        border-radius: 10px; 
        width: 95%;
        background-color: white;
        display: flex; 
        flex-direction: column;
        align-items: center;
        padding: 1rem 0;
    }

    .active_link { 
        border-bottom: 4px solid #FFD700;
        font-weight: 700;
        transition: 150ms;
    }

    .active_link:hover { 
        background-color: rgb(230, 230, 230);
    }

    .inactive_link { 
        display: none;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        overflow-y: auto;
        padding: 2rem 0;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 10px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<script>
    // Modal Handling
    const modal = document.getElementById('ticket_modal');
    const createBtn = document.getElementById('create_ticket_btn');
    const closeBtn = document.getElementById('close_modal');

    createBtn.addEventListener('click', ()=> {
        modal.classList.add('active');
    });

    closeBtn.addEventListener('click', () =>{
        modal.classList.remove('active');
    })
    // Close modal when clicking outside
    modal.addEventListener('click', (e) =>{
        if (e.target === modal){
            modal.classList.remove('active');
        }
    });

    // Document Management
    let documents = [];
    const docSource = document.getElementById('doc_source');
    const specificTypeSection = document.getElementById('specific_type_section');
    const specificTypeSelect = document.getElementById('doc_specific_type');

    // Source Type options
    const sourceTypeOptions = {
        eoms: [
            { value: '4.1', label: '4.1 Interested Parties'},
            { value: '4.2', label: '4.2 Risk Assessment'},
            { value: '7.4', label: '7.4 Communication'},
            { value: '8.1', label: '8.1 EOMS Plan'}
        ],
        records: [
            { value: '3.0', label: '3.0 Records Retention Schedule'},
            { value: '4.0', label: '4.0 Definition and Records Series Title'}
        ]
    };

    // Show/hide specific type dropdown
    docSource.addEventListener('change', () => {
        const source = docSource.value;

        if (source === 'eoms' || source === 'records'){
            specificTypeSection.style.display = 'block';
            specificTypeSelect.innerHTML = `<option value="">Select... </option>`;

            const options = sourceTypeOptions[source];
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                specificTypeSelect.appendChild(option);
            });
        } else {
            specificTypeSection.style.display = 'none';
            specificTypeSelect.value = '';
        }
    });

    // Add Document to List
    document.getElementById('add_document_btn').addEventListener('click', () => {
        const code = document.getElementById('doc_code').value.trim();
        const title = document.getElementById('doc_title').value.trim();
        const classification = document.getElementById('doc_classification').value;
        const source = document.getElementById('doc_source').value;
        const specificType = document.getElementById('doc_specific_type').value;

        // Validation
        if (!code || !title || !classification || !source){
            alert('Please select a specific type');
            return;
        }

        if ((source === 'eoms' || source === 'records') && !specificType){
            alert('Please select a specific type');
            return;
        }

        // Create document object
        const doc = {
            code,
            title,
            classification,
            source,
            specificType: specificType || null,
            id: Date.now() //Give a unique ID for removal
        };
        documents.push(doc);
        updateDocumentsList();
        clearDocumentForm();
    });

    function updateDocumentsList() {
        const tbody = document.getElementById('documents_list');
        const emptyRow = document.getElementById('empty_row');
        const docCount = document.getElementById('doc_count');

        // Update docCount
        docCount.textContent = documents.length;

        // Update hidden input
        document.getElementById('documents_json').value = JSON.stringify(documents);

        if(documents.length === 0){
            emptyRow.style.display = 'table-row';
            // Remove all rows except empty row
            Array.from(tbody.children).forEach(row => {
                if (row.id !== 'empty_row'){
                    row.remove();
                }
            });
            return;
        }
        emptyRow.style.display = 'none';

        // Clear all rows except empty row
        Array.from(tbody.children).forEach(row => {
            if (row.id !== 'empty_row'){
                row.remove();
            }
        });

        // Rebuild table
        documents.forEach(doc => {
            const row = document.createElement('tr');
            row.className = "border-t";

            const sourceLabel = getSourceLabel(doc.source, doc.specificType);
            const classLabel = doc.classification.charAt(0).toUpperCase() + doc.classification.slice(1);

            row.innerHTML = `
                <td class="px-3 py-2">${doc.code}</td>
                <td class="px-3 py-2">${doc.title}</td>
                <td class="px-3 py-2">
                    <span class="inline-block px-2 py-1 text-xs rounded ${getClassificationColor(doc.classification)}">
                        ${classLabel}
                    </span>
                </td>
                <td class="px-2 py-2">${sourceLabel}</td>
                <td class="px-2 py-2" text-center">
                    <button type="button" onclick="removeDocument(${doc.id})" class="text-red-600" hover:text-red-800 text-sm">
                     Remove
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function getSourceLabel(source, specificType){
        const labels = {
            eoms: 'EOMS Manual',
            procedures: 'Procedures',
            forms: 'Forms',
            records: 'Records Management',
            others: 'Others'
        };
        let label = labels[source] || source;
        if (specificType) {
            label += ` (${specificType})`;
        }
        return label;
    }

    function getClassificationColor(classification){
        const colors = {
            revision: 'bg-yellow-200 text-yellow-800',
            addition: 'bg-green-200 text-green-800',
            deletion: 'bg-red-200 text-red-800'
        };
        return colors[classification] || 'bg-gray-200 text-gray-800';
    }

    function removeDocument(id){
        documents = documents.filter(doc => doc.id !== id);
        updateDocumentsList();
    }

    function clearDocumentForm(){
        document.getElementById('doc_code').value = '';
        document.getElementById('doc_title').value = '';
        document.getElementById('doc_classification').value = '';
        document.getElementById('doc_source').value = '';
        document.getElementById('doc_specific_type').value = '';
        specificTypeSection.style.display = 'none';
    }

    //Form submission Validation
    document.getElementById('ticket_form').addEventListener('submit', (e) => {
        if (documents.length === 0){
            e.preventDefault();
            alert('Please add at least one document to the ticket');
            return false;
        }
    })

    // Auto-hide messages in 5 seconds or 5000 milliseconds
    setTimeout(() =>{
        const msgElement = document.getElementById('msg');
        if(msgElement) msgElement.style.display = 'none';

        const errorElement = document.getElementById('error-msg');
        if(errorElement) errorElement.style.display = 'none';
    }, 5000);

    // ============================================
    // VIEW TICKET DETAILS MODAL
    // ============================================

    const detailsModal = document.getElementById('details_modal');
    const closeDetailsBtn = document.getElementById('close_details_modal');

    // Close details modal
    closeDetailsBtn.addEventListener('click', () => {
        detailsModal.classList.remove('active');
    });

    // Close when clicking outside
    detailsModal.addEventListener('click', (e) => {
        if (e.target === detailsModal) {
            detailsModal.classList.remove('active');
        }
    });

    // Handle all "View Details" button clicks
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('view-details-btn')) {
            // Get the ticket data from the button's data attributes
            const ticketId = e.target.dataset.ticketId;
            const section = e.target.dataset.ticketSection;
            const status = e.target.dataset.ticketStatus;
            const created = e.target.dataset.ticketCreated;
            const creator = e.target.dataset.ticketCreator;
            const sharepoint = e.target.dataset.ticketSharepoint;
            const message = e.target.dataset.ticketMessage;
            const documentsJson = e.target.dataset.ticketDocuments;
            const documentsList = document.getElementById('detail_documents_list');

            // TODO: Remove this Debug in the future
            console.log('Raw JSON string:', documentsJson);
            console.log('Type:', typeof documentsJson);
            console.log('length', documentsJson.length);

            // Debug: Try to parse with error Handling
            let ticketDocuments = [];
            try{
                ticketDocuments = JSON.parse(documentsJson)
                console.log('Parsed Succesfully:', documents);
            } catch(error){
                console.error('JSON parse failed:', error);
                console.error('Failed string:', documentsJson);
                alert('Error loading ticket documents. Check console.');
                return;
            }
            
            // // Parse the documents JSON string into an array
            // const documents = JSON.parse(documentsJson);
            
            // Fill the modal with the data
            document.getElementById('detail_ticket_id').textContent = '#' + ticketId;
            document.getElementById('detail_section').textContent = section;
            document.getElementById('detail_created').textContent = created;
            document.getElementById('detail_creator').textContent = creator;
            document.getElementById('detail_message').textContent = message;
            
            // Set the SharePoint link
            const sharepointLink = document.getElementById('detail_sharepoint');
            sharepointLink.href = sharepoint;
            sharepointLink.textContent = sharepoint;
            
            // Format and display status with color
            const statusElement = document.getElementById('detail_status');
            const statusText = status.replace(/_/g, ' ');
            const statusFormatted = statusText.charAt(0).toUpperCase() + statusText.slice(1);
            statusElement.innerHTML = `<span class="inline-block px-2 py-1 rounded text-xs ${getStatusColorJS(status)}">${statusFormatted}</span>`;
            
            // Fill the documents table
            documentsList.innerHTML = ''; // Clear existing rows
            
            ticketDocuments.forEach(doc => {
                const row = document.createElement('tr');
                row.className = 'border-t';
                
                const classLabel = doc.classification.charAt(0).toUpperCase() + doc.classification.slice(1);
                const sourceLabel = getSourceLabel(doc.source_type, doc.specific_type);
                
                row.innerHTML = `
                    <td class="px-3 py-2">${doc.document_code}</td>
                    <td class="px-3 py-2">${doc.document_title}</td>
                    <td class="px-3 py-2">
                        <span class="inline-block px-2 py-1 text-xs rounded ${getClassificationColor(doc.classification)}">
                            ${classLabel}
                        </span>
                    </td>
                    <td class="px-3 py-2">${sourceLabel}</td>
                `;
                
                documentsList.appendChild(row);
            });
            
            // Show the modal
            detailsModal.classList.add('active');
        }
    });

    // Helper function for status colors in JavaScript
    function getStatusColorJS(status) {
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'submitted_to_idc': 'bg-blue-100 text-blue-800',
            'with_qmr': 'bg-purple-100 text-purple-800',
            'approved': 'bg-green-100 text-green-800',
            'on_hold': 'bg-red-100 text-red-800',
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }

// ============================================
// EDIT TICKET FUNCTIONALITY
// ============================================
    let editDocuments = [];
    let currentEditTicketId = null;

    const editModal = document.getElementById('edit_modal');
    const closeEditBtn = document.getElementById('close_edit_modal');
    const cancelEditBtn = document.getElementById('cancel_edit_btn');
    const editDocSource = document.getElementById('edit_doc_source');
    const editSpecificTypeSection = document.getElementById('edit_specific_type_section');
    const editSpecificTypeSelect = document.getElementById('edit_doc_specific_type');

    // Close edit modal
    closeEditBtn.addEventListener('click', ()=> {
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
        resetEditForm();
    });

    cancelEditBtn.addEventListener('click', ()=> {
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
        resetEditForm();
    });

    // Close when clicking outside
    editModal.addEventListener('click', (e)=> {
        if (e.target === editModal){
            editModal.classList.add('hidden');
            editModal.classList.remove('flex');
            resetEditForm();
        }
    });

    // Handle Source Type Change (for Edit Modal)
    editDocSource.addEventListener('change', () => {
        const source = editDocSource.value;

        if(source === 'eoms' || source === 'records'){
            editSpecificTypeSection.style.display = 'block';
            editSpecificTypeSelect.innerHTML = `<option value="">Select...</option>`;

            const options = sourceTypeOptions[source];
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                editSpecificTypeSelect.appendChild(option);
            });
        } else {
            editSpecificTypeSection.style.display = 'none';
            editSpecificTypeSelect.value = '';
        }
    });

    // Add document to Edit List
    document.getElementById('edit_add_document_btn').addEventListener('click', () => {
        const code = document.getElementById('edit_doc_code').value.trim();
        const title =document.getElementById('edit_doc_title').value.trim();
        const classification = document.getElementById('edit_doc_classification').value;
        const source = document.getElementById('edit_doc_source').value;
        const specificType = document.getElementById('edit_doc_specific_type').value;

        // Validation
        if (!code || !title || !classification || !source){
            alert('Please fill in all Document details (Code, Title, Classification, and Source).')
            return;
        }
        if((source === 'eoms' || source === 'records') && !specificType){
            alert('Please select a specific type');
            return;
        }
        // Create document object
        const doc = {
            code,
            title,
            classification,
            source,
            specificType: specificType || null,
            id: Date.now() + Math.random()
        };

        editDocuments.push(doc);
        updateEditDocumentsList();
        clearEditDocumentForm();
    });

    // Open Edit Modal and fetch ticket data
    function openEditModal(ticketId){
        currentEditTicketId = ticketId;

        // Fetch ticket from server
        fetch(`/iso/document/${ticketId}/edit`)
            .then(response => {
                if(!response.ok){
                    throw new Error('Failed to fetch ticket data')
                }
                return response.json();
            })
            .then(ticket=> {
                // Fill form with existing data
                document.getElementById('edit_originating_section').value = ticket.originating_section;
                document.getElementById('edit_sharepoint_link').value = ticket.sharepoint_link;
                document.getElementById('edit_message_to_idc').value = ticket.message_to_idc;

                // Set form action url
                document.getElementById('edit_ticket_form').action = `/iso/document/${ticketId}`;

                // Load existing documents
                editDocuments = ticket.documents.map(doc => ({
                    code: doc.document_code,
                    title: doc.document_title,
                    classification: doc.classification,
                    source: doc.source_type,
                    specificType: doc.specific_type,
                    id: Date.now() + Math.random() //Unique ID for removal
                }));
                updateEditDocumentsList();

                // Show modal
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            })
            .catch(error => {
                console.error('Error: ', error);
                alert('Failed to load ticket data. Please try again.')
            });
    }
    // Update Edit Documents List
    function updateEditDocumentsList(){
        const tbody = document.getElementById('edit_documents_list');
        const emptyRow = document.getElementById('edit_empty_row');
        const docCount = document.getElementById('edit_doc_count');

        docCount.textContent = editDocuments.length;
        document.getElementById('edit_documents_json').value = JSON.stringify(editDocuments);

        // Clear all rows *except* the empty row placeholder
        Array.from(tbody.children).forEach(row => {
            if(row.id !== 'edit_empty_row'){
                row.remove();
            }
        });

        if (editDocuments.length === 0){
            if(emptyRow){
                emptyRow.style.display = 'table-row';
            }
            return;
        }

        // If documents exist, hide the empty row placeholder
        if(emptyRow){
            emptyRow.style.display = 'none';
        }

        // Rebuild table rows
        editDocuments.forEach(doc => {
            const row = document.createElement('tr');
            row.className = "border-t";

            const sourceLabel = getSourceLabel(doc.source, doc.specificType);
            const classLabel = doc.classification.charAt(0).toUpperCase() + doc.classification.slice(1);

            row.innerHTML = `
                <td class="px-3 py-2">${doc.code}</td>
                <td class="px-3 py-2">${doc.title}</td>
                <td class="px-3 py-2">
                    <span class="inline-block px-2 py-1 text-xs rounded ${getClassificationColor(doc.classification)}">
                        ${classLabel}
                    </span>
                </td>
                <td class="px-2 py-2">${sourceLabel}</td>
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="removeEditDocument(${doc.id})" class="text-red-600 hover:text-red-800 text-sm">
                        Remove
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    // Remove Document from Edit List
    function removeEditDocument(id){
        editDocuments = editDocuments.filter(doc => doc.id !== id);
        updateEditDocumentsList();
    }

    // Clear Edit Document Form
    function clearEditDocumentForm(){
        // The title and code needs to be swapped.
        document.getElementById('edit_doc_title').value = '';
        document.getElementById('edit_doc_code').value = '';
        document.getElementById('edit_doc_classification').value = '';
        document.getElementById('edit_doc_source').value = '';
        document.getElementById('edit_doc_specific_type').value = '';
        editSpecificTypeSection.style.display = 'none';
    }

    // Reset Entire edit form
    function resetEditForm(){
        document.getElementById('edit_ticket_form').reset();
        editDocuments = [];
        updateEditDocumentsList();
        clearEditDocumentForm();
        currentEditTicketId = null;
    }

    // Form submission Validation
    document.getElementById('edit_ticket_form').addEventListener('submit', (e)=>{
        if (editDocuments.length === 0){
            e.preventDefault();
            alert('Please add at least one document to the ticket');
            return false;
        }
    })

    // ============================================
    // DELETE TICKET FUNCTIONALITY
    // ============================================
    function confirmDelete(ticketId){
        // Show confirmation dialog
        const confirmed = confirm(
            'Are you sure you want to delete this ticket?\n\n' +
            'This action cannot be undone. All documents in this ticket will also be deleted.'
        );

        if (confirmed){
            // Get the hidden form
            const form = document.getElementById('delete_form');

            // Set the form action to the delete route
            form.action = `/iso/document/${ticketId}`;

            // Submit the form
            form.submit();
        }
    }
</script>