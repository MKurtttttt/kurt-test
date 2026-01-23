<!-- Reset Ticketing system Modal -->
<div id="reset_modal" class="modal-overlay">
    <div class="modal-content max-w-md">
        <div class="modal-header bg-red-600 text-white">
            <h2 class="text-xl font-bold">DANGER: Reset Ticketing System</h2>
            <button id="close_reset_modal" class="text-white hover:text-gray-200 text-2xl">&times;</button>
        </div>

        <div class="modal-body">
            <!-- Warning message -->
            <div class="bg-red-50 border-l-4 border-red-600 p-4 mb-4">
                <p class="text-red-800 font-semibold mb-2">THIS ACTION CANNOT BE UNDONE!</p>
                <p class="text-red-700 text-sm">This will permanently:</p>
                <ul class="text-red-700 text-sm list-disc ml-5 mt-2">
                    <li>Delete ALL Tickets</li>
                    <li>Delete ALL Documents</li>
                    <li>Reset ticket numbers to start from #1</li>
                </ul>
            </div>
            <!-- Confirm Button -->
            <form id="reset_form" method="POST" action="{{ route('iso.idc.reset.system') }}">
                @csrf
                @method('DELETE')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type <span class="font-mono font-bold text-red-600">CONFIRM</span> to proceed:
                    </label>
                    <input type="text"
                            id="reset_confirmation"
                            class="w-full px-3 py-2 border-2 border-red-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 font-mono"
                            placeholder="CONFIRM"
                            autocomplete="off"
                            required>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button"
                            id="cancel_reset_btn"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            id="confirm_reset_btn"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-semibold opacity-50 cursor-not-allowed"
                            disabled>
                        Reset System
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>