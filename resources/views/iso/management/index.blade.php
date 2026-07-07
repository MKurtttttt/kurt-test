<x-app-layout>
<!-- Management Dashboard -->
<div class="min-h-screen">
    <div class="container mx-auto">
        <div class="con-box">
            <!-- Success and Message -->
            @if (session('success') || session('msg'))
                <div class="flex items-center p-4 mb-4 text-emerald-800 border-t-4 border-emerald-300 bg-emerald-50 rounded-lg shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <div class="ml-3 text-sm font-medium">{{ session('success') ?? session('msg') }}</div>
                </div>
            @endif
            <!-- Error message -->
            @if (session('error'))
                <div class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-emerald-50 red-lg shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div class="ml-3 text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif
            <!-- Header -->
            <div class="w-[95%] px-4 flex my-4 items-center">
                <img src="{{ asset('images/icons/portal_nav/iso-title.png') }}" class="w-[100px] h-[100px] mr-4"/>
                <div class="w-full flex flex-col justify-center">
                    <h1 class="text-[1.5rem] font-bold leading-tight text-purple-700">Document Management System</h1>
                    <span class="text-gray-500 text-sm">Registered Documents Dashboard</span>
                </div>
                <div class="w-full flex gap-4">
                    <!-- Manage Departments -->
                    <a href="{{ route('iso.management.departments.index') }}" class="bg-red-900 hover:bg-red-800 text-white px-4 py-2 rounded-lg font-semibold flex items-center">
                        <span>Manage Departments</span>
                    </a>
                    <!-- Switch to IDC Admin Dashboard -->
                    <a href="{{ route('iso.idc.dashboard') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-semibold">
                        Switch to IDC Admin Dashboard
                    </a>
                    <!-- Switch to Document Handler Dashboard -->
                    <a href="{{ route('iso.document') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold">
                        Switch to Document Handler
                    </a>
                </div>
            </div>
            <hr class="w-full opacity-100">

            <!-- Summary Statistics Cards -->
            <div class="w-[95%] px-4 py-4">
                <div class="grid grid-cols-3 gap-6">
                    <!-- LEFT COLUMN: Total Documents & Status Breakdown -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-600 mb-3 px-1">Document Status</h4>
                        <!-- Status Breakdown -->
                        <div class="space-y-3">
                            <!-- Active Documents -->
                            <x-stat-card
                                title="Active Documents"
                                :value="number_format($activeDocuments)"
                                color="emerald"
                                >
                                    <x-slot:icon>
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </x-slot:icon>
                                    <x-slot:value>
                                        <span data-stat="activeDocuments">{{ number_format($activeDocuments) }}</span>
                                    </x-slot:value>
                            </x-stat-card>
                            <!-- Superseded Documents -->
                            <x-stat-card
                                title="Superseded Documents"
                                :value="number_format($supersededDocuments)"
                                color="yellow"
                                >
                                    <x-slot:icon>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                            <path d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375Z" />
                                            <path fill-rule="evenodd" d="m3.087 9 .54 9.176A3 3 0 0 0 6.62 21h10.757a3 3 0 0 0 2.995-2.824L20.913 9H3.087Zm6.163 3.75A.75.75 0 0 1 10 12h4a.75.75 0 0 1 0 1.5h-4a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                                        </svg>
                                    </x-slot:icon>
                                    <x-slot:value>
                                        <span data-stat="supersededDocuments">{{ number_format($supersededDocuments) }}</span>
                                    </x-slot:value>
                            </x-stat-card>
                            <!-- Deleted Documents -->
                            <x-stat-card
                                title="Deleted Documents"
                                :value="number_format($deletedDocuments)"
                                color="red"
                                >
                                    <x-slot:icon>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                            <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                                        </svg>
                                    </x-slot:icon>
                                    <x-slot:value>
                                        <span data-stat="deletedDocuments">{{ number_format($deletedDocuments) }}</span>
                                    </x-slot:value>
                            </x-stat-card>
                        </div>
                        <hr class="w-full opacity-100">
                        <!-- Total Document Card -->
                        <x-stat-card 
                            title="Total Registered Documents" 
                            :value="number_format($totalDocuments)"
                            color="blue"
                            class="border-2 border-blue-300 shadow-md"
                        >
                            <x-slot:icon>
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/>
                                    <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                </svg> 
                            </x-slot:icon>
                            <x-slot:value>
                                <span data-stat="totalDocuments">{{ number_format($totalDocuments) }}</span>
                            </x-slot:value>
                        </x-stat-card>
                    </div>
                    <!-- Middle Column: Document Type Breakdown -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-600 mb-3 px-1">Document Type</h4>
                        <div class="space-y-3">
                            <!-- Original Documents -->
                            <x-stat-card
                                title="Total Original Documents"
                                :value="number_format($originalDocuments)"
                                color="green"
                                >
                                    <x-slot:icon>
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                                        </svg>
                                    </x-slot:icon>
                                    <x-slot:value>
                                        <span data-stat="originalDocuments">{{ number_format($originalDocuments) }}</span>
                                    </x-slot:value>
                            </x-stat-card>
                            <!-- Revised Documents -->
                            <x-stat-card
                                title="Total Documents with Revision"
                                :value="number_format($revisedDocuments)"
                                color="yellow"
                                >
                                    <x-slot:icon>
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                        </svg>
                                    </x-slot:icon>
                                    <x-slot:value>
                                        <span data-stat="revisedDocuments">{{ number_format($revisedDocuments) }}</span>
                                    </x-slot:value>
                            </x-stat-card>
                        </div>
                    </div>
                    <!-- RIGHT COLUMN TODO: Add a way to manually include documents on management -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-600 mb-3 px-1">Action Panel</h4>
                        <!-- TODO: Insert buttons here for reading an excel sheet and potentially a modal to manually enter documents? -->
                        <!-- Import Button -->
                        <x-iso-button
                            label="Import from Excel"
                            color="purple"
                            onClick="openImportModal()"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </x-iso-button>
                        <!-- Export Button -->
                        <x-iso-button
                            label="Export Filtered Documents"
                            color="blue"
                            onClick="exportDocuments()"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </x-iso-button>
                        <!-- Download Template Button -->
                        <x-iso-button
                            label="Download Template"
                            color="green"
                            onClick="downloadTemplate()"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </x-iso-button>
                        <!-- Info Card -->
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex gap-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold mb-1">Quick Tips:</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Export respects current filters</li>
                                        <li>Download template for proper format</li>
                                        <li>Revisions require original document first</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Breakdown cards -->
            <div class="w[95%] grid grid-cols-2 gap-4 px-4 pb-4">
                @php
                    $typeConfig = [
                        'eoms'          => ['label' => 'EOMS Manual',           'color' => 'purple'],
                        'procedures'    => ['label' => 'Procedures',            'color' => 'blue'],
                        'forms'         => ['label' => 'Forms',                 'color' => 'green'],
                        'records'       => ['label' => 'Records Management',    'color' => 'yellow'],
                        'others'        => ['label' => 'Others',                'color' => 'gray'],
                    ]
                @endphp
                <!-- By Source Type -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h5 class="font-bold text-gray-700">By Source Type</h5>
                    </div>
                    <div class="p-4">
                        @forelse($byClassification as $item)
                            @php
                                // Fallback to 'others' just in case the type isn't on the list.
                                $config = $typeConfig[$item->source_type] ?? $typeConfig['others'];
                            @endphp
                            <div class="flex justify-between items-center py-2 px-2 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center">
                                    <div class="bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-600 rounded-full p-2 mr-3">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-gray-700">{{ $config['label'] }}</span>
                                </div>
                                <span class="bg-{{ $config['color'] }}-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ number_format($item->count) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-center py-6 italic">No documents registered yet</p>
                        @endforelse
                    </div>
                </div>
                <!-- By Department/Office -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h5 class="font-bold text-gray-700">Top Departments/Offices</h5>
                    </div>
                    <div class="p-4 flex flex-col gap-2 max-h-[300px] overflow-y-auto scrollbar-thin scrollbar-track-transparent">
                        <ul class="p-4 flex flex-col gap-2">
                        @forelse($byDepartment as $item)
                            <li class="flex justify-between gap-y-4 items-center py-2.5 px-4 bg-gray-50 rounded-xl hover:bg-white hover:shadow-md hover:ring-1 hover:ring-gray-200 transition-all duration-200 group">
                                <div class="flex items-center min-w-0">
                                    <div class="flex shrink-0 bg-green-100 text-green-600 rounded-lg p-2 mr-3 group-hover:bg-green-600 group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-gray-700 text-sm truncate">
                                        {{ $item->originating_section }}
                                    </span>
                                </div>

                                <span class="ml-4 bg-green-500 text-white px-2.5 py-0.5 rounded-full text-xs font-bold tabular-nums">
                                    {{ number_format($item->count) }}
                                </span>
                            </li>
                        @empty
                            <li class="flex flex-col items-center justify-center py-12 px-4 border-2 border-dashed border-gray-200 rounded-xl">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-500 text-sm font-medium">No documents registered yet</p>
                                <p class="text-gray-400 text-xs">New entries will appear here automatically.</p>
                            </li>
                        @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Filter Button -->
            <div class="w-[95%] px-4 text-center mb-4">
                <button type="button"
                    id="open_filter_modal"
                    class="bg-purple-500 hover:bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold text-lg shadow-sm transition">
                    Open Filters & View Documents
                </button>
            </div>

            <!-- Documents Table (hidden until filters are applied) -->
            <div class="w-full max-w-[98%] mx-auto px-4 pb-8" id="documents_table_section" style="display:none;">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="flex flex-col sm:flex-row justify-between items-center p-4 gap-4 border-b border-gray-100 bg-gray-50/50">
                        <div>
                            <h5 class="font-bold text-gray-80 flex-items-center gap-2">
                                <span class="w-2 h-5 bg-indigo-500 rounded-full"></span>
                                Filtered Documents
                            </h5>
                            <p class="text-xs text-gray-500 mt-0.5">Showing results based on your current selection</p>
                        </div>
                        <button type="button"
                                onclick="clearFilters()"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-indigo-500 outline-none">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Filters
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-200">
                                    <x-iso-table-header field="document_code" label="Document Code"/>
                                    <x-iso-table-header field="document_title" label="Document Title"/>
                                    <x-iso-table-header field="source_type" label="Source Type"/>
                                    <x-iso-table-header field="specific_type" label="Type"/>
                                    <x-iso-table-header field="originating_section" label="Dept/Office"/>
                                    <x-iso-table-header field="current_revision" label="Revision Count"/>
                                    <x-iso-table-header field="status" label="Status"/>
                                    <x-iso-table-header field="registered_at" label="Effectivity Date"/>
                                    <x-iso-table-header field="superseded_at" label="Superseded Date"/>
                                </tr>
                            </thead>
                            <tbody id="documents_table_body" class="divide-y divide-gray-100 text-sm text-gray-600">
                                <!-- Will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="no_results_message" class="flex flex-col items-center justify-center py-16 px-4" style="display:none;">
                        <div class="bg-gray-100 p-4 rounded-full mb-2">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-600 font-medium">No documents match your filters</p>
                        <p class="text-gray-400 text-sm mt-1">Try adjusting your search criteria or clearing all filters.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Filter Modal -->
