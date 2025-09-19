<!-- This is the Sharepoint Sites dashboard -->
<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="w-full flex justify-center py-8">
            <div class="w-[95%] flex flex-col bg-white rounded-lg px-8 py-8 shadow-lg">
                @if(session('success'))
                    <div id="success-msg-popup" style="position:fixed;top:90px;left:50%;transform:translateX(-50%);z-index:9999;min-width:300px;max-width:90vw;box-shadow:0 2px 12px rgba(0,0,0,0.15);background:#d1fae5;border:2px solid #10b981;color:#065f46;padding:18px 32px;font-size:1.1rem;border-radius:12px;text-align:center;transition:opacity 0.7s;">
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                    <script>
                        setTimeout(function() {
                            var msg = document.getElementById('success-msg-popup');
                            if (msg) {
                                msg.style.opacity = '0';
                                setTimeout(function() { msg.style.display = 'none'; }, 700);
                            }
                        }, 3000);
                    </script>
                @endif
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
                        @foreach ($category as $i => $cat)
                            <li class="-mb-px mr-2">
                                <button class="tab-btn {{ $i === 0 ? 'active' : '' }}" data-tab="tab-{{ Str::slug($cat, '-') }}">
                                    {{ $cat }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div style="position: relative; display: flex; align-items: center;">
                        <input
                            type="text"
                            id="sharepoint-search"
                            placeholder="Search SharePoint links..."
                            style="width: 100%; border: 2px solid #fca5a5; border-radius: 8px; padding: 12px 80px 12px 16px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); font-size: 14px; outline: none;"
                            autocomplete="off"
                            onfocus="this.style.borderColor='#dc2626'; this.style.boxShadow='0 0 0 2px rgba(220, 38, 38, 0.2)';"
                            onblur="this.style.borderColor='#fca5a5'; this.style.boxShadow='0 1px 2px 0 rgba(0, 0, 0, 0.05)';"
                        >
                        <button 
                            type="button" 
                            id="clear-sharepoint-search" 
                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background-color: #fee2e2; color: #b91c1c; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#fecaca';"
                            onmouseout="this.style.backgroundColor='#fee2e2';"
                        >Clear</button>
                    </div>
                    <div id="search-results" class="hidden mt-4">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Search Results:</h3>
                        <div id="search-results-content" class="space-y-2"></div>
                    </div>
                </div>

                @foreach ($category as $i => $cat)
                    <div id="tab-{{ Str::slug($cat, '-') }}" class="tab-content {{ $i === 0 ? '' : 'hidden' }}">
                        <div class="w-full flex flex-col gap-8">
                            <ul class="space-y-4">
                                @foreach (collect($linksByCategory[$cat] ?? [])->sortKeys() as $department => $deptLinks)
                                    <li>
                                        <button type="button" class="department-btn w-full text-left font-bold text-red-900 px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                            {{ $department ?? 'Uncategorized Department' }}
                                        </button>
                                        @php $offices = $deptLinks->groupBy('office')->sortKeys(); @endphp
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
                                                                        style="display:inline-block; background-color:#bfdbfe; padding:6px 12px; border-radius:6px; color:#2563eb !important; text-decoration:none; cursor:pointer;"
                                                                        onmouseover="this.style.backgroundColor='#93c5fd';"
                                                                        onmouseout="this.style.backgroundColor='#bfdbfe';">
                                                                        {{ $link->label }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        @foreach ($officeLinks as $link)
                                                            <li class="mb-2">
                                                                <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}"
                                                                    style="display:inline-block; background-color:#bfdbfe; padding:6px 12px; border-radius:6px; color:#2563eb !important; text-decoration:none; cursor:pointer;"
                                                                    onmouseover="this.style.backgroundColor='#93c5fd';"
                                                                    onmouseout="this.style.backgroundColor='#bfdbfe';">
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
                @endforeach

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
                
                // Create result item with proper DOM manipulation for better compatibility
                const resultItem = document.createElement('div');
                resultItem.style.padding = '12px';
                resultItem.style.backgroundColor = '#f9fafb';
                resultItem.style.borderRadius = '8px';
                resultItem.style.border = '1px solid #e5e7eb';
                resultItem.style.marginBottom = '8px';
                // Create elements manually instead of using innerHTML for better compatibility
                const linkElement = document.createElement('a');
                linkElement.href = link.href;
                linkElement.target = '_blank';
                linkElement.style.color = '#2563eb';
                linkElement.style.fontWeight = '500';
                linkElement.onmouseover = () => linkElement.style.color = '#1e40af';
                linkElement.onmouseout = () => linkElement.style.color = '#2563eb';
                
                // Add highlighting to the link text
                const originalText = link.textContent;
                if (originalText.toLowerCase().includes(searchTerm)) {
                    // Create highlighted text using DOM elements
                    const parts = originalText.split(new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi'));
                    parts.forEach((part, index) => {
                        if (part.toLowerCase() === searchTerm.toLowerCase()) {
                            const highlightSpan = document.createElement('span');
                            highlightSpan.style.backgroundColor = '#ffe066';
                            highlightSpan.style.fontWeight = 'bold';
                            highlightSpan.textContent = part;
                            linkElement.appendChild(highlightSpan);
                        } else if (part) {
                            const textNode = document.createTextNode(part);
                            linkElement.appendChild(textNode);
                        }
                    });
                } else {
                    linkElement.textContent = originalText;
                }
                
                const metaDiv = document.createElement('div');
                metaDiv.style.fontSize = '14px';
                metaDiv.style.color = '#4b5563';
                metaDiv.style.marginTop = '4px';
                
                let metaText = '';
                if (departmentOriginal) metaText += `Department: ${departmentOriginal}`;
                if (officeOriginal) metaText += ` | Office: ${officeOriginal}`;
                
                // Add highlighting to metadata if it contains the search term
                if (metaText && (department.includes(searchTerm) || office.includes(searchTerm))) {
                    // Create highlighted metadata using DOM elements
                    const parts = metaText.split(new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi'));
                    parts.forEach((part, index) => {
                        if (part.toLowerCase() === searchTerm.toLowerCase()) {
                            const highlightSpan = document.createElement('span');
                            highlightSpan.style.backgroundColor = '#ffe066';
                            highlightSpan.style.fontWeight = 'bold';
                            highlightSpan.textContent = part;
                            metaDiv.appendChild(highlightSpan);
                        } else if (part) {
                            const textNode = document.createTextNode(part);
                            metaDiv.appendChild(textNode);
                        }
                    });
                } else {
                    metaDiv.textContent = metaText;
                }
                
                const contentDiv = document.createElement('div');
                contentDiv.style.flex = '1';
                contentDiv.appendChild(linkElement);
                if (metaText) contentDiv.appendChild(metaDiv);
                
                if (link.getAttribute('title')) {
                    const titleDiv = document.createElement('div');
                    titleDiv.style.fontSize = '12px';
                    titleDiv.style.color = '#6b7280';
                    titleDiv.style.marginTop = '4px';
                    titleDiv.textContent = link.getAttribute('title');
                    contentDiv.appendChild(titleDiv);
                }
                
                const containerDiv = document.createElement('div');
                containerDiv.style.display = 'flex';
                containerDiv.style.alignItems = 'center';
                containerDiv.style.justifyContent = 'space-between';
                containerDiv.appendChild(contentDiv);
                
                resultItem.appendChild(containerDiv);
                
                searchResultsContent.appendChild(resultItem);
            }
        });

        if (!hasResults) {
            const noResultsDiv = document.createElement('div');
            noResultsDiv.style.padding = '16px';
            noResultsDiv.style.textAlign = 'center';
            noResultsDiv.style.color = '#6b7280';
            noResultsDiv.textContent = 'No SharePoint links found matching your search.';
            searchResultsContent.appendChild(noResultsDiv);
        }
    }

    // Remove the old highlightText function since we're not using innerHTML anymore

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
