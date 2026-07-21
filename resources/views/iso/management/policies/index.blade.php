<x-app-layout>
<div class="min-h-screen py-8 bg-gray-50/50 pb-[450px]">
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Back -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('information-hub.dashboard') }}" 
               class="inline-flex items-center font-bold text-[#70121D] hover:underline transition-all">
                <span class="mr-1">&larr;</span> Back to Information Hub
            </a>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('iso.management.policy-categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-white text-[#70121D] border border-[#70121D] hover:bg-gray-50 transition-all duration-200 focus:outline-none shadow-sm">
                    <i class="bi bi-folder-fill mr-2 leading-none"></i>
                    <span>Manage Categories</span>
                </a>
                
                <a href="{{ route('iso.management.policies.create') }}" 
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold text-white hover:scale-[1.01] hover:shadow transition-all duration-200 focus:outline-none" 
                   style="background-color: #70121D;">
                    <i class="bi bi-plus-lg mr-2 leading-none"></i>
                    <span>Add Policy Document</span>
                </a>
            </div>
        </div>

        <!-- Session Message -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-sm font-medium shadow-sm flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white shadow border border-gray-200 transition-all duration-300 overflow-hidden" style="border-radius: 16px;">
            <!-- Header Block -->
            <div class="p-6 text-white" style="background-color: #70121D; border-bottom: 4px solid #c5a059; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h2 class="text-2xl font-bold tracking-tight">Policies Directory</h2>
                <p class="text-sm mt-1 text-white/80">Manage institutional policy links, subcategories, and search references.</p>
            </div>

            <!-- Search & Filter Form Bar -->
            <div class="bg-gray-50/50 p-5 border-b border-gray-100 flex justify-center bg-gradient-to-b from-gray-50/50 to-white">
                <form method="GET" action="{{ route('iso.management.policies.index') }}" class="w-full">
                    <!-- Main Search Row -->
                    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                        <!-- Search Text Input -->
                        <div class="relative flex-1" style="height: 42px;">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="bi bi-search text-sm"></i>
                            </div>
                            <input type="text" 
                                   name="query" 
                                   value="{{ $query ?? '' }}"
                                   placeholder="Search code, title, description..." 
                                   class="w-full h-full pl-10 pr-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-[#70121D] focus:ring-4 focus:ring-[#70121D]/5 text-gray-700 bg-gray-50/50 transition-all duration-200">
                        </div>

                        <!-- Actions Row -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <!-- Search Button -->
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-5 py-2 text-white font-bold rounded-xl text-sm transition-all hover:opacity-90 active:scale-95 whitespace-nowrap shadow-sm" 
                                    style="background-color: #70121D; border: 1px solid rgba(197, 160, 89, 0.2); height: 42px;">
                                Search
                            </button>

                            <!-- Filters Toggle Button -->
                            <button type="button" 
                                    id="filter-toggle-btn"
                                    onclick="toggleAdvancedFilters()"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:text-[#70121D] hover:border-red-200 transition-all text-sm font-semibold shadow-sm duration-200 {{ (!empty($category) || !empty($revisionFilter) || !empty($policyYear)) ? 'bg-[#70121D]/5 text-[#70121D] border-[#70121D]/30' : '' }}"
                                    style="height: 42px;">
                                <i class="bi bi-sliders text-base leading-none"></i>
                                <span>Filters</span>
                            </button>
                            
                            <!-- Clear Button if filters active -->
                            @if(!empty($query) || !empty($category) || !empty($revisionFilter) || !empty($policyYear))
                                <a href="{{ route('iso.management.policies.index') }}" 
                                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold border border-gray-200 transition-all shadow-sm" 
                                   style="height: 42px;"
                                   title="Clear Filters">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Collapsible Advanced Filters Section -->
                    <div id="advanced-filters-panel" 
                         class="{{ (!empty($category) || !empty($revisionFilter) || !empty($policyYear)) ? '' : 'hidden' }} mt-4 p-5 bg-gray-50/50 rounded-2xl border border-gray-200/50">
                        <div class="advanced-filters-grid">
                            <!-- Category Filter -->
                            <div>
                                <label class="block text-xs font-bold text-[#70121D] mb-1.5 uppercase tracking-wider">Category</label>
                                <input type="hidden" name="category" id="filter-category" value="{{ $categoryId ?? '' }}">
                                <div class="relative custom-dropdown" id="dropdown-category">
                                    <button type="button" class="dropdown-trigger w-full px-4 py-2 border border-gray-200 rounded-xl text-sm bg-white text-gray-700 shadow-sm hover:border-gray-300 transition-all flex items-center justify-between" style="height: 42px;">
                                        <span class="selected-text font-semibold text-gray-800">
                                            @php
                                                $selectedCat = $categories->firstWhere('id', $categoryId);
                                            @endphp
                                            {{ $selectedCat ? $selectedCat->name : 'All Categories' }}
                                        </span>
                                        <i class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                                    </button>
                                    <div class="dropdown-menu absolute top-full left-0 w-full mt-1.5 bg-white border border-gray-250 rounded-xl shadow-xl z-[100] hidden max-h-60 overflow-y-auto py-1.5 space-y-0.5 animate-fade-in">
                                        <div class="dropdown-option px-4 py-2.5 text-sm text-gray-800 hover:bg-red-50 hover:text-[#70121D] cursor-pointer transition-colors {{ empty($categoryId) ? 'font-semibold bg-red-50/40 text-[#70121D]' : '' }}" data-value="">All Categories</div>
                                        @foreach($categories as $cat)
                                            <div class="dropdown-option px-4 py-2.5 text-sm text-gray-800 hover:bg-red-50 hover:text-[#70121D] cursor-pointer transition-colors {{ ($categoryId ?? '') == $cat->id ? 'font-semibold bg-red-50/40 text-[#70121D]' : '' }}" data-value="{{ $cat->id }}">{{ $cat->name }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Revision Status Filter -->
                            <div>
                                <label class="block text-xs font-bold text-[#70121D] mb-1.5 uppercase tracking-wider">Revision Status</label>
                                <input type="hidden" name="revision_filter" id="filter-revision" value="{{ $revisionFilter ?? '' }}">
                                <div class="relative custom-dropdown" id="dropdown-revision">
                                    <button type="button" class="dropdown-trigger w-full px-4 py-2 border border-gray-200 rounded-xl text-sm bg-white text-gray-700 shadow-sm hover:border-gray-300 transition-all flex items-center justify-between" style="height: 42px;">
                                        <span class="selected-text font-semibold text-gray-800">
                                            @if(($revisionFilter ?? '') == 'original') Original Only @elseif(($revisionFilter ?? '') == 'revised') Revised Only @else All Revisions @endif
                                        </span>
                                        <i class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                                    </button>
                                    <div class="dropdown-menu absolute top-full left-0 w-full mt-1.5 bg-white border border-gray-250 rounded-xl shadow-xl z-[100] hidden max-h-60 overflow-y-auto py-1.5 space-y-0.5 animate-fade-in">
                                        <div class="dropdown-option px-4 py-2.5 text-sm text-gray-800 hover:bg-red-50 hover:text-[#70121D] cursor-pointer transition-colors {{ empty($revisionFilter) ? 'font-semibold bg-red-50/40 text-[#70121D]' : '' }}" data-value="">All Revisions</div>
                                        <div class="dropdown-option px-4 py-2.5 text-sm text-gray-800 hover:bg-red-50 hover:text-[#70121D] cursor-pointer transition-colors {{ ($revisionFilter ?? '') == 'original' ? 'font-semibold bg-red-50/40 text-[#70121D]' : '' }}" data-value="original">Original Only</div>
                                        <div class="dropdown-option px-4 py-2.5 text-sm text-gray-800 hover:bg-red-50 hover:text-[#70121D] cursor-pointer transition-colors {{ ($revisionFilter ?? '') == 'revised' ? 'font-semibold bg-red-50/40 text-[#70121D]' : '' }}" data-value="revised">Revised Only</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Policy Year Filter -->
                            <div>
                                <label class="block text-xs font-bold text-[#70121D] mb-1.5 uppercase tracking-wider">Policy Date / Year</label>
                                <input type="hidden" name="policy_year" id="filter-year" value="{{ $policyYear ?? '' }}">
                                <div class="relative custom-dropdown" id="dropdown-year">
                                    <button type="button" class="dropdown-trigger w-full px-4 py-2 border border-gray-200 rounded-xl text-sm bg-white text-gray-700 shadow-sm hover:border-gray-300 transition-all flex items-center justify-between" style="height: 42px;">
                                        <span class="selected-text font-semibold text-gray-800">{{ ($policyYear ?? '') ?: 'All Years' }}</span>
                                        <i class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                                    </button>
                                    <div class="dropdown-menu absolute top-full left-0 w-full mt-1.5 bg-white border border-gray-250 rounded-xl shadow-xl z-[100] hidden max-h-60 overflow-y-auto py-1.5 space-y-0.5 animate-fade-in">
                                        <div class="dropdown-option px-4 py-2.5 text-sm text-gray-800 hover:bg-red-50 hover:text-[#70121D] cursor-pointer transition-colors {{ empty($policyYear) ? 'font-semibold bg-red-50/40 text-[#70121D]' : '' }}" data-value="">All Years</div>
                                        @foreach($policyYears as $year)
                                            <div class="dropdown-option px-4 py-2.5 text-sm text-gray-800 hover:bg-red-50 hover:text-[#70121D] cursor-pointer transition-colors {{ ($policyYear ?? '') == $year ? 'font-semibold bg-red-50/40 text-[#70121D]' : '' }}" data-value="{{ $year }}">{{ $year }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider bg-white">
                            <th class="px-6 py-4 whitespace-nowrap w-[12%]">Document Code</th>
                            <th class="px-6 py-4 w-[32%]">Title & Details</th>
                            <th class="px-6 py-4 whitespace-nowrap w-[12%]">Category</th>
                            <th class="px-6 py-4 text-center whitespace-nowrap w-[8%]">Revision</th>
                            <th class="px-6 py-4 whitespace-nowrap w-[12%]">Effectivity Date</th>
                            <th class="px-6 py-4 text-center whitespace-nowrap w-[8%]">Policy Date</th>
                            <th class="px-6 py-4 whitespace-nowrap w-[10%]">Document Link</th>
                            <th class="px-6 py-4 text-right whitespace-nowrap w-[6%]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-600 bg-white">
                        @forelse($policies as $policy)
                            <tr class="policy-row transition-colors">
                                <!-- Document Code -->
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-700 text-xs tracking-wider uppercase">
                                    {{ $policy->document_code ?: 'N/A' }}
                                </td>
                                <!-- Title & Details -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-base leading-tight hover:text-[#70121D] transition-colors duration-200">{{ $policy->title }}</div>
                                    @if($policy->description)
                                        <div class="text-xs text-gray-500 mt-1 max-w-md line-clamp-2 leading-relaxed">{{ $policy->description }}</div>
                                    @endif
                                </td>
                                <!-- Category -->
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-750 text-xs">
                                    {{ $policy->category ? $policy->category->name : 'Unassigned' }}
                                </td>
                                <!-- Revision Count -->
                                <td class="px-6 py-4 text-center font-medium whitespace-nowrap text-gray-700">
                                    @if(empty($policy->revision_count) || $policy->revision_count == 0)
                                        <span class="italic text-gray-500 font-medium">Original</span>
                                    @else
                                        <span class="font-semibold text-gray-900">Rev {{ $policy->revision_count }}</span>
                                    @endif
                                </td>
                                <!-- Effectivity Date -->
                                <td class="px-6 py-4 font-medium text-gray-600 whitespace-nowrap">
                                    {{ $policy->effectivity_date ? \Carbon\Carbon::parse($policy->effectivity_date)->format('M j, Y') : 'N/A' }}
                                </td>
                                <!-- Policy Date -->
                                <td class="px-6 py-4 text-center font-medium text-gray-600 whitespace-nowrap">
                                    {{ $policy->policy_date ?: 'N/A' }}
                                </td>
                                <!-- Document Link -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ $policy->url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 bg-[#fff0f0] text-[#70121D] hover:bg-[#70121D] hover:text-white transition-all text-xs font-bold shadow-sm group">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span>Open Link</span>
                                        <i class="bi bi-arrow-up-right text-[10px] transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                                    </a>
                                </td>
                                <!-- Actions -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end">
                                        <a href="{{ route('iso.management.policies.edit', $policy->id) }}" class="inline-flex items-center justify-center p-2 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <span class="text-sm font-semibold">No policy documents found</span>
                                        <span class="text-xs text-gray-400 mt-1">Try refining your search or add a new policy to start.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($policies->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $policies->appends([
                        'query' => $query,
                        'category' => $category,
                        'revision_filter' => $revisionFilter ?? '',
                        'policy_year' => $policyYear ?? ''
                    ])->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    
    /* Elegant Row Hover Tint */
    .policy-row {
        transition: background-color 0.2s ease, border-left-color 0.2s ease;
        border-left: 3px solid transparent;
    }
    .policy-row:hover {
        background-color: rgba(112, 18, 29, 0.015) !important;
        border-left-color: #70121D;
    }
    .policy-row td {
        vertical-align: middle;
    }

    /* Custom layout styling to guarantee horizontal alignment */
    .compact-filter-bar {
        display: flex;
        gap: 12px;
        align-items: center;
        width: 100%;
    }
    .search-input-wrapper {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .search-icon-wrapper {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        pointer-events: none;
        color: #9ca3af;
    }
    .filter-toggle-btn {
        width: 130px;
        flex-shrink: 0;
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }
    .search-submit-btn {
        width: 130px;
        flex-shrink: 0;
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }
    .clear-filter-btn {
        width: 90px;
        flex-shrink: 0;
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }
    .advanced-filters-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    @media (max-width: 768px) {
        .compact-filter-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .search-input-wrapper {
            width: 100%;
        }
        .compact-filter-bar > div:last-child {
            display: flex;
            width: 100%;
            gap: 8px;
        }
        .filter-toggle-btn, 
        .search-submit-btn,
        .clear-filter-btn {
            flex: 1;
            width: auto;
        }
        .advanced-filters-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }
</style>

<script>
    function toggleAdvancedFilters() {
        const panel = document.getElementById('advanced-filters-panel');
        const btn = document.getElementById('filter-toggle-btn');
        if (panel) {
            panel.classList.toggle('hidden');
            if (btn) {
                btn.classList.toggle('bg-[#70121D]/5');
                btn.classList.toggle('text-[#70121D]');
                btn.classList.toggle('border-[#70121D]/30');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Toggle custom dropdowns
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            const trigger = dropdown.querySelector('.dropdown-trigger');
            const menu = dropdown.querySelector('.dropdown-menu');
            const icon = trigger.querySelector('.bi-chevron-down');

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                
                // Close all other dropdowns
                document.querySelectorAll('.custom-dropdown').forEach(other => {
                    if (other !== dropdown) {
                        other.querySelector('.dropdown-menu').classList.add('hidden');
                        other.querySelector('.bi-chevron-down').classList.remove('rotate-180');
                    }
                });

                const isHidden = menu.classList.contains('hidden');
                if (isHidden) {
                    menu.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                } else {
                    menu.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            });

            // Handle option selection
            dropdown.querySelectorAll('.dropdown-option').forEach(option => {
                option.addEventListener('click', function () {
                    const value = this.getAttribute('data-value');
                    const hiddenInput = dropdown.parentElement.querySelector('input[type="hidden"]');
                    hiddenInput.value = value;
                    
                    // Submit form
                    dropdown.closest('form').submit();
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function () {
            document.querySelectorAll('.custom-dropdown .dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
            document.querySelectorAll('.custom-dropdown .bi-chevron-down').forEach(icon => {
                icon.classList.remove('rotate-180');
            });
        });
    });
</script>
</x-app-layout>
