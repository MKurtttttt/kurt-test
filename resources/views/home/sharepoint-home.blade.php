@extends('layouts.home')

@section('title', 'SharePoint Sites')

@php
$iconGradients = [
    ['a' => '#c5a059', 'b' => '#d4af37'],
    ['a' => '#70121D', 'b' => '#9b1a2a'],
    ['a' => '#1d3557', 'b' => '#457b9d'],
    ['a' => '#2d6a4f', 'b' => '#52b788'],
    ['a' => '#3a0ca3', 'b' => '#7209b7'],
    ['a' => '#f77f00', 'b' => '#e63946'],
    ['a' => '#0077b6', 'b' => '#00b4d8'],
    ['a' => '#6b2d8b', 'b' => '#c77dff'],
    ['a' => '#2b9348', 'b' => '#55a630'],
    ['a' => '#9d0208', 'b' => '#dc2f02'],
];
@endphp

@section('content')

    {{-- Page Header --}}
    <div class="flex flex-col items-center justify-center pt-10 md:pt-14 px-4 relative z-10 w-full mb-10">
        <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] mb-4 px-4 py-1.5 rounded-full" style="background: rgba(197,160,89,0.22); color: #f0d080; border: 1px solid rgba(197,160,89,0.35);">
            <i class="fas fa-folder-open text-xs"></i>
            OIE &mdash; Resources
        </div>
        <h1 class="page-hero-title text-3xl md:text-4xl lg:text-5xl text-center mb-3">SharePoint Sites</h1>
        <p class="page-hero-sub text-base md:text-lg font-light text-center max-w-xl">
            Access institutional SharePoint sites and resources
        </p>
    </div>

    {{-- Main Container --}}
    <div class="w-full flex justify-center z-10 relative mb-16 px-4">
        <div class="w-full max-w-6xl shadow-2xl rounded-2xl overflow-hidden" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); border: 1px solid rgba(255,255,255,0.65); border-radius: 1rem;">

            {{-- Card Header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b" style="background: rgba(255,255,255,0.8); border-color: rgba(197,160,89,0.2);">
                <div class="w-1 h-6 rounded-full flex-shrink-0" style="background: linear-gradient(180deg, #c5a059, #d4af37);"></div>
                <span class="text-sm font-bold text-gray-700" style="font-family: 'Playfair Display', serif;">SharePoint Sites</span>
            </div>

            {{-- Centered Search Bar --}}
            <div style="display:flex; align-items:center; justify-content:center; padding: 12px 24px; border-bottom: 1px solid rgba(197,160,89,0.2); background: rgba(255,255,255,0.8);">
                <div style="position:relative; width:100%; max-width:440px;">
                    <div style="position:absolute; top:0; bottom:0; left:0; padding-left:12px; display:flex; align-items:center; pointer-events:none;">
                        <i class="fas fa-search" style="color:#9ca3af; font-size:0.7rem;"></i>
                    </div>
                    <input type="text" id="sharepoint-search" placeholder="Search departments, sites…"
                        style="width:100%; box-sizing:border-box; border-radius:8px; padding:7px 58px 7px 32px; font-size:0.75rem; font-family:inherit; border:1.5px solid #e5e7eb; background:rgba(248,248,248,0.95); color:#374151; outline:none; box-shadow:0 1px 3px rgba(0,0,0,0.06); transition:border-color 0.2s, box-shadow 0.2s;"
                        autocomplete="off"
                        onfocus="this.style.borderColor='#c5a059'; this.style.boxShadow='0 0 0 3px rgba(197,160,89,0.18)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)';">
                    <button type="button" id="clear-sharepoint-search"
                        style="position:absolute; right:6px; top:50%; transform:translateY(-50%); background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; border-radius:5px; padding:3px 10px; font-size:0.7rem; font-family:inherit; white-space:nowrap; cursor:pointer; transition:background 0.15s, color 0.15s;"
                        onmouseover="this.style.background='#e9eaed'; this.style.color='#374151';"
                        onmouseout="this.style.background='#f3f4f6'; this.style.color='#6b7280';">Clear</button>
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="px-6 border-b" style="border-color: rgba(197,160,89,0.15); background: rgba(250,249,247,0.6);">
                <ul class="flex gap-1">
                    @foreach ($categories as $i => $cat)
                        <li>
                            <button class="sp-tab-btn px-5 py-3 text-sm font-semibold transition-all duration-200 {{ $i === 0 ? 'sp-tab-active' : '' }}" data-tab="sptab-{{ Str::slug($cat, '-') }}">
                                {{ $cat }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Search Results Overlay --}}
            <div id="sp-search-results" class="hidden px-8 py-6" style="background: rgba(250,249,247,0.8); max-height: 520px; overflow-y: auto;">
                <h3 class="text-sm font-bold mb-4 text-gray-600 uppercase tracking-widest">Search Results</h3>
                <div id="sp-search-content" class="space-y-2"></div>
            </div>

            {{-- App Grid --}}
            <div id="sp-grid-ui">
                @foreach ($categories as $i => $cat)
                    <div id="sptab-{{ Str::slug($cat, '-') }}" class="sp-tab-content {{ $i === 0 ? '' : 'hidden' }} p-8 md:p-12">
                        <div class="flex flex-wrap gap-6 md:gap-8 justify-start">
                            @foreach (($linksByCategory[$cat] ?? []) as $department => $deptLinks)
                                @php
                                    $deptSlug     = Str::slug($department ?? 'uncategorized', '-');
                                    $catSlug      = Str::slug($cat, '-');
                                    $gi           = $loop->index % count($iconGradients);
                                    $grad         = $iconGradients[$gi];
                                    $cnt          = $deptLinks->count();
                                    $d            = strtolower($department ?? '');
                                    $dIcon = match(true) {
                                        str_contains($d, 'doc_log') || str_contains($d, 'doc log') || ($d === 'doc_log')
                                            => 'fa-folder-open',
                                        str_contains($d, 'iso') || str_contains($d, 'quality assur')
                                            => 'fa-certificate',
                                        str_contains($d, 'human resource') || str_contains($d, 'personnel') || str_contains($d, ' hr ')
                                            => 'fa-users',
                                        str_contains($d, 'finance') || str_contains($d, 'financial') || str_contains($d, 'budget') || str_contains($d, 'treasury') || str_contains($d, 'accounting')
                                            => 'fa-coins',
                                        str_contains($d, 'academic innovation') || str_contains($d, 'aie')
                                            => 'fa-suitcase',
                                        str_contains($d, 'records systems') || str_contains($d, 'rss')
                                            => 'fa-book',
                                        str_contains($d, 'record') || str_contains($d, 'system') || str_contains($d, 'registry') || str_contains($d, 'data manag')
                                            => 'fa-database',
                                        str_contains($d, 'student services & affairs') || str_contains($d, 'ssa')
                                            => 'fa-scroll',
                                        str_contains($d, 'student')
                                            => 'fa-user-graduate',
                                        str_contains($d, 'external') || str_contains($d, 'international') || str_contains($d, 'public relation') || str_contains($d, 'linkage')
                                            => 'fa-globe',
                                        str_contains($d, 'campus') || str_contains($d, 'facilit') || str_contains($d, 'maintenance') || str_contains($d, 'physical plant')
                                            => 'fa-wrench',
                                        str_contains($d, 'audit') || str_contains($d, 'compliance')
                                            => 'fa-clipboard-check',
                                        str_contains($d, 'academic') || str_contains($d, 'curriculum') || str_contains($d, 'instruction')
                                            => 'fa-graduation-cap',
                                        str_contains($d, 'christian') || str_contains($d, 'formation') || str_contains($d, 'ministry') || str_contains($d, 'theology') || str_contains($d, 'icfsi')
                                            => 'fa-church',
                                        str_contains($d, 'research') || str_contains($d, 'innovation') || (str_contains($d, 'institute') && !str_contains($d, 'institutional'))
                                            => 'fa-flask',
                                        str_contains($d, 'president') || str_contains($d, 'chancellor') || str_contains($d, 'rector') || str_contains($d, 'executive')
                                            => 'fa-landmark',
                                        str_contains($d, 'institutional') || str_contains($d, 'effectiveness') || str_contains($d, 'planning')
                                            => 'fa-chart-line',
                                        str_contains($d, 'library') || str_contains($d, 'knowledge')
                                            => 'fa-book-open',
                                        str_contains($d, 'admission')
                                            => 'fa-door-open',
                                        str_contains($d, 'office')
                                            => 'fa-building',
                                        default => 'fa-sitemap',
                                    };
                                @endphp
                                <button type="button"
                                    class="sp-app-btn group flex flex-col items-center gap-2.5"
                                    data-tpl="sptpl-{{ $catSlug }}-{{ $deptSlug }}"
                                    style="width: 100px;">
                                    {{-- App Icon --}}
                                    <div class="sp-app-icon" style="background: linear-gradient(145deg, {{ $grad['a'] }} 0%, {{ $grad['b'] }} 100%);">
                                        {{-- Gloss highlight --}}
                                        <div class="sp-app-gloss"></div>
                                        {{-- Icon --}}
                                        <i class="fas {{ $dIcon }} text-white" style="font-size: 1.6rem; position: relative; z-index: 1;"></i>
                                        {{-- Count badge --}}
                                        <span class="sp-app-badge">{{ $cnt }}</span>
                                    </div>
                                    <span class="sp-app-label">{{ str_replace('(CFS)', '(ICFSI)', $department ?? 'Uncategorized') }}</span>
                                </button>
                            @endforeach
                        </div>

                        @if (empty($linksByCategory[$cat]) || count($linksByCategory[$cat]) === 0)
                            <div class="flex flex-col items-center justify-center text-center text-gray-400 py-16">
                                <i class="fas fa-folder-open text-5xl mb-4 opacity-30"></i>
                                <p class="text-sm">No sites available in this category.</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Modal templates (hidden from view) --}}
            <div style="display:none;" aria-hidden="true">
                @foreach ($categories as $cat)
                    @foreach (($linksByCategory[$cat] ?? []) as $department => $deptLinks)
                        @php
                            $deptSlug = Str::slug($department ?? 'uncategorized', '-');
                            $catSlug  = Str::slug($cat, '-');
                            $offices  = $deptLinks->groupBy('office');
                            $gi       = $loop->index % count($iconGradients);
                            $grad     = $iconGradients[$gi];
                        @endphp
                        <template id="sptpl-{{ $catSlug }}-{{ $deptSlug }}">
                            <div class="flex items-center gap-4 mb-6 pb-4" style="border-bottom: 1px solid rgba(197,160,89,0.15);">
                                <div class="flex-shrink-0 flex items-center justify-center text-white rounded-2xl shadow-md" style="width:52px; height:52px; background: linear-gradient(145deg, {{ $grad['a'] }}, {{ $grad['b'] }});">
                                    <i class="fas fa-folder-open" style="font-size:1.3rem;"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-800" style="font-family:'Playfair Display',serif;">{{ str_replace('(CFS)', '(ICFSI)', $department ?? 'Uncategorized Department') }}</h2>
                                    <p class="text-xs text-gray-400">{{ $deptLinks->count() }} {{ Str::plural('site', $deptLinks->count()) }}</p>
                                </div>
                            </div>
                            <div class="space-y-6">
                                @foreach ($offices as $office => $officeLinks)
                                    <div>
                                        @if ($office)
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="w-5 h-5 rounded flex items-center justify-center text-xs" style="background: rgba(197,160,89,0.15); color: #c5a059;"><i class="fas fa-layer-group"></i></span>
                                                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ str_replace('CFS-', 'ICFSI-', $office) }}</h4>
                                                <div class="flex-1 h-px bg-gray-100"></div>
                                            </div>
                                        @endif
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach ($officeLinks as $link)
                                                <a href="{{ $link->url }}" target="_blank" title="{{ $link->description }}"
                                                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-white hover:shadow-md transition-all duration-200 group"
                                                    style="text-decoration:none;">
                                                    <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center" style="background: rgba(112,18,29,0.07);">
                                                        <i class="fab fa-microsoft text-sm" style="color:#70121D;"></i>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700 leading-tight flex-1 group-hover:text-[#70121D] transition-colors">{{ $link->label }}</span>
                                                    <i class="fas fa-arrow-up-right-from-square text-xs text-gray-300 group-hover:text-[#c5a059] transition-all flex-shrink-0"></i>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </template>
                    @endforeach
                @endforeach
            </div>

        </div>
    </div>

    {{-- Modal Overlay (inline styles = reliable) --}}
    <div id="sp-modal-overlay"
        style="display:none; opacity:0; pointer-events:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem; background:rgba(15,6,2,0.65); backdrop-filter:blur(6px); transition:opacity 0.25s ease;">
        <div id="sp-modal-box"
            style="transform:scale(0.93); opacity:0; transition:transform 0.25s ease, opacity 0.25s ease; width:100%; max-width:680px; max-height:85vh; background:#fff; border-radius:1.25rem; box-shadow:0 25px 60px rgba(0,0,0,0.35); display:flex; flex-direction:column; overflow:hidden;">
            {{-- Modal Header --}}
            <div style="display:flex; justify-content:flex-end; padding:0.5rem; border-bottom:1px solid #f3f4f6; flex-shrink:0;">
                <button type="button" id="sp-modal-close"
                    style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:50%; border:none; background:transparent; cursor:pointer; color:#9ca3af; transition:background 0.15s, color 0.15s;"
                    onmouseover="this.style.background='#f3f4f6';this.style.color='#374151';"
                    onmouseout="this.style.background='transparent';this.style.color='#9ca3af';">
                    <i class="fas fa-times" style="font-size:0.85rem;"></i>
                </button>
            </div>
            {{-- Modal Body --}}
            <div id="sp-modal-body" style="padding:1.5rem; overflow-y:auto; scrollbar-width:thin;"></div>
        </div>
    </div>

@push('scripts')
<script>
(function() {
    // ── Tabs ─────────────────────────────────────────
    document.querySelectorAll('.sp-tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            spClearSearch();
            document.querySelectorAll('.sp-tab-btn').forEach(function(b) {
                b.classList.remove('sp-tab-active');
            });
            document.querySelectorAll('.sp-tab-content').forEach(function(tc) {
                tc.classList.add('hidden');
            });
            this.classList.add('sp-tab-active');
            var target = document.getElementById(this.getAttribute('data-tab'));
            if (target) target.classList.remove('hidden');
        });
    });

    // ── Modal ─────────────────────────────────────────
    var overlay   = document.getElementById('sp-modal-overlay');
    var modalBox  = document.getElementById('sp-modal-box');
    var modalBody = document.getElementById('sp-modal-body');
    var closeBtn  = document.getElementById('sp-modal-close');

    function spOpenModal(tplId) {
        var tpl = document.getElementById(tplId);
        if (!tpl) { return; }
        modalBody.innerHTML = tpl.innerHTML;

        overlay.style.display = 'flex';
        // Force reflow before opacity transition
        overlay.getBoundingClientRect();
        overlay.style.opacity = '1';
        overlay.style.pointerEvents = 'auto';
        modalBox.style.transform = 'scale(1)';
        modalBox.style.opacity = '1';
        document.body.style.overflow = 'hidden';
    }

    function spCloseModal() {
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        modalBox.style.transform = 'scale(0.93)';
        modalBox.style.opacity = '0';
        document.body.style.overflow = '';
        setTimeout(function() {
            overlay.style.display = 'none';
            modalBody.innerHTML = '';
        }, 260);
    }

    document.querySelectorAll('.sp-app-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            spOpenModal(this.getAttribute('data-tpl'));
        });
    });

    closeBtn.addEventListener('click', spCloseModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) spCloseModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.style.opacity === '1') spCloseModal();
    });

    // ── Search ────────────────────────────────────────
    var searchInput   = document.getElementById('sharepoint-search');
    var clearBtn      = document.getElementById('clear-sharepoint-search');
    var searchBox     = document.getElementById('sp-search-results');
    var searchContent = document.getElementById('sp-search-content');
    var gridUI        = document.getElementById('sp-grid-ui');

    function spPerformSearch() {
        var term = searchInput.value.toLowerCase().trim();
        if (term === '') { spClearSearch(); return; }

        gridUI.style.display = 'none';
        searchBox.classList.remove('hidden');
        searchContent.innerHTML = '';

        var allTpls = document.querySelectorAll('[id^="sptpl-"]');
        var found = false;

        allTpls.forEach(function(tpl) {
            var tmp = document.createElement('div');
            tmp.innerHTML = tpl.innerHTML;
            var deptH = tmp.querySelector('h2');
            var deptName = deptH ? deptH.textContent.trim() : '';
            var links = tmp.querySelectorAll('a[href]');

            links.forEach(function(link) {
                var href = link.getAttribute('href') || '';
                if (!href || href === '#') return;
                var label = link.textContent.trim().toLowerCase();
                var title = (link.getAttribute('title') || '').toLowerCase();
                var office = '';
                var og = link.closest('[class]');
                if (og) {
                    var h4 = og.querySelector('h4');
                    if (h4) office = h4.textContent.trim();
                }
                if (label.includes(term) || title.includes(term) || deptName.toLowerCase().includes(term) || office.toLowerCase().includes(term)) {
                    found = true;
                    var el = document.createElement('div');
                    el.style.cssText = 'display:flex; align-items:center; gap:12px; padding:12px; background:#fff; border-radius:12px; border:1px solid #f3f4f6;';
                    el.innerHTML =
                        '<div style="width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(112,18,29,0.07); flex-shrink:0;">' +
                            '<i class="fab fa-microsoft" style="color:#70121D; font-size:0.85rem;"></i>' +
                        '</div>' +
                        '<div style="flex:1; min-width:0;">' +
                            '<a href="' + link.href + '" target="_blank" style="font-size:0.875rem; font-weight:600; color:#70121D; text-decoration:none; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">' + spHighlight(link.textContent.trim(), term) + '</a>' +
                            '<p style="font-size:0.7rem; color:#9ca3af; margin:2px 0 0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">' + deptName + (office ? ' · ' + office : '') + '</p>' +
                        '</div>' +
                        '<i class="fas fa-external-link-alt" style="font-size:0.7rem; color:#d1d5db; flex-shrink:0;"></i>';
                    searchContent.appendChild(el);
                }
            });
        });

        if (!found) {
            searchContent.innerHTML = '<div style="padding:2rem; text-align:center; color:#9ca3af; font-size:0.875rem;">No sites found matching your search.</div>';
        }
    }

    function spClearSearch() {
        searchInput.value = '';
        searchBox.classList.add('hidden');
        gridUI.style.display = '';
    }

    function spHighlight(text, term) {
        var esc = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return text.replace(new RegExp('(' + esc + ')', 'gi'), '<mark style="background:#8B1538;color:#fff;padding:0.05em 0.2em;border-radius:0.2em;">$1</mark>');
    }

    searchInput.addEventListener('input', spPerformSearch);
    clearBtn.addEventListener('click', function() { spClearSearch(); searchInput.focus(); });
})();
</script>
@endpush

