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
                </select>
                <p class="text-sm text-orange-600 mt-2">
                    ⚠️ Warning: This will change ALL document statuses to match the ticket status.
                </p>
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