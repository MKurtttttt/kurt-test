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
                    </div>
                @endforeach
            </div>

        </div>
    </div>

@push('scripts')
<script>
(function () {
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

        // Hide/show entire sections if all their cards are hidden
        document.querySelectorAll('.ih-section').forEach(function (section) {
            var visibleCards = section.querySelectorAll('.ih-resource-card:not([style*="display: none"]):not([style*="display:none"])');
            // Re-check manually
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
</style>
@endpush

@endsection

@php
$useWhiteOverlay = false;
@endphp
