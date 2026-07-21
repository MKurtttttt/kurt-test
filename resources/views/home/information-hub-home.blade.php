@extends('layouts.home')

@section('title', 'Information Hub')

@section('content')

    {{-- Page Header --}}
    <div class="flex flex-col items-center justify-center pt-10 md:pt-14 px-4 relative z-10 w-full mb-10">
        <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] mb-4 px-4 py-1.5 rounded-full" style="background: rgba(197,160,89,0.22); color: #f0d080; border: 1px solid rgba(197,160,89,0.35);">
            <i class="fas fa-book-open text-xs"></i>
            OIE &mdash; Knowledge
        </div>
        <h1 class="page-hero-title text-3xl md:text-4xl lg:text-5xl text-center mb-3">Information Hub</h1>
        <p class="page-hero-sub text-base md:text-lg font-light text-center max-w-xl">
            Institutional documents, videos, and resources in one place
        </p>
    </div>

    {{-- Main Container --}}
    <div class="w-full flex justify-center z-10 relative mb-16 px-4">
        <div class="w-full max-w-6xl shadow-2xl rounded-2xl overflow-hidden" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); border: 1px solid rgba(255,255,255,0.65); border-radius: 1rem;">

            {{-- Card Header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b" style="background: rgba(255,255,255,0.8); border-color: rgba(197,160,89,0.2);">
                <div class="w-1 h-6 rounded-full flex-shrink-0" style="background: linear-gradient(180deg, #c5a059, #d4af37);"></div>
                <span class="text-sm font-bold text-gray-700" style="font-family: 'Playfair Display', serif;">Information Hub</span>
            </div>

            {{-- Centered Search Bar --}}
            <div style="display:flex; align-items:center; justify-content:center; padding:12px 24px; border-bottom:1px solid rgba(197,160,89,0.2); background:rgba(255,255,255,0.8);">
                <div style="position:relative; width:100%; max-width:440px;">
                    <div style="position:absolute; top:0; bottom:0; left:0; padding-left:12px; display:flex; align-items:center; pointer-events:none;">
                        <i class="fas fa-search" style="color:#9ca3af; font-size:0.7rem;"></i>
                    </div>
                    <input type="text" id="ih-search" placeholder="Search resources, documents&hellip;"
                        style="width:100%; box-sizing:border-box; border-radius:8px; padding:7px 58px 7px 32px; font-size:0.75rem; font-family:inherit; border:1.5px solid #e5e7eb; background:rgba(248,248,248,0.95); color:#374151; outline:none; box-shadow:0 1px 3px rgba(0,0,0,0.06); transition:border-color 0.2s, box-shadow 0.2s;"
                        autocomplete="off"
                        onfocus="this.style.borderColor='#c5a059'; this.style.boxShadow='0 0 0 3px rgba(197,160,89,0.18)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)';">
                    <button type="button" id="ih-clear"
                        style="position:absolute; right:6px; top:50%; transform:translateY(-50%); background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; border-radius:5px; padding:3px 10px; font-size:0.7rem; font-family:inherit; white-space:nowrap; cursor:pointer; transition:background 0.15s, color 0.15s;"
                        onmouseover="this.style.background='#e9eaed'; this.style.color='#374151';"
                        onmouseout="this.style.background='#f3f4f6'; this.style.color='#6b7280';">Clear</button>
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="border-b" style="border-color: rgba(197,160,89,0.15); background: rgba(250,249,247,0.6);">
                <ul class="flex gap-1 px-6">
                    @foreach ($categories as $i => $cat)
                        <li>
                            <button class="ih-tab-btn px-5 py-3 text-sm font-semibold transition-all duration-200 {{ $i === 0 ? 'ih-tab-active' : '' }}"
                                data-tab="ihtab-{{ Str::slug($cat, '-') }}">
                                {{ $cat }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- No Results Message (hidden by default) --}}
            <div id="ih-no-results" style="display:none; padding:3rem; text-align:center; color:#9ca3af;">
                <i class="fas fa-search" style="font-size:2rem; margin-bottom:0.75rem; opacity:0.3; display:block;"></i>
                <p style="font-size:0.875rem;">No resources found matching your search.</p>
            </div>

            {{-- Tab Content Panels --}}
            <div id="ih-tabs-container">
                @foreach ($categories as $i => $cat)
                    <div id="ihtab-{{ Str::slug($cat, '-') }}"
                         class="ih-tab-content {{ $i !== 0 ? 'hidden' : '' }}"
                         style="padding: 2rem 2.5rem;">

                        @if (strtolower($cat) === 'policies')
                            {{-- Custom Grid Layout for Policies --}}
                            @if ($policyCategories->isEmpty())
                                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; color:#9ca3af; padding:4rem 0;">
                                    <i class="fas fa-folder-open" style="font-size:3rem; margin-bottom:1rem; opacity:0.3;"></i>
                                    <p style="font-size:0.875rem;">No policies available in this category yet.</p>
                                </div>
                            @else
                                <p class="text-xs text-gray-400 mb-5 uppercase tracking-widest font-semibold">Browse by Category</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 animate-fade-in">
                                    @foreach ($policyCategories as $catRecord)
                                        @php
                                            $categoryName = $catRecord->name;
                                            $catPolicies = $policies[$categoryName] ?? collect();
                                            $categoryIcons = [
                                                'academic'   => 'fa-book-open',
                                                'admin'      => 'fa-building',
                                                'research'   => 'fa-magnifying-glass',
                                                'employment' => 'fa-briefcase',
                                                'safety'     => 'fa-shield-alt',
                                                'tech'       => 'fa-microchip',
                                                'student'    => 'fa-graduation-cap',
                                                'quality'    => 'fa-square-check',
                                                'governance' => 'fa-users',
                                                'default'    => 'fa-file-lines',
                                            ];

                                            $catLower = strtolower($categoryName ?? 'uncategorized');
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

                                            $count = count($catPolicies);
                                            $label = $categoryName ?: 'General';

                                            $searchHaystack = strtolower($label);
                                            foreach ($catPolicies as $p) {
                                                $searchHaystack .= ' ' . strtolower($p->title) . ' ' . strtolower($p->description ?? '') . ' ' . strtolower($p->document_code ?? '');
                                            }
                                        @endphp
                                         <button type="button"
                                             class="policy-category-card group relative overflow-hidden rounded-3xl text-left transition-all duration-300 focus:outline-none"
                                             onclick="openHomePolicyModal('{{ addslashes($label) }}')"
                                             data-category="{{ $label }}"
                                             data-search="{{ $searchHaystack }}">
                                             
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
                                                     <i class="fas {{ $iconClass }} text-lg" style="color: #e6c17a;"></i>
                                                 </div>
                                                 {{-- Text --}}
                                                 <div class="flex-1 min-w-0 pr-8">
                                                     <h3 class="font-bold text-white text-sm md:text-base uppercase tracking-wider leading-snug mb-0.5 transition-colors duration-300 group-hover:text-[#e6c17a]" style="color: #ffffff !important; font-family: 'Inter', sans-serif; margin: 0 0 2px;">
                                                         {{ $label }}
                                                     </h3>
                                                     <p class="text-[13px] font-medium flex items-center gap-1.5" style="color: rgba(255,255,255,0.6); margin: 0;">
                                                         <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#e6c17a] opacity-75"></span>
                                                         {{ $count }} {{ Str::plural('policy', $count) }}
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
                        @else
                            @php $hasAny = !empty($linksByCategory[$cat]) && count($linksByCategory[$cat]) > 0; @endphp

                            @if (!$hasAny)
                            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; color:#9ca3af; padding:4rem 0;">
                                <i class="fas fa-folder-open" style="font-size:3rem; margin-bottom:1rem; opacity:0.3;"></i>
                                <p style="font-size:0.875rem;">No resources available in this category yet.</p>
                            </div>
                        @else
                            @foreach (($linksByCategory[$cat] ?? []) as $subCat => $subLinks)
                                @php
                                    $firstType = strtolower($subLinks->first()?->type ?? '');
                                    $subIcon = match($firstType) {
                                        'video'    => 'fa-play-circle',
                                        'document' => 'fa-file-lines',
                                        default    => 'fa-folder-open',
                                    };
                                @endphp

                                {{-- Sub-category section --}}
                                <div class="ih-section" style="margin-bottom: 2.5rem;">
                                    @if ($subCat)
                                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:1rem;">
                                            <span style="display:flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:6px; background:rgba(197,160,89,0.15); color:#c5a059; font-size:0.7rem; flex-shrink:0;">
                                                <i class="fas {{ $subIcon }}"></i>
                                            </span>
                                            <h3 style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280;">{{ $subCat }}</h3>
                                            <div style="flex:1; height:1px; background:#f3f4f6;"></div>
                                            <span style="font-size:0.7rem; color:#d1d5db; flex-shrink:0;">{{ count($subLinks) }} {{ Str::plural('item', count($subLinks)) }}</span>
                                        </div>
                                    @endif

                                    <div class="ih-cards-grid">
                                        @foreach ($subLinks as $link)
                                            @php
                                                $type       = strtolower($link->type ?? 'document');
                                                $typeIcon   = match($type) { 'video' => 'fas fa-play-circle', 'document' => 'fas fa-file-pdf', default => 'fas fa-link' };
                                                $badgeBg    = match($type) { 'video' => 'rgba(112,18,29,0.08)', 'document' => 'rgba(197,160,89,0.12)', default => 'rgba(107,114,128,0.1)' };
                                                $badgeColor = match($type) { 'video' => '#70121D', 'document' => '#8a6a1e', default => '#4b5563' };
                                                $badgeLabel = match($type) { 'video' => 'Video', 'document' => 'Document', default => ucfirst($type) };
                                            @endphp
                                            <a href="{{ $link->url }}" target="_blank"
                                                class="ih-resource-card"
                                                title="{{ $link->description ?? $link->title }}"
                                                data-search="{{ strtolower($link->title . ' ' . ($link->description ?? '') . ' ' . ($subCat ?? '') . ' ' . $cat) }}">

                                                {{-- Thumbnail --}}
                                                <div class="ih-card-thumb">
                                                    @if ($link->image_path)
                                                        <img src="{{ asset($link->image_path) }}" alt="{{ $link->title }}" loading="lazy"
                                                            style="width:100%; height:100%; object-fit:cover;">
                                                    @else
                                                        <div class="ih-card-thumb-placeholder">
                                                            <i class="{{ $typeIcon }}" style="font-size:2rem; color:rgba(197,160,89,0.4);"></i>
                                                        </div>
                                                    @endif
                                                    {{-- Type badge overlay --}}
                                                    <span class="ih-type-badge" style="background:{{ $badgeBg }}; color:{{ $badgeColor }}; border-color:{{ $badgeColor }}33;">
                                                        <i class="{{ $typeIcon }}" style="font-size:8px;"></i>
                                                        {{ $badgeLabel }}
                                                    </span>
                                                </div>

                                                {{-- Body --}}
                                                <div class="ih-card-body">
                                                    <p class="ih-card-title">{{ $link->title }}</p>
                                                    @if ($link->description)
                                                        <p class="ih-card-desc">{{ $link->description }}</p>
                                                    @endif
                                                    <div class="ih-card-cta">
                                                        <i class="fas fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                                                        Open resource
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        @endif
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- Policy Category Modal overlay for Viewer Homepage --}}
    <div id="ih-policy-modal-overlay"
        class="fixed inset-0 z-50 flex items-center justify-center hidden"
        style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); margin: 0 !important; outline: none; opacity: 0; transition: opacity 0.25s ease;"
        onclick="closeHomePolicyModalOnOverlay(event)">
        
        <div id="ih-policy-modal"
            class="bg-white rounded-2xl shadow-2xl w-full mx-4 overflow-hidden flex flex-col transition-all duration-300"
            style="max-width: 800px; max-height: 85vh; border-radius: 1.5rem;">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"
                style="background: linear-gradient(135deg, #70121D 0%, #4a070f 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.15)">
                        <i id="ih-policy-modal-icon" class="fas fa-file-pdf text-base text-white"></i>
                    </div>
                    <div style="text-align: left;">
                        <p class="text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.65); margin: 0 0 2px;">Policies Category</p>
                        <h2 id="ih-policy-modal-title" class="text-base font-bold text-white leading-tight" style="color: #ffffff !important; margin: 0;"></h2>
                    </div>
                </div>
                <button onclick="closeHomePolicyModal()" class="w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-200" style="background:rgba(255,255,255,0.15); color:white; border:none; cursor:pointer;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div id="ih-policy-modal-body" class="overflow-y-auto flex-1 p-6 bg-gray-50/50">
                {{-- Dynamically populated by JS --}}
            </div>
        </div>
    </div>

