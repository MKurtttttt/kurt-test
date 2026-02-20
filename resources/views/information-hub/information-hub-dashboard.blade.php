<!-- This is the Information Hub Sites dashboard -->
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
                                    <a href="{{ route('information-hub.add') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add Link</a>
                                    <a href="{{ route('information-hub.edit-list') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit Link</a>
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
                            id="information-hub-search"
                            placeholder="Search Information Hub Sites..."
                            style="width: 100%; border: 2px solid #fca5a5; border-radius: 8px; padding: 12px 80px 12px 16px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); font-size: 14px; outline: none;"
                            autocomplete="off"
                            onfocus="this.style.borderColor='#dc2626'; this.style.boxShadow='0 0 0 2px rgba(220, 38, 38, 0.2)';"
                            onblur="this.style.borderColor='#fca5a5'; this.style.boxShadow='0 1px 2px 0 rgba(0, 0, 0, 0.05)';"
                        >
                        <button 
                            type="button" 
                            id="clear-information-hub-search" 
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
                    <div id="tab-{{ Str::slug($cat, '-') }}" class="tab-content {{ $i === 0 ? '' : 'hidden' }}" style="border: 2px solid #70121D; border-radius: 0.75rem; padding: 1rem;">
                        <div class="w-full flex flex-col gap-8">
                            <ul class="space-y-4">
                                @foreach (($linksByCategory[$cat] ?? []) as $subCategory => $subCatLinks)
                                    <li>
                                        @if ($subCategory)
                                            {{-- Show collapsible button if subcategory exists --}}
                                            <button type="button" class="subcategory-btn w-full text-left font-bold px-4 py-2 rounded transition">
                                                {{ $subCategory }}
                                            </button>
                                            <ul class="ml-6 mt-2 hidden link-list space-y-8">
                                                @foreach ($subCatLinks as $link)
                                                    <li class="mb-8">
                                                        <div class="flex flex-row">
                                                            @php
                                                                $imageClass = 'w-60 h-60'; // default
                                                                if ($link->type === 'Document') {
                                                                    $imageClass = 'w-48 h-68';
                                                                } elseif ($link->type === 'Video') {
                                                                    $imageClass = 'w-72 h-48';
                                                                }
                                                            @endphp
                                                            <img src="{{ asset($link->image_path) }}" class="{{ $imageClass }}" alt="{{ $link->title }}" data-type="{{ $link->type }}"/>
                                                            <div class="flex flex-col px-6">
                                                                <div class="text-2xl text-[#70121D] font-bold mb-4">
                                                                    {{ $link->title }}
                                                                </div>
                                                                <a href="{{ $link->url }}" target="_blank" title="{{ $link->description ?? '' }}">
                                                                    [ Click here to view ]
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            {{-- No subcategory - show links directly without button --}}
                                            <ul class="space-y-8">
                                                @foreach ($subCatLinks as $link)
                                                    <li class="mb-8">
                                                        <div class="flex flex-row">
                                                            @php
                                                                $imageClass = 'w-60 h-60'; // default
                                                                if ($link->type === 'Document') {
                                                                    $imageClass = 'w-48 h-68';
                                                                } elseif ($link->type === 'Video') {
                                                                    $imageClass = 'w-72 h-48';
                                                                }
                                                            @endphp
                                                            <img src="{{ asset($link->image_path) }}" class="{{ $imageClass }}" alt="{{ $link->title }}" data-type="{{ $link->type }}"/>
                                                            <div class="flex flex-col px-6">
                                                                <div class="text-2xl text-[#70121D] font-bold mb-4">
                                                                    {{ $link->title }}
                                                                </div>
                                                                <a href="{{ $link->url }}" target="_blank" title="{{ $link->description ?? '' }}">
                                                                    [ Click here to view ]
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
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

        // Initialize collapsible subcategory sections
        document.querySelectorAll('.subcategory-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const linkList = this.nextElementSibling;
                document.querySelectorAll('.link-list').forEach(list => {
                    if (list !== linkList) list.classList.add('hidden');
                });
                linkList.classList.toggle('hidden');
            });
        });
    });

    // Search functionality
    const searchInput = document.getElementById('information-hub-search');
    const clearSearchBtn = document.getElementById('clear-information-hub-search');
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

        // Search through all link items by title
        const allLinkContainers = document.querySelectorAll('.tab-content li .flex.flex-row');
        let resultsHTML = '';
        let hasResults = false;

        allLinkContainers.forEach(container => {
            // Get the title element
            const titleElement = container.querySelector('.text-2xl.font-bold');
            if (!titleElement) return;
            
            const title = titleElement.textContent.trim();
            const titleLower = title.toLowerCase();
            
            // Check if title matches search term
            if (titleLower.includes(searchTerm)) {
                hasResults = true;
                
                // Get image src, type, and link info
                const img = container.querySelector('img');
                const link = container.querySelector('a[href]');
                
                const imgSrc = img ? img.getAttribute('src') : '';
                const imgType = img ? img.getAttribute('data-type') : '';
                const linkHref = link ? link.getAttribute('href') : '';
                const linkTitle = link ? link.getAttribute('title') : '';
                
                // Determine image class based on type
                let imageClass = 'w-60 h-60'; // default
                if (imgType === 'Document') {
                    imageClass = 'w-48 h-68';
                } else if (imgType === 'Video') {
                    imageClass = 'w-72 h-48';
                }
                
                // Get subcategory if exists
                let subCategoryOriginal = '';
                const linkLi = container.closest('li');
                if (linkLi) {
                    let currentElement = linkLi;
                    while (currentElement) {
                        const subCategoryBtn = currentElement.querySelector('.subcategory-btn');
                        if (subCategoryBtn) {
                            subCategoryOriginal = subCategoryBtn.textContent.trim();
                            break;
                        }
                        currentElement = currentElement.parentElement?.closest('li');
                    }
                }
                
                // Add result HTML
                resultsHTML += `
                    <li class="mb-8">
                        <div class="flex flex-row">
                            <img src="${imgSrc}" class="${imageClass}" alt="${title}"/>
                            <div class="flex flex-col px-6">
                                <div class="text-2xl text-[#70121D] font-bold mb-4">
                                    ${highlightText(title, searchTerm)}
                                </div>
                                ${subCategoryOriginal ? `<div class="text-sm text-gray-600 mb-2">Sub-category: ${subCategoryOriginal}</div>` : ''}
                                <a href="${linkHref}" target="_blank" title="${linkTitle}">
                                    [ Click here to view ]
                                </a>
                            </div>
                        </div>
                    </li>
                `;
            }
        });

        if (hasResults) {
            searchResultsContent.innerHTML = `
                <div style="border: 2px solid #70121D; border-radius: 0.75rem; padding: 1rem;">
                    <ul class="space-y-8">
                        ${resultsHTML}
                    </ul>
                </div>
            `;
        } else {
            searchResultsContent.innerHTML = '<div class="p-4 text-center text-gray-500">No Information Hub links found matching your search.</div>';
        }
    }

    function highlightText(text, searchTerm) {
        if (!searchTerm) return text;
        
        const tempDiv = document.createElement('div');
        tempDiv.textContent = text;
        
        const plainText = tempDiv.textContent;
        const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        
        const parts = plainText.split(regex);
        const fragment = document.createDocumentFragment();
        
        parts.forEach((part, index) => {
            if (index % 2 === 1) {
                const span = document.createElement('span');
                span.className = 'search-highlight px-1 rounded';
                span.style.backgroundColor = '#8B1538';
                span.style.color = '#ffffff';
                span.style.padding = '0.15rem 0.25rem';
                span.style.borderRadius = '0.25rem';
                span.textContent = part;
                fragment.appendChild(span);
            } else {
                fragment.appendChild(document.createTextNode(part));
            }
        });
        
        const container = document.createElement('div');
        container.appendChild(fragment);
        return container.innerHTML;
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
    .subcategory-btn {
        background-color: #f5f5f5 !important;
        color: #70121D !important;
        transition: all 0.3s ease;
    }
    
    .subcategory-btn:hover {
        background-color: #e5e5e5 !important;
    }

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