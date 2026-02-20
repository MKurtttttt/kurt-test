<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="flex justify-center items-center w-full py-8">
            <div class="w-[95%] max-w-6xl bg-white rounded-xl shadow-lg">
                <!-- Header Section -->
                <div class="px-8 py-6 border-b border-gray-200">
                    <a href="{{ route('kpis.dashboard') }}" class="inline-flex gap-1 items-center bg-red-900 hover:bg-red-700 px-6 py-1 text-white rounded-xl mb-4">
                        <img src="{{ asset('images/icons/back.png') }}" class="w-[20px] h-[20px]" alt="">
                        <span>Back to KPI Library</span>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">Advanced KPI Search</h1>
                    <p class="text-gray-600 mt-1">Use the filters below to find specific KPIs</p>
                </div>

                <form action="{{ route('kpis.advanced-search') }}" method="GET">
                    <input type="hidden" name="source" value="advanced-search">
                    
                    <div class="px-8 py-6 space-y-8">
                        <!-- Basic Filters Section -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-1 h-6 bg-red-700 rounded"></div>
                                <h2 class="text-lg font-semibold text-gray-800">Basic Filters</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Category -->
                                <div class="space-y-2 md:col-span-2">
                                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                    <select 
                                        id="category" 
                                        name="category"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                    >
                                        <option value="">All Categories</option>
                                        <option value="institutional" {{ request('category') == 'institutional' ? 'selected' : '' }}>Institutional</option>
                                        <option value="departmental" {{ request('category') == 'departmental' ? 'selected' : '' }}>Departmental</option>
                                        <option value="personnel" {{ request('category') == 'personnel' ? 'selected' : '' }}>Personnel</option>
                                    </select>
                                </div>

                                <!-- Measure Code -->
                                <div class="space-y-2">
                                    <label for="code" class="block text-sm font-medium text-gray-700">Measure Code</label>
                                    <input 
                                        type="text" 
                                        id="code" 
                                        name="code"
                                        value="{{ request('code') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter measure code"
                                    >
                                </div>

                                <!-- Measure Owner -->
                                <div class="space-y-2">
                                    <label for="measure_owner" class="block text-sm font-medium text-gray-700">Measure Owner</label>
                                    <input 
                                        type="text" 
                                        id="measure_owner" 
                                        name="measure_owner"
                                        value="{{ request('measure_owner') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter measure owner"
                                    >
                                </div>

                                <!-- Perspective -->
                                <div class="space-y-2 md:col-span-2">
                                    <label for="perspective" class="block text-sm font-medium text-gray-700">Perspective</label>
                                    <input 
                                        type="text" 
                                        id="perspective" 
                                        name="perspective"
                                        value="{{ request('perspective') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter perspective"
                                    >
                                </div>

                                <!-- Theme -->
                                <div class="space-y-2 md:col-span-2">
                                    <label for="theme" class="block text-sm font-medium text-gray-700">Strategic Theme</label>
                                    <input 
                                        type="text" 
                                        id="theme" 
                                        name="theme"
                                        value="{{ request('theme') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter theme"
                                    >
                                </div>

                                <!-- Objective -->
                                <div class="space-y-2">
                                    <label for="objective" class="block text-sm font-medium text-gray-700">Objective</label>
                                    <input 
                                        type="text" 
                                        id="objective" 
                                        name="objective"
                                        value="{{ request('objective') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter objective"
                                    >
                                </div>

                                <!-- Objective Owner -->
                                <div class="space-y-2">
                                    <label for="objective_owner" class="block text-sm font-medium text-gray-700">Objective Owner</label>
                                    <input 
                                        type="text" 
                                        id="objective_owner" 
                                        name="objective_owner"
                                        value="{{ request('objective_owner') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter objective owner"
                                    >
                                </div>

                                <!-- Measure Type -->
                                <div class="space-y-2 md:col-span-2">
                                    <label for="measure_type" class="block text-sm font-medium text-gray-700">Measure Type</label>
                                    <input 
                                        type="text" 
                                        id="measure_type" 
                                        name="measure_type"
                                        value="{{ request('measure_type') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter measure type"
                                    >
                                </div>

                                <!-- Collection Frequency -->
                                <div class="space-y-2">
                                    <label for="collection_frequency" class="block text-sm font-medium text-gray-700">Collection Frequency</label>
                                    <input 
                                        type="text" 
                                        id="collection_frequency" 
                                        name="collection_frequency"
                                        value="{{ request('collection_frequency') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter collection frequency"
                                    >
                                </div>

                                <!-- Reporting Frequency -->
                                <div class="space-y-2">
                                    <label for="reporting_frequency" class="block text-sm font-medium text-gray-700">Reporting Frequency</label>
                                    <input 
                                        type="text" 
                                        id="reporting_frequency" 
                                        name="reporting_frequency"
                                        value="{{ request('reporting_frequency') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter reporting frequency"
                                    >
                                </div>

                                <!-- Verified by -->
                                <div class="space-y-2">
                                    <label for="verified_by" class="block text-sm font-medium text-gray-700">Verified By</label>
                                    <input 
                                        type="text" 
                                        id="verified_by" 
                                        name="verified_by"
                                        value="{{ request('verified_by') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter verified by"
                                    >
                                </div>

                                <!-- Validated by -->
                                <div class="space-y-2">
                                    <label for="validated_by" class="block text-sm font-medium text-gray-700">Validated By</label>
                                    <input 
                                        type="text" 
                                        id="validated_by" 
                                        name="validated_by"
                                        value="{{ request('validated_by') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter validated by"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Segmentations Section -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-1 h-6 bg-red-700 rounded"></div>
                                <h2 class="text-lg font-semibold text-gray-800">Segmentations</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Segmentation -->
                                <div class="space-y-2">
                                    <label for="segmentation" class="block text-sm font-medium text-gray-700">Segmentation</label>
                                    <input 
                                        type="text" 
                                        id="segmentation" 
                                        name="segmentation"
                                        value="{{ request('segmentation') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter segmentation"
                                    >
                                </div>

                                <!-- Segmentation Code -->
                                <div class="space-y-2">
                                    <label for="seg_code" class="block text-sm font-medium text-gray-700">Code</label>
                                    <input 
                                        type="text" 
                                        id="seg_code" 
                                        name="seg_code"
                                        value="{{ request('seg_code') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter code"
                                    >
                                </div>

                                <!-- Segmentation Owner -->
                                <div class="space-y-2">
                                    <label for="seg_owner" class="block text-sm font-medium text-gray-700">Owner</label>
                                    <input 
                                        type="text" 
                                        id="seg_owner" 
                                        name="seg_owner"
                                        value="{{ request('seg_owner') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter owner"
                                    >
                                </div>

                                <!-- Target Level -->
                                <div class="space-y-2">
                                    <label for="target_level" class="block text-sm font-medium text-gray-700">Target Level</label>
                                    <input 
                                        type="text" 
                                        id="target_level" 
                                        name="target_level"
                                        value="{{ request('target_level') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter target level"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Accreditations Section -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-1 h-6 bg-red-700 rounded"></div>
                                <h2 class="text-lg font-semibold text-gray-800">Accreditations</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Accrediting Body ID -->
                                <div class="space-y-2">
                                    <label for="accrediting_body_id" class="block text-sm font-medium text-gray-700">Accrediting Body ID</label>
                                    <input 
                                        type="text" 
                                        id="accrediting_body_id" 
                                        name="accrediting_body_id"
                                        value="{{ request('accrediting_body_id') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter accrediting body ID"
                                    >
                                </div>

                                <!-- Accrediting Body Name -->
                                <div class="space-y-2">
                                    <label for="accrediting_body_name" class="block text-sm font-medium text-gray-700">Accrediting Body Name</label>
                                    <input 
                                        type="text" 
                                        id="accrediting_body_name" 
                                        name="accrediting_body_name"
                                        value="{{ request('accrediting_body_name') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter accrediting body name"
                                    >
                                </div>

                                <!-- Program Unit -->
                                <div class="space-y-2 md:col-span-2">
                                    <label for="program_unit" class="block text-sm font-medium text-gray-700">Program Unit</label>
                                    <input 
                                        type="text" 
                                        id="program_unit" 
                                        name="program_unit"
                                        value="{{ request('program_unit') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter program unit"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Dimensions Section -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-1 h-6 bg-red-700 rounded"></div>
                                <h2 class="text-lg font-semibold text-gray-800">Dimensions</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Dimensions -->
                                <div class="space-y-2">
                                    <label for="dimensions" class="block text-sm font-medium text-gray-700">Dimensions</label>
                                    <input 
                                        type="text" 
                                        id="dimensions" 
                                        name="dimensions"
                                        value="{{ request('dimensions') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter dimensions"
                                    >
                                </div>

                                <!-- Descriptions -->
                                <div class="space-y-2">
                                    <label for="dim_descriptions" class="block text-sm font-medium text-gray-700">Descriptions</label>
                                    <input 
                                        type="text" 
                                        id="dim_descriptions" 
                                        name="dim_descriptions"
                                        value="{{ request('dim_descriptions') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                                        placeholder="Enter descriptions"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-6 border-t border-gray-200 rounded-b-xl flex gap-3 justify-end">
                        <a href="{{ route('kpis.advanced-search') }}" class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors duration-200 inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Filters
                        </a>
                        <button type="submit" class="px-8 py-2.5 bg-red-700 text-white font-medium rounded-lg hover:bg-red-600 transition-colors duration-200 inline-flex items-center gap-2 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search KPIs
                        </button>
                    </div>
                </form> 
            </div>
        </div>
    </div>
    <x-back-top-button />
</x-app-layout>