@push('scripts')
<script>
(function () {
    // ── Policies Data for Modal ────────────────────────────
    @php
        $policiesForJs = [];
        foreach ($policyCategories as $catRecord) {
            $catName = $catRecord->name;
            $items = $policies[$catName] ?? collect();
            $policiesForJs[$catName] = $items->map(function($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'url' => $p->url,
                    'description' => $p->description,
                    'document_code' => $p->document_code,
                    'revision_count' => $p->revision_count,
                    'effectivity_date' => $p->effectivity_date ? date('n/j/Y', strtotime($p->effectivity_date)) : null,
                    'policy_date' => $p->policy_date,
                ];
            });
        }
    @endphp
    const policiesData = @json($policiesForJs);

    function getHomeCategoryIcon(catName) {
        const catLower = catName.toLowerCase();
        if (catLower.includes('academic')) return 'fa-book-open';
        if (catLower.includes('admin') || catLower.includes('business') || catLower.includes('finance')) return 'fa-building';
        if (catLower.includes('research')) return 'fa-magnifying-glass';
        if (catLower.includes('employ') || catLower.includes('human')) return 'fa-briefcase';
        if (catLower.includes('safe') || catLower.includes('security') || catLower.includes('health')) return 'fa-shield-alt';
        if (catLower.includes('tech') || catLower.includes('system') || /\bit\b/.test(catLower)) return 'fa-microchip';
        if (catLower.includes('student') || catLower.includes('education') || catLower.includes('learn')) return 'fa-graduation-cap';
        if (catLower.includes('quality') || catLower.includes('audit') || catLower.includes('iso') || catLower.includes('compliance') || catLower.includes('qms') || catLower.includes('iqa')) return 'fa-square-check';
        if (catLower.includes('board') || catLower.includes('governance') || catLower.includes('regent')) return 'fa-users';
        return 'fa-file-lines';
    }

    window.openHomePolicyModal = function(categoryName) {
        const overlay = document.getElementById('ih-policy-modal-overlay');
        const title = document.getElementById('ih-policy-modal-title');
        const body = document.getElementById('ih-policy-modal-body');
        const modalIcon = document.getElementById('ih-policy-modal-icon');

        title.textContent = categoryName;

        const iconClass = getHomeCategoryIcon(categoryName);
        if (modalIcon) {
            modalIcon.className = `fas ${iconClass} text-base text-white`;
        }

        const items = [...(policiesData[categoryName] ?? [])];
        // Sort alphabetically by title (A-Z)
        items.sort((a, b) => a.title.localeCompare(b.title, undefined, {numeric: true, sensitivity: 'base'}));

        if (items.length === 0) {
            body.innerHTML = `
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:4rem 0; color:#9ca3af; text-align:center; width:100%;">
                    <i class="fas fa-folder-open" style="font-size:3rem; margin-bottom:1rem; opacity:0.3;"></i>
                    <p style="font-size:0.875rem;">No policies available in this category yet.</p>
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
                        <div class="flex items-center mb-4" style="text-align: left;">
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
                                const typeIcon = 'fas fa-file-pdf';
                                const hasUrl = p.url && p.url.trim() !== '';
                                
                                return hasUrl ? `
                                    <a href="${p.url}" target="_blank" class="ih-resource-card" title="${p.description ?? p.title}">
                                        {{-- Thumbnail --}}
                                        <div class="ih-card-thumb">
                                            <div class="ih-card-thumb-placeholder">
                                                <i class="${typeIcon}" style="font-size:2rem; color:rgba(197,160,89,0.4);"></i>
                                            </div>
                                            {{-- Type badge overlay --}}
                                            <span class="ih-type-badge" style="background:${badgeBg}; color:${badgeColor}; border-color:${badgeColor}33;">
                                                <i class="${typeIcon}" style="font-size:8px;"></i>
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
                                                <i class="fas fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                                                Open resource
                                            </div>
                                        </div>
                                    </a>
                                ` : `
                                    <div class="ih-resource-card opacity-75" style="cursor: default;" title="${p.description ?? p.title}">
                                        {{-- Thumbnail --}}
                                        <div class="ih-card-thumb">
                                            <div class="ih-card-thumb-placeholder" style="background: linear-gradient(135deg, rgba(156,163,175,0.08), rgba(156,163,175,0.05));">
                                                <i class="fas fa-file-pdf" style="font-size:2rem; color:rgba(156,163,175,0.4);"></i>
                                            </div>
                                            {{-- Type badge overlay --}}
                                            <span class="ih-type-badge" style="background:rgba(156,163,175,0.12); color:#6b7280; border-color:rgba(156,163,175,0.2);">
                                                <i class="fas fa-clock" style="font-size:8px;"></i>
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
                                                <i class="fas fa-exclamation-circle" style="font-size:9px;"></i>
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
        const modal = document.getElementById('ih-policy-modal');
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

    window.closeHomePolicyModal = function() {
        const overlay = document.getElementById('ih-policy-modal-overlay');
        const modal = document.getElementById('ih-policy-modal');

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

    window.closeHomePolicyModalOnOverlay = function(event) {
        if (event.target === document.getElementById('ih-policy-modal-overlay')) {
            closeHomePolicyModal();
        }
    }

    // ── Tabs ──────────────────────────────────────────────
    document.querySelectorAll('.ih-tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('ih-search').value = '';
            ihRunSearch('');
            document.querySelectorAll('.ih-tab-btn').forEach(function (b) { b.classList.remove('ih-tab-active'); });
            document.querySelectorAll('.ih-tab-content').forEach(function (tc) { tc.classList.add('hidden'); });
            this.classList.add('ih-tab-active');
            var t = document.getElementById(this.getAttribute('data-tab'));
            if (t) t.classList.remove('hidden');
        });
    });

    // ── Search ─────────────────────────────────────────────
    var searchInput = document.getElementById('ih-search');
    var clearBtn    = document.getElementById('ih-clear');
    var noResults   = document.getElementById('ih-no-results');

    function ihRunSearch(term) {
        term = term.toLowerCase().trim();
        var anyVisible = false;

        document.querySelectorAll('.ih-resource-card').forEach(function (card) {
            var haystack = card.getAttribute('data-search') || '';
            var show = term === '' || haystack.includes(term);
            card.style.display = show ? '' : 'none';
            if (show) anyVisible = true;
        });

        // Filter category cards under Policies tab
        document.querySelectorAll('.policy-category-card').forEach(function (card) {
            var haystack = card.getAttribute('data-search') || '';
            var show = term === '' || haystack.includes(term);
            card.style.display = show ? '' : 'none';
            if (show) anyVisible = true;
        });

        // Hide/show entire sections if all their cards are hidden
        document.querySelectorAll('.ih-section').forEach(function (section) {
            var visibleCards = section.querySelectorAll('.ih-resource-card:not([style*="display: none"]):not([style*="display:none"])');
            var hasVisible = false;
            section.querySelectorAll('.ih-resource-card').forEach(function(c){
                if (c.style.display !== 'none') hasVisible = true;
            });
            section.style.display = hasVisible ? '' : 'none';
        });

        noResults.style.display = (term !== '' && !anyVisible) ? 'block' : 'none';
    }

    searchInput.addEventListener('input', function () { ihRunSearch(this.value); });
    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        ihRunSearch('');
        searchInput.focus();
    });
})();
</script>
@endpush

@push('head')
<style>
    /* Tabs */
    .ih-tab-btn {
        background: transparent;
        color: #6b7280;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
    }
    .ih-tab-btn:hover:not(.ih-tab-active) { color: #1f2937; background: rgba(0,0,0,0.03); }
    .ih-tab-active { color: #70121D !important; background: white !important; border-bottom-color: #70121D !important; }
    .ih-tab-content { animation: ihFadeIn 0.22s ease-out; }
    @keyframes ihFadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

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
    .policy-category-card i.fas {
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: inline-block;
    }
    .policy-category-card:hover i.fas {
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

    /* Scrollbar for modal */
    #ih-policy-modal-body::-webkit-scrollbar { width: 6px; }
    #ih-policy-modal-body::-webkit-scrollbar-track { background: #f9fafb; }
    #ih-policy-modal-body::-webkit-scrollbar-thumb { background: #e0b4b9; border-radius: 4px; }
    #ih-policy-modal-body::-webkit-scrollbar-thumb:hover { background: #70121D; }
</style>
@endpush

@endsection

@php
$useWhiteOverlay = false;
@endphp
