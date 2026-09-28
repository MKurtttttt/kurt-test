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

    /* ── Edit Ticket Details button in action bar ── */
    .edit-ticket-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border: 1.5px solid #3b82f6;
        border-radius: 8px;
        color: #2563eb;
        background: white;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.18s, color 0.18s, border-color 0.18s;
    }
    .edit-ticket-btn:hover {
        background: #2563eb;
        color: white;
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

    /* ── Edit Document Card in Edit Mode ── */
    .edit-doc-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem 1.1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .edit-doc-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
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
    const editTicketBtn = document.getElementById('edit_ticket_btn');
    const cancelEditTicketBtn = document.getElementById('cancel_edit_ticket_btn');
    const saveEditTicketBtn = document.getElementById('save_edit_ticket_btn');
    const ticketViewModeContainer = document.getElementById('ticket_view_mode_container');
    const ticketEditModeContainer = document.getElementById('ticket_edit_mode_container');
    const modalActionBar = document.getElementById('modal_action_bar');
    const editTicketClusterDropdown = document.getElementById('edit_ticket_cluster');
    const editTicketOfficeDropdown = document.getElementById('edit_ticket_office');

    // Specific Office options loaded dynamically from database
    @php
        $dbDepartments = \App\Models\Departments::orderBy('code', 'asc')->get();
        $jsOptions = [];
        foreach ($dbDepartments as $d) {
            $parts = explode('-', $d->code);
            $cluster = strtolower($parts[0]);
            $formatted = "({$d->code}) {$d->dept}";
            $jsOptions[$cluster][] = $formatted;
        }
    @endphp
    const specificOfficeOptions = @json($jsOptions);

    const sourceTypeOptions = {
        eoms: [
            { value: '4.1', label: '4.1 Interested Parties' },
            { value: '4.2', label: '4.2 Risk Assessment' },
            { value: '7.4', label: '7.4 Communication' },
            { value: '8.1', label: '8.1 EOMS Plan' }
        ],
        records: [
            { value: '3.0', label: '3.0 Records Retention Schedule' },
            { value: '4.0', label: '4.0 Definition and Records Series Title' }
        ]
    };

    let currentTicketId = null;
    let currentTicketData = null;
    let currentEditDocsList = [];

    // Helper to find cluster key from office full name
    function findClusterByOffice(officeName) {
        if (!officeName) return null;
        for (const [cluster, offices] of Object.entries(specificOfficeOptions)) {
            if (offices.includes(officeName)) {
                return cluster;
            }
        }
        // Fallback: match cluster code inside parentheses e.g. (AAC-SOC) -> aac
        const match = officeName.match(/\(([^)-]+)/);
        if (match) {
            const clusterCode = match[1].toLowerCase();
            if (specificOfficeOptions[clusterCode]) {
                return clusterCode;
            }
        }
        return null;
    }

    // ======================================
    // Document Prefix Functions & Helpers
    // ======================================
    function getOfficeCode(officeValue) {
        if (!officeValue) return null;
        const match = officeValue.match(/\(([^)]+)\)/);
        return match ? match[1] : officeValue;
    }

    function generateDocCodePrefix(source, officeValue, specificType) {
        const officeCode = getOfficeCode(officeValue);
        if (!source || !officeCode) return '';

        switch (source) {
            case 'forms':
                return `FM-${officeCode}`;
            case 'procedures':
                return `PD-${officeCode}`;
            case 'eoms':
                return specificType ? `${specificType} ${officeCode}` : '';
            case 'records':
                if (!specificType) return '';
                let cleanSpec = specificType.replace(/0+$/, '');
                if (!cleanSpec.endsWith('.')) cleanSpec += '.';
                return `${cleanSpec}RMM-${officeCode}`;
            case 'others':
                return null; // Skip autofill for others
            default:
                return '';
        }
    }

    function extractSuffixFromDocCode(currentCode, source, officeValue, specificType) {
        if (!currentCode) return '';

        // 1. If code starts with the generated prefix from the specified office
        const prefix = generateDocCodePrefix(source, officeValue, specificType);
        if (prefix && currentCode.startsWith(prefix)) {
            return currentCode.slice(prefix.length);
        }

        // 2. Standard pattern: hyphen or space followed by digits e.g. -001, -1105, -2023
        const numMatch = currentCode.match(/([-\s]\d[A-Za-z0-9-_]*)$/);
        if (numMatch) {
            return numMatch[1];
        }

        return '';
    }

    function attachDocCodeProtected(inputElement) {
        inputElement.addEventListener('keydown', (e) => {
            const prefix = inputElement.dataset.prefix || '';
            if (!prefix) return;
            if ((e.key === 'Backspace' || e.key === 'Delete') && inputElement.selectionStart <= prefix.length) {
                e.preventDefault();
            }
        });

        inputElement.addEventListener('input', () => {
            const prefix = inputElement.dataset.prefix || '';
            if (prefix && !inputElement.value.startsWith(prefix)) {
                inputElement.value = prefix;
            }
        });
    }

    function updateCardDocCodePrefix(card, newOfficeValue) {
        const codeInput = card.querySelector('.edit-doc-code');
        const sourceSelect = card.querySelector('.edit-doc-source');
        const specElem = card.querySelector('.edit-doc-specific');

        if (!codeInput || !sourceSelect) return;

        const source = sourceSelect.value;
        const specificType = specElem ? specElem.value : '';
        const newPrefix = generateDocCodePrefix(source, newOfficeValue, specificType);

        // If source is 'others', skip autofill and clear prefix protection
        if (newPrefix === null) {
            codeInput.dataset.prefix = '';
            return;
        }

        // Incomplete prefix (e.g. no office or eoms/records without specificType selected yet)
        if (!newPrefix) return;

        const currentVal = codeInput.value;
        const oldPrefix = codeInput.dataset.prefix || '';

        let suffix = '';
        if (oldPrefix && currentVal.startsWith(oldPrefix)) {
            suffix = currentVal.slice(oldPrefix.length);
        } else {
            suffix = extractSuffixFromDocCode(currentVal, source, newOfficeValue, specificType);
        }

        codeInput.value = newPrefix + suffix;
        codeInput.dataset.prefix = newPrefix;
    }

    window.handleEditDocSpecificChange = function(specElem) {
        const card = specElem.closest('.edit-doc-card');
        if (!card) return;
        const currentOffice = editTicketOfficeDropdown ? editTicketOfficeDropdown.value : '';
        updateCardDocCodePrefix(card, currentOffice);
        syncEditDocsFromInputs();
    };

    // Cluster dropdown change listener
    if (editTicketClusterDropdown && editTicketOfficeDropdown) {
        editTicketClusterDropdown.addEventListener('change', () => {
            const cluster = editTicketClusterDropdown.value;
            editTicketOfficeDropdown.innerHTML = '<option value="">Select Office...</option>';

            if (cluster && specificOfficeOptions[cluster]) {
                const offices = specificOfficeOptions[cluster];
                offices.forEach(office => {
                    const option = document.createElement('option');
                    option.value = office;
                    option.textContent = office;
                    editTicketOfficeDropdown.appendChild(option);
                });
                editTicketOfficeDropdown.disabled = false;
                editTicketOfficeDropdown.classList.remove('opacity-50', 'cursor-not-allowed');

                if (offices.length === 1) {
                    editTicketOfficeDropdown.value = offices[0];
                    editTicketOfficeDropdown.dispatchEvent(new Event('change'));
                }
            } else {
                editTicketOfficeDropdown.innerHTML = '<option value="">Select Cluster first...</option>';
                editTicketOfficeDropdown.disabled = true;
                editTicketOfficeDropdown.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });

        editTicketOfficeDropdown.addEventListener('change', () => {
            const newOffice = editTicketOfficeDropdown.value;
            if (!newOffice) return;

            const cards = document.querySelectorAll('#edit_documents_list_container .edit-doc-card');
            cards.forEach(card => {
                updateCardDocCodePrefix(card, newOffice);
            });

            syncEditDocsFromInputs();
        });
    }

    // Helper to switch to View Mode
    function showTicketViewMode() {
        if (ticketViewModeContainer) ticketViewModeContainer.style.display = 'block';
        if (ticketEditModeContainer) ticketEditModeContainer.style.display = 'none';
        if (modalActionBar) {
            modalActionBar.style.display = (currentTicketData && !currentTicketData.isRegistered) ? 'flex' : 'none';
        }
    }

    // Helper to enter Edit Mode
    function enterTicketEditMode() {
        if (!currentTicketData || currentTicketData.isRegistered) return;

        // Deep copy of documents array
        currentEditDocsList = JSON.parse(JSON.stringify(currentTicketData.documents));

        const currentOffice = currentTicketData.section || '';
        const currentCluster = findClusterByOffice(currentOffice);

        if (editTicketClusterDropdown && editTicketOfficeDropdown) {
            if (currentCluster) {
                editTicketClusterDropdown.value = currentCluster;
                editTicketOfficeDropdown.innerHTML = '<option value="">Select Office...</option>';

                if (specificOfficeOptions[currentCluster]) {
                    specificOfficeOptions[currentCluster].forEach(office => {
                        const option = document.createElement('option');
                        option.value = office;
                        option.textContent = office;
                        editTicketOfficeDropdown.appendChild(option);
                    });
                    editTicketOfficeDropdown.disabled = false;
                    editTicketOfficeDropdown.classList.remove('opacity-50', 'cursor-not-allowed');
                    editTicketOfficeDropdown.value = currentOffice;
                }
            } else {
                editTicketClusterDropdown.value = '';
                editTicketOfficeDropdown.innerHTML = '<option value="">Select Cluster first...</option>';
                if (currentOffice) {
                    // Support legacy/custom office values
                    const customOption = document.createElement('option');
                    customOption.value = currentOffice;
                    customOption.textContent = `${currentOffice} (Current)`;
                    customOption.selected = true;
                    editTicketOfficeDropdown.appendChild(customOption);
                    editTicketOfficeDropdown.disabled = false;
                    editTicketOfficeDropdown.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    editTicketOfficeDropdown.disabled = true;
                    editTicketOfficeDropdown.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        renderEditDocumentsList();

        if (ticketViewModeContainer) ticketViewModeContainer.style.display = 'none';
        if (ticketEditModeContainer) ticketEditModeContainer.style.display = 'block';
        if (modalActionBar) modalActionBar.style.display = 'none';
    }

    // Generate specific type field HTML based on source
    function getSpecificTypeFieldHtml(source, specificType) {
        if (source === 'eoms') {
            return `
                <div class="edit-doc-specific-wrapper">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Specific Type <span class="text-red-500">*</span>
                    </label>
                    <select class="edit-doc-specific w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        onchange="handleEditDocSpecificChange(this)">
                        <option value="">Select Specific Type...</option>
                        <option value="4.1" ${specificType === '4.1' ? 'selected' : ''}>4.1 Interested Parties</option>
                        <option value="4.2" ${specificType === '4.2' ? 'selected' : ''}>4.2 Risk Assessment</option>
                        <option value="7.4" ${specificType === '7.4' ? 'selected' : ''}>7.4 Communication</option>
                        <option value="8.1" ${specificType === '8.1' ? 'selected' : ''}>8.1 EOMS Plan</option>
                    </select>
                </div>
            `;
        } else if (source === 'records') {
            return `
                <div class="edit-doc-specific-wrapper">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Specific Type <span class="text-red-500">*</span>
                    </label>
                    <select class="edit-doc-specific w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        onchange="handleEditDocSpecificChange(this)">
                        <option value="">Select Specific Type...</option>
                        <option value="3.0" ${(specificType === '3.0' || specificType === '3') ? 'selected' : ''}>3.0 Records Retention Schedule</option>
                        <option value="4.0" ${(specificType === '4.0' || specificType === '4') ? 'selected' : ''}>4.0 Definition and Records Series Title</option>
                    </select>
                </div>
            `;
        } else if (source === 'others') {
            return `
                <div class="edit-doc-specific-wrapper">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Specify Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" class="edit-doc-specific w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        value="${escapeHtml(specificType || '')}" placeholder="Enter custom source type...">
                </div>
            `;
        } else {
            return `
                <div class="edit-doc-specific-wrapper" style="display:none;"></div>
            `;
        }
    }

    // Render documents list inside edit mode
    function renderEditDocumentsList() {
        const container = document.getElementById('edit_documents_list_container');
        if (!container) return;
        container.innerHTML = '';

        const docCountBadge = document.getElementById('edit_doc_count');
        if (docCountBadge) docCountBadge.textContent = currentEditDocsList.length;

        const currentOffice = editTicketOfficeDropdown ? editTicketOfficeDropdown.value : (currentTicketData?.section || '');

        currentEditDocsList.forEach((doc, index) => {
            const card = document.createElement('div');
            card.className = 'edit-doc-card';
            card.dataset.index = index;

            const removeBtnHtml = currentEditDocsList.length > 1
                ? `<button type="button" onclick="removeDocFromEditList(${index})"
                       class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg transition font-medium cursor-pointer"
                       title="Remove this document from ticket">
                       <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                       Remove
                   </button>`
                : `<span class="text-xs text-gray-400 italic">Minimum 1 document required</span>`;

            card.innerHTML = `
                <div class="flex items-center justify-between pb-2 mb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-800 font-bold text-xs flex items-center justify-center">${index + 1}</span>
                        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Document #${index + 1}</span>
                        ${!doc.id ? `<span class="text-[11px] text-green-600 font-medium bg-green-50 px-1.5 py-0.5 rounded">New</span>` : ''}
                    </div>
                    ${removeBtnHtml}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Document Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" class="edit-doc-code w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            value="${escapeHtml(doc.document_code || '')}" placeholder="e.g. FM-AAC-SOC-001" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Document Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" class="edit-doc-title w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            value="${escapeHtml(doc.document_title || '')}" placeholder="e.g. Quality Manual" required>
                        ${(doc.classification === 'revision' && doc.revising_master_document && doc.revising_master_document.document_title && doc.revising_master_document.document_title !== doc.document_title)
                            ? `<div class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 rounded px-2 py-0.5 mt-1 inline-flex items-center gap-1">
                                <span class="font-semibold">Original Title:</span> ${escapeHtml(doc.revising_master_document.document_title)}
                               </div>`
                            : ''}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Nature of Modification <span class="text-red-500">*</span>
                        </label>
                        <select class="edit-doc-classification w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="addition" ${doc.classification === 'addition' ? 'selected' : ''}>Addition (Add new Document)</option>
                            <option value="revision" ${doc.classification === 'revision' ? 'selected' : ''}>Revision (Update an Existing Document)</option>
                            <option value="deletion" ${doc.classification === 'deletion' ? 'selected' : ''}>Deletion (Delete an Existing Document)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Source Document <span class="text-red-500">*</span>
                        </label>
                        <select class="edit-doc-source w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                            onchange="handleEditDocSourceChange(this)">
                            <option value="forms" ${doc.source_type === 'forms' ? 'selected' : ''}>Forms</option>
                            <option value="procedures" ${doc.source_type === 'procedures' ? 'selected' : ''}>Procedures</option>
                            <option value="eoms" ${doc.source_type === 'eoms' ? 'selected' : ''}>EOMS Manual</option>
                            <option value="records" ${doc.source_type === 'records' ? 'selected' : ''}>Records Management Manual</option>
                            <option value="others" ${doc.source_type === 'others' ? 'selected' : ''}>Others</option>
                        </select>
                    </div>
                    ${getSpecificTypeFieldHtml(doc.source_type, doc.specific_type)}
                </div>
            `;
            container.appendChild(card);

            // Set prefix dataset and attach protection
            const codeInput = card.querySelector('.edit-doc-code');
            if (codeInput) {
                if (doc.source_type === 'others') {
                    codeInput.dataset.prefix = '';
                } else {
                    const expectedPrefix = generateDocCodePrefix(doc.source_type, currentOffice, doc.specific_type);
                    if (expectedPrefix && doc.document_code && doc.document_code.startsWith(expectedPrefix)) {
                        codeInput.dataset.prefix = expectedPrefix;
                    } else {
                        const suffix = extractSuffixFromDocCode(doc.document_code, doc.source_type, currentOffice, doc.specific_type);
                        if (suffix && expectedPrefix) {
                            codeInput.dataset.prefix = expectedPrefix;
                        } else {
                            codeInput.dataset.prefix = expectedPrefix || '';
                        }
                    }
                }
                attachDocCodeProtected(codeInput);
            }
        });
    }

    // Toggle specific type input when source changes
    window.handleEditDocSourceChange = function(selectElem) {
        const card = selectElem.closest('.edit-doc-card');
        if (!card) return;
        const specificWrapper = card.querySelector('.edit-doc-specific-wrapper');
        if (!specificWrapper) return;

        const source = selectElem.value;
        if (source === 'eoms') {
            specificWrapper.style.display = 'block';
            specificWrapper.innerHTML = `
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Specific Type <span class="text-red-500">*</span>
                </label>
                <select class="edit-doc-specific w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    onchange="handleEditDocSpecificChange(this)">
                    <option value="">Select Specific Type...</option>
                    <option value="4.1">4.1 Interested Parties</option>
                    <option value="4.2">4.2 Risk Assessment</option>
                    <option value="7.4">7.4 Communication</option>
                    <option value="8.1">8.1 EOMS Plan</option>
                </select>
            `;
        } else if (source === 'records') {
            specificWrapper.style.display = 'block';
            specificWrapper.innerHTML = `
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Specific Type <span class="text-red-500">*</span>
                </label>
                <select class="edit-doc-specific w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    onchange="handleEditDocSpecificChange(this)">
                    <option value="">Select Specific Type...</option>
                    <option value="3.0">3.0 Records Retention Schedule</option>
                    <option value="4.0">4.0 Definition and Records Series Title</option>
                </select>
            `;
        } else if (source === 'others') {
            specificWrapper.style.display = 'block';
            specificWrapper.innerHTML = `
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Specify Type <span class="text-red-500">*</span>
                </label>
                <input type="text" class="edit-doc-specific w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Enter custom source type...">
            `;
        } else {
            specificWrapper.style.display = 'none';
            specificWrapper.innerHTML = '';
        }

        const currentOffice = editTicketOfficeDropdown ? editTicketOfficeDropdown.value : '';
        updateCardDocCodePrefix(card, currentOffice);
        syncEditDocsFromInputs();
    };

    // Add new document to edit list
    window.addDocToEditList = function() {
        syncEditDocsFromInputs();
        const currentOffice = editTicketOfficeDropdown ? editTicketOfficeDropdown.value : '';
        const defaultPrefix = generateDocCodePrefix('forms', currentOffice, null) || '';

        currentEditDocsList.push({
            id: null,
            document_code: defaultPrefix,
            document_title: '',
            classification: 'addition',
            source_type: 'forms',
            specific_type: ''
        });
        renderEditDocumentsList();
    };

    // Sync input values into currentEditDocsList state
    function syncEditDocsFromInputs() {
        const cards = document.querySelectorAll('#edit_documents_list_container .edit-doc-card');
        cards.forEach((card, idx) => {
            if (currentEditDocsList[idx]) {
                const codeInput = card.querySelector('.edit-doc-code');
                const titleInput = card.querySelector('.edit-doc-title');
                const classSelect = card.querySelector('.edit-doc-classification');
                const sourceSelect = card.querySelector('.edit-doc-source');
                const specElem = card.querySelector('.edit-doc-specific');

                if (codeInput) currentEditDocsList[idx].document_code = codeInput.value.trim();
                if (titleInput) currentEditDocsList[idx].document_title = titleInput.value.trim();
                if (classSelect) currentEditDocsList[idx].classification = classSelect.value;
                if (sourceSelect) currentEditDocsList[idx].source_type = sourceSelect.value;
                if (specElem) currentEditDocsList[idx].specific_type = specElem.value.trim();
                else currentEditDocsList[idx].specific_type = '';
            }
        });
    }

    // Remove single document from edit list
    window.removeDocFromEditList = function(index) {
        if (currentEditDocsList.length <= 1) {
            alert('A ticket must contain at least one document.');
            return;
        }
        syncEditDocsFromInputs();
        const docCode = currentEditDocsList[index]?.document_code || `Document #${index + 1}`;
        if (!confirm(`Are you sure you want to remove "${docCode}" from this ticket?\n\nThis will take effect when you click "Save Changes".`)) {
            return;
        }
        currentEditDocsList.splice(index, 1);
        renderEditDocumentsList();
    };

    // Save changes via AJAX
    function saveEditTicketMode() {
        if (!currentTicketId) return;

        const officeSelect = document.getElementById('edit_ticket_office');
        const newSection = officeSelect ? officeSelect.value.trim() : '';
        if (!newSection) {
            alert('Please select an Originating Cluster and Specific Office.');
            if (officeSelect) officeSelect.focus();
            return;
        }

        syncEditDocsFromInputs();

        if (currentEditDocsList.length === 0) {
            alert('A ticket must have at least one document.');
            return;
        }

        for (let i = 0; i < currentEditDocsList.length; i++) {
            const doc = currentEditDocsList[i];
            if (!doc.document_code || !doc.document_code.trim()) {
                alert(`Document #${i + 1} requires a Document Code.`);
                return;
            }
            if (!doc.document_title || !doc.document_title.trim()) {
                alert(`Document #${i + 1} requires a Document Title.`);
                return;
            }
            if (!['addition', 'revision', 'deletion'].includes(doc.classification)) {
                alert(`Document #${i + 1} has an invalid modification type.`);
                return;
            }
            if (doc.source_type === 'others' && (!doc.specific_type || !doc.specific_type.trim())) {
                alert(`Document #${i + 1} requires specifying the Custom Type when "Others" is selected.`);
                return;
            }
        }

        const saveBtn = document.getElementById('save_edit_ticket_btn');
        const originalText = saveBtn ? saveBtn.innerHTML : 'Save Changes';
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Saving...</span>
            `;
        }

        fetch(`/iso/idc/tickets/${currentTicketId}/update-details`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                originating_section: newSection,
                documents: currentEditDocsList
            })
        })
        .then(response => response.json())
        .then(data => {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }

            if (data.success) {
                alert(data.message || 'Ticket details updated successfully!');
                sessionStorage.setItem('reopen_ticket_id', currentTicketId);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update ticket details.'));
            }
        })
        .catch(error => {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
            console.error('Update ticket details error:', error);
            alert('An error occurred while updating ticket details. Please try again.');
        });
    }

    // Bind action bar buttons
    if (editTicketBtn) {
        editTicketBtn.addEventListener('click', enterTicketEditMode);
    }
    if (cancelEditTicketBtn) {
        cancelEditTicketBtn.addEventListener('click', showTicketViewMode);
    }
    if (saveEditTicketBtn) {
        saveEditTicketBtn.addEventListener('click', saveEditTicketMode);
    }

    // Close modal
    closeDetailsBtn.addEventListener('click', () => {
        detailsModal.classList.remove('active');
        showTicketViewMode();
    });

    // Close when clicking outside
    detailsModal.addEventListener('click', (e) => {
        if (e.target === detailsModal) {
            detailsModal.classList.remove('active');
            showTicketViewMode();
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

        // Store ticket data
        currentTicketData = {
            id: ticketId,
            number: ticketNumber,
            section: section,
            status: status,
            created: created,
            creator: creator,
            sharepoint: sharepoint,
            message: message,
            documents: ticketDocuments,
            isRegistered: isRegistered
        };

        // Reset to View Mode
        showTicketViewMode();

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
            const hasRevisedTitle = doc.classification === 'revision' && doc.revising_master_document && doc.revising_master_document.document_title && doc.revising_master_document.document_title !== doc.document_title;
            row.innerHTML = `
                <td class="px-3 py-2">${escapeHtml(doc.document_code)}</td>
                <td class="px-3 py-2">
                    <div class="font-medium text-gray-900">${escapeHtml(doc.document_title)}</div>
                    ${hasRevisedTitle ? `
                        <div class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5 mt-1 inline-flex items-center gap-1">
                            <span class="font-semibold">Original:</span> ${escapeHtml(doc.revising_master_document.document_title)}
                        </div>` : ''}
                </td>
                <td class="px-3 py-2">
                    <span class="inline-block px-2 py-1 text-xs rounded ${getClassificationColor(doc.classification)}">${classLabel}</span>
                </td>
                <td class="px-3 py-2">${escapeHtml(sourceLabel)}</td>
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

        // Show / hide Delete Ticket and Edit Ticket button + action bar
        if (!isRegistered) {
            if (deleteTicketBtn) deleteTicketBtn.style.display = 'inline-flex';
            if (editTicketBtn) editTicketBtn.style.display = 'inline-flex';
            if (modalActionBar) modalActionBar.style.display = 'flex';
        } else {
            if (deleteTicketBtn) deleteTicketBtn.style.display = 'none';
            if (editTicketBtn) editTicketBtn.style.display = 'none';
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
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

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

    // Auto-reopen ticket modal if reloaded after editing
    const reopenTicketId = sessionStorage.getItem('reopen_ticket_id');
    if (reopenTicketId) {
        sessionStorage.removeItem('reopen_ticket_id');
        const targetBtn = document.querySelector(`.view-details-btn[data-ticket-id="${reopenTicketId}"]`);
        if (targetBtn) {
            targetBtn.click();
        }
    }
</script>