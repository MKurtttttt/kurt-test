<!-- ============================================ -->
<!-- CREATE TICKET MODAL -->
<!-- ============================================ -->
<div id="ticket_modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="text-xl font-bold">Create New DMCN Ticket</h2>
            <button id="close_modal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        
        <form id="ticket_form" action="{{ route('iso.document.store') }}" method="POST" class="modal-body">
            @csrf
            <!-- New Originating Section (Cluster)-->
                    <div class="form-group mb-2">
                        <label class="form-label text-sm">Originating Section (Cluster/Department) <span class="text-red-500">*</span></label>
                        <select id="ticket_cluster" class="form-input">
                            <option value="">Select Cluster/Department...</option>
                            <option value="aac">Academic Affairs Cluster (AAC)</option>
                            <option value="aie">Institute for Academic Innovation & Entrepreneurship (AIE)</option>
                            <option value="cfs">Institute for Christian Formation & Social Integration (CFS)</option>
                            <option value="csd">Campus Services & Development Office (CSD)</option>
                            <option value="eac">External Affairs Cluster (EAC)</option>
                            <option value="frm">Finance & Resources Management Services (FRM)</option>
                            <option value="hro">Human Resource Management Office (HRO)</option>
                            <option value="oie">Office of Institutional Effectiveness (OIE)</option>
                            <option value="oop">Office of the President (OOP)</option>
                            <option value="rss">Records Services & Affairs (RSS)</option>
                            <option value="ssa">Student Services & Affairs (SSA)</option>
                        </select>
                    </div>
                    <!-- New Originating Section (Office) (Conditional) -->
                    <!-- Based from the ticket_cluster id -->
                    <div class="form-group">
                        <label class="form-label text-sm">Specific Office<span class="text-red-500">*</span></label>
                        <select id="ticket_office" name="originating_section" class="form-input opacity-50 cursor-not-allowed" required disabled>
                            <option value="">Select a Department first...</option>
                        </select>
                    </div>

            <hr class="my-4">

            <!-- Add Document Section -->
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <h3 class="font-semibold mb-3 text-gray-700">Add Documents to Ticket</h3>
                <!-- Classification / Nautre of Document Modification -->
                <div class="form-group mb-2">
                    <label class="form-label text-sm">Nature of Document Modification<span class="text-red-800"> *</span></label>
                    <select id="doc_classification" class="form-input opacity-50 cursor-not-allowed" required disabled>
                        <option value="">Select...</option>
                        <option value="addition">Addition (Add new Document)</option>
                        <option value="revision">Revision (Update an Existing Document)</option>
                        <option value="deletion">Deletion (Delete an Existing Document)</option>
                    </select>
                </div>
                <!-- For ADDITION: Show all manual input fields -->
                <div id="addition_fields" style="display: none;">
                    <div class="grid grid-cols-2 gap-3">
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
                        <!-- Specific Type (conditional) -->
                        <div id="specific_type_section" class="form-group mb-2" style="display: none;">
                            <label class="form-label text-sm">Specific Type</label>
                            <select id="doc_specific_type" class="form-input">
                                <option value="">Select...</option>
                            </select>
                        </div>
                        <!-- Custom Source Input (for "Others") -->
                        <div id="custom_source_section" class="form-group mb-2 col-span-2" style="display: none;">
                            <label class="form-label text-sm">Specify Type <span class="text-red-500"> *</span></label>
                            <input type="text"
                                    id="doc_custom_source"
                                    class="form-input"
                                    placeholder="Enter custom source type...">
                            <small class="text-gray-500">Required when "Others" is selected</small>
                        </div>
                    </div>
                </div>
                <!-- For REVISION/DELETION: Show existing documents dropdown -->
                <div id="existing_doc_fields" style="display:none;">
                    <div class="form-group mb-3">
                        <label class="form-label text-sm">Select Existing Document <span class="text-red-500"> *</span></label>
                        <select id="existing_doc_select" class="form-input">
                            <option value="">Select an Office first...</option>
                        </select>
                        <small class="text-gray-500 block mt-1">
                            Only documents from the selected office will appear
                        </small>
                    </div>

                    <!-- Show selected document details (read-only) -->
                    <div id="selected_doc_preview" class="bg-white p-3 rounded border border-gray-300" style="display: none;">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Selected Document:</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-600">Code:</span>
                                <span id="preview_code" class="font-mono text-blue-600"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Source:</span>
                                <span id="preview_source"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-600">Title:</span>
                                <span id="preview_title"></span>
                            </div>
                        </div>
                    </div>
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
                                <th class="px-3 py-2 text-left">Nature of Document Modification</th>
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

            <!-- Create Ticket button -->
            <div class="form-group">
                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded hover:bg-green-700 font-semibold">
                    Create Ticket
                </button>
            </div>
        </form>
    </div>
</div>