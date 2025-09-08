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
    <div class="kpi-results-container">
        @forelse($kpis as $kpi)
            <div class="bg-white rounded-xl shadow-lg p-6 flex flex-row items-center justify-between hover:shadow-xl transition border border-red-100 cursor-pointer mb-6" onclick="window.location='{{ route('kpis.show', $kpi->measure_code) }}'">
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-bold text-red-700 mb-1 line-clamp-2 overflow-hidden">
                        <a href="{{ route('kpis.show', $kpi->measure_code) }}" class="hover:underline">{!! highlightText($kpi->measure_name, $search ?? '') !!}</a>
                    </h2>
                    <p class="text-gray-600 text-sm mb-2 line-clamp-2 overflow-hidden">{!! highlightText($kpi->description, $search ?? '') !!}</p>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span class="bg-red-50 text-red-700 px-2 py-1 rounded text-xs font-semibold">{!! highlightText($kpi->objective, $search ?? '') !!}</span>
                        <span class="bg-red-100 text-gray-700 px-2 py-1 rounded text-xs font-semibold">{!! highlightText($kpi->strategic_theme, $search ?? '') !!}</span>
                        <span class="bg-gray-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">{!! highlightText($kpi->perspective, $search ?? '') !!}</span>
                    </div>
                    <div class="text-xs text-gray-400 mb-2">Code: {!! highlightText($kpi->measure_code, $search ?? '') !!} | Owner: {{ $kpi->measure_owner }}</div>
                </div>
                @if(Auth::user()->role === 'SuperAdmin')
                    <div class="flex flex-col gap-2 items-end ml-6">
                        <a href="{{ route('kpis.edit', $kpi->measure_code) }}" class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow transition w-20 text-center" onclick="event.stopPropagation();">Edit</a>
                        <form action="{{ route('kpis.destroy', $kpi->measure_code) }}" method="POST" onclick="event.stopPropagation();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg text-xs font-semibold shadow transition w-20" onclick="return confirm('Are you sure you want to delete this KPI? This action cannot be undone.');">Delete</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="min-h-[70vh] flex items-center justify-center">
                <div class="bg-white rounded-xl shadow-lg p-8 text-gray-400 text-center border border-red-100 max-w-md">
                    <div class="text-6xl mb-4">📋</div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No KPIs found</h3>
                    <p class="text-sm">Try adjusting your search criteria or browse all KPIs.</p>
                </div>
            </div>
        @endforelse
    </div>
