<x-app-layout>
<div class="min-h-screen py-8 bg-gray-50/50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('iso.management.policies.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-red-900 transition-colors group">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Policies
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800">Add New Policy</h2>
                <p class="text-sm text-gray-500 mt-1">Register a new policy document for the Information Hub.</p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="p-4 mx-6 mt-6 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-800 text-sm font-medium">
                    <strong class="font-semibold block mb-1">Please fix the following:</strong>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('iso.management.policies.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Policy Title -->
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 mb-1.5">Policy Title <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}" 
                           required 
                           placeholder="e.g. Student Code of Conduct & Policy" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-950 focus:border-red-950 text-gray-800 shadow-sm">
                </div>

                <!-- URL -->
                <div>
                    <label for="url" class="block text-sm font-bold text-gray-700 mb-1.5">Document Link URL</label>
                    <input type="url" 
                           name="url" 
                           id="url" 
                           value="{{ old('url') }}" 
                           placeholder="e.g. https://hauph.sharepoint.com/.../document.pdf" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-950 focus:border-red-950 text-gray-800 shadow-sm">
                    <small class="text-gray-400 block mt-1.5">Provide the full URL link to the official policy document (optional).</small>
                </div>

                <!-- Category -->
                <div>
                    <label for="category-select" class="block text-sm font-bold text-gray-700 mb-1.5">Category</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select id="category-select" class="w-full sm:w-1/2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-950 focus:border-red-950 bg-white text-gray-700 shadow-sm">
                            <option value="">-- Custom Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <input type="text" 
                               name="category" 
                               id="category" 
                               value="{{ old('category') }}" 
                               placeholder="Type custom category name..." 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-950 focus:border-red-950 text-gray-800 shadow-sm">
                    </div>
                    <small class="text-gray-400 block mt-1.5">Select an existing category or enter a custom one (e.g. Student Affairs, Academic, Human Resources).</small>
                </div>
                
                <!-- Classifications Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Document Code -->
                    <div>
                        <label for="document_code" class="block text-sm font-bold text-gray-700 mb-1.5">Document Code</label>
                        <input type="text" 
                               name="document_code" 
                               id="document_code" 
                               value="{{ old('document_code') }}" 
                               placeholder="e.g. OIE-POL-001" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-950 focus:border-red-950 text-gray-800 shadow-sm">
                    </div>

                    <!-- Revision Count -->
                    <div>
                        <label for="revision_count" class="block text-sm font-bold text-gray-700 mb-1.5">Revision Count</label>
                        <input type="number" 
                               name="revision_count" 
                               id="revision_count" 
                               value="{{ old('revision_count', 0) }}" 
                               min="0"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-950 focus:border-red-950 text-gray-800 shadow-sm">
                    </div>

                    <!-- Effectivity Date -->
                    <div>
                        <label for="effectivity_date" class="block text-sm font-bold text-gray-700 mb-1.5">Effectivity Date</label>
                        <input type="date" 
                               name="effectivity_date" 
                               id="effectivity_date" 
                               value="{{ old('effectivity_date') }}" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-950 focus:border-red-950 text-gray-800 shadow-sm">
                    </div>

                    <!-- Policy Date/Year -->
                    <div>
                        <label for="policy_date" class="block text-sm font-bold text-gray-700 mb-1.5">Policy Date / Year</label>
                        <input type="text" 
                               name="policy_date" 
                               id="policy_date" 
                               value="{{ old('policy_date') }}" 
                               placeholder="e.g. 2017 or 2015-2016" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-950 focus:border-red-950 text-gray-800 shadow-sm">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-1.5">Brief Description</label>
                    <textarea name="description" 
                              id="description" 
                              rows="3" 
                              placeholder="Add a brief description of the policy's purpose..." 
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-950 focus:border-red-950 text-gray-800 shadow-sm">{{ old('description') }}</textarea>
                </div>

                <!-- Form Footer Buttons -->
                <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('iso.management.policies.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-bold hover:shadow-md transition-all shadow-sm" style="background: linear-gradient(135deg, #800000, #a02020);">
                        Create Policy
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const catSelect = document.getElementById('category-select');
        const catInput = document.getElementById('category');

        catSelect.addEventListener('change', function() {
            if (this.value) {
                catInput.value = this.value;
            } else {
                catInput.value = '';
            }
        });

        catInput.addEventListener('input', function() {
            const val = this.value;
            let matched = false;
            for(let i=0; i<catSelect.options.length; i++) {
                if(catSelect.options[i].value === val) {
                    catSelect.selectedIndex = i;
                    matched = true;
                    break;
                }
            }
            if(!matched) {
                catSelect.selectedIndex = 0;
            }
        });

        // Pre-fill category if present in the URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const categoryParam = urlParams.get('category');
        if (categoryParam) {
            catInput.value = categoryParam;
            
            // Sync with select options
            let matched = false;
            for(let i=0; i<catSelect.options.length; i++) {
                if(catSelect.options[i].value.toLowerCase() === categoryParam.toLowerCase()) {
                    catSelect.selectedIndex = i;
                    matched = true;
                    break;
                }
            }
            if(!matched) {
                catSelect.selectedIndex = 0;
            }
        }
    });
</script>
</x-app-layout>