@include('iso.management.partials.filter-modal')
<!-- Import Modal -->
@include('iso.management.partials.import-modal')
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
        max-width: 900px;
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
</style>

<script>
@php
    $dbDepartments = \App\Models\Departments::orderBy('code', 'asc')->get();
    $jsOptions = [];
    foreach ($dbDepartments as $d) {
        $parts = explode('-', $d->code);
        $cluster = strtolower($parts[0]);
        $formatted = "({$d->code}) {$d->dept}";
        $jsOptions[$cluster][] = $formatted;
    }
@endphp
const specificOfficeOptions = @json($jsOptions);
const filterModal = document.getElementById('filter_modal');
const openFilterBtn = document.getElementById('open_filter_modal');
const closeFilterBtn = document.getElementById('close_filter_modal');

// Open Modal
openFilterBtn.addEventListener('click', ()=>{
    filterModal.classList.add('active');
});
// Close modal
closeFilterBtn.addEventListener('click', ()=>{
    filterModal.classList.remove('active');
});
// Close modal when clicking outside
filterModal.addEventListener('click', (e)=>{
    if(e.target === filterModal){
        filterModal.classList.remove('active');
    }
});

// Department Search
const departmentSelect = document.getElementById('filter_department');
const officesContainer = document.getElementById('offices_container');
const officesCheckboxes = document.getElementById('offices_checkboxes');

