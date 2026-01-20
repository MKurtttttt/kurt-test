@php
function highlightText($text, $search) {
    if (empty($search) || empty($text)) {
        return e($text);
    }
    
    $highlighted = preg_replace(
        '/\b(' . preg_quote($search, '/') . ')/i',
        '<mark class="bg-yellow-200 text-gray-900 px-1 rounded">$1</mark>',
        e($text)
    );
    
    return $highlighted;
}
@endphp

@if(request()->ajax())
    {{-- AJAX Response: Return only the KPI list --}}
    <div style="overflow-x:auto; margin: 0 auto; max-width: 1024px; padding: 0 24px; width:100%; box-sizing:border-box;">
        <table style="width:100%; border-collapse:collapse; background:#fff; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,0.10);">
            <thead>
                <tr style="background:#f3f4f6; color:#800000;">
                    <th style="padding:14px 8px; font-size:16px; font-weight:bold; border-bottom:2px solid #800000;">Name</th>
                    <th style="padding:14px 8px; font-size:16px; font-weight:bold; border-bottom:2px solid #800000;">Description</th>
                    <th style="padding:14px 8px; font-size:16px; font-weight:bold; border-bottom:2px solid #800000;">Objective</th>
                    <th style="padding:14px 8px; font-size:16px; font-weight:bold; border-bottom:2px solid #800000;">Theme</th>
                    <th style="padding:14px 8px; font-size:16px; font-weight:bold; border-bottom:2px solid #800000;">Perspective</th>
                    <th style="padding:14px 8px; font-size:16px; font-weight:bold; border-bottom:2px solid #800000;">Code</th>
                    <th style="padding:14px 8px; font-size:16px; font-weight:bold; border-bottom:2px solid #800000;">Owner</th>
                    @if(Auth::user()->role === 'SuperAdmin')
                        <th style="padding:14px 8px; font-size:16px; font-weight:bold; border-bottom:2px solid #800000;">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @forelse($kpis as $kpi)
                <tr style="border-bottom:1px solid #eee; cursor:pointer; transition:background 0.2s;" onclick="window.location='{{ route('kpis.show', $kpi->measure_code) }}'" onmouseover="this.style.background='#f9fafb';" onmouseout="this.style.background='#fff';">
                    <td style="padding:12px 8px; font-size:15px; color:#b91c1c; font-weight:bold; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        <a href="{{ route('kpis.show', $kpi->measure_code) }}" style="color:#b91c1c; text-decoration:underline;">{!! highlightText($kpi->measure_name, $search ?? '') !!}</a>
                    </td>
                    <td style="padding:12px 8px; font-size:14px; color:#52525b; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{!! highlightText($kpi->description, $search ?? '') !!}</td>
                    <td style="padding:12px 8px; font-size:13px; font-weight:600; background:#fef2f2; color:#b91c1c; border-radius:6px;">{!! highlightText($kpi->objective, $search ?? '') !!}</td>
                    <td style="padding:12px 8px; font-size:13px; font-weight:600; background:#fee2e2; color:#52525b; border-radius:6px;">{!! highlightText($kpi->strategic_theme, $search ?? '') !!}</td>
                    <td style="padding:12px 8px; font-size:13px; font-weight:600; background:#f3f4f6; color:#b91c1c; border-radius:6px;">{!! highlightText($kpi->perspective, $search ?? '') !!}</td>
                    <td style="padding:12px 8px; font-size:13px; color:#a3a3a3;">{!! highlightText($kpi->measure_code, $search ?? '') !!}</td>
                    <td style="padding:12px 8px; font-size:13px; color:#a3a3a3;">{{ $kpi->measure_owner }}</td>
                    @if(Auth::user()->role === 'SuperAdmin')
                    <td style="padding:12px 8px;">
                        <a href="{{ route('kpis.edit', $kpi->measure_code) }}" style="background:#b91c1c; color:#fff; border-radius:8px; font-size:13px; font-weight:600; box-shadow:0 1px 3px rgba(0,0,0,0.04); padding:6px 16px; min-width:80px; text-align:center; border:none; text-decoration:none; transition:background 0.2s, color 0.2s; margin-right:6px;" onclick="event.stopPropagation();" onmouseover="this.style.background='#ffe066';this.style.color:'#800000';" onmouseout="this.style.background='#b91c1c';this.style.color='#fff';">Edit</a>
                        <form action="{{ route('kpis.destroy', $kpi->measure_code) }}" method="POST" style="display:inline;" onclick="event.stopPropagation();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:#ffe066; color:#800000; border-radius:8px; font-size:13px; font-weight:600; box-shadow:0 1px 3px rgba(0,0,0,0.04); padding:6px 16px; min-width:80px; text-align:center; border:none; transition:background 0.2s, color 0.2s;" onmouseover="this.style.background='#b91c1c';this.style.color:'#ffe066';" onmouseout="this.style.background='#ffe066';this.style.color='#800000';" onclick="if (!confirm('Are you sure you want to delete this KPI? This action cannot be undone.')) { event.preventDefault(); }">Delete</button>
                        </form>
                    </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding:32px 0; text-align:center; color:#b0b0b0; font-size:18px;">No KPIs found. Try adjusting your search criteria or browse all KPIs.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@else
    {{-- Full Page Response: Return complete layout --}}
    <x-app-layout>
        <div style="min-height:100vh; background-color:#f8fafc;">
            <div style="max-width:1024px; width:100%; margin:0 auto; padding:32px 24px; box-sizing:border-box;">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="ml-4 text-green-700 hover:text-green-900 font-bold text-lg leading-none">&times;</button>
                    </div>
                @endif

                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-3xl font-bold drop-shadow" style="color: #b91c1c;">KPI Library</h1>
                    @if(Auth::user()->role === 'SuperAdmin')
                    <a href="{{ route('kpis.add') }}"
                    class="bg-red-700 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg shadow text-sm transition">
                        + Add KPI
                    </a>
                    @endif
                </div>

                <!-- Search Form -->
                <div class="relative mb-6 sticky top-0 z-20" x-data="{ searchTerm: '', sortBy: 'id' }">
                    <div class="flex gap-4 items-center">
                        <div class="flex-1 relative relative h-12 flex items-center">
                            <input
                                type="text"
                                name="search"
                                id="search"
                                x-model="searchTerm"
                                placeholder="Search for KPIs..."
                                class="w-full border border-red-300 rounded-lg px-4 py-3 pr-16 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                                autocomplete="off"
                                @input="$dispatch('search-updated', { search: searchTerm, sort: sortBy })"
                            >
                            <button type="button" id="clear-search" class="absolute right-2 top-1/2 -translate-y-1/2 bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-xs font-semibold transition"
                                    @click="searchTerm = ''; $dispatch('search-updated', { search: '', sort: sortBy })">Clear</button>
                        </div>

                        <x-dropdown width="48">
                            <x-slot name="trigger">
                                <button type="button" class="flex items-center justify-between w-32 sm:w-40 h-10 px-3 sm:px-4 bg-red-700 hover:bg-red-800 text-white text-sm rounded-lg whitespace-nowrap">
                                    Tools
                                    <svg class="w-4 h-4 ml-2 transition-transform" :class="open ? 'rotate-180' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content" class="py-1 bg-white dark:bg-gray-700">

                                <a href="{{ route('kpis.advanced-search') }}" class="block px-4 py-2 text-sm sm:text-base hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Advanced Search
                                </a>

                                <div class="border-t border-gray-200 my-1 dark:border-gray-600"></div>
                                
                                <div x-data="{ openSort: false }" class="relative"
                                    @mouseenter="openSort = true" 
                                    @mouseleave="openSort = false">

                                    <button 
                                        class="flex items-center justify-between w-full px-4 py-2 text-sm sm:text-base hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Sort by
                                        <svg class="w-4 h-4 ml-2 transition-transform"
                                            :class="openSort ? 'rotate-180' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>


                                    <div x-show="openSort"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="border-t">
                                        <a @click.prevent="$dispatch('search-updated', { search: searchTerm, sort: 'id' }); sortBy = 'id'" 
                                        class="block px-4 py-2 text-sm sm:text-base hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Default
                                        </a>
                                        <a @click.prevent="$dispatch('search-updated', { search: searchTerm, sort: 'name' }); sortBy = 'name'" 
                                        class="block px-4 py-2 text-sm sm:text-base hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Name (A-Z)
                                        </a>
                                        <a @click.prevent="$dispatch('search-updated', { search: searchTerm, sort: 'objective' }); sortBy = 'objective'" 
                                        class="block px-4 py-2 text-sm sm:text-base hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Objective (A-Z)
                                        </a>
                                        <a @click.prevent="$dispatch('search-updated', { search: searchTerm, sort: 'theme' }); sortBy = 'theme'" 
                                        class="block px-4 py-2 text-sm sm:text-base hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Theme (A-Z)
                                        </a>
                                        <a @click.prevent="$dispatch('search-updated', { search: searchTerm, sort: 'perspective' }); sortBy = 'perspective'" 
                                        class="block px-4 py-2 text-sm sm:text-base hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Perspective (A-Z)
                                        </a>
                                    </div>
                                </div>

                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>

                <div class="mb-6" x-data="{ 
                    activeTab: 'all',
                    searchTerm: '',
                    sortBy: 'id',
                    filteredKpis: @js($kpis),
                    allKpis: @js($kpis),
                    
                    filterKpis() {
                        let filtered = this.allKpis;
                        
                        // Filter by category if not 'all'
                        if (this.activeTab !== 'all') {
                            filtered = filtered.filter(kpi => kpi.category === this.activeTab);
                        }
                        
                        // Filter by search term
                        if (this.searchTerm) {
                            const search = this.searchTerm.toLowerCase();
                            filtered = filtered.filter(kpi => {
                                // Create regex that matches words starting with the search term
                                const regex = new RegExp('\\b' + search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'i');
                                
                                return regex.test(kpi.measure_name);
                            });
                        }
                        
                        // Sort results
                        if (this.sortBy === 'name') {
                            filtered.sort((a, b) => a.measure_name.localeCompare(b.measure_name));
                        } else if (this.sortBy === 'objective') {
                            filtered.sort((a, b) => (a.objective || '').localeCompare(b.objective || ''));
                        } else if (this.sortBy === 'theme') {
                            filtered.sort((a, b) => (a.strategic_theme || '').localeCompare(b.strategic_theme || ''));
                        } else if (this.sortBy === 'perspective') {
                            filtered.sort((a, b) => (a.perspective || '').localeCompare(b.perspective || ''));
                        }
                        
                        this.filteredKpis = filtered;
                    },
                    
                    highlightText(text, searchTerm) {
                        if (!searchTerm || !text) {
                            return text;
                        }
                        
                        // Create regex that matches words starting with the search term
                        const escapedTerm = searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        const regex = new RegExp('\\b(' + escapedTerm + ')', 'gi');
                        return text.toString().replace(regex, '<mark class=&quot;bg-yellow-200 text-gray-900 px-1 rounded&quot;>$1</mark>');
                    },
                    
                    init() {
                        this.filterKpis();
                        this.$watch('searchTerm', () => this.filterKpis());
                        this.$watch('activeTab', () => this.filterKpis());
                        this.$watch('sortBy', () => this.filterKpis());
                    }
                }"
                @search-updated.window="searchTerm = $event.detail.search; sortBy = $event.detail.sort; filterKpis()">
                    <!-- Category Tabs -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex gap-4 sm:gap-8">
                            <button 
                                @click="activeTab = 'all'" 
                                :class="activeTab === 'all' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-xs sm:text-sm transition-colors flex-shrink-0"
                            >
                                All KPIs <span x-show="activeTab === 'all'" x-text="`(${filteredKpis.length})`" class="text-xs"></span>
                            </button>
                            <button 
                                @click="activeTab = 'institutional'" 
                                :class="activeTab === 'institutional' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-xs sm:text-sm transition-colors flex-shrink-0"
                            >
                                Institutional <span x-show="activeTab === 'institutional'" x-text="`(${filteredKpis.length})`" class="text-xs"></span>
                            </button>
                            <button 
                                @click="activeTab = 'departmental'" 
                                :class="activeTab === 'departmental' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-xs sm:text-sm transition-colors flex-shrink-0"
                            >
                                Departmental <span x-show="activeTab === 'departmental'" x-text="`(${filteredKpis.length})`" class="text-xs"></span>
                            </button>
                            <button 
                                @click="activeTab = 'personnel'" 
                                :class="activeTab === 'personnel' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-xs sm:text-sm transition-colors flex-shrink-0"
                            >
                                Personnel <span x-show="activeTab === 'personnel'" x-text="`(${filteredKpis.length})`" class="text-xs"></span>
                            </button>
                        </nav>
                    </div>

                    <!-- KPI list when activeTab is 'all' -->
                    <div x-show="activeTab === 'all'" 
                        class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4 mt-4" 
                        style="min-height:60vh;">
                        <template x-for="category in ['institutional', 'departmental', 'personnel']" :key="category">
                            <div class="bg-white rounded-xl shadow-lg border p-4"
                                style="border-color: #800000;">
                                <h2 class="text-lg md:text-xl font-bold mb-2 text-center"
                                    x-text="category.charAt(0).toUpperCase() + category.slice(1)">
                                </h2>
                                <ul class="space-y-2 pr-2">
                                    <template 
                                        x-for="kpi in filteredKpis.filter(k => k.category === category)" 
                                        :key="kpi.measure_code">
                                        
                                        <li class="flex gap-2 items-start rounded-lg transition-colors">
                                            <span class="text-black font-bold mt-0.5">•</span>
                                            <a :href="`/kpis/${kpi.measure_code}`"
                                            class="text-sm md:text-base font-medium hover:underline text-gray-700 leading-relaxed"
                                            x-html="highlightText(kpi.measure_name, searchTerm)">
                                            </a>
                                        </li>
                                    </template>
                                    <li x-show="filteredKpis.filter(k => k.category === category).length === 0"
                                        class="text-gray-400 text-sm text-center py-8">
                                        <div class="text-6xl mb-4" x-text="category === 'departmental' ? '🏢' : category === 'institutional' ? '🏛️' : '👥'"></div>
                                        <h3 class="text-lg font-semibold text-gray-600 mb-2">
                                            <span x-show="searchTerm">No matching KPIs found</span>
                                            <span x-show="!searchTerm && category === 'departmental'">No Departmental KPIs found</span>
                                            <span x-show="!searchTerm && category === 'institutional'">No Institutional KPIs found</span>
                                            <span x-show="!searchTerm && category === 'personnel'">No Personnel KPIs found</span>
                                        </h3>
                                        <p class="text-sm">
                                            <span x-show="searchTerm">Try adjusting your search criteria or browse all KPIs.</span>
                                            <span x-show="!searchTerm">No KPIs categorized as <span x-text="activeTab"></span> yet.</span>
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </template>
                    </div>

                    <!-- KPI list when activeTab is not 'all' -->
                    <div x-show="activeTab !== 'all'" class="space-y-4 mt-2" style="min-height:60vh;">
                        <template x-for="kpi in filteredKpis" :key="kpi.id">
                            <div class="bg-white rounded-xl shadow-lg p-6 flex flex-row items-center justify-between hover:shadow-xl transition border cursor-pointer"
                                style="border-color: #800000; margin-bottom: 16px;"
                                @click="window.location = `/kpis/${kpi.measure_code}`">
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-xl font-bold mb-2 line-clamp-2 overflow-hidden">
                                        <a :href="`/kpis/${kpi.measure_code}`" class="hover:underline" 
                                        x-html="highlightText(kpi.measure_name, searchTerm)"></a>
                                    </h2>
                                    <p class="text-sm mb-3 line-clamp-2 overflow-hidden"
                                    style="color: #52525b;"
                                    x-text="kpi.description || ''"></p>
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="px-3 py-1.5 rounded text-xs font-semibold"
                                            style="background-color: #fef2f2; color: #b91c1c;"
                                            x-html="kpi.objective"></span>
                                        <span class="px-3 py-1.5 rounded text-xs font-semibold"
                                            style="background-color: #ffe066; color: #800000;"
                                            x-html="kpi.strategic_theme"></span>
                                        <span class="px-3 py-1.5 rounded text-xs font-semibold"
                                            style="background-color: #f3f4f6; color: #b91c1c;"
                                            x-html="kpi.perspective"></span>
                                        <span x-show="kpi.category" class="px-3 py-1.5 rounded text-xs font-semibold"
                                            style="background-color: #ffe066; color: #800000;"
                                            x-text="kpi.category ? kpi.category.charAt(0).toUpperCase() + kpi.category.slice(1) : ''"></span>
                                    </div>
                                    <div class="text-xs"
                                        style="color: #a3a3a3;">
                                        Code: <span x-html="kpi.measure_code"></span> | Owner: <span x-text="kpi.measure_owner"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Empty State -->
                        <div x-show="filteredKpis.length === 0" class="min-h-[70vh] flex items-center justify-center">
                            <div class="bg-white rounded-xl shadow-lg p-8 text-gray-400 text-center max-w-md"
                                style="border: 1px solid #800000;">
                                <div class="text-6xl mb-4" x-text="activeTab === 'departmental' ? '🏢' : activeTab === 'institutional' ? '🏛️' : '👥'"></div>
                                <h3 class="text-lg font-semibold text-gray-600 mb-2">
                                    <span x-show="searchTerm">No matching KPIs found</span>
                                    <span x-show="!searchTerm && activeTab === 'departmental'">No Departmental KPIs found</span>
                                    <span x-show="!searchTerm && activeTab === 'institutional'">No Institutional KPIs found</span>
                                    <span x-show="!searchTerm && activeTab === 'personnel'">No Personnel KPIs found</span>
                                </h3>
                                <p class="text-sm">
                                    <span x-show="searchTerm">Try adjusting your search criteria or browse all KPIs.</span>
                                    <span x-show="!searchTerm">No KPIs categorized as <span x-text="activeTab"></span> yet.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Legacy support for AJAX search (if needed for other parts)
            // The main search functionality is now handled by Alpine.js
            document.addEventListener('DOMContentLoaded', function() {
                // Any additional initialization can go here
            });
        </script>
    </x-app-layout>
@endif