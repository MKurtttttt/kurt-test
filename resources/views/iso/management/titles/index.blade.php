<x-app-layout>
<div class="min-h-screen py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Back -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('iso.management.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition-all">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
            
            <a href="{{ route('iso.document') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-all">
                <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                Switch to Document Handler
            </a>
        </div>

        <!-- Session Message -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-800 text-sm font-medium shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <span>ISO Document Titles Directory</span>
                        <span class="text-xs bg-indigo-100 text-indigo-800 font-semibold px-2 py-0.5 rounded-full">For Revisions</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update and stage new document titles for department and office documents prior to revision ticketing.
                    </p>
                </div>

                <!-- Search & Filter Form -->
                <form method="GET" action="{{ route('iso.management.titles.index') }}" class="w-full md:w-[700px] flex flex-wrap md:flex-nowrap gap-2">
                    <!-- Office Filter -->
                    <select name="office" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 bg-white max-w-[200px]">
                        <option value="">All Offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office }}" {{ ($officeFilter ?? '') == $office ? 'selected' : '' }}>
                                {{ $office }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Staged Filter -->
                    <select name="has_staged_title" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 bg-white">
                        <option value="">All Statuses</option>
                        <option value="yes" {{ ($revisionFilter ?? '') == 'yes' ? 'selected' : '' }}>Staged for Revision</option>
                        <option value="no" {{ ($revisionFilter ?? '') == 'no' ? 'selected' : '' }}>Unstaged</option>
                    </select>

                    <input type="text" 
                           name="query" 
                           value="{{ $searchQuery ?? '' }}"
                           placeholder="Search code or title..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600">
                    
                    <button type="submit" 
                            style="background-color: #4338ca !important; color: #ffffff !important;"
                            class="px-4 py-2 hover:opacity-90 rounded-lg text-sm font-semibold transition-all">
                        Search
                    </button>
                    @if(!empty($searchQuery) || !empty($officeFilter) || !empty($revisionFilter))
                        <a href="{{ route('iso.management.titles.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition-all flex items-center">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Document Code</th>
                            <th class="px-6 py-4">Department / Office</th>
                            <th class="px-6 py-4">Current Registered Title</th>
                            <th class="px-6 py-4">Title for Next Revision</th>
                            <th class="px-6 py-4 text-center">Revision</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-600">
                        @forelse($documents as $doc)
                            @php
                                $activeTicketDoc = ($activeTicketsByCode ?? collect())->get($doc->document_code);
                                $activeTicketInfo = ($activeTicketDoc && $activeTicketDoc->ticket)
                                    ? "Ticket #" . ($activeTicketDoc->ticket->ticket_number ?? $activeTicketDoc->ticket_id) . " (" . ucfirst(str_replace('_', ' ', $activeTicketDoc->ticket->status)) . ")"
                                    : "";
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $doc->document_code }}
                                    <div class="text-[11px] text-gray-500 font-normal uppercase">{{ $doc->source_type }}</div>
                                    @if(!empty($activeTicketInfo))
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded px-1.5 py-0.5" title="Ticket is currently in review">
                                                <svg class="w-3 h-3 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $activeTicketInfo }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-700">
                                    {{ $doc->originating_section }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $doc->document_title }}
                                </td>
                                <td class="px-6 py-4">
                                    @if(!empty($doc->revised_document_title) && $doc->revised_document_title !== $doc->document_title)
                                        <div class="font-semibold text-indigo-900">
                                            {{ $doc->revised_document_title }}
                                        </div>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5 mt-0.5">
                                            <svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            Staged for Revision
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Same as current</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 text-xs rounded-full font-semibold {{ $doc->current_revision > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700' }}">
                                        Rev {{ $doc->current_revision }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <button type="button"
                                            onclick="openUpdateTitleModal({{ $doc->id }}, '{{ addslashes($doc->document_code) }}', '{{ addslashes($doc->originating_section) }}', '{{ addslashes($doc->document_title) }}', '{{ addslashes($doc->revised_document_title ?? '') }}', '{{ addslashes($activeTicketInfo) }}')"
                                            style="background-color: #eef2ff !important; color: #4338ca !important; border-color: #c7d2fe !important;"
                                            class="inline-flex items-center px-3 py-1.5 hover:opacity-90 border rounded-lg text-xs font-semibold transition-all">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Update Title
                                        </button>

                                        @if(!empty($doc->revised_document_title))
                                            <form action="{{ route('iso.management.titles.clear', $doc->id) }}" method="POST" onsubmit="return confirm('Clear the staged revision title for {{ $doc->document_code }}? It will revert back to the current title.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Clear staged title">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                    No documents found matching the filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($documents->hasPages())
                <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- UPDATE DOCUMENT TITLE MODAL -->
<!-- ============================================ -->
<div id="update_title_modal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-800">Update Title for Revision</h3>
                    <p class="text-xs text-gray-500">Document Code: <span id="modal_doc_code" class="font-semibold text-gray-700"></span></p>
                </div>
            </div>
            <button type="button" onclick="closeUpdateTitleModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
        </div>

        <!-- Form -->
        <form id="update_title_form" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-4">
                <!-- Info banner -->
                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 text-xs text-indigo-800 leading-relaxed">
                    <strong>How revision works:</strong> Setting this title stages it for upcoming revision tickets. When an employee creates a revision ticket for this document, this updated title will be used. Upon registration, the original document will retain its old name as superseded, and the revised document (Rev 1+) will carry this updated title.
                </div>

                <!-- Active In-Flight Ticket Notice -->
                <div id="modal_ticket_notice" class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-800 leading-relaxed" style="display:none;">
                    <strong>Active Ticket In Progress:</strong> <span id="modal_ticket_info" class="font-semibold"></span>.
                    <div class="text-blue-600 mt-1">A ticket is currently in review for this document. If you update this title now, it will apply to subsequent revision workflows.</div>
                </div>

                <!-- Office Display -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Department / Office</label>
                    <div id="modal_office" class="text-sm font-medium text-gray-700 bg-gray-100 px-3 py-2 rounded-lg"></div>
                </div>

                <!-- Current Original Title -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Current Active Title</label>
                    <div id="modal_current_title" class="text-sm font-semibold text-gray-900 bg-gray-50 border border-gray-200 px-3 py-2 rounded-lg"></div>
                </div>

                <!-- Revised Title Input -->
                <div>
                    <label for="modal_revised_title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        New Title for Revision <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="modal_revised_title" 
                           name="revised_document_title" 
                           required 
                           placeholder="Enter updated document title..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600">
                    <small class="text-xs text-gray-500 mt-1 block">
                        Enter the new title that this document should have when revised.
                    </small>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeUpdateTitleModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" 
                        style="background-color: #4338ca !important; color: #ffffff !important;"
                        class="px-5 py-2 hover:opacity-90 rounded-lg text-sm font-semibold shadow-sm transition">
                    Save Revision Title
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const updateModal = document.getElementById('update_title_modal');
    const updateForm = document.getElementById('update_title_form');
    const modalDocCode = document.getElementById('modal_doc_code');
    const modalOffice = document.getElementById('modal_office');
    const modalCurrentTitle = document.getElementById('modal_current_title');
    const modalRevisedTitle = document.getElementById('modal_revised_title');
    const modalTicketNotice = document.getElementById('modal_ticket_notice');
    const modalTicketInfo = document.getElementById('modal_ticket_info');

    function openUpdateTitleModal(id, code, office, currentTitle, revisedTitle, activeTicketInfo = '') {
        updateForm.action = `/iso/management/document-titles/${id}`;
        modalDocCode.textContent = code;
        modalOffice.textContent = office;
        modalCurrentTitle.textContent = currentTitle;
        modalRevisedTitle.value = revisedTitle ? revisedTitle : currentTitle;

        if (activeTicketInfo && activeTicketInfo.trim() !== '') {
            modalTicketInfo.textContent = activeTicketInfo;
            modalTicketNotice.style.display = 'block';
        } else {
            modalTicketNotice.style.display = 'none';
        }

        updateModal.classList.remove('hidden');
        updateModal.classList.add('flex');
        modalRevisedTitle.focus();
        modalRevisedTitle.select();
    }

    function closeUpdateTitleModal() {
        updateModal.classList.add('hidden');
        updateModal.classList.remove('flex');
    }

    // Close on backdrop click
    updateModal.addEventListener('click', (e) => {
        if (e.target === updateModal) {
            closeUpdateTitleModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !updateModal.classList.contains('hidden')) {
            closeUpdateTitleModal();
        }
    });
</script>
</x-app-layout>