departmentSelect.addEventListener('change', (e)=>{
    const selectedDept = e.target.value;

    officesCheckboxes.innerHTML = '';
    if(!selectedDept || selectedDept===''){
        officesContainer.style.display = 'none';
        return;
    }

    officesContainer.style.display = 'block';

    const offices = specificOfficeOptions[selectedDept];

    offices.forEach((office) =>{
        const label = document.createElement('label');
        label.className = 'flex items-center bg-white p-2 mb-1 rounded hover:bg-gray-100 cursor-pointer';

        label.innerHTML = `
            <input type="checkbox"
                name="originating_section[]"
                value="${office}"
                class="mr-2 text-purple-500 focus:ring-purple-500">
            <span class="text-sm">${office}</span>
        `;

        officesCheckboxes.appendChild(label);
    });
});

// Clear All filters Button
document.getElementById('clear_all_filters').addEventListener('click', ()=>{
    document.getElementById('filter_form').reset();
    officesContainer.style.display = 'none';
    officesCheckboxes.innerHTML = '';
    document.getElementById('search_input').value = '';
});

document.getElementById('search_input').addEventListener('input', (e)=>{
    toggleSearchBar(e.target);
})
// ==============================
// Search Field Helpers
// ==============================
function toggleSearchBar(input){
    const clearBtn = document.getElementById('clear_search_button');
    if(input.value.length > 0){
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }
}

