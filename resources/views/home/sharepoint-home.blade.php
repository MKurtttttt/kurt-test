@extends('layouts.main')

@section('title', 'SharePoint Sites')

@section('content')
<div style="background-image: url('{{ asset('images/hau-side.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.3;"></div>
<div class="max-w-3xl mx-auto py-10 px-4" style="position: relative; z-index: 1; background-color: rgba(255, 255, 255, 0.9); border-radius: 1rem; backdrop-filter: blur(10px); margin-top: 2rem;">
    <!-- Header -->
    <div class="text-center mb-6">
        <img src="{{ asset('images/logo-circle.png') }}" class="mx-auto mb-4" alt="Logo Circle" style="width: 180px; height: 180px; object-fit: contain;" />
        <h1 class="text-2xl font-bold" style="color: #70121D;">SharePoint Sites</h1>
        <p class="mt-2" style="color: #5c5c5c;">Access institutional SharePoint sites and resources</p>
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
                            id="sharepoint-search"
                            placeholder="Search SharePoint Sites..."
                            class="w-full rounded-lg px-4 py-3 pr-16 shadow-sm text-sm focus:outline-none"
                            style="border: 2px solid #70121D; focus:ring: 2px solid #8B1538;"
                            autocomplete="off"
                        >
                        <button type="button" id="clear-sharepoint-search" class="absolute right-2 px-3 py-1 rounded-lg text-xs font-semibold transition" style="top: 50%; transform: translateY(-50%); background-color: #ffe066; color: #70121D; hover:background-color: #ffd700;">Clear</button>
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
                                @foreach (($linksByCategory[$cat] ?? []) as $department => $deptLinks)
                                    <li>
                                        <button type="button" class="department-btn w-full text-left font-bold px-4 py-2 rounded transition">
                                            {{ $department ?? 'Uncategorized Department' }}
                                        </button>
                                        @php $offices = $deptLinks->groupBy('office'); @endphp
                                        <ul class="ml-6 mt-2 hidden office-list">
                                            @foreach ($offices as $office => $officeLinks)
                                                <li>
                                                    @if ($office)
                                                        <button type="button" class="office-btn w-full text-left font-semibold px-3 py-1 rounded">
                                                            {{ $office }}
                                                        </button>
                                                        <ul class="ml-6 mt-1 hidden file-list">
                                                            @foreach ($officeLinks as $link)
                                                                <li class="mb-2">
                                                                    <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}" 
                                                                        class="inline-block px-3 py-1 rounded transition">
                                                                        {{ $link->label }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        @foreach ($officeLinks as $link)
                                                            <li class="mb-2">
                                                                <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}" 
                                                                    class="inline-block px-3 py-1 rounded transition">
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

    <!-- Login Link -->
    <div class="mt-8 text-center">
        <a href="{{ route('login') }}" class="login-btn inline-block px-6 py-3 rounded-md text-white hover:bg-red-600 transition">
            Login to Access Full System
        </a>
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

        // Search through all links inside the tab content areas (not just those with sharepoint/onedrive in the URL)
        // This finds links the user actually sees in the lists and avoids accidentally matching unrelated site links.
        const allLinks = document.querySelectorAll('.tab-content a[href]');
        let hasResults = false;

        allLinks.forEach(link => {
            // Skip links without a usable href (javascript:void, mailto, anchor-only)
            const href = link.getAttribute('href') || '';
            if (!href || href.startsWith('javascript:') || href.startsWith('#') || href.startsWith('mailto:')) return;
            const linkText = link.textContent.toLowerCase();
            const linkTitle = (link.getAttribute('title') || '').toLowerCase();
            
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

            if (linkText.includes(searchTerm) || linkTitle.includes(searchTerm) || 
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
        
        // Create a temporary div to safely process the text
        const tempDiv = document.createElement('div');
        tempDiv.textContent = text;
        
        // Get the plain text and create highlighted version
        const plainText = tempDiv.textContent;
        const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        
        // Split the text and rebuild with highlights
        const parts = plainText.split(regex);
        const fragment = document.createDocumentFragment();
        
        parts.forEach((part, index) => {
            if (index % 2 === 1) {
                // This is a matched part, create highlighted span (pale maroon to contrast with HAU gold)
                const span = document.createElement('span');
                span.className = 'search-highlight px-1 rounded';
                // stronger maroon highlight with white text for higher contrast
                span.style.backgroundColor = '#8B1538'; /* darker maroon */
                span.style.color = '#ffffff';
                span.style.padding = '0.15rem 0.25rem';
                span.style.borderRadius = '0.25rem';
                span.textContent = part;
                fragment.appendChild(span);
            } else {
                // Regular text
                fragment.appendChild(document.createTextNode(part));
            }
        });
        
        // Return the HTML string
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
    .department-btn {
        background-color: #f5f5f5 !important;
        color: #70121D !important;
        transition: all 0.3s ease;
    }
    
    .department-btn:hover {
        background-color: #e5e5e5 !important;
    }
    
    .office-btn {
        background-color: #f5f5f5 !important;
        color: #70121D !important;
        transition: all 0.3s ease;
    }
    
    .office-btn:hover {
        background-color: #e5e5e5 !important;
    }

    /* SharePoint Links with HAU gold - applies everywhere except search results */
    a[href*="sharepoint"], a[href*="onedrive"] {
        background-color: #ffe066 !important;
        color: #70121D !important;
        transition: all 0.3s ease;
    }

    a[href*="sharepoint"]:hover, a[href*="onedrive"]:hover {
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
        color: #70121D !important; /* maroon */
    }

    #search-results-content .text-blue-600:hover {
        color: #8B1538 !important; /* darker maroon on hover */
    }

    /* Highlight used for matched search terms */
    .search-highlight {
        background-color: #8B1538; /* darker maroon for visibility */
        color: #ffffff !important;
        padding: 0.15rem 0.25rem;
        border-radius: 0.25rem;
    }

    /* Remove HAU gold background for links inside search results so they appear clean */
    #search-results-content a[href] {
        background-color: transparent !important;
        color: #70121D !important;
        padding: 0; /* let highlight spans handle padding */
        border-radius: 0;
        text-decoration: none; /* remove underline */
    }

    #search-results-content a[href]:hover {
        background-color: transparent !important;
        color: #8B1538 !important;
        text-decoration: none;
    }

    /* Custom Scrollbar Styles for Tab Content Areas - No Track Background */
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

    /* Hide scrollbar arrow buttons */
    .tab-content::-webkit-scrollbar-button {
        display: none;
    }

    /* For Firefox */
    .tab-content {
        scrollbar-width: thin;
        scrollbar-color: #70121D transparent;
    }
</style>
@endsection

@php
$useWhiteOverlay = false;
@endphp