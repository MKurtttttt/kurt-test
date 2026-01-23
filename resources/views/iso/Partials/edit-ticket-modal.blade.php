<!-- ============================================ -->
<!-- EDIT TICKET MODAL -->
<!-- ============================================ -->
<div id="edit_modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] m-4 overflow-y-auto">
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
                
                <!-- Originating Section - New Dropdown -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Originating Section <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Cluster/Department</label>
                            <select id="edit_ticket_cluster" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Select Cluster...</option>
                                <option value="oop">Office of the President (OOP)</option>
                                <option value="aac">Academic Affairs Cluster (AAC)</option>
                                <option value="oie">Office of Institutional Effectiveness (OIE)</option>
                                <option value="cfs">Institute for Christian Formation & Social Integration (CFS)</option>
                                <option value="hro">Human Resource Management Office (HRO)</option>
                                <option value="frm">Finance & Resources Management Services (FRM)</option>
                                <option value="rss">Records Services & Affairs (RSS)</option>
                                <option value="ssa">Student Services & Affairs (SSA)</option>
                                <option value="eac">External Affairs Cluster (EAC)</option>
                                <option value="csd">Campus Services & Development Office (CSD)</option>
                                <option value="aie">Institute for Academic Innovation & Entrepreneurship (AIE)</option>
                            </select>
                        </div>

                        <!-- Office Dropdown -->
                         <div>
                            <label class="block text-xs text-gray-600 mb-1">Specific Office</label>
                            <select id="edit_ticket_office" name="originating_section" class="w-full px-3 py-2 border border-gray-300 rounded-md" required disabled>
                                <option value="">Select cluster first...</option>
                            </select>
                         </div>
                    </div>
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
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Nature of Document Modification</label>
                            <select id="edit_doc_classification" class="w-full px-2 py-1 border rounded text-sm">
                                <option value="">Select...</option>
                                <option value="revision">Revision</option>
                                <option value="addition">Addition</option>
                                <option value="deletion">Deletion</option>
                            </select>
                        </div>
                        <!-- For Addition -->
                        <div id="edit_addition_fields" style="display:none;">
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
                            <!-- Specific Type (shown only when Source Type is "EOMS Manual" or "Records Management") -->
                            <div id="edit_specific_type_section" style="display: none;" class="mb-3">
                                <label class="block text-xs text-gray-600 mb-1">Specific Type</label>
                                <select id="edit_doc_specific_type" class="w-full px-2 py-1 border rounded text-sm">
                                    <option value="">Select...</option>
                                </select>
                            </div>
                            <!-- Custom Source input for Edit modal -->
                            <div id="edit_custom_source_section" style="display: none;" class="col-span-2 mb-3">
                                <label class="block text-xs text-gray-600 mb-1">Specify Source Type <span class="text-red-500">*</span></label>
                                <input type="text"
                                        id="edit_doc_custom_source"
                                        class="w-full px-2 py-1 border rounded text-sm"
                                        placeholder="Enter custom source type...">
                            </div>
                        </div>
                        <!-- FOR REVISION/DELETION: Only show existing documents -->
                        <div id="edit_existing_doc_fields" style="display:none;">
                            <div class="mb-3">
                                <label class="block text-xs text-gray-600 mb-1">Select Existing Document <span class="text-red-500">*</span></label>
                                <select id="edit_existing_doc_select" class="w-full px-2 py-1 border rounded text-sm">
                                    <option value="">Loading...</option>
                                </select>
                                <small class="text-gray-500 block mt-1">
                                    Only documents from the selected office will appear
                                </small>
                            </div>
                            <!-- Show selected document details -->
                            <div id="edit_selected_doc_preview" class="bg-white p-3 rounded border border-gray-300" style="display:none;">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Selected Document:</h4>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-600">Code: </span>
                                        <span id="edit_preview_code" class="font-mono text-blue-600"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Source: </span>
                                        <span id="edit_preview_source"></span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-600">Title: </span>
                                        <span id="edit_preview_title"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Submit/Add Document to list button -->
                        <button type="button" id="edit_add_document_btn" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                            Add Document to List
                        </button>
                    </div>

                    <!-- Documents List Table -->
                    <div class="border rounded-md overflow-hidden">
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