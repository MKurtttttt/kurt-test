<!-- This is the Sharepoint Sites dashboard -->
<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="w-full flex justify-center py-8">
            <div class="w-[95%] flex flex-col bg-white rounded-lg px-8 py-8 shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-red-900">Dashboard</h1>
                    @if(Auth::user()->role === 'SuperAdmin')
                        <div class="relative inline-block text-left">
                            <button id="adminDropdownBtn" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-red-900 hover:bg-gray-100 focus:outline-none">
                                Options
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="adminDropdownMenu" class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-20">
                                <div class="py-1">
                                    <a href="{{ route('sharepoint-sites.add') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add Link</a>
                                    <a href="{{ route('sharepoint-sites.edit-list') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit Link</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <hr class="opacity-100 my-4">

                <!-- Tabs -->
                <div class="mb-6">
                    <ul class="flex border-b" id="tabs">
                        <li class="-mb-px mr-2">
                            <button class="tab-btn active" data-tab="tab-iso">
                                ISO
                            </button>
                        </li>
                        <li class="-mb-px mr-2">
                            <button class="tab-btn" data-tab="tab-planning">
                                Planning and Review
                            </button>
                        </li>
                        <li class="-mb-px mr-2">
                            <button class="tab-btn" data-tab="tab-quality">
                                Quality Assurance
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input
                            type="text"
                            id="sharepoint-search"
                            placeholder="Search SharePoint links..."
                            class="w-full border border-red-300 rounded-lg px-4 py-3 pr-16 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                            autocomplete="off"
                        >
                        <button type="button" id="clear-sharepoint-search" class="absolute right-2 top-1/2 -translate-y-1/2 bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-xs font-semibold transition">Clear</button>
                    </div>
                    <div id="search-results" class="hidden mt-4">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Search Results:</h3>
                        <div id="search-results-content" class="space-y-2"></div>
                    </div>
                </div>

                <!-- ISO Tab -->
                <div id="tab-iso" class="tab-content overflow-y-auto" style="max-height: 70vh;">
                    <div class="w-full flex flex-col gap-8">
                        <ul id="departments-list" class="space-y-4">
                            @foreach ($isoLinks as $department => $deptLinks)
                                <li>
                                    <button type="button" class="department-btn w-full text-left font-bold text-red-900 px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                        {{ $department ?? 'Uncategorized Department' }}
                                    </button>
                                    @php $offices = $deptLinks->groupBy('office'); @endphp
                                    <ul class="ml-6 mt-2 hidden office-list">
                                        @foreach ($offices as $office => $officeLinks)
                                            <li>
                                                @if ($office)
                                                    <button type="button" class="office-btn w-full text-left font-semibold text-gray-800 px-3 py-1 bg-gray-50 rounded hover:bg-gray-100">
                                                        {{ $office }}
                                                    </button>
                                                    <ul class="ml-6 mt-1 hidden file-list">
                                                        @foreach ($officeLinks as $link)
                                                            <li class="mb-2">
                                                                <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}" 
                                                                    class="inline-block bg-blue-100 px-3 py-1 rounded hover:bg-blue-200"
                                                                    style="color: #2563eb !important;">
                                                                    {{ $link->label }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    @foreach ($officeLinks as $link)
                                                        <li class="mb-2">
                                                            <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}" 
                                                                class="inline-block bg-blue-100 px-3 py-1 rounded hover:bg-blue-200"
                                                                style="color: #2563eb !important;">
                                                                {{ $link->label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Planning and Review Tab -->
                <div id="tab-planning" class="tab-content hidden overflow-y-auto" style="max-height: 70vh;">
                    <div class="w-full flex flex-col gap-8">
                        <ul class="space-y-4">
                            @foreach ($planningLinks as $department => $deptLinks)
                                <li>
                                    <button type="button" class="department-btn w-full text-left font-bold text-red-900 px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                        {{ $department ?? 'Uncategorized Department' }}
                                    </button>
                                    @php $offices = $deptLinks->groupBy('office'); @endphp
                                    <ul class="ml-6 mt-2 hidden office-list">
                                        @foreach ($offices as $office => $officeLinks)
                                            <li>
                                                @if ($office)
                                                    <button type="button" class="office-btn w-full text-left font-semibold text-gray-800 px-3 py-1 bg-gray-50 rounded hover:bg-gray-100">
                                                        {{ $office }}
                                                    </button>
                                                    <ul class="ml-6 mt-1 hidden file-list">
                                                        @foreach ($officeLinks as $link)
                                                            <li class="mb-2">
                                                                <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}" 
                                                                    class="inline-block bg-blue-100 px-3 py-1 rounded hover:bg-blue-200"
                                                                    style="color: #2563eb !important;">
                                                                    {{ $link->label }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    @foreach ($officeLinks as $link)
                                                        <li class="mb-2">
                                                            <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}" 
                                                                class="inline-block bg-blue-100 px-3 py-1 rounded hover:bg-blue-200"
                                                                style="color: #2563eb !important;">
                                                                {{ $link->label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Quality Assurance Tab -->
                <div id="tab-quality" class="tab-content hidden overflow-y-auto" style="max-height: 70vh;">
                    <div class="w-full flex flex-col gap-8">
                        <ul class="space-y-4">
                            @foreach ($qaLinks as $department => $deptLinks)
                                <li>
                                    <button type="button" class="department-btn w-full text-left font-bold text-red-900 px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                        {{ $department ?? 'Uncategorized Department' }}
                                    </button>
                                    @php $offices = $deptLinks->groupBy('office'); @endphp
                                    <ul class="ml-6 mt-2 hidden office-list">
                                        @foreach ($offices as $office => $officeLinks)
                                            <li>
                                                @if ($office)
                                                    <button type="button" class="office-btn w-full text-left font-semibold text-gray-800 px-3 py-1 bg-gray-50 rounded hover:bg-gray-100">
                                                        {{ $office }}
                                                    </button>
                                                    <ul class="ml-6 mt-1 hidden file-list">
                                                        @foreach ($officeLinks as $link)
                                                            <li class="mb-2">
                                                                <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}" 
                                                                    class="inline-block bg-blue-100 px-3 py-1 rounded hover:bg-blue-200"
                                                                    style="color: #2563eb !important;">
                                                                    {{ $link->label }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    @foreach ($officeLinks as $link)
                                                        <li class="mb-2">
                                                            <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}" 
                                                                class="inline-block bg-blue-100 px-3 py-1 rounded hover:bg-blue-200"
                                                                style="color: #2563eb !important;">
                                                                {{ $link->label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize dropdown functionality
        const dropdownBtn = document.getElementById('adminDropdownBtn');
        const dropdownMenu = document.getElementById('adminDropdownMenu');
        if(dropdownBtn) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('hidden');
            });
            document.addEventListener('click', function() {
                dropdownMenu.classList.add('hidden');
            });
        }

        // Initialize tabs functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                // Clear search first if there's an active search
                if (searchInput.value.trim() !== '') {
                    searchInput.value = '';
                    searchResults.classList.add('hidden');
                }
                
                // Reset all tab styles and content visibility
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(tc => {
                    tc.classList.add('hidden');
                    tc.style.display = '';
                });
                
                // Activate the clicked tab
                btn.classList.add('active');
                const targetTab = document.getElementById(btn.getAttribute('data-tab'));
                if (targetTab) {
                    targetTab.classList.remove('hidden');
                }
            });
        });

        // Initialize collapsible sections
        document.querySelectorAll('.department-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const officeList = this.nextElementSibling;
                document.querySelectorAll('.office-list').forEach(list => {
                    if (list !== officeList) list.classList.add('hidden');
                });
                officeList.classList.toggle('hidden');
            });
        });

        document.querySelectorAll('.office-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const fileList = this.nextElementSibling;
                document.querySelectorAll('.file-list').forEach(list => {
                    if (list !== fileList) list.classList.add('hidden');
                });
                fileList.classList.toggle('hidden');
            });
        });
    });

    // Search functionality
    const searchInput = document.getElementById('sharepoint-search');
    const clearSearchBtn = document.getElementById('clear-sharepoint-search');
    const searchResults = document.getElementById('search-results');
    const searchResultsContent = document.getElementById('search-results-content');
    const tabContents = document.querySelectorAll('.tab-content');

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show normal tabs, hide search results
            searchResults.classList.add('hidden');
            // Restore normal tab functionality - show only the active tab
            tabContents.forEach(content => {
                content.style.display = '';
                content.classList.add('hidden');
            });
            // Show the currently active tab
            const activeTabBtn = document.querySelector('.tab-btn.active');
            if (activeTabBtn) {
                const activeTabId = activeTabBtn.getAttribute('data-tab');
                const activeTab = document.getElementById(activeTabId);
                if (activeTab) {
                    activeTab.classList.remove('hidden');
                }
            }
            return;
        }

        // Hide tab contents, show search results
        tabContents.forEach(content => {
            content.style.display = 'none';
            content.classList.add('hidden');
        });
        searchResults.classList.remove('hidden');
        
        // Clear previous results
        searchResultsContent.innerHTML = '';

        // Search through all links (exclude admin dropdown)
        const allLinks = document.querySelectorAll('.tab-content a[href*="sharepoint"], .tab-content a[href*="onedrive"], .tab-content a[target="_blank"]');
        let hasResults = false;

        allLinks.forEach(link => {
            const linkText = link.textContent.toLowerCase();
            // Removed linkTitle from search to exclude descriptions
            
            // Get department information by traversing up the DOM properly
            let departmentOriginal = '';
            let department = '';
            
            // Find the closest li that contains a department-btn
            const linkLi = link.closest('li');
            if (linkLi) {
                // Traverse up to find the department li (the one with department-btn as direct child)
                let currentElement = linkLi;
                while (currentElement) {
                    const departmentBtn = currentElement.querySelector('.department-btn');
                    if (departmentBtn) {
                        departmentOriginal = departmentBtn.textContent.trim();
                        department = departmentOriginal.toLowerCase();
                        break;
                    }
                    // Move up to the parent li
                    currentElement = currentElement.parentElement?.closest('li');
                }
            }
            
            // Find the office by looking for the closest office-btn in the parent structure
            let office = '';
            let officeOriginal = '';
            const linkItem = link.closest('li');
            const parentOfficeList = linkItem?.closest('.file-list')?.previousElementSibling;
            if (parentOfficeList?.classList.contains('office-btn')) {
                officeOriginal = parentOfficeList.textContent.trim();
                office = officeOriginal.toLowerCase();
            }

            if (linkText.includes(searchTerm) || 
                department.includes(searchTerm) || office.includes(searchTerm)) {
                
                hasResults = true;
                
                // Create result item
                const resultItem = document.createElement('div');
                resultItem.className = 'p-3 bg-gray-50 rounded-lg border';
                resultItem.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <a href="${link.href}" target="_blank" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                ${highlightText(link.textContent, searchTerm)}
                            </a>
                            <div class="text-sm text-gray-600 mt-1">
                                ${departmentOriginal ? `Department: ${highlightText(departmentOriginal, searchTerm)}` : ''}
                                ${officeOriginal ? ` | Office: ${highlightText(officeOriginal, searchTerm)}` : ''}
                            </div>
                            ${link.getAttribute('title') ? `<div class="text-xs text-gray-500 mt-1">${link.getAttribute('title')}</div>` : ''}
                        </div>
                    </div>
                `;
                
                searchResultsContent.appendChild(resultItem);
            }
        });

        if (!hasResults) {
            searchResultsContent.innerHTML = '<div class="p-4 text-center text-gray-500">No SharePoint links found matching your search.</div>';
        }
    }

    function highlightText(text, searchTerm) {
        if (!searchTerm) return text;
        const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(regex, '<span class="bg-yellow-200 text-gray-900 px-1 rounded">$1</span>');
    }

    // Event listeners
    searchInput.addEventListener('input', performSearch);
    
    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        performSearch();
        searchInput.focus();
    });
</script>

<style>
    .tab-btn {
        background-color: white;
        padding: 0.5rem 1rem;
        font-weight: 600;
        color: #70121D;
        border: 1px solid #e5e7eb;
        border-bottom: none;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    .tab-btn.active {
        background-color: #f3f4f6;
        border-bottom: 2px solid #70121D;
        color: #70121D;
    }

    .tab-content {
        animation: fadeIn 0.2s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .maroon {
        transition: 300ms;
    }

    .maroon:hover {
        background-color: #A84655;
    }
</style>
