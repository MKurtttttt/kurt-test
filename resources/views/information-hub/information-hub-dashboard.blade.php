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
                    @if(in_array(Auth::user()->role, ['SuperAdmin', 'IDC Admin']))
                        <div class="relative inline-block text-left">
                            <button id="adminDropdownBtn" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-red-900 hover:bg-gray-100 focus:outline-none">
                                Options
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="adminDropdownMenu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-20">
                                <div class="py-1">
                                    {{-- Links options section --}}
                                    @php
                                        $firstCategory = isset($category) && count($category) > 0 ? $category->first() : '';
                                    @endphp
                                    <div id="dropdown-links-section">
                                        <div class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Links</div>
                                        <a id="dropdown-add-link-btn" href="{{ route('information-hub.add', ['category' => $firstCategory]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add Link</a>
                                        <a id="dropdown-edit-link-btn" href="{{ route('information-hub.edit-list', ['category' => $firstCategory]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit Link</a>
                                        <a id="dropdown-delete-link-btn" href="{{ route('information-hub.edit-list', ['category' => $firstCategory]) }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Delete Link</a>
                                    </div>
                                    {{-- Policies options section --}}
                                    <div id="dropdown-policies-section" class="hidden">
                                        <a href="{{ route('iso.management.policies.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-semibold text-red-900">Manage Policies</a>
                                    </div>
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
                        <li class="-mb-px mr-2">
                            <button class="tab-btn" data-tab="tab-policies">
                                Policies
                            </button>
                        </li>
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
                    <div id="tab-{{ Str::slug($cat, '-') }}" class="tab-content {{ $i === 0 ? '' : 'hidden' }}" style="border: 2px solid #70121D; border-radius: 0.75rem; padding: 1.5rem;">
                        <div class="w-full flex flex-col gap-6">
                            @php
                                $hasSubcategories = false;
                                foreach (($linksByCategory[$cat] ?? []) as $subCategory => $subCatLinks) {
                                    if ($subCategory) {
                                        $hasSubcategories = true;
                                        break;
                                    }
                                }
                            @endphp

                            @if ($hasSubcategories)
                                @if ($cat === 'Learning Videos')
                                    <!-- Featured Asymmetric Video Categories Layout -->
                                    @php
                                        $videoCats = $linksByCategory[$cat]->filter(function($links, $subCat) {
                                            return !empty($subCat);
                                        });
                                        
                                        $featuredSubCat = null;
                                        $featuredLinks = collect();
                                        $otherVideoCats = collect();
                                        
                                        $cnt = 0;
                                        foreach ($videoCats as $subCat => $links) {
                                            if ($cnt === 0) {
                                                $featuredSubCat = $subCat;
                                                $featuredLinks = $links;
                                            } else {
                                                $otherVideoCats[$subCat] = $links;
                                            }
                                            $cnt++;
                                        }
                                        
                                        $featuredDesc = match($featuredSubCat) {
                                            'ISO System Guides' => 'Step-by-step walkthroughs of the ISO ticketing and documentation flow.',
                                            'QualiThink: EOMS Awareness Videos' => 'Learn the fundamentals of Educational Organizations Management System (EOMS) standards.',
                                            default => 'Access helpful tutorials and videos for this category.'
                                        };
                                    @endphp
                                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-2">Video Categories</p>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                        <!-- Left Side: Large Featured Card -->
                                        @if ($featuredSubCat)
                                            <div class="relative overflow-hidden p-8 flex flex-col justify-between min-h-[260px] border border-white/10 shadow-lg text-left transition-all duration-300 hover:scale-[1.01]" style="background: linear-gradient(135deg, #70121D 0%, #4a070f 100%); border-radius: 1.5rem !important;">
                                                <div class="relative z-10">
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-white/12 border border-white/5 mb-4" style="color: #ffffff !important; border-radius: 9999px !important;">
                                                        {{ count($featuredLinks) }} {{ Str::plural('video', count($featuredLinks)) }}
                                                    </span>
                                                    <h3 class="text-2xl font-bold text-white mb-2 leading-tight" style="color: #ffffff !important;">{{ $featuredSubCat }}</h3>
                                                    <p class="text-sm mb-6 max-w-sm leading-relaxed" style="color: rgba(255, 255, 255, 0.85) !important;">{{ $featuredDesc }}</p>
                                                </div>
                                                <div class="relative z-10">
                                                    <button type="button"
                                                            class="subcategory-btn inline-flex items-center gap-2 px-5 py-2.5 bg-white text-[#70121D] font-bold text-sm hover:bg-gray-50 hover:scale-[1.03] hover:shadow-md transition-all active:scale-[0.98] focus:outline-none"
                                                            style="border-radius: 9999px !important;"
                                                            data-target="list-{{ Str::slug($cat . '-' . $featuredSubCat) }}">
                                                        Browse guides <i class="bi bi-arrow-right-short text-lg leading-none"></i>
                                                    </button>
                                                </div>
                                                <!-- Background Icon Watermark -->
                                                <div class="absolute bottom-2 right-2 pointer-events-none">
                                                    <i class="bi bi-collection-play-fill" style="font-size: 8rem; line-height: 1; color: rgba(255, 255, 255, 0.05) !important;"></i>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Right Side: Small Vertical Stack -->
                                        <div class="flex flex-col gap-4">
                                            @foreach ($otherVideoCats as $subCat => $links)
                                                <button type="button"
                                                        class="subcategory-btn maroon-btn w-full text-left p-5 border border-white/10 shadow-sm flex justify-between items-center group focus:outline-none"
                                                        style="background-color: #70121D !important; border-radius: 1rem !important;"
                                                        data-target="list-{{ Str::slug($cat . '-' . $subCat) }}">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 bg-white shadow-sm" style="border-radius: 0.75rem !important;">
                                                            <i class="bi bi-camera-video-fill text-lg leading-none" style="color: #70121D !important;"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-bold text-white transition-colors text-base" style="color: #ffffff !important;">{{ $subCat }}</h4>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-semibold px-2.5 py-1 flex-shrink-0 transition-colors" style="background-color: #ffffff !important; color: #70121D !important; border-radius: 9999px !important;">
                                                        {{ count($links) }} {{ Str::plural('video', count($links)) }}
                                                    </span>
                                                </button>
                                            @endforeach

                                            @if(in_array(Auth::user()->role, ['SuperAdmin', 'IDC Admin']))
                                                <!-- Mock Add Category Card -->
                                                <div class="w-full p-5 border border-dashed flex justify-between items-center" style="border-color: rgba(112, 18, 29, 0.4) !important; background-color: rgba(112, 18, 29, 0.03) !important; border-radius: 1rem !important;">
                                                    <div class="flex items-center gap-4 text-[#70121D]">
                                                        <div class="w-10 h-10 border border-dashed bg-white flex items-center justify-center flex-shrink-0" style="border-color: rgba(112, 18, 29, 0.3) !important; border-radius: 0.75rem !important;">
                                                            <i class="bi bi-plus-lg text-lg" style="color: #70121D !important;"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-semibold text-base" style="color: #70121D !important;">Add category</h4>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-bold px-2.5 py-1" style="background-color: rgba(112, 18, 29, 0.1) !important; color: #70121D !important; border-radius: 9999px !important;">soon</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <!-- Standard Category Cards Grid -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-4">
                                        @foreach (($linksByCategory[$cat] ?? []) as $subCategory => $subCatLinks)
                                            @if ($subCategory)
                                                <button type="button"
                                                        class="subcategory-btn h-16 flex items-center justify-center text-center font-semibold px-6 py-3 rounded-lg border border-red-200 shadow-sm hover:scale-[1.02] transition-all duration-200 text-sm focus:outline-none"
                                                        data-target="list-{{ Str::slug($cat . '-' . $subCategory) }}">
                                                    {{ $subCategory }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endif

                            <!-- Link Lists Section -->
                            <div class="w-full">
                                @foreach (($linksByCategory[$cat] ?? []) as $subCategory => $subCatLinks)
                                    @if ($subCategory)
                                        <div id="list-{{ Str::slug($cat . '-' . $subCategory) }}" class="hidden link-list grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                            @foreach ($subCatLinks as $link)
                                                <div class="link-card bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-row items-center gap-6 md:gap-8 animate-fade-in">
                                                    <span class="link-meta hidden" data-type="{{ $link->type }}" data-image="{{ $link->image_path ? asset($link->image_path) : '' }}"></span>
                                                    @if ($link->type === 'Video' && $link->image_path)
                                                        <div class="relative w-32 h-24 md:w-44 md:h-32 rounded-xl overflow-hidden shadow-sm border border-gray-200 flex-shrink-0 group">
                                                            <img src="{{ asset($link->image_path) }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="{{ $link->title }}" />
                                                            <div class="absolute inset-0 bg-black/35 flex items-center justify-center transition-colors duration-300 group-hover:bg-black/45">
                                                                <i class="bi bi-play-circle-fill text-3xl md:text-4xl text-white drop-shadow-sm transition-transform duration-300 group-hover:scale-110"></i>
                                                            </div>
                                                        </div>
                                                    @elseif ($link->type === 'Video')
                                                        <div class="w-32 h-24 md:w-44 md:h-32 bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl shadow-sm flex items-center justify-center flex-shrink-0 text-red-700">
                                                            <i class="bi bi-play-circle-fill text-4xl md:text-5xl transition-transform duration-300 hover:scale-110"></i>
                                                        </div>
                                                    @else
                                                        @php
                                                            $imageClass = 'w-28 h-28 md:w-36 md:h-36 object-cover rounded-xl shadow-md border border-gray-200 flex-shrink-0';
                                                            if ($link->type === 'Document') {
                                                                $imageClass = 'w-32 h-44 md:w-40 md:h-56 object-cover rounded-xl shadow-md border border-gray-200 flex-shrink-0';
                                                            }
                                                        @endphp
                                                        <img src="{{ asset($link->image_path) }}" class="{{ $imageClass }}" alt="{{ $link->title }}" data-type="{{ $link->type }}"/>
                                                    @endif
                                                    <div class="flex flex-col flex-grow justify-between min-h-[140px] py-1">
                                                        <div>
                                                            <h4 class="link-card-title text-lg md:text-2xl font-bold text-red-955 hover:text-red-700 transition leading-snug mb-2 line-clamp-2">
                                                                {{ $link->title }}
                                                            </h4>
                                                            <p class="text-xs md:text-sm text-gray-500 font-medium mb-4">
                                                                {{ $link->sub_category ?: ($link->type ?: 'Access Resource') }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <a href="{{ $link->url }}" target="_blank" class="inline-flex items-center text-xs md:text-sm font-semibold text-red-700 hover:text-red-900 transition-colors">
                                                                [ Click here to view ]
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        {{-- No subcategory - show links directly without button --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-2">
                                            @foreach ($subCatLinks as $link)
                                                <div class="link-card bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-row items-center gap-6 md:gap-8 animate-fade-in">
                                                    <span class="link-meta hidden" data-type="{{ $link->type }}" data-image="{{ $link->image_path ? asset($link->image_path) : '' }}"></span>
                                                    @if ($link->type === 'Video' && $link->image_path)
                                                        <div class="relative w-32 h-24 md:w-44 md:h-32 rounded-xl overflow-hidden shadow-sm border border-gray-200 flex-shrink-0 group">
                                                            <img src="{{ asset($link->image_path) }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="{{ $link->title }}" />
                                                            <div class="absolute inset-0 bg-black/35 flex items-center justify-center transition-colors duration-300 group-hover:bg-black/45">
                                                                <i class="bi bi-play-circle-fill text-3xl md:text-4xl text-white drop-shadow-sm transition-transform duration-300 group-hover:scale-110"></i>
                                                            </div>
                                                        </div>
                                                    @elseif ($link->type === 'Video')
                                                        <div class="w-32 h-24 md:w-44 md:h-32 bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl shadow-sm flex items-center justify-center flex-shrink-0 text-red-700">
                                                            <i class="bi bi-play-circle-fill text-4xl md:text-5xl transition-transform duration-300 hover:scale-110"></i>
                                                        </div>
                                                    @else
                                                        @php
                                                            $imageClass = 'w-28 h-28 md:w-36 md:h-36 object-cover rounded-xl shadow-md border border-gray-200 flex-shrink-0';
                                                            if ($link->type === 'Document') {
                                                                $imageClass = 'w-32 h-44 md:w-40 md:h-56 object-cover rounded-xl shadow-md border border-gray-200 flex-shrink-0';
                                                            }
                                                        @endphp
                                                        <img src="{{ asset($link->image_path) }}" class="{{ $imageClass }}" alt="{{ $link->title }}" data-type="{{ $link->type }}"/>
                                                    @endif
                                                    <div class="flex flex-col flex-grow justify-between min-h-[140px] py-1">
                                                        <div>
                                                            <h4 class="link-card-title text-lg md:text-2xl font-bold text-red-955 hover:text-red-700 transition leading-snug mb-2 line-clamp-2">
                                                                {{ $link->title }}
                                                            </h4>
                                                            <p class="text-xs md:text-sm text-gray-500 font-medium mb-4">
                                                                {{ $link->sub_category ?: ($link->type ?: 'Access Resource') }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <a href="{{ $link->url }}" target="_blank" class="inline-flex items-center text-xs md:text-sm font-semibold text-red-700 hover:text-red-900 transition-colors">
                                                                [ Click here to view ]
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <div id="tab-policies" class="tab-content hidden" style="border: 2px solid #70121D; border-radius: 0.75rem; padding: 2rem;">
                    @if($policyCategories->isEmpty())
                        <div class="text-center py-12 text-gray-400">
                            <svg class="mx-auto w-12 h-12 mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                            </svg>
                            <p class="italic text-sm">No policies registered yet.</p>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 mb-5 uppercase tracking-widest font-semibold">Browse by Category</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                            @foreach ($policyCategories as $catRecord)
                                @php
                                    $subCategory = $catRecord->name;
                                    $subCatPolicies = $policies[$subCategory] ?? collect();
                                    $categoryIcons = [
                                        'academic'   => 'bi-book-half',
                                        'admin'      => 'bi-building-fill',
                                        'research'   => 'bi-search',
                                        'employment' => 'bi-briefcase-fill',
                                        'safety'     => 'bi-shield-fill',
                                        'tech'       => 'bi-cpu-fill',
                                        'student'    => 'bi-mortarboard-fill',
                                        'quality'    => 'bi-patch-check-fill',
                                        'governance' => 'bi-people-fill',
                                        'default'    => 'bi-file-earmark-text-fill',
                                    ];

                                    $catLower = strtolower($subCategory ?? 'uncategorized');
                                    $iconClass = $categoryIcons['default'];

                                    $keywordMappings = [
                                        'academic'   => ['academic'],
                                        'admin'      => ['admin', 'business', 'finance'],
                                        'research'   => ['research'],
                                        'employment' => ['employ', 'human'],
                                        'safety'     => ['safe', 'security', 'health'],
                                        'tech'       => ['tech', 'system', 'it'],
                                        'student'    => ['student', 'education', 'learn'],
                                        'quality'    => ['quality', 'audit', 'iso', 'compliance', 'qms', 'iqa'],
                                        'governance' => ['board', 'governance', 'regent'],
                                    ];

                                    foreach ($keywordMappings as $key => $keywords) {
                                        foreach ($keywords as $kw) {
                                            if ($kw === 'it') {
                                                if (preg_match('/\bit\b/i', $catLower)) {
                                                    $iconClass = $categoryIcons[$key];
                                                    break 2;
                                                }
                                            } else {
                                                if (str_contains($catLower, $kw)) {
                                                    $iconClass = $categoryIcons[$key];
                                                    break 2;
                                                }
                                            }
                                        }
                                    }

                                    $count = count($subCatPolicies);
                                    $label = $subCategory ?: 'General';
                                @endphp
                                <button type="button"
                                    class="policy-category-card group relative overflow-hidden rounded-3xl text-left transition-all duration-300 focus:outline-none"
                                    onclick="openPolicyModal('{{ addslashes($label) }}')"
                                    data-category="{{ $label }}">
                                    
                                    {{-- Card Dot grid pattern --}}
                                    <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none" 
                                         style="background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px); background-size: 10px 10px;"></div>

                                    {{-- Card Ambient highlight glow --}}
                                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
                                         style="background: radial-gradient(circle at 80% 20%, rgba(197, 160, 89, 0.15) 0%, transparent 60%);"></div>

                                    {{-- Card Body --}}
                                    <div class="relative z-10 p-5 h-full flex flex-row items-center gap-4" style="box-sizing: border-box;">
                                        {{-- Icon --}}
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-105 relative"
                                            style="background: linear-gradient(135deg, rgba(197,160,89,0.2) 0%, rgba(197,160,89,0.05) 100%); border: 1px solid rgba(197,160,89,0.3); box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                                            <i class="bi {{ $iconClass }} text-xl" style="color: #e6c17a;"></i>
                                        </div>
                                        {{-- Text --}}
                                        <div class="flex-1 min-w-0 pr-8">
                                            <h3 class="font-bold text-white text-sm md:text-base uppercase tracking-wider leading-snug mb-0.5 transition-colors duration-300 group-hover:text-[#e6c17a]" style="color: #ffffff !important; font-family: 'Inter', sans-serif; margin: 0 0 2px;">
                                                {{ $label }}
                                            </h3>
                                            <p class="text-[13px] font-medium flex items-center gap-1.5" style="color: rgba(255,255,255,0.6); margin: 0;">
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#e6c17a] opacity-75"></span>
                                                {{ $count }} {{ Str::plural('document', $count) }}
                                            </p>
                                        </div>
                                    </div>
                                    {{-- Arrow indicator --}}
                                    <div class="absolute right-5 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0"
                                         style="background: linear-gradient(135deg, #70121D 0%, #4a070f 100%); border: 1px solid rgba(197, 160, 89, 0.4); box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                                        <svg class="w-4 h-4" style="color: #e6c17a;" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                                        </svg>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<style>
    /* Card Grid */
    .ih-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    /* Resource Card */
    .ih-resource-card {
        display: flex;
        flex-direction: column;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #f0f0f0;
        background: #fff;
        text-decoration: none;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .ih-resource-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        transform: translateY(-3px);
    }

    /* Thumbnail */
    .ih-card-thumb {
        position: relative;
        aspect-ratio: 16/9;
        overflow: hidden;
        background: #f9fafb;
    }
    .ih-card-thumb-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, rgba(197,160,89,0.08), rgba(112,18,29,0.05));
    }
    .ih-type-badge {
        position: absolute;
        bottom: 7px; left: 7px;
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 9px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; padding: 2px 7px; border-radius: 6px;
        border: 1px solid transparent;
        backdrop-filter: blur(6px);
    }

    /* Card Body */
    .ih-card-body {
        padding: 12px 14px 14px;
        display: flex; flex-direction: column; flex: 1;
    }
    .ih-card-title {
        font-size: 0.8125rem; font-weight: 700; color: #1f2937;
        line-height: 1.35; margin: 0 0 5px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ih-card-desc {
        font-size: 0.7rem; color: #9ca3af; line-height: 1.5; margin: 0 0 10px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .ih-card-cta {
        margin-top: auto;
        display: flex; align-items: center; gap: 5px;
        font-size: 0.7rem; font-weight: 600; color: #c5a059;
        transition: color 0.15s;
    }
    .ih-resource-card:hover .ih-card-cta { color: #70121D; }

    /* ── Policy Category Cards ── */
    .policy-category-card {
        background: linear-gradient(135deg, #4a070f 0%, #1f0104 100%);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        cursor: pointer;
        border-radius: 1.25rem;
        min-height: 84px;
        border: 1px solid rgba(197, 160, 89, 0.2);
        position: relative;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .policy-category-card:hover {
        transform: translateY(-5px);
        border-color: rgba(197, 160, 89, 0.55);
        box-shadow: 0 12px 30px rgba(112, 18, 29, 0.35), 0 0 0 1px rgba(197, 160, 89, 0.3);
    }
    .policy-category-card i.bi {
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: inline-block;
    }
    .policy-category-card:hover i.bi {
        transform: scale(1.18) rotate(-4deg);
    }

    /* subtle shimmer sweep on hover */
    .policy-category-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,0.06) 50%, transparent 70%);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
        z-index: 1;
        pointer-events: none;
    }
    .policy-category-card:hover::before {
        transform: translateX(100%);
    }

    /* ── Modal scrollbar ── */
    #policy-modal-body::-webkit-scrollbar { width: 6px; }
    #policy-modal-body::-webkit-scrollbar-track { background: #f9fafb; }
    #policy-modal-body::-webkit-scrollbar-thumb { background: #e0b4b9; border-radius: 4px; }
    #policy-modal-body::-webkit-scrollbar-thumb:hover { background: #70121D; }

    /* selected states for subcategory toggle buttons */
    .subcategory-btn.selected-category {
        background-color: #70121D !important;
        color: white !important;
        border-color: #70121D !important;
    }
    
    button.subcategory-btn.selected-category div {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
        border-color: transparent !important;
    }
    button.subcategory-btn.selected-category h4 {
        color: white !important;
    }
    button.subcategory-btn.selected-category i.bi {
        color: white !important;
    }
    button.subcategory-btn.selected-category span {
        background: rgba(255, 255, 255, 0.15) !important;
        color: white !important;
    }

    /* Maroon Button Custom Styles */
    .subcategory-btn.maroon-btn {
        background-color: #70121D !important;
        transition: background-color 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
    }
    .subcategory-btn.maroon-btn:hover {
        background-color: #550e16 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(112, 18, 29, 0.2);
    }
    .subcategory-btn.maroon-btn.selected-category {
        background-color: #4a070f !important;
        border: 2px solid rgba(255, 255, 255, 0.4) !important;
    }
    .subcategory-btn.maroon-btn.selected-category div {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
    .subcategory-btn.maroon-btn.selected-category i.bi {
        color: white !important;
    }
    .subcategory-btn.maroon-btn.selected-category span {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }
</style>

{{-- ============================================================ --}}
{{-- Policy Category Modal                                        --}}
{{-- ============================================================ --}}
<div id="policy-modal-overlay"
    class="fixed inset-0 z-50 flex items-center justify-center hidden"
    style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); margin: 0 !important; outline: none; opacity: 0; transition: opacity 0.25s ease;"
    onclick="closePolicyModalOnOverlay(event)">

    <div id="policy-modal"
        class="bg-white rounded-2xl shadow-2xl w-full mx-4 overflow-hidden flex flex-col transition-all duration-300"
        style="max-width: 800px; max-height: 85vh; border-radius: 1.5rem;">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100"
            style="background: linear-gradient(135deg, #70121D 0%, #4a070f 100%);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.15)">
                    <i id="policy-modal-icon" class="bi bi-file-earmark-text-fill text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.6);">Policy Category</p>
                    <h2 id="policy-modal-title" class="text-lg font-bold text-white leading-tight"></h2>
                </div>
            </div>
            <button onclick="closePolicyModal()" class="w-9 h-9 rounded-full flex items-center justify-center transition-colors duration-200" style="background:rgba(255,255,255,0.1); color:white;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="bi bi-x-lg text-sm text-white"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div id="policy-modal-body" class="overflow-y-auto flex-1 p-6 bg-gray-50/50">
            {{-- Dynamically populated by JS --}}
        </div>

    </div>
</div>


{{-- Store policies data for JS --}}
@php
    $policiesForJs = [];
    foreach ($policyCategories as $catRecord) {
        $cat = $catRecord->name;
        $items = $policies[$cat] ?? collect();
        $policiesForJs[$cat] = $items->map(function($p) {
            return [
                'title'            => $p->title,
                'description'      => $p->description,
                'url'              => $p->url,
                'document_code'    => $p->document_code,
                'revision_count'   => $p->revision_count,
                'effectivity_date' => $p->effectivity_date ? \Carbon\Carbon::parse($p->effectivity_date)->format('M d, Y') : null,
                'policy_date'      => $p->policy_date,
            ];
        })->values()->toArray();
    }
@endphp
<script>
    const policiesData = @json($policiesForJs);
    const isAdmin = @json(Auth::check() && in_array(Auth::user()->role, ['SuperAdmin', 'IDC Admin']));

    function getCategoryIcon(categoryName) {
        const catLower = categoryName.toLowerCase();
        if (catLower.includes('academic')) {
            return 'bi-book-half';
        }
        if (catLower.includes('admin') || catLower.includes('business') || catLower.includes('finance')) {
            return 'bi-building-fill';
        }
        if (catLower.includes('research')) {
            return 'bi-search';
        }
        if (catLower.includes('employ') || catLower.includes('human')) {
            return 'bi-briefcase-fill';
        }
        if (catLower.includes('safe') || catLower.includes('security') || catLower.includes('health')) {
            return 'bi-shield-fill';
        }
        if (catLower.includes('tech') || catLower.includes('system') || /\bit\b/.test(catLower)) {
            return 'bi-cpu-fill';
        }
        if (catLower.includes('student') || catLower.includes('education') || catLower.includes('learn')) {
            return 'bi-mortarboard-fill';
        }
        if (catLower.includes('quality') || catLower.includes('audit') || catLower.includes('iso') || catLower.includes('compliance') || catLower.includes('qms') || catLower.includes('iqa')) {
            return 'bi-patch-check-fill';
        }
        if (catLower.includes('board') || catLower.includes('governance') || catLower.includes('regent')) {
            return 'bi-people-fill';
        }
        return 'bi-file-earmark-text-fill'; // fallback default
    }

    function openPolicyModal(categoryName) {
        const overlay = document.getElementById('policy-modal-overlay');
        const title = document.getElementById('policy-modal-title');
        const body = document.getElementById('policy-modal-body');
        const modalIcon = document.getElementById('policy-modal-icon');

        title.textContent = categoryName;

        // Determine matching icon class via helper
        const iconClass = getCategoryIcon(categoryName);

        // Set header icon class
        if (modalIcon) {
            modalIcon.className = `bi ${iconClass} text-lg text-white`;
        }

        // Find matching policies — Object.values converts keyed collection to array
        const rawItems = policiesData[categoryName] ?? {};
        const items = Object.values(rawItems);
        // Sort alphabetically by title (A-Z)
        items.sort((a, b) => a.title.localeCompare(b.title, undefined, {numeric: true, sensitivity: 'base'}));

        if (items.length === 0) {
            body.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <i class="bi bi-file-earmark-lock2-fill text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm italic mb-4">No policies in this category yet.</p>
                    ${isAdmin ? `
                        <a href="{{ route('iso.management.policies.create') }}?category=${encodeURIComponent(categoryName)}"
                           class="inline-flex items-center gap-2 px-4 py-2 border border-dashed border-gray-300 hover:border-red-900 rounded-xl text-sm font-semibold text-gray-600 hover:text-red-900 transition-all duration-200">
                            <i class="bi bi-plus-circle"></i> Add Policy for this category
                        </a>
                    ` : ''}
                </div>`;
        } else {
            // Group policies by starting letter (A-Z)
            const groups = {};
            items.forEach(p => {
                const titleStr = p.title || '';
                const letter = titleStr.trim().charAt(0).toUpperCase();
                const groupKey = /^[A-Z]/.test(letter) ? letter : '#';
                if (!groups[groupKey]) {
                    groups[groupKey] = [];
                }
                groups[groupKey].push(p);
            });

            const sortedKeys = Object.keys(groups).sort();
            
            let modalHtml = '<div class="space-y-8 w-full">';
            sortedKeys.forEach(key => {
                modalHtml += `
                    <div class="mb-6">
                        <!-- Letter Header -->
                        <div class="flex items-center mb-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-sm font-bold text-white shadow-sm" style="background-color: #70121D; border: 1px solid rgba(197, 160, 89, 0.3);">
                                ${key}
                            </span>
                            <div class="flex-1 h-px bg-gray-200/80 ml-3.5"></div>
                        </div>
                        
                        <!-- Cards Grid -->
                        <div class="ih-cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); width:100%;">
                            ${groups[key].map(p => {
                                const badgeBg = 'rgba(197,160,89,0.12)';
                                const badgeColor = '#8a6a1e';
                                const badgeLabel = 'Document';
                                const typeIcon = 'bi-file-earmark-pdf';
                                const hasUrl = p.url && p.url.trim() !== '';
                                
                                return hasUrl ? `
                                    <a href="${p.url}" target="_blank" class="ih-resource-card" title="${p.description ?? p.title}">
                                        {{-- Thumbnail --}}
                                        <div class="ih-card-thumb">
                                            <div class="ih-card-thumb-placeholder">
                                                <i class="bi ${typeIcon}" style="font-size:2rem; color:rgba(197,160,89,0.4);"></i>
                                            </div>
                                            {{-- Type badge overlay --}}
                                            <span class="ih-type-badge" style="background:${badgeBg}; color:${badgeColor}; border-color:${badgeColor}33;">
                                                <i class="bi ${typeIcon}" style="font-size:8px;"></i>
                                                ${badgeLabel}
                                            </span>
                                        </div>

                                        {{-- Body --}}
                                        <div class="ih-card-body">
                                            <p class="ih-card-title">${p.title}</p>
                                            ${p.description ? `<p class="ih-card-desc">${p.description}</p>` : ''}
                                            
                                            <!-- Metadata Pills -->
                                            <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px; margin-top:4px;">
                                                ${p.document_code ? `
                                                    <span style="font-size:10px; font-weight:600; color:#8a6a1e; background:rgba(197,160,89,0.08); padding:3px 7px; border-radius:6px; text-transform:uppercase; letter-spacing:0.02em; white-space:nowrap; border: 1px solid rgba(197,160,89,0.15);">
                                                        Code: ${p.document_code}
                                                    </span>
                                                ` : ''}
                                                ${p.revision_count !== null && p.revision_count !== undefined ? `
                                                    <span style="font-size:10px; font-weight:600; color:#8a6a1e; background:rgba(197,160,89,0.08); padding:3px 7px; border-radius:6px; white-space:nowrap; border: 1px solid rgba(197,160,89,0.15);">
                                                        Rev: ${p.revision_count === 0 ? 'Original' : p.revision_count}
                                                    </span>
                                                ` : ''}
                                                ${p.effectivity_date ? `
                                                    <span style="font-size:10px; font-weight:600; color:#8a6a1e; background:rgba(197,160,89,0.08); padding:3px 7px; border-radius:6px; white-space:nowrap; border: 1px solid rgba(197,160,89,0.15);">
                                                        Effective: ${p.effectivity_date}
                                                    </span>
                                                ` : ''}
                                                ${p.policy_date ? `
                                                    <span style="font-size:10px; font-weight:600; color:#8a6a1e; background:rgba(197,160,89,0.08); padding:3px 7px; border-radius:6px; white-space:nowrap; border: 1px solid rgba(197,160,89,0.15);">
                                                        Year: ${p.policy_date}
                                                    </span>
                                                ` : ''}
                                            </div>

                                            <div class="ih-card-cta">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="display:inline-block; vertical-align:middle; width: 10px; height: 10px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                                </svg>
                                                <span style="vertical-align:middle;">Open resource</span>
                                            </div>
                                        </div>
                                    </a>
                                ` : `
                                    <div class="ih-resource-card opacity-75" style="cursor: default;" title="${p.description ?? p.title}">
                                        {{-- Thumbnail --}}
                                        <div class="ih-card-thumb">
                                            <div class="ih-card-thumb-placeholder" style="background: linear-gradient(135deg, rgba(156,163,175,0.08), rgba(156,163,175,0.05));">
                                                <i class="bi bi-file-earmark-pdf" style="font-size:2rem; color:rgba(156,163,175,0.4);"></i>
                                            </div>
                                            {{-- Type badge overlay --}}
                                            <span class="ih-type-badge" style="background:rgba(156,163,175,0.12); color:#6b7280; border-color:rgba(156,163,175,0.2);">
                                                <i class="bi bi-clock" style="font-size:8px;"></i>
                                                Pending Link
                                            </span>
                                        </div>

                                        {{-- Body --}}
                                        <div class="ih-card-body">
                                            <p class="ih-card-title" style="color: #6b7280;">${p.title}</p>
                                            ${p.description ? `<p class="ih-card-desc">${p.description}</p>` : ''}
                                            
                                            <!-- Metadata Pills -->
                                            <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px; margin-top:4px;">
                                                ${p.document_code ? `
                                                    <span style="font-size:10px; font-weight:600; color:#6b7280; background:rgba(156,163,175,0.08); padding:3px 7px; border-radius:6px; text-transform:uppercase; letter-spacing:0.02em; white-space:nowrap; border: 1px solid rgba(156,163,175,0.15);">
                                                        Code: ${p.document_code}
                                                    </span>
                                                ` : ''}
                                                ${p.revision_count !== null && p.revision_count !== undefined ? `
                                                    <span style="font-size:10px; font-weight:600; color:#6b7280; background:rgba(156,163,175,0.08); padding:3px 7px; border-radius:6px; white-space:nowrap; border: 1px solid rgba(156,163,175,0.15);">
                                                        Rev: ${p.revision_count === 0 ? 'Original' : p.revision_count}
                                                    </span>
                                                ` : ''}
                                                ${p.effectivity_date ? `
                                                    <span style="font-size:10px; font-weight:600; color:#6b7280; background:rgba(156,163,175,0.08); padding:3px 7px; border-radius:6px; white-space:nowrap; border: 1px solid rgba(156,163,175,0.15);">
                                                        Effective: ${p.effectivity_date}
                                                    </span>
                                                ` : ''}
                                                ${p.policy_date ? `
                                                    <span style="font-size:10px; font-weight:600; color:#6b7280; background:rgba(156,163,175,0.08); padding:3px 7px; border-radius:6px; white-space:nowrap; border: 1px solid rgba(156,163,175,0.15);">
                                                        Year: ${p.policy_date}
                                                    </span>
                                                ` : ''}
                                            </div>

                                            <div class="ih-card-cta text-gray-400 font-semibold italic" style="color: #9ca3af; background: none; border: none; padding: 0;">
                                                <i class="bi bi-exclamation-circle" style="font-size:9px; margin-right: 2px;"></i>
                                                Link pending upload
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            });
            modalHtml += '</div>';
            body.innerHTML = modalHtml;
        }

        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Animate in
        const modal = document.getElementById('policy-modal');
        overlay.style.opacity = '0';
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.95) translateY(10px)';
        
        setTimeout(() => {
            overlay.style.transition = 'opacity 0.25s ease';
            overlay.style.opacity = '1';
            modal.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
            modal.style.opacity = '1';
            modal.style.transform = 'scale(1) translateY(0)';
        }, 20);
    }

    function closePolicyModal() {
        const overlay = document.getElementById('policy-modal-overlay');
        const modal = document.getElementById('policy-modal');

        overlay.style.transition = 'opacity 0.2s ease';
        overlay.style.opacity = '0';
        modal.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.95) translateY(10px)';

        setTimeout(() => {
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    }

    function closePolicyModalOnOverlay(event) {
        if (event.target === document.getElementById('policy-modal-overlay')) {
            closePolicyModal();
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePolicyModal();
    });
</script>

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

                // Hide all open link lists when switching tabs
                document.querySelectorAll('.link-list').forEach(list => {
                    list.classList.add('hidden');
                });
                document.querySelectorAll('.subcategory-btn').forEach(b => {
                    b.classList.remove('selected-category');
                });
                
                // Activate the clicked tab
                btn.classList.add('active');
                const targetTabId = btn.getAttribute('data-tab');
                const targetTab = document.getElementById(targetTabId);
                if (targetTab) {
                    targetTab.classList.remove('hidden');
                }

                // Dynamically adjust admin dropdown options depending on active tab
                const linksSection = document.getElementById('dropdown-links-section');
                const policiesSection = document.getElementById('dropdown-policies-section');
                if (linksSection && policiesSection) {
                    if (targetTabId === 'tab-policies') {
                        linksSection.classList.add('hidden');
                        policiesSection.classList.remove('hidden');
                    } else {
                        linksSection.classList.remove('hidden');
                        policiesSection.classList.add('hidden');

                        // Dynamically update URLs with active category query param
                        const categoryName = btn.textContent.trim();
                        const addLinkBtn = document.getElementById('dropdown-add-link-btn');
                        const editLinkBtn = document.getElementById('dropdown-edit-link-btn');
                        const deleteLinkBtn = document.getElementById('dropdown-delete-link-btn');

                        if (addLinkBtn) {
                            addLinkBtn.href = `{{ route('information-hub.add') }}?category=${encodeURIComponent(categoryName)}`;
                        }
                        if (editLinkBtn) {
                            editLinkBtn.href = `{{ route('information-hub.edit-list') }}?category=${encodeURIComponent(categoryName)}`;
                        }
                        if (deleteLinkBtn) {
                            deleteLinkBtn.href = `{{ route('information-hub.edit-list') }}?category=${encodeURIComponent(categoryName)}`;
                        }
                    }
                }
            });
        });

        // Initialize collapsible subcategory sections
        document.querySelectorAll('.subcategory-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const linkList = document.getElementById(targetId);
                
                // Hide all link lists in this tab or overall
                document.querySelectorAll('.link-list').forEach(list => {
                    if (list !== linkList) {
                        list.classList.add('hidden');
                    }
                });
                
                // Reset all subcategory buttons selection styles in the current view
                document.querySelectorAll('.subcategory-btn').forEach(b => {
                    if (b !== this) {
                        b.classList.remove('selected-category');
                    }
                });

                if (linkList) {
                    const isHidden = linkList.classList.contains('hidden');
                    if (isHidden) {
                        linkList.classList.remove('hidden');
                        this.classList.add('selected-category');
                        
                        // Scroll to the links smoothly
                        setTimeout(() => {
                            linkList.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 100);
                    } else {
                        linkList.classList.add('hidden');
                        this.classList.remove('selected-category');
                    }
                }
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
        const allLinkContainers = document.querySelectorAll('.tab-content .link-card');
        let resultsHTML = '';
        let hasResults = false;

        allLinkContainers.forEach(container => {
            // Get the title element
            const titleElement = container.querySelector('.link-card-title');
            if (!titleElement) return;
            
            const title = titleElement.textContent.trim();
            const titleLower = title.toLowerCase();
            
            // Check if title matches search term
            if (titleLower.includes(searchTerm)) {
                hasResults = true;
                
                // Get image src, type, and link info via metadata
                const meta = container.querySelector('.link-meta');
                const imgType = meta ? meta.getAttribute('data-type') : '';
                const imgSrc = meta ? meta.getAttribute('data-image') : '';
                const link = container.querySelector('a[href]');
                const linkHref = link ? link.getAttribute('href') : '';
                
                // Get subcategory if exists
                let subCategoryOriginal = '';
                const linkListContainer = container.closest('.link-list');
                if (linkListContainer) {
                    const listId = linkListContainer.getAttribute('id');
                    const subCategoryBtn = document.querySelector(`.subcategory-btn[data-target="${listId}"]`);
                    if (subCategoryBtn) {
                        subCategoryOriginal = subCategoryBtn.textContent.trim();
                    }
                }
                
                let mediaHTML = '';
                if (imgType === 'Video' && imgSrc) {
                    mediaHTML = `
                        <div class="relative w-32 h-24 md:w-44 md:h-32 rounded-xl overflow-hidden shadow-sm border border-gray-200 flex-shrink-0 group">
                            <img src="${imgSrc}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="${title}" />
                            <div class="absolute inset-0 bg-black/35 flex items-center justify-center transition-colors duration-300 group-hover:bg-black/45">
                                <i class="bi bi-play-circle-fill text-3xl md:text-4xl text-white drop-shadow-sm"></i>
                            </div>
                        </div>
                    `;
                } else if (imgType === 'Video') {
                    mediaHTML = `
                        <div class="w-32 h-24 md:w-44 md:h-32 bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl shadow-sm flex items-center justify-center flex-shrink-0 text-red-700">
                            <i class="bi bi-play-circle-fill text-4xl md:text-5xl"></i>
                        </div>
                    `;
                } else {
                    let imageClass = 'w-28 h-28 md:w-36 md:h-36 object-cover rounded-xl shadow-md border border-gray-200 flex-shrink-0';
                    if (imgType === 'Document') {
                        imageClass = 'w-32 h-44 md:w-40 md:h-56 object-cover rounded-xl shadow-md border border-gray-200 flex-shrink-0';
                    }
                    mediaHTML = `<img src="${imgSrc}" class="${imageClass}" alt="${title}" data-type="${imgType}"/>`;
                }

                resultsHTML += `
                    <div class="link-card bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-row items-center gap-6 md:gap-8">
                        ${mediaHTML}
                        <div class="flex flex-col flex-grow justify-between min-h-[140px] py-1">
                            <div>
                                <h4 class="link-card-title text-lg md:text-2xl font-bold text-red-955 hover:text-red-700 transition leading-snug mb-2 line-clamp-2">
                                    ${highlightText(title, searchTerm)}
                                </h4>
                                <p class="text-xs md:text-sm text-gray-500 font-medium mb-4">
                                    ${subCategoryOriginal ? subCategoryOriginal : (imgType ? imgType : 'Access Resource')}
                                </p>
                            </div>
                            <div>
                                <a href="${linkHref}" target="_blank" class="inline-flex items-center text-xs md:text-sm font-semibold text-red-700 hover:text-red-900 transition-colors">
                                    [ Click here to view ]
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        // Search through policies stored in policiesData JS object
        for (const [categoryName, categoryPolicies] of Object.entries(policiesData)) {
            const policiesList = Object.values(categoryPolicies);
            policiesList.forEach(p => {
                const titleLower = p.title.toLowerCase();
                const descLower = (p.description || '').toLowerCase();
                
                if (titleLower.includes(searchTerm) || descLower.includes(searchTerm)) {
                    hasResults = true;
                    
                    // Determine matching category icon class
                    const iconClass = getCategoryIcon(categoryName);

                    resultsHTML += `
                        <div class="link-card bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-row items-center gap-6 md:gap-8">
                            <div class="w-28 h-28 md:w-36 md:h-36 rounded-xl flex items-center justify-center flex-shrink-0 animate-fade-in" style="background: rgba(112,18,29,0.08);">
                                <i class="bi ${iconClass} text-5xl" style="color:#70121D"></i>
                            </div>
                            <div class="flex flex-col flex-grow justify-between min-h-[140px] py-1">
                                <div>
                                    <h4 class="link-card-title text-lg md:text-2xl font-bold text-red-955 hover:text-red-700 transition leading-snug mb-2 line-clamp-2">
                                        ${highlightText(p.title, searchTerm)}
                                    </h4>
                                    <p class="text-xs md:text-sm text-gray-500 font-medium mb-4">
                                        ${categoryName} &bull; Policy
                                    </p>
                                </div>
                                <div>
                                    <a href="${p.url}" target="_blank" class="inline-flex items-center text-xs md:text-sm font-semibold text-red-700 hover:text-red-900 transition-colors">
                                        [ Click here to view ]
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
        }

        if (hasResults) {
            searchResultsContent.innerHTML = `
                <div style="border: 2px solid #70121D; border-radius: 0.75rem; padding: 1.5rem;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        ${resultsHTML}
                    </div>
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

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    .maroon {
        transition: 300ms;
    }

    .maroon:hover {
        background-color: #A84655;
    }
</style>