@else
    {{-- Full Page Response: Return complete layout --}}
    <x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto px-6 py-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-red-800 drop-shadow">KPI Library</h1>
            @if(Auth::user()->role === 'SuperAdmin')
            <a href="{{ route('kpis.add') }}"
               class="inline-block bg-red-700 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg shadow text-sm transition">
                + Add KPI
            </a>
            @endif
        </div>

        <!-- Search Form -->
        <div class="relative mb-6 sticky top-0 z-20 bg-white" x-data="{ searchTerm: '', sortBy: 'id' }">
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
                <div class="flex gap-2 items-center">
                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Sort by:</label>
                    <select id="sort-filter" x-model="sortBy" class="border border-red-300 rounded-lg px-7 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                            @change="$dispatch('search-updated', { search: searchTerm, sort: sortBy })">
                        <option value="id">Default</option>
                        <option value="name">Name (A-Z)</option>
                        <option value="objective">Objective (A-Z)</option>
                        <option value="theme">Theme (A-Z)</option>
                        <option value="perspective">Perspective (A-Z)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Category Tabs and KPI List -->
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
                        
                        return regex.test(kpi.measure_name) ||
                               regex.test(kpi.measure_code) ||
                               (kpi.objective && regex.test(kpi.objective)) ||
                               (kpi.strategic_theme && regex.test(kpi.strategic_theme)) ||
                               (kpi.perspective && regex.test(kpi.perspective)) ||
                               regex.test(kpi.measure_owner);
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
                <nav class="-mb-px flex space-x-8">
                    <button 
                        @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        All KPIs <span x-show="activeTab === 'all'" x-text="`(${filteredKpis.length})`" class="text-xs"></span>
                    </button>
                    <button 
                        @click="activeTab = 'institutional'" 
                        :class="activeTab === 'institutional' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Institutional <span x-show="activeTab === 'institutional'" x-text="`(${filteredKpis.length})`" class="text-xs"></span>
                    </button>
                    <button 
                        @click="activeTab = 'departmental'" 
                        :class="activeTab === 'departmental' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Departmental <span x-show="activeTab === 'departmental'" x-text="`(${filteredKpis.length})`" class="text-xs"></span>
                    </button>
                    <button 
                        @click="activeTab = 'personnel'" 
                        :class="activeTab === 'personnel' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Personnel <span x-show="activeTab === 'personnel'" x-text="`(${filteredKpis.length})`" class="text-xs"></span>
                    </button>
                </nav>
            </div>

            <!-- KPI List Container -->
            <div class="flex flex-col gap-6 mt-2 min-h-[60vh]">
                <template x-for="kpi in filteredKpis" :key="kpi.id">
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-row items-center justify-between hover:shadow-xl transition border cursor-pointer"
                         :class="{
                             'border-red-100': activeTab === 'all',
                             'border-blue-100': activeTab === 'departmental',
                             'border-green-100': activeTab === 'institutional',
                             'border-purple-100': activeTab === 'personnel'
                         }"
                         @click="window.location = `{{ route('kpis.show', '') }}/${kpi.measure_code}`">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-xl font-bold mb-1 line-clamp-2 overflow-hidden"
                                :class="{
                                    'text-red-700': activeTab === 'all',
                                    'text-blue-700': activeTab === 'departmental',
                                    'text-green-700': activeTab === 'institutional',
                                    'text-purple-700': activeTab === 'personnel'
                                }">
                                <a :href="`{{ route('kpis.show', '') }}/${kpi.measure_code}`" class="hover:underline" 
                                   x-html="highlightText(kpi.measure_name, searchTerm)"></a>
                            </h2>
                            <p class="text-gray-600 text-sm mb-2 line-clamp-2 overflow-hidden" x-text="kpi.description || ''"></p>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="px-2 py-1 rounded text-xs font-semibold"
                                      :class="{
                                          'bg-red-50 text-red-700': activeTab === 'all',
                                          'bg-blue-50 text-blue-700': activeTab === 'departmental',
                                          'bg-green-50 text-green-700': activeTab === 'institutional',
                                          'bg-purple-50 text-purple-700': activeTab === 'personnel'
                                      }"
                                      x-html="highlightText(kpi.objective || '', searchTerm)"></span>
                                <span class="px-2 py-1 rounded text-xs font-semibold text-gray-700"
                                      :class="{
                                          'bg-red-100': activeTab === 'all',
                                          'bg-blue-100': activeTab === 'departmental',
                                          'bg-green-100': activeTab === 'institutional',
                                          'bg-purple-100': activeTab === 'personnel'
                                      }"
                                      x-html="highlightText(kpi.strategic_theme || '', searchTerm)"></span>
                                <span class="bg-gray-100 px-2 py-1 rounded text-xs font-semibold"
                                      :class="{
                                          'text-red-700': activeTab === 'all',
                                          'text-blue-700': activeTab === 'departmental',
                                          'text-green-700': activeTab === 'institutional',
                                          'text-purple-700': activeTab === 'personnel'
                                      }"
                                      x-html="highlightText(kpi.perspective || '', searchTerm)"></span>
                                <span x-show="kpi.category" class="px-2 py-1 rounded text-xs font-semibold"
                                      :class="{
                                          'bg-blue-100 text-blue-700': activeTab === 'all',
                                          'bg-blue-200 text-blue-800': activeTab === 'departmental',
                                          'bg-green-200 text-green-800': activeTab === 'institutional',
                                          'bg-purple-200 text-purple-800': activeTab === 'personnel'
                                      }"
                                      x-text="kpi.category ? kpi.category.charAt(0).toUpperCase() + kpi.category.slice(1) : ''"></span>
                            </div>
                            <div class="text-xs text-gray-400 mb-2">
                                Code: <span x-html="highlightText(kpi.measure_code, searchTerm)"></span> | Owner: <span x-text="kpi.measure_owner"></span>
                            </div>
                        </div>
                        @if(Auth::user()->role === 'SuperAdmin')
                            <div class="flex flex-col gap-2 items-end ml-6">
                                <a :href="`/kpis/${kpi.measure_code}/edit`" class="px-4 py-2 rounded-lg text-xs font-semibold shadow transition w-20 text-center text-white"
                                   :class="{
                                       'bg-red-700 hover:bg-red-600': activeTab === 'all',
                                       'bg-blue-700 hover:bg-blue-600': activeTab === 'departmental',
                                       'bg-green-700 hover:bg-green-600': activeTab === 'institutional',
                                       'bg-purple-700 hover:bg-purple-600': activeTab === 'personnel'
                                   }"
                                   @click.stop>Edit</a>
                                <form :action="`{{ route('kpis.destroy', '') }}/${kpi.measure_code}`" method="POST" @click.stop>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 rounded-lg text-xs font-semibold shadow transition w-20"
                                            :class="{
                                                'bg-red-100 hover:bg-red-200 text-red-700': activeTab === 'all',
                                                'bg-blue-100 hover:bg-blue-200 text-blue-700': activeTab === 'departmental',
                                                'bg-green-100 hover:bg-green-200 text-green-700': activeTab === 'institutional',
                                                'bg-purple-100 hover:bg-purple-200 text-purple-700': activeTab === 'personnel'
                                            }"
                                            @click="return confirm('Are you sure you want to delete this KPI? This action cannot be undone.')">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </template>
                
                <!-- Empty State -->
                <div x-show="filteredKpis.length === 0" class="min-h-[70vh] flex items-center justify-center">
                    <div class="bg-white rounded-xl shadow-lg p-8 text-gray-400 text-center max-w-md"
                         :class="{
                             'border border-red-100': activeTab === 'all',
                             'border border-blue-100': activeTab === 'departmental',
                             'border border-green-100': activeTab === 'institutional',
                             'border border-purple-100': activeTab === 'personnel'
                         }">
                        <div class="text-6xl mb-4" x-text="activeTab === 'all' ? '📋' : activeTab === 'departmental' ? '🏢' : activeTab === 'institutional' ? '🏛️' : '👥'"></div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">
                            <span x-show="searchTerm">No matching KPIs found</span>
                            <span x-show="!searchTerm && activeTab === 'all'">No KPIs found</span>
                            <span x-show="!searchTerm && activeTab === 'departmental'">No Departmental KPIs found</span>
                            <span x-show="!searchTerm && activeTab === 'institutional'">No Institutional KPIs found</span>
                            <span x-show="!searchTerm && activeTab === 'personnel'">No Personnel KPIs found</span>
                        </h3>
                        <p class="text-sm">
                            <span x-show="searchTerm">Try adjusting your search criteria or browse all KPIs.</span>
                            <span x-show="!searchTerm && activeTab !== 'all'">No KPIs categorized as <span x-text="activeTab"></span> yet.</span>
                            <span x-show="!searchTerm && activeTab === 'all'">Try adjusting your search criteria.</span>
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
