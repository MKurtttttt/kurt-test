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
                <img src="{{ asset('images/icons/portal_nav/iso-title.png') }}" class="w-[100px] h-[100px] mr-4"/>
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
                            placeholder="Search by Ticket Number, Section, Document Code, or document Title..."
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
            <div class="w-full flex flex-col border border-gray-200 shadow-sm overflow-hidden">
                @if(count($tickets) > 0)
                    <table class="w-full">                      
                        <thead class="bg-gray-100 border-b">
                            <tr>  
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Ticket Number</th>
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
                                        {{ $ticket->ticket_number }}
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
                                            {{ str_replace(['Idc', 'Qmr'], ['IDC', 'QMR'], ucwords(str_replace('_', ' ', $ticket->status))) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $ticket->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex gap-2 justify-center items-center">
                                            <!-- Single combined action button (pencil SVG icon) -->
                                            <button
                                                class="view-details-btn pencil-btn"
                                                title="View Details & Manage"
                                                data-ticket-id='{{ $ticket->id }}'
                                                data-ticket-number='{{ $ticket->ticket_number }}'
                                                data-ticket-section='{{ $ticket->originating_section }}'
                                                data-ticket-status='{{ $ticket->status }}'
                                                data-ticket-created='{{ $ticket->created_at->format('M d, Y h:i A') }}'
                                                data-ticket-creator='{{ $ticket->creator->name ?? 'Unknown' }}'
                                                data-ticket-sharepoint='{{ $ticket->sharepoint_link }}'
                                                data-ticket-message='{{ $ticket->message_to_idc }}'
                                                data-ticket-documents='@json($ticket->documents)'
                                                data-ticket-registered='{{ $ticket->is_registered ? "true" : "false" }}'
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>

                                            <!-- Register Ticket Button - Only for APPROVED and NOT registered -->
                                            @if ($ticket->status === 'approved' && !$ticket->is_registered)
                                                <button onclick="confirmRegister({{ $ticket->id }}, '{{ $ticket->ticket_number }}')"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-semibold">
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
@include('iso.partials.idc-admin.register-ticket-modal')

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

    /* ── Modal Overlay ── */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 99999;                /* very high — direct body child now */
        justify-content: center;
        align-items: flex-start;
        overflow-y: auto;
        padding: 12vh 1rem 3rem;       /* top clears the fixed page header */
    }
    .modal-overlay.active {
        display: flex;
    }

    /* ── Modal Card ── */
    .modal-content {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 860px;
        max-height: 85vh;              /* cap at 85% of viewport */
        display: flex;
        flex-direction: column;        /* header + action-bar + body stacked */
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.22);
        overflow: hidden;              /* clip child radii */
    }

    /* ── Modal Header ── */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1.4rem 1.6rem 1.2rem;
        border-bottom: 1px solid #f0f0f0;
        flex-shrink: 0;                /* never shrink — always fully visible */
        border-radius: 16px 16px 0 0;
        background: white;
    }
    .modal-icon-box {
        width: 38px;
        height: 38px;
        min-width: 38px;
        background: #fef2f2;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        margin-top: 2px;
    }
    .modal-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.3;
    }
    .modal-subtitle {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        color: #9ca3af;
        margin-top: 2px;
        text-transform: uppercase;
    }
    .modal-close-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #6b7280;
        cursor: pointer;
        transition: background 0.15s, color 0.15s, border-color 0.15s;
        margin-top: 2px;
    }
    .modal-close-btn:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #ef4444;
    }

    /* ── Action Bar ── */
    .modal-action-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0.6rem 1.6rem;
        background: #fafafa;
        border-bottom: 1px solid #f0f0f0;
        flex-shrink: 0;                /* never shrink */
    }

    /* ── Modal Body ── */
    .modal-body {
        padding: 1.5rem 1.6rem;
        overflow-y: auto;              /* ONLY the body scrolls */
        flex: 1;                       /* take remaining height inside modal-content */
    }

    /* ── Info Cards ── */
    .info-card {
        background: #f9fafb;
        border: 1px solid #f0f0f0;
        border-radius: 12px;
        padding: 1rem 1.2rem;
    }
    .info-card--blue {
        background: #eff6ff;
        border-color: #bfdbfe;
    }
    .info-card--purple {
        background: #faf5ff;
        border-color: #e9d5ff;
    }

    /* ── Field labels ── */
    .field-label {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 2px;
    }
    .field-value {
        font-size: 0.9rem;
        font-weight: 500;
        color: #111827;
    }
    .section-label {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #374151;
    }

    /* ── Pencil icon button in table ── */
    .pencil-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        border-radius: 6px;
        color: #9ca3af;
        transition: color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        background: transparent;
        border: none;
        cursor: pointer;
    }
    .pencil-btn:hover {
        color: #ef4444;
        background-color: #fff1f1;
        box-shadow: 0 0 8px rgba(239, 68, 68, 0.45);
    }

    /* ── Delete Ticket button in action bar ── */
    .delete-ticket-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border: 1.5px solid #ef4444;
        border-radius: 8px;
        color: #ef4444;
        background: white;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.18s, color 0.18s;
    }
    .delete-ticket-btn:hover {
        background: #ef4444;
        color: white;
    }