function clearSearch(){
    const searchInput = document.getElementById('search_input');
    searchInput.value = '';
    document.getElementById('clear_search_button').classList.add('hidden');
}

// Apply Filters (AJAX)
document.getElementById('filter_form').addEventListener('submit', (e)=>{
    e.preventDefault();

    const formData = new FormData(e.target);

    // Convert to URL parameters
    const params = new URLSearchParams();

    formData.forEach((value, key) => {
        params.append(key, value);
    });

    fetch(`{{ route('iso.management.documents') }}?${params.toString()}`)
        .then(response => {
            if(!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data=>{
            displayDocuments(data.documents);
            updateStatCards(data.stats);
            filterModal.classList.remove('active');
            document.getElementById('documents_table_section').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load documents. Please try again.');
        });
});

function updateStatCards(stats){
    document.querySelector('[data-stat="totalDocuments"]').textContent = stats.totalDocuments.toLocaleString();
    document.querySelector('[data-stat="activeDocuments"]').textContent = stats.activeDocuments.toLocaleString();
    document.querySelector('[data-stat="supersededDocuments"]').textContent = stats.supersededDocuments.toLocaleString();
    document.querySelector('[data-stat="deletedDocuments"]').textContent = stats.deletedDocuments.toLocaleString();
    document.querySelector('[data-stat="originalDocuments"]').textContent = stats.originalDocuments.toLocaleString();
    document.querySelector('[data-stat="revisedDocuments"]').textContent = stats.revisedDocuments.toLocaleString();
}
// ==============================
// Table Sorting
// ==============================
// Store the current documents and sort table
let currentDocuments = [];
let currentSortColumn = [];
let currentSortDirection = [];

function displayDocuments(documents){
    const tbody = document.getElementById('documents_table_body');
    const noResults =document.getElementById('no_results_message');

    currentDocuments = documents;

    currentSortColumn = null;
    currentSortDirection = 'asc';
    resetSortIcons();

    // Clear exiting rows
    tbody.innerHTML = '';
    if(documents.length === 0){
        noResults.style.display = 'flex';
        return;
    }

    noResults.style.display = 'none';
    documents.forEach((doc) =>{
        const row = createDocumentRow(doc);
        tbody.appendChild(row);
    });
}

function sortTable(column){
    // If clicking same column, toggle direction
    // If clicking new column, reset to ascending
    if(currentSortColumn === column){
        currentSortDirection = currentSortDirection === 'asc' ? 'desc': 'asc';
    } else {
        currentSortColumn = column;
        currentSortDirection = 'asc';
    }

    // Update sort icons
    resetSortIcons();
    const icon = document.getElementById(`sort_icon_${column}`);
    if(icon){
        icon.classList.remove('opcacity-0', 'opacity-40', 'text-gray-400');
        icon.classList.add('opacity-100', 'text-purple-600');
        if(currentSortDirection === 'asc'){
            icon.style.transform = 'rotate(0deg)';
        } else {
            icon.style.transform = 'rotate(180deg)';
        }
    }

    // Sort the documents array
    const sorted = [...currentDocuments].sort((a,b) =>{
        let valA = a[column];
        let valB = b[column];

        // Handle null to put at the lowest of the table.
        if(valA === null || valA === undefined) return 1;
        if(valB === null || valB === undefined) return -1;

        // Handle numeric columns
        if(column === 'current_revision'){
            valA = parseInt(valA);
            valB = parseInt(valB);
            return currentSortDirection === 'asc' ? valA - valB : valB - valA;
        }

        // Handle date columns
        if(column === 'registered_at' || column === 'superseded_at'){
            valA = new Date(valA);
            valB = new Date(valB);
            return currentSortDirection === 'asc' ? valA - valB : valB - valA;
        }

        // Handle string columns (case-insensitive)
        valA = String(valA).toLowerCase();
        valB = String(valB).toLowerCase();

        if(valA < valB) return currentSortDirection === 'asc' ? -1 : 1;
        if(valB < valA) return currentSortDirection === 'asc' ? 1: -1;
        return 0;
    });

    // Re-render the table with sorted data
    const tbody = document.getElementById('documents_table_body');
    tbody.innerHTML = '';
    sorted.forEach((doc) => {
        const row = createDocumentRow(doc);
        tbody.appendChild(row);
    });
}

function createDocumentRow(doc){
    const tr = document.createElement('tr');
    tr.className = 'border-b bg-gray-50';

    const hasRevisions = doc.current_revision > 0;

    const registeredDate = new Date(doc.registered_at).toLocaleDateString();
    const supersededDate = doc.superseded_at ? new Date(doc.superseded_at).toLocaleDateString() : 'N/A';
    // Label Mapping
    const typeLabels = {
        'eoms' : 'EOMS Manual',
        'procedures' : 'Procedures',
        'forms' : 'Forms',
        'records' : 'Records',
        'others' : 'Others'
    }
    // Specific Type Mapping
    const specificTypeMapping = {
        '3.0' : '3.0 Records Rentention Schedule',
        '3'   : '3.0 Records Rentention Schedule',
        '4.0' : '4.0 Definition and Records Series Title',
        '4'   : '4.0 Definition and Records Series Title',
        '4.1' : '4.1 Interested Parties',
        '4.2' : '4.2 Risk Assessment',
        '7.4' : '7.4 Communication',
        '8.1' : '8.1 EOMS Plan'
    }
    // Status badge colors
    const statusColors = {
        'Active': 'bg-green-100 text-green-800',
        'Superseded': 'bg-yellow-100 text-yellow-800',
        'Deleted' : 'bg-red-100 text-red-800'
    };
    const formatLabel = typeLabels[doc.source_type] || doc.source_type;
    const formatSpecificType = doc.specific_type
                    ? (specificTypeMapping[doc.specific_type] || doc.specific_type)
                    : 'N/A';
    tr.innerHTML = `
        <td class="px-4 py-3 text-sm font-mono text-blue-600">${doc.document_code}</td>
        <td class="px-4 py-3 text-sm">${doc.document_title}</td>
        <td class="px-4 py-3 text-sm">${formatLabel}</td>
        <td class="px-4 py-3 text-sm">${formatSpecificType}</td>
        <td class="px-4 py-3 text-sm">${doc.originating_section}</td>
        <td class="px-4 py-3 text-sm">
            ${hasRevisions
            ? `<span class="text-purple-600 font-semibold">Rev ${doc.current_revision}</span>`
            : '<span class="text-gray-500">Original</span>'}
        </td>
        <td class="px-4 py-3 text-sm">
            <span class="inline-block px-2 py-1 rounded text-xs ${statusColors[doc.status] || 'bg-gray-100 text-gray-800'}">
                ${doc.status}
            </span>
        </td>
        <td class="px-4 py-3 text-sm text-gray-600">${registeredDate}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${supersededDate}</td>
    `;
    return tr;
}

function clearFilters(){
    document.getElementById('documents_table_section').style.display = 'none';
    // Reset form
    document.getElementById('filter_form').reset();
}

setTimeout(() => {
    const msg = document.getElementById('msg');
    const errorMsg = document.getElementById('error-msg');
    if(msg) msg.style.display = 'none';
    if(errorMsg) errorMsg.style.display = 'none';
}, 5000);

function resetSortIcons(){
    document.querySelectorAll('.sort-icon').forEach(icon => {
        icon.classList.remove('opacity-100', 'text-purple-600');
        icon.classList.add('opacity-0');
        icon.style.transform = 'rotate(0deg)';
    })
}

// ==============================
// Import Modal Functions
// ==============================
const importModal = document.getElementById('import_modal');
const closeImportBtn = document.getElementById('close_import_modal');
const cancelImportBtn = document.getElementById('cancel_import_btn');

function openImportModal(){
    importModal.classList.add('active');
    resetImportForm();
}

function closeImportModal(){
    importModal.classList.remove('active');
    resetImportForm();
}

// Handle close modal when clicking 'x' or Cancel button
closeImportBtn.addEventListener('click', closeImportModal);
cancelImportBtn.addEventListener('click', closeImportModal);

// Close modal when clicking outside
importModal.addEventListener('click', (e) =>{
    if(e.target === importModal){
        closeImportModal();
    }
});

function resetImportForm(){
    document.getElementById('import_form').reset();
    document.getElementById('file-name').textContent = '';
    document.getElementById('import-errors').classList.add('hidden');
    document.getElementById('import-success').classList.add('hidden')
}

function displayFileName(input){
    const fileName = input.files[0]?.name;
    if(fileName){
        document.getElementById('file-name').textContent = `Selected: ${fileName}`;
    }
}

// Handle Import form Submission
document.getElementById('import_form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = document.getElementById('import_submit_btn');
    const btnText = document.getElementById('import_btn_text');
    const btnIcon = document.getElementById('import_icon');
    const spinner = document.getElementById('import_spinner');

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    btnIcon.classList.add('hidden');
    spinner.classList.remove('hidden');
    btnText.textContent = 'Importing...';

    document.getElementById('import-errors').classList.add('hidden');
    document.getElementById('import-success').classList.add('hidden');

    try{
        const response = await fetch('/iso/management/import', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const contentType = response.headers.get("content-type");
        let data;
        if(contentType && contentType.indexOf("application/json") !== -1){
            data = await response.json();
        } else {
            throw new Error("Server returned a non-JSON response (likely a 500 error).")
        }

        // Success Message
        if(response.ok){
            document.getElementById('success-message').textContent = data.message;
            document.getElementById('import-success').classList.remove('hidden');

            setTimeout(() => {
                closeImportModal();
                window.location.reload();
            }, 2000);
        } else {
            // Hide success message if it was previously visible
            document.getElementById('import-success').classList.add('hidden');
            
            const errorList = document.getElementById('error-list');
            errorList.innerHTML = '';

            if(data.errors) {
                // Flatten the errors into one single array regardless of structure
                const allErrors = Object.values(data.errors).flat();
                allErrors.forEach(error => {
                    const li = document.createElement('li');
                    li.className = 'py-0.5' // add a little spacing
                    li.textContent = error;
                    errorList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = data.message || 'Import Failed';
                errorList.appendChild(li);
            }
            document.getElementById('import-errors').classList.remove('hidden');
        }
    } catch (error){
        console.error('Import Error: ', error);
        const errorList = document.getElementById('error-list');
        errorList.innerHTML = '<li>An unexpected error occured. Please try again.</li>';
        document.getElementById('import-errors').classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        btnIcon.classList.remove('hidden');
        spinner.classList.add('hidden');
        btnText.textContent = 'Import Documents';
    }
});

// Export function
async function exportDocuments(){
    const filterForm = document.getElementById('filter_form');
    const formData = new FormData(filterForm);

    // Check if any filters are applied
    let hasFilters = false;
    for (let pair of formData.entries()){
        if(pair[1]){
            hasFilters = true;
            break;
        }
    }
    // Warning if no filters applied
    // TODO: replace this with just: opacity-50 and cursor not allowed on the html tag instead
    if(!hasFilters){
        const confirmed = confirm('No filters applied. This will export ALL documents. Continue?');
        if(!confirmed) return;
    }

    // Convert FormData to URLSearchParams
    const params = new URLSearchParams();
    formData.forEach((value, key) => {
        params.append(key, value);
    });

    window.location.href = `/iso/management/export?${params.toString()}`;
}

// Download template function
function downloadTemplate(){
    window.location.href = '/iso/management/template';
}

// =================================
// Toggle All Checkboxes
// =================================
function toggleAllCheckboxes(groupName, button){
    let checkboxes;

    // Find checkboxes based on group name
    if(groupName === 'source_type'){
        checkboxes = document.querySelectorAll('input[name="source_type[]"]');
    } else if(groupName === 'status'){
        checkboxes = document.querySelectorAll('input[name="status[]"]');
    } else if(groupName === 'originating_section'){
        checkboxes = document.querySelectorAll('input[name="originating_section[]"]');
    }

    if(!checkboxes || checkboxes.length === 0) return;
    
    // Check if all are currently checked
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

    // Toggle: if all checked, uncheck all. Otherwise, check all.
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });

    button.textContent = allChecked ? 'Select All' : 'Deselect All';
}
</script>