@push('head')
<style>
    .sp-tab-btn {
        background: transparent;
        color: #6b7280;
        border-bottom: 2px solid transparent;
        cursor: pointer;
    }
    .sp-tab-btn:hover:not(.sp-tab-active) { color: #1f2937; background: rgba(0,0,0,0.03); }
    .sp-tab-active { color: #70121D !important; background: white !important; border-bottom-color: #70121D !important; }

    .sp-tab-content { animation: spFadeIn 0.22s ease-out; }
    @keyframes spFadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* ── App Icons ── */
    .sp-app-btn { cursor: pointer; background: transparent; border: none; padding: 0; }

    .sp-app-icon {
        position: relative;
        width: 72px;
        height: 72px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(0,0,0,0.22), 0 1px 3px rgba(0,0,0,0.15);
        overflow: hidden;
        transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s ease;
        will-change: transform;
    }
    .sp-app-btn:hover .sp-app-icon {
        transform: translateY(-5px) scale(1.06);
        box-shadow: 0 10px 28px rgba(0,0,0,0.28), 0 2px 6px rgba(0,0,0,0.15);
    }

    /* Glass gloss: top 45% highlight */
    .sp-app-gloss {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 45%;
        background: linear-gradient(180deg, rgba(255,255,255,0.32) 0%, transparent 100%);
        border-radius: 20px 20px 0 0;
        pointer-events: none;
        z-index: 2;
    }

    /* Count badge */
    .sp-app-badge {
        position: absolute;
        top: -5px; right: -5px;
        background: rgba(255,255,255,0.95);
        color: #70121D;
        font-size: 9px;
        font-weight: 800;
        line-height: 1;
        padding: 2px 5px;
        border-radius: 9px;
        border: 1.5px solid rgba(255,255,255,0.6);
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
        z-index: 3;
    }

    /* Label */
    .sp-app-label {
        font-size: 10.5px;
        font-weight: 600;
        color: #374151;
        text-align: center;
        line-height: 1.4;
        max-width: 100px;
        word-break: break-word;
        transition: color 0.15s;
    }
    .sp-app-btn:hover .sp-app-label { color: #70121D; }

    #sharepoint-search:focus {
        border-color: #c5a059 !important;
        box-shadow: 0 0 0 3px rgba(197,160,89,0.18) !important;
        outline: none;
    }
    #clear-sharepoint-search:hover { background: #e5e7eb !important; }
</style>
@endpush

@endsection

@php
$useWhiteOverlay = false;
@endphp
