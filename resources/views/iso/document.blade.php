<x-app-layout>
<div class="min-h-screen">
<div class="container mx-auto">
    <div class="con-box">
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
            <button id="mytickets_btn" class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2 active_link">My Tickets</button>
            <button id="submitted_btn" class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2">Submitted to IDC</button>
            <button id="qmr_btn" class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2">With QMR</button>
            <button id="approved_btn" class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2">Approved</button>
            <button id="onhold_btn" class="hover:bg-gray-100 text-gray-400 font-semibold px-8 py-2">On-hold</button>
         </div>
         <hr class="mb-2 opacity-90 w-full">

         <!-- My Tickets Table -->
          <div id="mytickets" class="w-full flex flex-col border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="w-full items-center justify-center text-sm text-gray-400 pt-4 pb-12">
                <span class="italic">No tickets found.</span>
            </div>
            <!-- TODO: Ticket List goes here -->
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
        <form id="ticket_form" action="#" method="POST" class="modal-body">
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
                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded hover:bg-green700 font-semibold">
                    Submit Ticket to IDC
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
    // Tab switching
    const mytickets_btn = document.getElementById('mytickets_btn');
    const submitted_btn = document.getElementById('submitted_btn');
    const qmr_btn = document.getElementById('qmr_btn');
    const approved_btn = document.getElementById('approved_btn');
    const onhold_btn = document.getElementById('onhold_btn');

    const mytickets_tbl = document.getElementById('mytickets');
    const submitted_tbl = document.getElementById('submitted');
    const qmr_tbl = document.getElementById('qmr');
    const approved_tbl = document.getElementById('approved');
    const onhold_tbl = document.getElementById('onhold');

    function switchTab(activeBtn, activeTbl){
        [mytickets_btn, submitted_btn, qmr_btn, approved_btn, onhold_btn].forEach(btn => {
            btn.classList.remove('active_link');
        });

        [mytickets_tbl, submitted_tbl, qmr_tbl, approved_tbl, onhold_tbl].forEach(tbl =>{
        tbl.classList.add('inactive_link');
    });

        activeBtn.classList.add('active_link');
        activeTbl.classList.remove('inactive_link');
    }

    mytickets_btn.addEventListener("click", () => switchTab(mytickets_btn, mytickets_tbl));
    submitted_btn.addEventListener("click", () => switchTab(submitted_btn, submitted_tbl));
    qmr_btn.addEventListener("click", () => switchTab(qmr_btn, qmr_tbl));
    approved_btn.addEventListener("click", () => switchTab(approved_btn, approved_tbl));
    onhold_btn.addEventListener("click", () => switchTab(onhold_btn, onhold_tbl));

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
</script>