</style>

<script>
    // Auto-hide messages after 5 seconds
    setTimeout(() => {
        const msgElement = document.getElementById('msg');
        if(msgElement) msgElement.style.display = 'none';
        const errorElement = document.getElementById('error-msg');
        if(errorElement) errorElement.style.display = 'none';
    }, 5000);

    // ============================================
    // COMBINED VIEW DETAILS + CHANGE STATUS MODAL
    // ============================================
    const detailsModal = document.getElementById('details_modal');

    // ── CRITICAL FIX: Move modal to <body> to escape the page's stacking context.
    // The .content wrapper has z-index:4, which places it BELOW the fixed .header
    // (z-index:5). By appending the modal directly to body it gets its own
    // top-level stacking context and z-index:99999 works globally.
    document.body.appendChild(detailsModal);

    const closeDetailsBtn = document.getElementById('close_details_modal');
    const statusForm = document.getElementById('status_form');
    const deleteTicketBtn = document.getElementById('delete_ticket_btn');
    let currentTicketId = null;

    // Close modal
    closeDetailsBtn.addEventListener('click', () => {
        detailsModal.classList.remove('active');
    });

    // Close when clicking outside
    detailsModal.addEventListener('click', (e) => {
        if (e.target === detailsModal) {
            detailsModal.classList.remove('active');
        }
    });

    // Handle pencil icon button clicks
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.view-details-btn');
        if (!btn) return;

        const ticketId = btn.dataset.ticketId;
        const ticketNumber = btn.dataset.ticketNumber;
        const section = btn.dataset.ticketSection;
        const status = btn.dataset.ticketStatus;
        const created = btn.dataset.ticketCreated;
        const creator = btn.dataset.ticketCreator;
        const sharepoint = btn.dataset.ticketSharepoint;
        const message = btn.dataset.ticketMessage;
        const documentsJson = btn.dataset.ticketDocuments;
        const isRegistered = btn.dataset.ticketRegistered === 'true';

        // Store current ticket id globally for delete/status actions
        currentTicketId = ticketId;

        // Parse documents JSON
        let ticketDocuments = [];
        try {
            ticketDocuments = JSON.parse(documentsJson);
        } catch (error) {
            console.error('JSON parse failed: ', error);
            alert('Error loading ticket documents.');
            return;
        }

        // Fill modal with ticket data
        document.getElementById('detail_ticket_number').textContent = ticketNumber;
        document.getElementById('detail_section').textContent = section;
        document.getElementById('detail_created').textContent = created;
        document.getElementById('detail_creator').textContent = creator;
        document.getElementById('detail_message').textContent = message;

        // Set SharePoint Link
        const sharepointLink = document.getElementById('detail_sharepoint');
        sharepointLink.href = sharepoint;
        sharepointLink.textContent = sharepoint;

        // Format and display status
        const statusElement = document.getElementById('detail_status');
        statusElement.innerHTML = `<span class="inline-block px-2 py-1 rounded text-xs ${getStatusColorJS(status)}">${formatStatusText(status)}</span>`;

        // Populate documents table
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
                    <span class="inline-block px-2 py-1 text-xs rounded ${getClassificationColor(doc.classification)}">${classLabel}</span>
                </td>
                <td class="px-3 py-2">${sourceLabel}</td>
                <td class="px-3 py-2">
                    <span class="inline-block px-2 py-1 text-xs rounded ${getStatusColorJS(doc.status)}">${formatStatusText(doc.status)}</span>
                </td>
                <td class="px-3 py-2">
                    <div class="flex gap-2 justify-center items-center">
                        ${isRegistered
                            ? `<span class="text-xs text-gray-500 italic">Locked</span>`
                            : `
                            <button onclick="updateDocStatus(${doc.id}, 'approved')"
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                Approved
                            </button>
                            <button onclick="updateDocStatus(${doc.id}, 'on_hold')"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                On-Hold
                            </button>
                            <button onclick="removeDocFromTicket(${doc.id}, '${escapeForJs(doc.document_code)}', ${ticketId})"
                                class="w-6 h-6 rounded-full bg-red-50 hover:bg-red-200 text-red-600 hover:text-red-800 flex items-center justify-center font-bold text-sm leading-none transition"
                                title="Remove document from ticket">
                                &times;
                            </button>`
                        }
                    </div>
                </td>
            `;
            documentsList.appendChild(row);
        });

        // Show / hide Change Status section
        const changeStatusSection = document.getElementById('change_status_section');
        if (!isRegistered) {
            changeStatusSection.style.display = 'block';
            // Set the current status label
            document.getElementById('current_status').textContent = formatStatusText(status);
            // Set the form action URL
            statusForm.action = `/iso/idc/${ticketId}/update-status`;
        } else {
            changeStatusSection.style.display = 'none';
        }


        // Show / hide Delete Ticket button + action bar
        const modalActionBar = document.getElementById('modal_action_bar');
        if (!isRegistered) {
            deleteTicketBtn.style.display = 'flex';
            if (modalActionBar) modalActionBar.style.display = 'flex';
        } else {
            deleteTicketBtn.style.display = 'none';
            if (modalActionBar) modalActionBar.style.display = 'none';
        }

        // Open modal
        detailsModal.classList.add('active');
    });

    // ============================================
    // DELETE TICKET (Hard Delete via AJAX)
    // ============================================
    deleteTicketBtn.addEventListener('click', () => {
        if (!currentTicketId) return;

        const confirmed = confirm(
            'Are you sure you want to PERMANENTLY DELETE this ticket?\n\n' +
            '⚠️ This will remove the ticket AND all its documents from the database.\n' +
            'This action CANNOT be undone!'
        );
        if (!confirmed) return;

        fetch(`/iso/idc/${currentTicketId}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                detailsModal.classList.remove('active');
                alert('Ticket permanently deleted.');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('An error occurred while deleting the ticket.');
        });
    });

    // ============================================
    // REMOVE ATTACHED DOCUMENT FROM TICKET
    // ============================================
    function removeDocFromTicket(documentId, documentCode, ticketId) {
        if (!confirm(`Are you sure you want to remove document "${documentCode}" from this ticket?\n\nThis action cannot be undone.`)) {
            return;
        }

        fetch(`/iso/tickets/${ticketId}/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Document removed successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error removing document:', error);
            alert('An error occurred while removing the document. Please try again.');
        });
    }

    // ============================================
    // Helper Functions
    // ============================================
    function escapeForJs(str) {
        if (!str) return '';
        return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }
    function formatStatusText(status) {
        let text = status.replace(/_/g, ' ');
        text = text.charAt(0).toUpperCase() + text.slice(1);
        return text.replace(/idc/gi, 'IDC').replace(/qmr/gi, 'QMR');
    }

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

    function getSourceLabel(source, specificType) {
        const labels = {
            eoms: 'EOMS Manual',
            procedures: 'Procedures',
            forms: 'Forms',
            records: 'Records Management',
            others: 'Others',
        };
        let label = labels[source] || source;
        if (specificType) label += ` (${specificType})`;
        return label;
    }

    function getClassificationColor(classification) {
        const colors = {
            revision: 'bg-yellow-200 text-yellow-800',
            addition: 'bg-green-200 text-green-800',
            deletion: 'bg-red-200 text-red-800'
        };
        return colors[classification] || 'bg-gray-200 text-gray-800';
    }

    // Update individual Document Status
    function updateDocStatus(documentId, newStatus) {
        const statusText = newStatus === 'approved' ? 'Approve' : 'Put On-Hold';
        if (!confirm(`Are you sure you want to ${statusText} this document?`)) return;

        fetch(`/iso/idc/${documentId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Document status updated successfully!');
                location.reload();
            } else {
                alert('Failed to update document status.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

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
    // Register Ticket — Styled Modal
    // ============================================
    const registerModal = document.getElementById('register_modal');
    document.body.appendChild(registerModal); // escape stacking context

    const closeRegisterModal = document.getElementById('close_register_modal');
    const cancelRegisterBtn = document.getElementById('cancel_register_btn');
    const confirmRegisterBtn = document.getElementById('confirm_register_btn');
    const registerCheckbox = document.getElementById('register_confirm_checkbox');
    let pendingRegisterTicketId = null;

    // Open the styled register modal
    function confirmRegister(ticketId, ticketNumber) {
        pendingRegisterTicketId = ticketId;

        // Populate ticket number in subtitle
        document.getElementById('register_ticket_number').textContent = ticketNumber || ('ID: ' + ticketId);

        // Reset checkbox and button state every time modal opens
        registerCheckbox.checked = false;
        confirmRegisterBtn.disabled = true;
        confirmRegisterBtn.style.background = '#d1d5db';
        confirmRegisterBtn.style.color = '#9ca3af';
        confirmRegisterBtn.style.cursor = 'not-allowed';

        registerModal.classList.add('active');
    }

    // Enable confirm button only when checkbox is ticked
    registerCheckbox.addEventListener('change', () => {
        if (registerCheckbox.checked) {
            confirmRegisterBtn.disabled = false;
            confirmRegisterBtn.style.background = '#16a34a';
            confirmRegisterBtn.style.color = 'white';
            confirmRegisterBtn.style.cursor = 'pointer';
        } else {
            confirmRegisterBtn.disabled = true;
            confirmRegisterBtn.style.background = '#d1d5db';
            confirmRegisterBtn.style.color = '#9ca3af';
            confirmRegisterBtn.style.cursor = 'not-allowed';
        }
    });

    // Close modal handlers
    closeRegisterModal.addEventListener('click', () => registerModal.classList.remove('active'));
    cancelRegisterBtn.addEventListener('click', () => registerModal.classList.remove('active'));
    registerModal.addEventListener('click', (e) => {
        if (e.target === registerModal) registerModal.classList.remove('active');
    });

    // Submit registration when confirmed
    confirmRegisterBtn.addEventListener('click', () => {
        if (!pendingRegisterTicketId || !registerCheckbox.checked) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/iso/idc/${pendingRegisterTicketId}/register`;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    });
</script>