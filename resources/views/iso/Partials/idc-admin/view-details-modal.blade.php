{{-- Combined View Details + Change Status Modal --}}
<div id="details_modal" class="modal-overlay">
    <div class="modal-content">

        {{-- ── Modal Header ── --}}
        <div class="modal-header">
            {{-- Left: document icon + ticket title/subtitle --}}
            <div class="flex items-start gap-3">
                <div class="modal-icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <div>
                    <h2 class="modal-title">Ticket: <span id="detail_ticket_number"></span></h2>
                    <p class="modal-subtitle">IDC ADMIN &mdash; DOCUMENT MANAGEMENT</p>
                </div>
            </div>

            {{-- Right: close button (isolated, clean) --}}
            <button id="close_details_modal" class="modal-close-btn" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        {{-- ── Action Bar (Edit / Delete Ticket, hidden for registered) ── --}}
        <div id="modal_action_bar" class="modal-action-bar gap-2" style="display:none;">
            <button id="edit_ticket_btn" type="button" class="edit-ticket-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                <span>Edit Ticket Details</span>
            </button>
            <button id="delete_ticket_btn" type="button" class="delete-ticket-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6M14 11v6"/>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
                Delete Ticket
            </button>
        </div>

        {{-- ── Modal Body ── --}}
        <div class="modal-body">

            {{-- ────────────────────────────────────────── --}}
            {{-- VIEW MODE CONTAINER                         --}}
            {{-- ────────────────────────────────────────── --}}
            <div id="ticket_view_mode_container">
                {{-- Ticket Information --}}
                <div class="info-card mb-4">
                    <h3 class="section-label mb-3">Ticket Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="field-label">Originating Section</p>
                            <p class="field-value" id="detail_section"></p>
                        </div>
                        <div>
                            <p class="field-label">Status</p>
                            <p class="field-value" id="detail_status"></p>
                        </div>
                        <div>
                            <p class="field-label">Created</p>
                            <p class="field-value" id="detail_created"></p>
                        </div>
                        <div>
                            <p class="field-label">Created By</p>
                            <p class="field-value" id="detail_creator"></p>
                        </div>
                    </div>
                </div>

                {{-- Sharepoint Link --}}
                <div class="info-card info-card--blue mb-4">
                    <p class="field-label mb-1">Sharepoint Folder</p>
                    <a id="detail_sharepoint" href="#" target="_blank"
                        class="text-blue-600 hover:text-blue-800 font-medium break-all text-sm"></a>
                </div>

                {{-- Documents List --}}
                <div class="mb-4">
                    <h3 class="section-label mb-2">Documents in this Ticket</h3>
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Code</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Title</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Modification</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Source</th>
                                    <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="detail_documents_list">
                                {{-- Populated via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Message to IDC --}}
                <div class="info-card mb-4">
                    <p class="field-label mb-1">Message to IDC</p>
                    <p class="text-gray-800 whitespace-pre-wrap text-sm leading-relaxed" id="detail_message"></p>
                </div>

                {{-- Change Status Section (hidden for registered tickets) --}}
                <div id="change_status_section" class="info-card info-card--purple" style="display:none;">
                    <h3 class="section-label text-purple-700 mb-3">Change Ticket Status</h3>
                    <form id="status_form" method="POST">
                        @csrf
                        @method('PATCH')
                        <p class="text-sm text-gray-600 mb-3">
                            Current Status:
                            <span id="current_status" class="font-semibold text-purple-700"></span>
                        </p>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                New Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="new_status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400"
                                required>
                                <option value="">Select new status...</option>
                                <option value="submitted_to_idc">Submitted to IDC</option>
                                <option value="with_qmr">Send to QMR</option>
                            </select>
                            <p class="text-xs text-orange-500 mt-2">
                                &#9888; Warning: This will change ALL document statuses to match the ticket status.
                            </p>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold transition">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>{{-- end ticket_view_mode_container --}}

            {{-- ────────────────────────────────────────── --}}
            {{-- EDIT MODE CONTAINER                         --}}
            {{-- ────────────────────────────────────────── --}}
            <div id="ticket_edit_mode_container" style="display:none;">
                {{-- Notice Banner --}}
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-start gap-3">
                    <div class="text-blue-600 mt-0.5 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                    </div>
                    <div class="text-xs text-blue-900 leading-relaxed">
                        <span class="font-semibold block text-sm mb-0.5">Editing Ticket Details</span>
                        Update the originating department/office, document codes, titles, nature of modification, or source types before approving. You can also add or remove documents.
                    </div>
                </div>

                {{-- Originating Section (Cluster + Office Dropdowns) --}}
                <div class="info-card mb-4">
                    <h3 class="section-label mb-2 text-gray-800">Originating Section</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                Cluster / Department <span class="text-red-500">*</span>
                            </label>
                            <select id="edit_ticket_cluster" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Select Cluster/Department...</option>
                                <option value="aac">Academic Affairs Cluster (AAC)</option>
                                <option value="aie">Institute for Academic Innovation & Entrepreneurship (AIE)</option>
                                <option value="icfsi">Institute for Catholic Formation & Social Integration (ICFSI)</option>
                                <option value="csd">Campus Services & Development Office (CSD)</option>
                                <option value="eac">External Affairs Cluster (EAC)</option>
                                <option value="frm">Finance & Resources Management Services (FRM)</option>
                                <option value="hro">Human Resource Management Office (HRO)</option>
                                <option value="iat">Internal Audit Team (IAT)</option>
                                <option value="oie">Office of Institutional Effectiveness (OIE)</option>
                                <option value="oop">Office of the President (OOP)</option>
                                <option value="rss">Records Services & Affairs (RSS)</option>
                                <option value="ssa">Student Services & Affairs (SSA)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                Specific Office <span class="text-red-500">*</span>
                            </label>
                            <select id="edit_ticket_office" name="originating_section" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition opacity-50 cursor-not-allowed" required disabled>
                                <option value="">Select Cluster first...</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Editable Documents List --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="section-label">Documents in this Ticket (<span id="edit_doc_count">0</span>)</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Edit codes, titles, modification nature, source types, or add/remove items</p>
                        </div>
                        <button type="button" id="add_doc_to_edit_btn" onclick="addDocToEditList()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Document
                        </button>
                    </div>

                    <div id="edit_documents_list_container" class="space-y-3">
                        {{-- Populated via JS --}}
                    </div>
                </div>

                {{-- Edit Mode Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-200">
                    <button type="button" id="cancel_edit_ticket_btn"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" id="save_edit_ticket_btn"
                        class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition flex items-center gap-2 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>{{-- end ticket_edit_mode_container --}}

        </div>{{-- end modal-body --}}
    </div>
</div>