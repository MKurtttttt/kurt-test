<x-app-layout>
    <!-- ISO DOCUMENT HANDLER VIEW -->
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
        <div class="w-full bg-green-600 text-white rounded-xl px-4 py-2 mb-4 text-center" id="success">
            {{ session('success') }}
        </div>
        @endif
        @if (session('msg'))
            <div class="w-full bg-green-600 text-white rounded-xl px-4 py-2 mb-4 text-center" id ="msg">
                {{ session('msg') }}
            </div>
        @endif
        @if(session('error'))
            <div class="w-full bg-red-600 text-white rounded-xl px-4 py-2 mb-4 text-center" id="error-msg">
                {{ session('error') }}
            </div>
        @endif

        <div class="w-[95%] px-4 flex my-4 items-center">
            <img src="{{ asset('images/icons/portal_nav/iso-title.png') }}" class="w-[100px] h-[100px] mr-4"/>
            <div class="w-full flex flex-col justify-center">
                <h1 class="text-[1.5rem] font-bold leading-tight">ISO Document Handler Ticket System</h1>
                <span class="text-gray-500 text-sm"> Document Modification / Creation Notice (DMCN) Tracking</span>
            </div>
            <!-- DEBUG/ADMIN links - Only visible to roles that has SuperAdmin or IDC Admin -->
            @if(in_array(auth()->user()->role, ['IDC Admin', 'SuperAdmin']))
                <div class="flex flex-warp items-center gap-3 shrink-0">
                    <a href="{{ route('iso.idc.dashboard') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-semibold">
                        Switch to IDC Admin view
                    </a>
                    <a href="{{ route('iso.management.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-semibold">
                        Switch to Management View
                    </a>
                </div>
            @endif
        </div>
        <hr class="w-full opacity-100">

        <!-- Status Tabs -->
         <div class="w-full flex">
            <a href="{{ route('iso.document', ['status' => 'all', 'search' => $search ?? null]) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'all' ? 'active_link' : '' }}">
                My Tickets
            </a>
            <a href="{{ route('iso.document', ['status' => 'submitted_to_idc', 'search' => $search ?? null]) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'submitted_to_idc' ? 'active_link' : '' }}">
                Submitted to IDC
            </a>
            <a href="{{ route('iso.document', ['status' => 'with_qmr', 'search' => $search ?? null]) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'with_qmr' ? 'active_link' : '' }}">
                With QMR
            </a>
            <a href="{{ route('iso.document', ['status' => 'approved', 'search' => $search ?? null]) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'approved' ? 'active_link' : '' }}">
                Approved
            </a>
            <a href="{{ route('iso.document', ['status' => 'on_hold', 'search' => $search ?? null]) }}"
                class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 {{ ($statusFilter ?? 'all') === 'on_hold' ? 'active_link' : '' }}">
                On-hold
            </a>
         </div>
         <hr class="mb-2 opacity-90 w-full">

        <!-- Search Box -->
        <div class="w-full px-4 py-3 bg-gray-50 rounded mb-4">
            <form method="GET" action="{{ route('iso.document') }}" class="flex gap-3 items-center">
                <!-- Preserve status filter -->
                <input type="hidden" name="status" value="{{ $statusFilter ?? 'all' }}">

                <!-- Search input -->
                <div class="flex-1">
                    <input type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Search by Ticket Number, Section, Document Code, or Title..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <!-- Search button -->
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold">
                    Search
                </button>

                <!-- Clear Button (only show if searching) -->
                @if($search ?? false)
                    <a href="{{ route('iso.document', ['status' => $statusFilter ?? 'all']) }}"
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

        <!-- My Tickets Table -->
        <div id="mytickets" class="w-[95%] flex flex-col border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            @if(count($tickets) > 0)
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Ticket Number</th>
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
                                    @if($ticket->ticket_number)
                                        {{ $ticket->ticket_number }}
                                    @else
                                        Pending
                                    @endif
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
                                        {{ str_replace(['Idc', 'Qmr'], ['IDC', 'QMR'], ucwords(str_replace('_', ' ', $ticket->status))) }}
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
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                                Edit
                                            </button>
                                            <!-- Delete Buton -->
                                            <button onclick="confirmDelete({{ $ticket->id }})"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                                Delete
                                            </button>
                                            <!-- Submit to IDC -->
                                            <button onclick="confirmSubmit({{ $ticket->id }})"
                                                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                                Submit to IDC
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

@include('iso.partials.create-ticket-modal')
@include('iso.partials.view-details-ticket-modal')
@include('iso.partials.edit-ticket-modal')

