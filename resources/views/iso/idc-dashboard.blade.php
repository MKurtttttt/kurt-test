<x-app-layout>
    <!-- IDC ADMIN MANAGEMENT DASHBOARD-->
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
                    <h1 class="text-[1.5rem] font-bold leading-tight text-purple-700">IDC Admin Management Dashboard</h1>
                    <span class="text-gray-500 text-sm">Institutional Document Controller - Ticket Management</span>
                </div>
                <div class="w-full flex gap-4">
                    <!-- TODO: Remove this in the future (Just for debugging purposes) -->
                    <a href="{{ route('iso.document') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold">
                        Switch to Document Handler View
                    </a>
                    <!-- TODO: Button to swtich to the management blade file -->
                    <a href="{{ route('iso.management.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-semibold">
                        Switch to Management View
                    </a>
                </div>
            </div>
            <!-- Reset System Button -->
            <div class="px-4 py-3 right-4">
                <button id="reset_system_btn"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2">
                    <span>Reset Ticketing System</span>
                </button>
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

            <!-- Status Tabs -->
            <div class="w-full flex">
                <a href="{{ route('iso.idc.dashboard', ['status' => 'submitted_to_idc', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'submitted_to_idc') === 'submitted_to_idc' ? 'active_link' : '' }}">
                    Submitted to IDC
                    <span class="ml-2 text-xs bg-purple-500 text-white px-2 py-1 rounded-full">
                        {{ $statusCounts['submitted_to_idc'] ?? 0 }} Docs
                    </span>
                </a>
                <a href="{{ route('iso.idc.dashboard', ['status' => 'with_qmr', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'with_qmr') === 'with_qmr' ? 'active_link' : '' }}">
                    With QMR
                    <span class="ml-2 text-xs bg-purple-500 text-white px-2 py-1 rounded-full">
                        {{ $statusCounts['with_qmr'] ?? 0 }} Docs
                    </span>
                </a>
                <a href="{{ route('iso.idc.dashboard', ['status' => 'approved', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'approved') === 'approved' ? 'active_link' : '' }}">
                    Approved
                    <span class="ml-2 text-xs bg-purple-500 text-white px-2 py-1 rounded-full">
                        {{ $statusCounts['approved'] ?? 0 }} Docs
                    </span>
                </a>
                <a href="{{ route('iso.idc.dashboard', ['status' => 'on_hold', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'on_hold') === 'on_hold' ? 'active_link' : '' }}">
                    On Hold
                    <span class="ml-2 text-xs bg-purple-500 text-white px-2 py-1 rounded-full">
                        {{ $statusCounts['on_hold'] ?? 0 }} Docs
                    </span>
                </a>
                <a href="{{ route('iso.idc.dashboard', ['status' => 'all', 'search' => $search ?? null]) }}"
                    class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'all' ? 'active_link' : '' }}">
                    All Tickets
                    <span class="ml-2 text-xs bg-purple-500 text-white px-2 py-1 rounded-full">
                        {{ $statusCounts['all'] ?? 0 }} Docs
                    </span>
                </a>
            </div>
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
                                            <!-- View Details button (Always visible) -->
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
                                                data-ticket-registered='{{ $ticket->is_registered ? "true" : "false" }}'
                                            >
                                                View Details
                                            </button>
                                            <!-- Change Status Button - Only if not registered -->
                                             @if (!$ticket->is_registered)
                                                <button onclick="openStatusModal({{ $ticket->id }}, '{{ $ticket->status }}')"
                                                    class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-sm">
                                                    Change Status
                                                </button>
                                            @endif
                                            <!-- Register Ticket Button - Only for APPROVED and NOT registered -->
                                            @if ($ticket->status === 'approved' && !$ticket->is_registered)
                                                <button onclick="confirmRegister({{ $ticket->id }})"
                                                    class="bg-green-600 hover::bg-green-700 text-white px-3 py-1 rounded text-sm font-semibold">
                                                    Register
                                                </button>
                                            @endif

                                            <!-- Registered confirmation -->
                                            @if ($ticket->is_registered)
                                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                                    Registered!
                                                </span>
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
        </div>
    </div>
