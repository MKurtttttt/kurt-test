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
            <!-- Success/Error messages -->
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

            <!-- Header -->
            <div class="w-[95%] px-4 flex my-4 items-center">
                <img src="{{ asset('images/logos/school/soc_logo.png') }}"class="w-[100px] h-[100px] mr-2"/>
                <div class="w-full flex flex-col justify-center">
                    <h1 class="text-[1.5rem] font-bold leading-tight text-purple-700">IDC Management Dashboard</h1>
                    <span class="text-gray-500 text-sm">Institutional Document Controller - Ticket Management</span>
                </div>
                <!-- TODO: Remove this in the future (Just for debugging purposes) -->
                <a href="{{ route('iso.document') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold">
                Switch to Document Handler View
            </a>
            </div>
            <hr class="w-full opacity-100">

            <!-- Search Box  (Same as the one on the document blade) -->
            <div class="w-full px-4 py-3 bg-gray-50 rounded-lg mb-b">
                <form method="GET" action="{{ route('iso.idc.dashboard') }}" class="flex gap-3 items-center">
                    <!-- Preserve status filter -->
                    <input type="hidden" name="status" value="{{ $statusFilter ?? 'submitted_to_idc' }}">

                    <!-- Search Input -->
                    <div class="flex-1">
                        <input type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Search by Ticket ID, Section, Document Code, or document Title..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Search button -->
                    <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold">
                        Search
                    </button>

                    <!-- Clear Button (only show if searching) -->
                    @if ($search ?? false)
                        <a href="{{ route('iso.idc.dashboard', ['status' => $statusFilter ?? 'submitted_to_idc']) }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Search results info -->
            @if($search ?? false)
                <div class="w-full px-4 py-2 bg-blue-50 border-l-4 border-blue-500 mb-4">
                    <p class="text-sm text-blue-800">
                        <strong>Searching for:</strong> "{{ $search }}"
                        <span class="text-blue-600">({{ count($tickets) }} result{{ count($tickets) != 1 ? 's': '' }} found)</span>
                    </p>
                </div>
            @endif

            <!-- TODO: Status tabs will go here next -->
            <div class="w-full flex">
                <a href="{{ route('iso.idc.dashboard', ['status' => 'submitted_to_idc', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'submitted_to_idc') === 'submitted_to_idc' ? 'active_link' : '' }}">
                    Submitted to IDC
                </a>
                <a href="{{ route('iso.idc.dashboard', ['status' => 'with_qmr', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'with_qmr') === 'with_qmr' ? 'active_link' : '' }}">
                    With QMR
                </a>
                <a href="{{ route('iso.idc.dashboard', ['status' => 'approved', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'approved') === 'approved' ? 'active_link' : '' }}">
                    Approved
                </a>
                <a href="{{ route('iso.idc.dashboard', ['status' => 'on_hold', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'on_hold') === 'on_hold' ? 'active_link' : '' }}">
                    On Hold
                </a>
                <a href="{{ route('iso.idc.dashboard', ['status' => 'all', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'all' ? 'active_link' : '' }}">
                    All Tickets
                </a>
            </div>
            <!-- TODO: Tickets table will go here -->
            <!-- Tickets Table -->
            <div class="w-full flex flex-col border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                @if(count($tickets) > 0)
                    <table class="w-full">                      
                        <thead class="bg-gray-100 border-b">
                            <tr>  
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Ticket ID</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Originating Section</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created By</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Documents</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Submitted</th>
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
                                        {{ $ticket->creator->name ?? 'Unknown' }}
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
                                            <!-- Change Status Button -->
                                            <button onclick="openStatusModal({{ $ticket->id }}, '{{ $ticket->status }}')"
                                                class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-sm">
                                                Change Status
                                            </button>
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
        </div>
    </div>
</div>
<!-- View Details modal -->
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
                        <p class="font-medium" id="detail_status"></p>
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
            <!-- Sharepoint Link -->
            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                <label class="text-sm text-gray-600 block mb-2">Sharepoint Folder:</label>
                <a id="detail_sharepoint" href="#" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium break-all"></a>
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
                        <tbody id="detail_documents_list"></tbody>
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
<!-- Change Status Modal -->
<div id="status_modal" class="modal-overlay">
    <div class="modal-content max-w-md">
        <div class="modal-header">
            <h2 class="text-xl font-bold text-purple-700">Change Ticket Status</h2>
            <button id="close_status_modal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <form id="status_form" method="POST" class="modal-body">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Current Status: <span id="current_status" class="font-bold text-purple-600"></span>
                </label>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    New Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="new_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    <option value="">Select new status...</option>
                    <option value="submitted_to_idc">Submitted to IDC</option>
                    <option value="with_qmr">Send to QMR</option>
                    <option value="approved">Approved</option>
                    <option value="on_hold">Put On Hold</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" id="cancel_status_btn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-md font-semibold">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>
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
        border-bottom: 4px solid #9333EA; /* Purple color for IDC */
        font-weight: 700;
        transition: 150ms;
    }

    .active_link:hover{
        background-color: rgb(230,230,230);
    }

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
</style>

