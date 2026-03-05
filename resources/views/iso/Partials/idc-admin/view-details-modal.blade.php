<!-- View Details modal -->
<div id="details_modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="text-xl font-bold">Ticket Details: <span id="detail_ticket_number"></span></h2>
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
                    <div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Code</th>
                            <th class="px-3 py-2 text-left">Title</th>
                            <th class="px-3 py-2 text-left">Modification Type</th>
                            <th class="px-3 py-2 text-left">Source</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="detail_documents_list">
                        <!-- Documents will be populated here via JavaScript -->
                    </tbody>
                </table>
            </div>
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