</div>
@include('iso.partials.idc-admin.view-details-modal')
@include('iso.partials.idc-admin.change-status-modal')
@include('iso.partials.idc-admin.reset-ticketing-system-modal')

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
    // TODO: Make a helper function js for both idc admin and document handler same functions
    // import {formatStatusText, getStatusColor} from '../../../js/helpers/helpers'
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
            const isRegistered = e.target.dataset.ticketRegistered === 'true';

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

            // Fill documents table
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
                    <td class="px-3 py-2">
                        <span class="inline-block px-2 py-1 text-xs rounded ${getClassificationColor(doc.classification)}">
                            ${classLabel}
                        </span>
                    </td>
                    <td class="px-3 py-2">${sourceLabel}</td>

                    <td class="px-3 py-2">
                        <span class="inline-block px-2 py-1 text-xs rounded ${getStatusColorJS(doc.status)}">
                            ${formatStatusText(doc.status)}
                        </span>
                    </td>

                    <td class="px-3 py-2">
                        <div class="flex gap-2 justify-center">
                            ${isRegistered ? 
                            `<span class="text-xs text-gray-500 italic"> Locked </span>`
                            :
                            `
                            <button onclick="updateDocStatus(${doc.id}, 'approved')"
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                Approved
                            </button>
                            <button onclick="updateDocStatus(${doc.id}, 'on_hold')"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                On-Hold
                            </button>`
                            }
                        </div>             
                    </td>
                `;
                documentsList.appendChild(row);
            });

            // Show modal
            detailsModal.classList.add('active');
        }
    });

    // Helper Functions
    function formatStatusText(status){
        const text = status.replace(/_/g, ' ');
        return text.charAt(0).toUpperCase() + text.slice(1);
    }

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
    // Update Document Status
    function updateDocStatus(documentId, newStatus){
        // Confirm the action
        const statusText = newStatus === 'approved' ? 'Approve' : 'Put On-Hold';
        const confirmed = confirm(`Are you sure you want to ${statusText} this document?`);

        if(!confirmed) return;

        // AJAX request to update status
        fetch(`/iso/idc/${documentId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        // .then(response => response.json())
        .then(response => {
            console.log("Response status: ", response.status);
            console.log("Response OK?: ", response.ok);

            // Clone the response so we can read it twice
            return response.clone().text().then(text => {
                console.log("Raw Response: ", text);

                // Try parsing
                try{
                    const data = JSON.parse(text);
                    return data;
                } catch(e){
                    console.error('JSON parse failed. Response was:'+text);
                    throw new Error('Invalid JSON response');
                }
            });
        })
        .then(data => {
            if(data.success){
                alert('Document status updated successfully!');
                location.reload();
            } else{
                alert('Failed to updated document status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        })
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

    // ============================================
    // Reset System Function
    // ============================================
    const resetBtn = document.getElementById('reset_system_btn');
    const resetModal = document.getElementById('reset_modal');
    const closeResetModal = document.getElementById('close_reset_modal');
    const cancelResetBtn = document.getElementById('cancel_reset_btn');
    const resetForm = document.getElementById('reset_form');
    const resetConfirmInput = document.getElementById('reset_confirmation');
    const confirmResetBtn = document.getElementById('confirm_reset_btn');

    // Open reset modal
    resetBtn.addEventListener('click', ()=> {
        resetModal.classList.add('active');
        resetConfirmInput.value = '';
        confirmResetBtn.disabled = true;
    });
    // Close modal when clicking outside
    resetModal.addEventListener('click', (e)=>{
        if(e.target === resetModal){
            resetModal.classList.remove('active');
        }
    })

    // Close modal functions
    closeResetModal.addEventListener('click', ()=> {
        resetModal.classList.remove('active');
    });
    cancelResetBtn.addEventListener('click', () => {
        resetModal.classList.remove('active');
    });

    // Enable submit button only when "CONFIRM" is typed
    resetConfirmInput.addEventListener('input', () =>{
        const inputValue = resetConfirmInput.value;
        if(inputValue === 'CONFIRM'){
            confirmResetBtn.disabled = false;
            confirmResetBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else{
            confirmResetBtn.disabled = true;
            confirmResetBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    });

    // Final confirmation before submit
    resetForm.addEventListener('submit', (e)=>{
        if(resetConfirmInput.value !== 'CONFIRM'){
            e.preventDefault();
            alert('You must type CONFIRM to proceed');
            return false;
        }
        // One last confirmation dialog
        const finalConfirm = confirm(
            'Final Warning!\n'+
            'Are you ABSOLUTELY SURE you want to delete ALL tickets?\n' +
            'This action is PERMANENT and IRREVERSIBLE!'
        );

        if(!finalConfirm) {
            e.preventDefault();
            return false;
        }
    });

    // ============================================
    // Register Ticket Function
    // ============================================
    function confirmRegister(ticketId){
        const confirmed = confirm(
            'Register this ticket?\n\n'+
            'This will:\n'+
            '• Lock the ticket (no more edits)\n' +
            '• Move documents to Management Menu\n' +
            '• Mark as officially registered\n\n' +
            'Continue?'
        );
        if(confirmed){
            // Submit registration
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/iso/idc/${ticketId}/register`;

            // Add CSRF Token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);

            // Add method for spoofing for PATCH
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>