<script>
    // Auto-hide messages after 5 seconds
    setTimeout(() => {
        const msgElement = document.getElementById('msg');
        if(msgElement) msgElement.style.display = 'none';
        const errorElement = document.getElementById('error-msg');
        if(errorElement) errorElement.styledisplay = 'none';
    }, 5000)

    // ============================================
    // VIEW DETAILS MODAL
    // ============================================
    const detailsModal = document.getElementById('details_modal');
    const closeDetailsBtn = document.getElementById('close_details_modal');

    // Close details modal
    closeDetailsBtn.addEventListener('click', ()=> {
        detailsModal.classList.remove('active');
    })

    // Close when clicking outside
    detailsModal.addEventListener('click', (e)=>{
        if(e.target === detailsModal){
            detailsModal.classList.remove('active');
        }
    });

    // Handle "View Details" button clicks
    document.addEventListener('click', (e)=> {
        if (e.target.classList.contains('view-details-btn')){
            const ticketId = e.target.dataset.ticketId;
            const section = e.target.dataset.ticketSection;
            const status = e.target.dataset.ticketStatus;
            const created = e.target.dataset.ticketCreated;
            const creator = e.target.dataset.ticketCreator;
            const sharepoint = e.target.dataset.ticketSharepoint;
            const message = e.target.dataset.ticketMessage;
            const documentsJson = e.target.dataset.ticketDocuments;

            // Parse documents json
            let ticketDocuments = [];
            try{
                ticketDocuments = JSON.parse(documentsJson);
            } catch(error) {
                console.error('JSON parse failed: ', error);
                alert('Error loading ticket documents.');
                return;
            }

            // Fill modal with data
            document.getElementById('detail_ticket_id').textContent = '#' + ticketId;
            document.getElementById('detail_section').textContent = section;
            document.getElementById('detail_created').textContent = created;
            document.getElementById('detail_creator').textContent = creator;
            document.getElementById('detail_message').textContent = message;

            // Set SharePoint Link
            const sharepointLink = document.getElementById('detail_sharepoint');
            sharepointLink.href = sharepoint;
            sharepointLink.textContent = sharepoint

            // Format and display status
            const statusElement = document.getElementById('detail_status');
            const statusText = status.replace(/_/g, ' ');
            const statusFormatted = statusText.charAt(0).toUpperCase() + statusText.slice(1);
            statusElement.innerHTML = `<span class="inline-block px-2 py-1 rounded text-xs ${getStatusColorJS(status)}"> ${statusFormatted}</span>`;

            // FIll documents table
            const documentsList = document.getElementById('detail_documents_list');
            documentsList.innerHTML = '';

            ticketDocuments.forEach(doc => {
                const row = document.createElement('tr');
                row.className = 'border-t';

                const classLabel = doc.classification.charAt(0).toUpperCase() + doc.classification.slice(1);
                const sourceLabel = getSourceLabel(doc.source_type, doc.specific_type);

                row.innerHTML = `
                    <td class="px-3 py-2">${doc.document_code}</td>
                    <td class="px-3 py-2">${doc.document_title}</td>
                    <td class="px-3 py-2">$
                        <span class="inline-block px-2 py-1 text-xs rounded ${getClassificationColor(doc.classification)}">
                            ${classLabel}
                        </span>
                    </td>
                    <td class="px-3 py-2">${sourceLabel}</td>
                `;

                documentsList.appendChild(row);
            });

            // Show modal
            detailsModal.classList.add('active');
        }
    });

    // Helper Functions
    function getStatusColorJS(status){
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'submitted_to_idc': 'bg-blue-100 text-blue-800',
            'with_qmr': 'bg-purple-100 text-purple-800',
            'approved': 'bg-green-100 text-green-800',
            'on_hold': 'bg-red-100 text-red-800',
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }

    function getSourceLabel(source, specificType){
        const labels = {
            eoms: 'EOMS Manual',
            procedures: 'Procedures',
            forms: 'Forms',
            records: 'Records Management',
            others: 'Others',
        };
        let label = labels[source] || source;
        if (specificType){
            label += `${specificType}`;
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

    // ============================================
    // CHANGE STATUS MODAL
    // ============================================
    const statusModal = document.getElementById('status_modal');
    const closeStatusBtn = document.getElementById('close_status_modal');
    const cancelStatusBtn = document.getElementById('cancel_status_btn');
    const statusForm = document.getElementById('status_form');

    function openStatusModal(ticketId, currentStatus){
        // Format current status for display
        const formattedStatus = currentStatus.replace(/_/g, ' ').toUpperCase();
        document.getElementById('current_status').textContent = formattedStatus;

        // Set form action URL
        statusForm.action = `/iso/idc/${ticketId}/update-status`;

        // Show modal
        statusModal.classList.add('active');
    }
    // Close Modal
    closeStatusBtn.addEventListener('click', ()=>{
        statusModal.classList.remove('active');
    })

    cancelStatusBtn.addEventListener('click', ()=>{
        statusModal.classList.remove('active');
    })

    // Close Modal when clicking outside
    statusModal.addEventListener('click', (e)=>{
        if(e.target === statusModal){
            statusModal.classList.remove('active');
        }
    })

</script>