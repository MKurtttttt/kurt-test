@extends('layouts.main')

@section('title', 'Information Hub')

@section('content')
<div style="background-image: url('{{ asset('images/hau-side.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.3;"></div>
    <div class="max-w-6xl mx-auto py-10 px-4" style="position: relative; z-index: 1; background-color: rgba(255, 255, 255, 0.9); border-radius: 1rem; backdrop-filter: blur(10px); margin-top: 2rem;">
        <!-- Header -->
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo-circle.png') }}" class="mx-auto mb-4" alt="Logo Circle" style="width: 180px; height: 180px; object-fit: contain;" />
            <h1 class="text-2xl font-bold" style="color: #70121D;">Information Hub</h1>
            <p class="mt-2" style="color: #5c5c5c;">Access institutional Information Hub and resources</p>
        </div>
        
        <hr class="opacity-100 my-4">

        <!-- Tabs -->
        <div class="mb-6">
            <ul class="flex border-b justify-start" id="tabs">
                @foreach ($categories as $i => $cat)
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
            <div class="relative">
                <input
                    type="text"
                    id="information-hub-search"
                    placeholder="Search Information Hub Sites..."
                    class="w-full rounded-lg px-4 py-3 pr-16 shadow-sm text-sm focus:outline-none"
                    style="border: 2px solid #70121D; focus:ring: 2px solid #8B1538;"
                    autocomplete="off"
                />
                <button type="button" id="clear-information-hub-search" class="absolute right-2 px-3 py-1 rounded-lg text-xs font-semibold transition" style="top: 50%; transform: translateY(-50%); background-color: #ffe066; color: #70121D; hover:background-color: #ffd700;">
                    Clear
                </button>
            </div>
            <div id="search-results" class="hidden mt-4">
                <h3 class="text-lg font-semibold mb-3" style="color: #70121D;">Search Results:</h3>
                <div id="search-results-content" class="space-y-2"></div>
            </div>
        </div>

        @foreach ($categories as $i => $cat)
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

        <!-- Login Link -->
        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="login-btn inline-block px-6 py-3 rounded-md text-white hover:bg-red-600 transition">
                Login to Access Full System
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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

    searchInput.addEventListener('input', performSearch);
    
    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        performSearch();
        searchInput.focus();
    });
</script>

<style>
    /* HAU Colors */
    .subcategory-btn {
        background-color: #f5f5f5 !important;
        color: #70121D !important;
        transition: all 0.3s ease;
    }
    
    .subcategory-btn:hover {
        background-color: #e5e5e5 !important;
    }

    /* Information Hub Links with HAU gold */
    a[href*="information-hub"], a[href*="onedrive"] {
        background-color: #ffe066 !important;
        color: #70121D !important;
        transition: all 0.3s ease;
    }

    a[href*="information-hub"]:hover, a[href*="onedrive"]:hover {
        background-color: #ffd700 !important;
        color: #70121D !important;
    }

    /* Custom CSS for consistency with HAU branding */
    .login-btn {
        background-color: #70121D;
    }

    .tab-btn {
        background-color: white;
        padding: 0.5rem 1rem;
        font-weight: 600;
        color: #70121D;
        border: 1px solid #70121D;
        border-bottom: none;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .tab-btn.active {
        background-color: #ffe066;
        border-bottom: 2px solid #70121D;
        color: #70121D;
    }

    .tab-btn:hover:not(.active) {
        background-color: #f9f9f9;
    }

    .tab-content {
        animation: fadeIn 0.2s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Search results styling */
    #search-results-content .text-blue-600 {
        color: #70121D !important;
    }

    #search-results-content .text-blue-600:hover {
        color: #8B1538 !important;
    }

    .search-highlight {
        background-color: #8B1538;
        color: #ffffff !important;
        padding: 0.15rem 0.25rem;
        border-radius: 0.25rem;
    }

    #search-results-content a[href] {
        background-color: transparent !important;
        color: #70121D !important;
        padding: 0;
        border-radius: 0;
        text-decoration: none;
    }

    #search-results-content a[href]:hover {
        background-color: transparent !important;
        color: #8B1538 !important;
        text-decoration: none;
    }

    /* Custom Scrollbar Styles */
    .tab-content::-webkit-scrollbar {
        width: 12px;
    }

    .tab-content::-webkit-scrollbar-track {
        background: transparent;
        border: none;
    }

    .tab-content::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #70121D, #8B1538);
        border-radius: 10px;
        border: none;
        transition: all 0.3s ease;
    }

    .tab-content::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #8B1538, #70121D);
    }

    .tab-content::-webkit-scrollbar-corner {
        background: transparent;
    }

    .tab-content::-webkit-scrollbar-button {
        display: none;
    }

    .tab-content {
        scrollbar-width: thin;
        scrollbar-color: #70121D transparent;
    }
</style>
@endsection

@php
$useWhiteOverlay = false;
@endphp