<!-- Hidden Delete Form -->
<form id="delete_form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
<form id="submit_form" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
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
        z-index: 10;
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
    // ============================================
    // CREATE TICKET MODAL
    // ============================================
    // Variable Instantiation
    // --------------------------------------------
    const modal = document.getElementById('ticket_modal');
    const createBtn = document.getElementById('create_ticket_btn');
    const closeBtn = document.getElementById('close_modal');

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

    // Specific Office options
    const specificOfficeOptions = {
        oop: [
            "(OOP) Office of the President",
            "(OOP-AVI) Aviation Insitute",
            "(OOP-CKS) Center for Kapampangan Studies",
            "(OOP-DPO) Data Privacy Office",
            "(OOP-ITC) Institutional Testing and Evaluation Center",
            "(OOP-ITS) Information Technology Systems & Services",
            "(OOP-OIA) Office of International Affairs",
            "(OOP-TRO) Treasury Office",
            "(OOP-UCO) University Chaplain Office"
        ],
        aac: [
            '(AAC) Academic Affairs Office',
            '(AAC-BED) School of Basic Education',
            '(AAC-CJE) College of Criminal Justice Education & Forensics',
            '(AAC-CTL) Center for Teaching & Learning',
            '(AAC-GSR) Graduate Studies & Research',
            '(AAC-HAT) Holy Angel Travel Services',
            '(AAC-IRB) Institutional Review Board',
            '(AAC-LIB) Library Department',
            '(AAC-LMS) Learning Management System',
            '(AAC-SAS) School of Arts & Sciences',
            '(AAC-SBA) School of Business & Accountancy',
            '(AAC-SEA) School of Engineering & Architecture',
            '(AAC-SED) School of Education',
            '(AAC-SNA) SChool of Nursing & Allied Medical Sciences',
            '(AAC-SOC) School of Computing',
            '(AAC-STM) School of Hospitality & Tourism Management',
            '(AAC-URO) University Research Office'
        ],
        oie: [
            '(OIE) Office of the Institutional Effectiveness',
            '(OIE-DMO) Institutional Database Management Office',
            '(OIE-IDC) Insitutional Document Controller',
            '(OIE-IPR) Institutional Research, Planning & Publications Office',
            '(OIE-QAO) Quality Assurance Office'
        ],
        cfs: [
            '(CFS) Institute for Catholic Formation & Social Integration',
            '(CFS-CES) Office of the Community Extension Services',
            '(CFS-CEP) Character Education Program Desk',
            '(CFS-CLE) Christian Living Education',
            '(CFS-CMO) Campus Ministry Office'
        ],
        hro: [
            '(HRO) Human Resource Management',
            '(HRO-HRD) Human Resource Development Office',
            '(HRO-HRM) Recruitment and Maintenance'
        ],
        frm: [
            '(FRM) Finance and Resource Management Office',
            '(FRM-ACC) Accounts & Collection',
            '(FRM-ASA) Ancillary Services Accounting',
            '(FRM-ASE) Ancillary Services',
            '(FRM-ATO) Accounting',
            '(FRM-GRT) Grants Accounttant',
            '(FRM-PAO) Payroll'
        ],
        rss: [
            '(RSS) Records Systems & Services',
            '(RSS-ADO) Admissions Office'
        ],
        ssa: [
            '(SSA-CPO) Career and Placement Office',
            '(SSA-MDS) Medical and Dental Services',
            '(SSA-SAO) Student Affairs',
            '(SSA-SGO) Scholarships & Grants',
            '(SSA-UGC) University Guidance Center',
            '(SSA-USO) University Sports'
        ],
        eac: [
            '(EAC) External Affairs Office',
            '(EAC-ARO) Alumni Relations Office',
            '(EAC-CRE) Creative Services',
            '(EAC-PAM) Performing Arts and Events Management',
            '(EAC-PRO) Public Relations Office'
        ],
        csd: [
            '(CSD) Campus Services & Development Office',
            '(CSD-CPO) Central Purchasing Office',
            '(CSD-CSO) Campus Services Office',
            '(CSD-ECM) Engineering Construction and Maintenance',
            '(CSD-MCM) Motorpool/Campus Maintenance',
            '(CSD-PCO) Property Custodianship Office',
            '(CSD-PUO) Purchasing Office',
            '(CSD-SEC) Campus Security',
            '(CSD-VLO) Venues and Logistics Office'
        ],
        aie: [
            '(AIE) Institute for academic Innovation & Entrepreneurship',
            '(AIE-ETA) Expanded Tertiary Education, Equivalency & Accreditation',
            '(AIE-SPL) School of Professional Education and Lifelong Learning',
            '(AIE-TBI) Technology Business Incubator - KITTO'
        ],
        iat: [
            '(IAT) Internal Audit Team'
        ]
    }
    // --------------------------------------------
    // Prefix Logic
    // --------------------------------------------
    // Create Modal - Prefix
    const docCodeInput = document.getElementById('doc_code');
    attachDocCodeProtected(docCodeInput);

    // --------------------------------------------
    // Cluster and Office Logic
    // --------------------------------------------
    const ticketClusterDropdown = document.getElementById('ticket_cluster');
    const ticketOfficeDropdown = document.getElementById('ticket_office');

    // Document Classification reference
    const classificationSelect = document.getElementById('doc_classification');

    // Custom Source
    const customSourceSection = document.getElementById('custom_source_section');
    const customSourceInput = document.getElementById('doc_custom_source');

    // New Doc Classification logic
    let currentSelectedOffice = null;

    // ============================================
    // Event Listerners
    // ============================================
    // Create Ticket Modal
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

    // ======================================
    // Document Prefix Function
    // ======================================
    // Document code prefix
    function getOfficeCode(officeValue){
        // Extracts the Code from the originating_section
        // e.g., extracts "AAC-SOC" from "(AAC-SOC) School of Computing"
        const match = officeValue.match(/\(([^)]+)\)/);
        return match ? match[1] : null;
    }
    function generateDocCodePrefix(source, officeValue, specificType){
        const officeCode = getOfficeCode(officeValue);

        if(!source || !officeCode) return;

        switch(source){
            case 'forms':
                return `FM-${officeCode}`;
            case 'procedures':
                return `PD-${officeCode}`;
            case 'eoms':
                return specificType ? `${specificType} ${officeCode}` : '';
            case 'records':
                return specificType ? `${specificType.replace('0', '')}RMM${"-"+officeCode}` : '';
            case 'others':
                return null; //skip auto-fill for others
            default:
                return '';
        }
    }
    function updateDocField(inputElement, source, officeValue, specificType){
        const newPrefix = generateDocCodePrefix(source, officeValue, specificType);

        // null for "others"
        if(newPrefix === null){
            inputElement.value = '';
            inputElement.dataset.prefix = '';
            return;
        }

        if(!newPrefix) return; //Incomplete don't update yet

        const oldPrefix = inputElement.dataset.prefix || '';
        const currentValue = inputElement.value;

        if(oldPrefix && currentValue.startsWith(oldPrefix)){
            const userSuffix = currentValue.slice(oldPrefix.length);
            inputElement.value = newPrefix + userSuffix;
        } else {
            inputElement.value = newPrefix;
        }

        inputElement.dataset.prefix = newPrefix;
    }
    function attachDocCodeProtected(inputElement){
        // Prevent the user from deleting the prefix
        inputElement.addEventListener('keydown', (e) =>{
            const prefix = inputElement.dataset.prefix || '';
            if((e.key === 'Backspace' || e.key === 'Delete') && inputElement.selectionStart <= prefix.length){
                e.preventDefault();
            }
        });
        // Guard against paste/cut that could corrupt the prefix
        inputElement.addEventListener('input', ()=>{
            const prefix = inputElement.dataset.prefix || '';
            if(!inputElement.value.startsWith(prefix)){
                inputElement.value = prefix;
            }
        });
    }
    
    specificTypeSelect.addEventListener('change', ()=>{
        updateDocField(docCodeInput,
            docSource.value,
            currentSelectedOffice,
            specificTypeSelect.value
        );
    });
    // ======================================
    // Cluster/Department and Offices
    // ======================================
    ticketClusterDropdown.addEventListener('change', ()=> {
        const newValue = ticketClusterDropdown.value;
        const oldValue = ticketClusterDropdown.dataset.lastValue || null;
        if(!clearAllDocuments()){
            // User cancelled - Revert the dropdown to its previous value
            ticketClusterDropdown.value = oldValue;
            return;
        }
        ticketClusterDropdown.dataset.lastValue = newValue;
        // Reset downstream state
        currentSelectedOffice = null;
        clearDocumentForm();
        // console.log("Before toggleClassification method");
        toggleClassification(null);
        // console.log("After toggleClassification method");
        document.getElementById('addition_fields').style.display = 'none';
        document.getElementById('existing_doc_fields').style.display = 'none';

        if(newValue !== ''){
            // Reset office dropdown when choosing a department
            ticketOfficeDropdown.innerHTML = '<option value="">Select Office...</option>';
            ticketOfficeDropdown.classList.remove('opacity-50', 'cursor-not-allowed')
    
            // If a cluser is selected, populate the office choices
            if (specificOfficeOptions[newValue]){
                const offices = specificOfficeOptions[newValue];
    
                offices.forEach(office => {
                    const option = document.createElement('option');
                    option.value = office;
                    option.textContent = office;
                    ticketOfficeDropdown.appendChild(option);
                });
    
                ticketOfficeDropdown.disabled = false;
                if(offices.length === 1){
                    ticketOfficeDropdown.value = offices[0];
                    // If only one office, treat it as selected
                    currentSelectedOffice = offices[0];
                    toggleClassification(currentSelectedOffice);
                }
            }
        } else{
            ticketOfficeDropdown.innerHTML = '<option value="">Select a Department first...</option>';
            ticketOfficeDropdown.classList.add('opacity-50', 'cursor-not-allowed');
            ticketOfficeDropdown.disabled = true;

            toggleClassification(null);
        }
    });
    ticketOfficeDropdown.addEventListener('change', ()=>{
        const newValue = ticketOfficeDropdown.value;
        const oldValue = ticketOfficeDropdown.dataset.lastValue || null;
        if(!clearAllDocuments()){
            ticketOfficeDropdown.value = oldValue;
            return;
        }
        currentSelectedOffice = newValue || null;
        ticketOfficeDropdown.dataset.lastValue = currentSelectedOffice;

        classificationSelect.value = '';
        clearDocumentForm();
        document.getElementById('addition_fields').style.display = 'none';
        document.getElementById('existing_doc_fields').style.display = 'none';
        toggleClassification(currentSelectedOffice);
        updateDocField(
            docCodeInput,
            docSource.value,
            currentSelectedOffice,
            specificTypeSelect.value
        ); // Autofill function
    });

    classificationSelect.addEventListener('change', (e)=>{
        if(!currentSelectedOffice){
            alert('Please select an office first');
            e.target.value = '';
            return;
        }

        const classification = e.target.value;
        const additionFields = document.getElementById('addition_fields');
        const existingDocFields = document.getElementById('existing_doc_fields');

        // Reset fields when classification Changes
        clearDocumentForm()

        if(classification === 'addition'){
            additionFields.style.display = 'block';
            existingDocFields.style.display = 'none';
        } else if(classification === 'revision' || classification === 'deletion'){
            additionFields.style.display = 'none';
            existingDocFields.style.display = 'block';
            loadExistingDocuments(currentSelectedOffice);
        } else{
            // No classification selected
            additionFields.style.display = 'none';
            existingDocFields.style.display = 'none';
        }
    })

    // Show/hide specific type dropdown
    docSource.addEventListener('change', () => {
        const source = docSource.value;

        if (source === 'eoms' || source === 'records'){
            specificTypeSection.style.display = 'block';
            customSourceSection.style.display = 'none';
            specificTypeSelect.innerHTML = `<option value="">Select... </option>`;

            const options = sourceTypeOptions[source];
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                specificTypeSelect.appendChild(option);
            });
        } else if (source === 'others'){
            specificTypeSection.style.display = 'none';
            customSourceSection.style.display = 'block';
            specificTypeSelect.value = '';
            customSourceInput.value = '';
        } else {
            specificTypeSection.style.display = 'none';
            customSourceSection.style.display = 'none';
            specificTypeSelect.value = '';
            customSourceInput.value = '';
        }
        updateDocField(       //Autofill function
            docCodeInput,
            docSource.value,
            currentSelectedOffice,
            specificTypeSelect.value
        ); 
    });


    async function loadExistingDocuments(office){
        const selectElement = document.getElementById('existing_doc_select');

        // Show loading state
        selectElement.innerHTML = '<option value="">Loading Documents...</option>';
        selectElement.disabled = true;

        try{
            const response = await fetch(`/iso/documents/by-office?office=${encodeURIComponent(office)}`);

            if(!response.ok){
                throw new Error('Failed to load documents');
            }
            const documents = await response.json();

            selectElement.innerHTML = '<option value="">Select a document...</option>'

            if(documents.length === 0){
                selectElement.innerHTML = '<option value="">No documents found for this office</option>';
            } else {
                documents.forEach(doc => {
                    const option = document.createElement('option');
                    option.value = doc.id;
                    option.textContent = `${doc.document_code} | ${doc.document_title}`;

                    // Store document data in data attributes
                    option.dataset.code = doc.document_code;
                    option.dataset.title = doc.document_title;
                    option.dataset.source = doc.source_type;
                    option.dataset.specificType = doc.specific_type || '';

                    selectElement.appendChild(option);
                });
            }

            selectElement.disabled = false;
        } catch (error) {
            console.error('Error loading documents: ', error);
            selectElement.innerHTML = '<option value="">Error loading documents</option>';
            alert('Failed to load documents. Please try again.');
        }
    }

    document.getElementById('existing_doc_select').addEventListener('change', (e)=>{
        const selectedOption = e.target.options[e.target.selectedIndex];
        const preview = document.getElementById('selected_doc_preview');

        if(selectedOption.value){
            const specificType = selectedOption.dataset.specificType || null;
            // show document preview with data from option
            document.getElementById('preview_code').textContent = selectedOption.dataset.code;
            document.getElementById('preview_title').textContent = selectedOption.dataset.title;
            document.getElementById('preview_source').textContent = getSourceLabel(
                selectedOption.dataset.source,
                specificType
            );
            preview.style.display = 'block';
        } else{
            preview.style.display = 'none';
        }
    });

    // Updated add Document button logic
    document.getElementById('add_document_btn').addEventListener('click', ()=>{
        const classification = document.getElementById('doc_classification').value;

        if(!classification){
            alert('Please select Nature of Document Modification first');
            return;
        }
        if(classification === 'addition'){
            // Handle addition
            addNewDocument();
        } else if (classification === 'revision' || classification === 'deletion'){
            addExistingDocument(classification);
        }
    });

    async function addNewDocument(){
        const code = document.getElementById('doc_code').value.trim();
        const title = document.getElementById('doc_title').value.trim();
        const classification = 'addition';
        const source = document.getElementById('doc_source').value;
        const specificType = document.getElementById('doc_specific_type').value;
        const customSource = document.getElementById('doc_custom_source').value.trim();

        if (!validationCheckForm(code, title, classification, source, specificType, customSource)){
            return;
        }
        // Check for duplicate before adding
        const isDuplicate = await checkDocumentCodeExists(code);
        if (isDuplicate){
            alert(`Document code "${code}" already exists in the system. Please use a different code.`);
            return;
        }

        // Create document object
        const doc = {
            code,
            title,
            classification,
            source,
            specificType: source === 'others' ? customSource : (specificType || null),
            id: Date.now(),
            revisingMasterId: null //New documents doesn't revise anything
        };
        documents.push(doc);
        updateDocumentsList();
        clearDocumentForm();
    }
    async function checkDocumentCodeExists(code){
        try{
            const response = await fetch(`/iso/documents/check-code?document_code=${encodeURIComponent(code)}`);
            if(!response.ok) throw new Error('Check failed'); // TODO: Replace this with the error message with HTML
            const data = await response.json();
            return data.exists;
        } catch (error){
            console.error('Error checking document code: ', error);
            return false;
        }
    }

    function addExistingDocument(classification){
        const selectElement = document.getElementById('existing_doc_select');
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        if(!selectedOption.value){
            alert('Please select a document');
            return;
        }

        const doc = {
            code: selectedOption.dataset.code,
            title: selectedOption.dataset.title,
            classification: classification,
            source: selectedOption.dataset.source,
            specificType: selectedOption.dataset.specificType || null,
            id: Date.now(),
            revisingMasterId: selectedOption.value
        };
        documents.push(doc);
        updateDocumentsList();
        clearDocumentForm();
    }

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
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="removeDocument(${doc.id})" class="text-red-600 hover:text-red-800 text-sm">
                     Remove
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
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
        const successElement = document.getElementById('success');
        if(successElement) successElement.style.display = 'none';

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
            
            // // Parse the documents JSON string into an array TODO: REMOVE THIS WHEN SENDING EMAIL
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

                    <td class="px-3 py-2">
                        <span class="inline-block px-2 py-1 text-xs rounded ${getStatusColorJS(doc.status)}">
                            ${formatStatusText(doc.status)}
                        </span>
                    </td>
                `;
                
                documentsList.appendChild(row);
            });
            
            // Show the modal
            detailsModal.classList.add('active');
        }
    });

// ============================================
// EDIT TICKET FUNCTIONALITY
// ============================================
    let editDocuments = [];
    let currentEditTicketId = null;
    let currentEditOffice = null;
    let isProgrammaticChange = false;

    const editModal = document.getElementById('edit_modal');
    const closeEditBtn = document.getElementById('close_edit_modal');
    const cancelEditBtn = document.getElementById('cancel_edit_btn');
    const editDocSource = document.getElementById('edit_doc_source');
    const editSpecificTypeSection = document.getElementById('edit_specific_type_section');
    const editCustomSourceSection = document.getElementById('edit_custom_source_section');
    const editSpecificTypeSelect = document.getElementById('edit_doc_specific_type');
    const editCustomSourceInput = document.getElementById('edit_doc_custom_source');
    const editTicketClusterDropdown = document.getElementById('edit_ticket_cluster');
    const editTicketOfficeDropdown = document.getElementById('edit_ticket_office');

    // Event Listerners
    // Close edit modal
    closeEditBtn.addEventListener('click', ()=> {
        editModalClose();
    });

    cancelEditBtn.addEventListener('click', ()=> {
        editModalClose();
    });

    // Close when clicking outside
    editModal.addEventListener('click', (e)=> {
        if (e.target === editModal){
            editModalClose();
        }
    });

    // Edit Modal - Prefix
    const editDocCodeInput = document.getElementById('edit_doc_code');
    attachDocCodeProtected(editDocCodeInput);

    // Start of New Originating Section Edit function
    editTicketClusterDropdown.addEventListener('change', ()=>{
        const newValue = editTicketClusterDropdown.value;
        const oldValue = editTicketClusterDropdown.dataset.lastValue || null;
        // Clear document if user didn't confirm
        if(!clearAllDocuments(true)){ // true because the parameter of the method is edit mode
            editTicketClusterDropdown.value = oldValue;
            return;
        }
        editTicketClusterDropdown.dataset.lastValue = newValue;
        if(!isProgrammaticChange){
            currentEditOffice = null;
            document.getElementById('edit_doc_classification').value = '';
            document.getElementById('edit_addition_fields').style.display = 'none';
            document.getElementById('edit_existing_doc_fields').style.display = 'none';
            clearEditDocumentForm();
        }

        editTicketOfficeDropdown.innerHTML = '<option value="">Select Office...</option>';
        editTicketOfficeDropdown.disabled = true;
        
        if(newValue && specificOfficeOptions[newValue]){
            const offices = specificOfficeOptions[newValue];

            offices.forEach(office => {
                const option = document.createElement('option');
                option.value = office;
                option.textContent = office;
                editTicketOfficeDropdown.appendChild(option);
            });

            editTicketOfficeDropdown.disabled = false;

            if(!isProgrammaticChange && offices.length === 1){
                editTicketOfficeDropdown.value = offices[0];
                currentEditOffice = offices[0];
            }
        }
    });
    // End of New Originating Section Edit function

    editTicketOfficeDropdown.addEventListener('change', ()=>{
        const newValue = editTicketOfficeDropdown.value;
        const oldValue = editTicketOfficeDropdown.dataset.lastValue || null;
        if(!clearAllDocuments(true)){
            editTicketOfficeDropdown.value = oldValue;
            return;
        }
        editTicketOfficeDropdown.dataset.lastValue = newValue;
        currentEditOffice = newValue || null;

        document.getElementById('edit_doc_classification').value = '';
        document.getElementById('edit_addition_fields').style.display = 'none';
        document.getElementById('edit_existing_doc_fields').style.display = 'none';
        clearEditDocumentForm();
        updateDocField(
            editDocCodeInput,
            editDocSource.value,
            currentEditOffice,
            editSpecificTypeSelect.value
        );
    });

    document.getElementById('edit_doc_classification').addEventListener('change', (e)=>{
        const classification = e.target.value;
        const additionFields = document.getElementById('edit_addition_fields');
        const existingDocFields = document.getElementById('edit_existing_doc_fields');

        clearEditDocumentForm();

        if(classification === 'addition'){
            additionFields.style.display = 'block';
            existingDocFields.style.display = 'none';
        } else if (classification === 'revision' || classification === 'deletion'){
            additionFields.style.display = 'none';
            existingDocFields.style.display = 'block';

            if(currentEditOffice){
                loadExistingDocumentsForEdit(currentEditOffice);
            } else{
                alert('Please select an office first');
                e.target.value = '';
            }
        } else{
            additionFields.style.display = 'none';
            existingDocFields.style.display = 'none';
        }
    });

    // Handle Source Type Change (for Edit Modal - ADDITION ONLY)
    editDocSource.addEventListener('change', () => {
        const source = editDocSource.value;

        // Always reset custom source input value on change
        editCustomSourceInput.value = '';
        editSpecificTypeSelect.innerHTML = '<option value="">Select...</option>';

        if (source === 'eoms' || source === 'records') {
            editSpecificTypeSection.style.display = 'block';
            editCustomSourceSection.style.display = 'none';
            const options = sourceTypeOptions[source];
            if (Array.isArray(options)) {
                options.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.label;
                    editSpecificTypeSelect.appendChild(option);
                });
            }
        } else if (source === 'others') {
            editSpecificTypeSection.style.display = 'none';
            editCustomSourceSection.style.display = 'block';
            editSpecificTypeSelect.value = '';
        } else {
            editSpecificTypeSection.style.display = 'none';
            editCustomSourceSection.style.display = 'none';
            editSpecificTypeSelect.value = '';
        }
        updateDocField(editDocCodeInput,
            editDocSource.value,
            currentEditOffice,
            editSpecificTypeSelect.value
        );
    });
    editSpecificTypeSelect.addEventListener('change', ()=>{
        updateDocField(
            editDocCodeInput,
            editDocSource.value,
            currentEditOffice,
            editSpecificTypeSelect.value
        )
    })

    async function loadExistingDocumentsForEdit(office){
        const selectElement = document.getElementById('edit_existing_doc_select');

        selectElement.innerHTML = '<option value="">Loading Documents...</option>';
        selectElement.disabled = true;

        try{
            const response = await fetch(`/iso/documents/by-office?office=${encodeURIComponent(office)}`);

            if(!response.ok){
                throw new Error('Failed to load documents');
            }

            const documents = await response.json();
            selectElement.innerHTML = '<option value="">Select a document...</option>';

            if(documents.length === 0){
                selectElement.innerHTML = '<option value="">No documents found for this office</option>';
            } else {
                documents.forEach(doc =>{
                    const option = document.createElement('option');
                    option.value = doc.id;
                    option.textContent = `${doc.document_code} - ${doc.document_title}`;

                    option.dataset.code = doc.document_code;
                    option.dataset.title = doc.document_title;
                    option.dataset.source = doc.source_type;
                    option.dataset.specificType = doc.specific_type || '';

                    selectElement.appendChild(option);
                });
            }
            selectElement.disabled = false;
        } catch (error){
            console.error('Error loading documents:', error);
            selectElement.innerHTML = '<option value="">Error loading documents</option>';
            alert('Failed to load documents. Please try again.');
        }
    }

    // Handle existing document selection in edit mode
    document.getElementById('edit_existing_doc_select').addEventListener('change',(e)=>{
        const selectedOption = e.target.options[e.target.selectedIndex];
        const preview = document.getElementById('edit_selected_doc_preview');

        if(selectedOption.value){
            const specificType = selectedOption.dataset.specificType || null;

            document.getElementById('edit_preview_code').textContent = selectedOption.dataset.code;
            document.getElementById('edit_preview_title').textContent = selectedOption.dataset.title;
            document.getElementById('edit_preview_source').textContent = getSourceLabel(
                selectedOption.dataset.source,
                specificType
            );
            preview.style.display = 'block';
        } else{
            preview.style.display = 'none';
        }
    });
    

    function editModalClose(){
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
        resetEditForm();

        // Re-enable scrolling on the main page body
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('edit_add_document_btn').addEventListener('click', () => {
        const classification = document.getElementById('edit_doc_classification').value;

        if(!classification){
            alert('Please select Nature of Document Modification first');
            return;
        }

        if(classification === 'addition'){
            addEditNewDocument();
        } else if (classification === 'revision' || classification === 'deletion'){
            addEditExistingDocument(classification);
        }
    });
    
    // Function to add NEW document in edit mode
    async function addEditNewDocument(){
        const code = document.getElementById('edit_doc_code').value.trim();
        const title = document.getElementById('edit_doc_title').value.trim();
        const classification = 'addition';
        const source = document.getElementById('edit_doc_source').value;
        const specificType = document.getElementById('edit_doc_specific_type').value;
        const customSource =document.getElementById('edit_doc_custom_source').value.trim();

        if(!validationCheckForm(code, title, classification, source, specificType, customSource)){
            return;
        }
        // Same duplicate check from adding new document
        const isDuplicate = await checkDocumentCodeExists(code);
        if(isDuplicate){
            alert(`Document code "${code}" already exists in the system. Please use a different document code.`);
            return;
        }

        const doc = {
            code,
            title,
            classification,
            source,
            specificType: source === 'others' ? customSource: (specificType || null),
            id:Date.now() + Math.random(),
            revisingMasterId : null //Addition has no revising Id's
        };
        editDocuments.push(doc);
        updateEditDocumentsList();
        clearEditDocumentForm();
    }

    function addEditExistingDocument(classification){
        const selectElement = document.getElementById('edit_existing_doc_select');
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        if(!selectedOption.value || !selectedOption.value){
            alert('Please select a document');
            return;
        }

        const doc = {
            code: selectedOption.dataset.code,
            title: selectedOption.dataset.title,
            classification: classification,
            source: selectedOption.dataset.source,
            specificType: selectedOption.dataset.specificType || null,
            id: Date.now() + Math.random(),
            revisingMasterId: selectedOption.value
        };
        editDocuments.push(doc);
        updateEditDocumentsList();
        document.getElementById('edit_existing_doc_select').value = '';
        document.getElementById('edit_selected_doc_preview').style.display = 'none';
    }


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
                // Start of new Originating section Logic
                // Find which cluster the office belongs to
                const currentOffice = ticket.originating_section;
                const currentCluster = findClusterByOffice(currentOffice);

                if (currentCluster){
                    isProgrammaticChange = true;
                    // Set the cluster dropdown
                    editTicketClusterDropdown.value = currentCluster;

                    // Trigger the change event to populate offices
                    editTicketClusterDropdown.dispatchEvent(new Event('change'));
                    editTicketOfficeDropdown.value = currentOffice;
                    currentEditOffice = currentOffice;
                    isProgrammaticChange = false;
                }
                // End of new Originating section logic

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

                // Disable scrolling on the main page Body
                document.body.classList.add('overflow-hidden');
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
                    <button type="button" onclick="removeEditDocument('${doc.id}')" class="text-red-600 hover:text-red-800 text-sm">
                        Remove
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
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
    });

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
    // ============================================
    // SUBMIT TO IDC FUNCTIONALITY
    // ============================================
    function confirmSubmit(ticketId){
        const confirmed = confirm(
            'Submit this ticket to IDC?\n\n' +
            'Once submitted, you will no longer be able to edit or delete this ticket.'
        );

        if(confirmed){
            const form = document.getElementById('submit_form');
            form.action = `/iso/document/${ticketId}/submit`;
            form.submit();
        }
    }

    // Validation function - both on edit and creating ticket
    function validationCheckForm(code, title, classification, source, specificType, customSource){
        // Validation/Make sure the user has an input, no blank answers
        if (!code || !title || !classification || !source){
            alert('Please fill in all Document details (Code, Title, Classification, and Source).');
            return false;
        }
        // Make sure EOMS/Records has a specific type
        if((source === 'eoms' || source === 'records') && !specificType){
            alert('Please select a specific type');
            return false;
        }
        // Make sure Others has input
        if((source === 'others' && !customSource)){
            alert('Please specify the type of file')
            return false;
        }
        return true;
    }
    
    // ============================================
    // Helper Functions
    // ============================================
    function getSourceLabel(source, specificType){
        const labels = {
            eoms: 'EOMS Manual',
            procedures: 'Procedures',
            forms: 'Forms',
            records: 'Records Management',
            others: 'Others'
        };
        const specificTypeLabel = {
            '3' : 'Records Retention Schedule',
            '4' : 'Definition and Records Series Title',
            '3.0' : 'Records Retention Schedule',
            '4.0' : 'Definition and Records Series Title',
            '4.1' : 'Interested Parties',
            '4.2' : 'Risk Assessment',
            '7.4' : 'Communication',
            '8.1' : 'EOMS Plan'
        }
        let label = labels[source] || source;
        if (specificType) {
            const typeLabel = specificTypeLabel[specificType];
             if(specificType === '3' || specificType === '4'){
                label += `- ${specificType}.0 : ${typeLabel}`;
            }else{
                typeLabel ? label += `- ${specificType} : ${typeLabel}` : label += `- ${specificType}`;
            }
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
    // Remove Document from Edit List
    function removeEditDocument(id){
        editDocuments = editDocuments.filter(doc => String(doc.id) !== String(id));
        updateEditDocumentsList();
    }
    function clearAllDocuments(isEditMode = false){
        const targetArray = isEditMode ? editDocuments : documents;
        const updateUI = isEditMode ? updateEditDocumentsList : updateDocumentsList;

        if(targetArray.length === 0) return true;

        const confirmed = confirm(
            'Changing the department or office will remove all documents you have added.'
        );
        if(!confirmed) return false;

        if(isEditMode){
            editDocuments = [];
        } else {
            documents = [];
        }

        updateUI();
    }

    // Clear - Create Ticket form
    function clearDocumentForm(){
        // Addition, input fields.
        document.getElementById('doc_code').value = '';
        document.getElementById('doc_title').value = '';
        document.getElementById('doc_source').value = '';
        document.getElementById('doc_specific_type').value = '';
        document.getElementById('doc_custom_source').value = '';
        document.getElementById('existing_doc_select').value = '';

        document.getElementById('specific_type_section').style.display = 'none';
        document.getElementById('custom_source_section').style.display = 'none';
        document.getElementById('selected_doc_preview').style.display = 'none';
        //Document Autofill
        const docCodeInput = document.getElementById('doc_code');
        docCodeInput.value = '';
        docCodeInput.dataset.prefix = '';
    }

    // Clear - Edit Document Form
    function clearEditDocumentForm(){
        // The title and code needs to be swapped.
        document.getElementById('edit_doc_title').value = '';
        document.getElementById('edit_doc_code').value = '';
        document.getElementById('edit_doc_source').value = '';
        document.getElementById('edit_doc_specific_type').value = '';
        document.getElementById('edit_doc_custom_source').value = '';
        
        document.getElementById('edit_addition_fields').style.display = 'none';
        document.getElementById('edit_existing_doc_fields').style.display = 'none';
        document.getElementById('edit_specific_type_section').style.display = 'none';
        document.getElementById('edit_custom_source_section').style.display = 'none';
        document.getElementById('edit_selected_doc_preview').style.display = 'none';
    }
    // Formatting Status Texts
    function formatStatusText(status){
        let text = status.replace(/_/g, ' ');
        text = text.charAt(0).toUpperCase() + text.slice(1);
        if(status.includes('idc') || status.includes('qmr')){
            text = text.replace(/idc/gi, 'IDC').replace(/qmr/gi, 'QMR');
        }
        return text;
    }
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

    // Helper function for finding cluster by office
    function findClusterByOffice(officeName){
        for(const [cluster, offices] of Object.entries(specificOfficeOptions)){
            if(offices.includes(officeName)){
                return cluster;
            }
        }
        return null;
    }
    function toggleClassification(selectedOffice){
        // console.log("I Enter toggleClassification");
        if(!selectedOffice){
            // visually + functionally disable
            classificationSelect.classList.add('opacity-50', 'cursor-not-allowed');
            classificationSelect.setAttribute('disabled', true);
            classificationSelect.value = '';

            document.getElementById('addition_fields').style.display = 'none';
            document.getElementById('existing_doc_fields').style.display = 'none';
        } else {
            // Enable
            classificationSelect.classList.remove('opacity-50','cursor-not-allowed');
            classificationSelect.removeAttribute('disabled');
        }
    }
</script>