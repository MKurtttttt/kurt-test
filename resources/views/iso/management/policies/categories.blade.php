<x-app-layout>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

    .cat-page * { font-family: 'Inter', sans-serif; }

    /* Clean gradient page background without polkadots */
    .cat-page {
        min-height: 100vh;
        background: radial-gradient(circle at 10% 20%, #FAF8F5 0%, #EFE9DE 100%);
        padding: 3rem 0;
    }

    /* Left sidebar items */
    .cat-item {
        position: relative;
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        border-bottom: 1px solid #f0e8dd;
        padding: 0.95rem 1.15rem;
        cursor: pointer;
        display: block;
        transition: all 0.2s ease;
    }
    .cat-item:hover {
        background: #fdfbf9;
    }
    .cat-item.active {
        background: #fff;
        border-right: 1px solid #fff;
        margin-right: -1px;
        z-index: 10;
        box-shadow: -4px 0 15px rgba(0,0,0,0.015), 0 4px 12px rgba(0,0,0,0.01);
    }
    .cat-item.active .cat-bar {
        opacity: 1;
        transform: scaleY(1);
    }
    .cat-item.active .cat-icon-box {
        background: #70121D;
        color: #fff;
        transform: scale(1.05);
    }
    .cat-item.active .cat-title-text {
        color: #70121D;
        font-weight: 800;
    }
    .cat-item.active .cat-count-badge {
        background: #70121D;
        color: #fff;
    }

    /* Selection Accent Line */
    .cat-bar {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #70121D;
        border-radius: 0 4px 4px 0;
        opacity: 0;
        transform: scaleY(0.4);
        transition: all 0.2s ease;
    }

    /* Category list visual elements */
    .cat-icon-box {
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: rgba(112, 18, 29, 0.04);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #70121D;
        transition: all 0.2s ease;
    }
    .cat-title-text {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1c1209;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: all 0.2s ease;
    }
    .cat-count-badge {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: #ede8e0;
        color: #5a4f45;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    /* Status dot indicators */
    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }
    .status-dot.active-status { background-color: #16a34a; }
    .status-dot.empty-status { background-color: #d97706; }

    /* Primary Creation Button */
    .btn-create-trigger {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0.7rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: #fff;
        background: #70121D;
        border: 1px solid #70121D;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(112, 18, 29, 0.15);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-create-trigger:hover {
        background: #871927;
        border-color: #871927;
        box-shadow: 0 4px 12px rgba(112, 18, 29, 0.25);
        transform: translateY(-1px);
    }
    .btn-create-trigger:active {
        transform: translateY(0);
    }

    /* Form Controls */
    .cat-input {
        width: 100%;
        padding: 0.85rem 1.1rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #1c1209;
        background: #faf8f5;
        border: 1.5px solid #ede8e0;
        border-radius: 12px;
        outline: none;
        transition: all 0.2s ease;
    }
    .cat-input:focus {
        border-color: #70121D;
        box-shadow: 0 0 0 4px rgba(112, 18, 29, 0.08);
        background: #fff;
    }
    .cat-input::placeholder { color: #b0a89a; font-weight: 400; }

    /* Buttons */
    .btn-save {
        width: 100%;
        padding: 0.85rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 700;
        color: #fff;
        transition: all 0.2s ease;
        border: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }
    .btn-save:disabled { background: #cec5b8 !important; cursor: not-allowed; box-shadow: none; }
    .btn-save:not(:disabled):hover { opacity: 0.9; cursor: pointer; }
    .btn-save:not(:disabled):active { transform: scale(0.99); }

    /* Scrollbar */
    #category-list { scrollbar-width: thin; scrollbar-color: #d1c9bf transparent; }
    #category-list::-webkit-scrollbar { width: 4px; }
    #category-list::-webkit-scrollbar-track { background: transparent; }
    #category-list::-webkit-scrollbar-thumb { background: #d1c9bf; border-radius: 4px; }

    /* Panel animations */
    .panel-section { animation: panelFade 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes panelFade {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Inputs in sidebar */
    .search-input {
        width: 100%;
        padding: 0.55rem 0.75rem 0.55rem 2.25rem;
        font-size: 0.9rem;
        border: 1.5px solid #e8e0d6;
        border-radius: 10px;
        background: #fff;
        outline: none;
        color: #3d3530;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-input:focus {
        border-color: #70121D;
        box-shadow: 0 0 0 3px rgba(112, 18, 29, 0.07);
    }
    .sort-select {
        flex: 1;
        padding: 0.45rem 0.625rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1.5px solid #e8e0d6;
        border-radius: 9px;
        background: #fff;
        color: #3d3530;
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .sort-select:focus { border-color: #70121D; }

    /* Danger row formatting */
    .danger-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        background: #fff8f8;
        border: 1px solid #fce8e8;
        border-left: 3.5px solid #ef4444;
        border-radius: 12px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .btn-delete {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.45rem 1rem;
        font-size: 0.85rem;
        font-weight: 700;
        color: #6b7280;
        background: transparent;
        border: 1.5px solid #d1d5db;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-delete:not(:disabled):hover {
        color: #991b1b;
        border-color: #fca5a5;
        background: #fef2f2;
    }
    .btn-delete:disabled { opacity: 0.38; cursor: not-allowed; }

    /* Fine Stat Cards */
    .stat-card {
        flex: 1;
        background: #FAF9F6;
        border: 1px solid #e8e0d6;
        border-radius: 16px;
        padding: 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: all 0.25s ease;
    }
    .stat-card:hover {
        border-color: #70121D;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(112, 18, 29, 0.03);
    }
    .stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(112, 18, 29, 0.06);
        color: #70121D;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .stat-icon.alt {
        background: rgba(197, 160, 89, 0.12);
        color: #8a6a1e;
    }
    .stat-card:hover .stat-icon {
        transform: scale(1.05);
    }
    .stat-info {
        min-width: 0;
    }
    .stat-value { font-size: 1.55rem; font-weight: 800; color: #1c1209; line-height: 1.1; }
    .stat-label { font-size: 0.75rem; color: #9d9288; margin-top: 0.2rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }

    /* Elegant Dashboard Style Empty State Card */
    .empty-state-dashboard {
        width: 100%;
        max-width: 580px;
        background: #fff;
        border: 1px solid #ede8e0;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        margin: auto;
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin: 1.5rem 0 2rem;
    }
    .db-card {
        background: #FAF9F6;
        border: 1px solid #ede8e0;
        border-radius: 14px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s ease;
    }
    .db-card:hover {
        border-color: #d1c9bf;
        transform: translateY(-2px);
    }
    .db-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        margin: 0 auto 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .db-val { font-size: 1.7rem; font-weight: 900; color: #1c1209; line-height: 1; }
    .db-lbl { font-size: 0.75rem; font-weight: 600; color: #9d9288; text-transform: uppercase; margin-top: 0.3rem; letter-spacing: 0.02em; }
</style>

<div class="cat-page">
    <div style="max-width: 1100px; margin: 0 auto; padding: 0 1.5rem;">

        {{-- Breadcrumb --}}
        <div style="margin-bottom: 1.75rem;">
            <a href="{{ route('iso.management.policies.index') }}"
               style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; color: #70121D; text-decoration: none; background: rgba(112,18,29,0.07); padding: 0.5rem 1.15rem 0.5rem 0.85rem; border-radius: 99px; transition: background 0.15s;"
               onmouseover="this.style.background='rgba(112,18,29,0.13)'" onmouseout="this.style.background='rgba(112,18,29,0.07)'">
                <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                Back to Policies Directory
            </a>
        </div>



        {{-- Flash Messages --}}
        @if (session('success'))
            <div style="margin-bottom: 1.25rem; padding: 0.875rem 1rem; background: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 0 10px 10px 0; color: #166534; font-size: 0.875rem; font-weight: 500;">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div style="margin-bottom: 1.25rem; padding: 0.875rem 1rem; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 0 10px 10px 0; color: #991b1b; font-size: 0.875rem; font-weight: 500;">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div style="margin-bottom: 1.25rem; padding: 0.875rem 1rem; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 0 10px 10px 0; color: #991b1b; font-size: 0.875rem; font-weight: 500;">
                @foreach ($errors->all() as $err)<p style="margin:0;">{{ $err }}</p>@endforeach
            </div>
        @endif

        {{-- Diamond divider --}}
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.25rem;">
            <div style="flex: 1; height: 1px; background: linear-gradient(to right, transparent, #d8cebc);"></div>
            <span style="color: #c5a059; font-size: 0.55rem; flex-shrink: 0; opacity: 0.8;">&#9670;</span>
            <div style="flex: 1; height: 1px; background: linear-gradient(to left, transparent, #d8cebc);"></div>
        </div>

        {{-- Main Card --}}
        <div style="display: flex; flex-direction: column; background: #fff; border-radius: 24px; border: 1px solid #e8e0d6; overflow: hidden; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.06); min-height: 580px;">

            {{-- Header Block --}}
            <div style="padding: 1.5rem 1.75rem; background-color: #70121D; border-bottom: 4px solid #c5a059; color: #fff;">
                <h2 style="font-size: 1.5rem; font-weight: 700; margin: 0; letter-spacing: -0.01em; color: #fff;">Manage Categories</h2>
                <p style="font-size: 0.85rem; margin: 0.35rem 0 0; color: rgba(255, 255, 255, 0.85); line-height: 1.5; max-width: 100%;">
                    <span style="color: #fff; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.4);">Create</span>,
                    <span style="color: #fff; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.4);">rename</span>, and
                    <span style="color: #fff; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.4);">remove</span>
                    the categories your policies are filed under.
                </p>
            </div>

            {{-- Inner Flex Area --}}
            <div style="display: flex; flex: 1;">

            {{-- ===== LEFT PANEL ===== --}}
            <div style="width: 290px; flex-shrink: 0; border-right: 1px solid #ede8e0; display: flex; flex-direction: column; background: #faf7f2; z-index: 5;">

                {{-- Search & Sort --}}
                <div style="padding: 1.25rem 1rem; border-bottom: 1px solid #ede8e0; display: flex; flex-direction: column; gap: 0.7rem;">
                    <div style="position: relative;">
                        <svg style="position:absolute; left: 0.65rem; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #9d9288;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="search-categories" placeholder="Search categories" class="search-input" style="padding-left: 2.15rem;">
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #9d9288; flex-shrink: 0;">Sort</span>
                        <select id="sort-categories" class="sort-select">
                            <option value="name_asc">Name A–Z</option>
                            <option value="name_desc">Name Z–A</option>
                            <option value="count_desc">Most Policies</option>
                            <option value="count_asc">Least Policies</option>
                        </select>
                    </div>
                </div>

                {{-- New Category Button (top) --}}
                <div style="padding: 0.75rem 0.875rem; border-bottom: 1px solid #ede8e0; background: #fbf9f6;">
                    <button type="button" onclick="showNewPanel()" class="btn-create-trigger">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        New category
                    </button>
                </div>

                {{-- Category list --}}
                <div id="category-list" style="flex: 1; overflow-y: auto; padding: 0.375rem 0;">
                    @forelse($categories as $cat)
                        <button type="button"
                                id="cat-item-{{ $cat->id }}"
                                onclick="selectCategory({{ $cat->id }})"
                                data-name="{{ $cat->name }}"
                                data-count="{{ $cat->policies_count }}"
                                data-created="{{ $cat->created_at->format('M d, Y') }}"
                                data-update-url="{{ route('iso.management.policy-categories.update', $cat->id) }}"
                                data-delete-url="{{ route('iso.management.policy-categories.destroy', $cat->id) }}"
                                class="cat-item">
                            <div class="cat-bar"></div>
                            <div style="display: flex; align-items: center; gap: 0.65rem; padding-left: 4px;">
                                <div class="cat-icon-box">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                </div>
                                <div style="min-width: 0; flex-grow: 1;">
                                    <p class="cat-title-text">
                                        <span class="status-dot {{ $cat->policies_count > 0 ? 'active-status' : 'empty-status' }}"></span>
                                        {{ $cat->name }}
                                    </p>
                                    <p style="font-size: 0.775rem; color: #a39485; margin: 1px 0 0; font-weight: 500; padding-left: 12px;">{{ $cat->policies_count === 0 ? 'Empty' : $cat->policies_count . ' ' . Str::plural('policy', $cat->policies_count) }}</p>
                                </div>
                                <span class="cat-count-badge">{{ $cat->policies_count }}</span>
                            </div>
                        </button>
                    @empty
                        <div style="padding: 3rem 1.25rem; text-align: center; color: #b0a89a;">
                            <svg style="width: 40px; height: 40px; margin: 0 auto 0.75rem; color: #d8d0c8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <p style="font-size: 0.8rem; font-weight: 600; margin: 0;">No categories yet</p>
                            <p style="font-size: 0.7rem; margin: 4px 0 0;">Click "+ New category" to add one.</p>
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- ===== RIGHT PANEL ===== --}}
            <div style="flex: 1; padding: 3rem 2.5rem; display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch; background: #fff; z-index: 1;">

                {{-- Empty State (Dashboard Style Overview) --}}
                <div id="panel-empty" style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div class="empty-state-dashboard panel-section">
                        {{-- Tag Badge --}}
                        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(197, 160, 89, 0.12); color: #8a6a1e; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; padding: 0.35rem 0.8rem; border-radius: 99px; margin-bottom: 1rem;">
                            <i class="bi bi-bar-chart-fill"></i>
                            Directory Status Overview
                        </div>
                        
                        <h3 style="font-size: 1.35rem; font-weight: 800; color: #1c1209; margin: 0 0 0.5rem;">Categories Dashboard</h3>
                        <p style="font-size: 0.825rem; color: #8a7a6e; margin: 0; line-height: 1.5;">Quick overview of policy documents currently filed across institutional categories.</p>

                        {{-- Stats Grid --}}
                        <div class="dashboard-grid">
                            @php
                                $totalCats = $categories->count();
                                $totalPols = $categories->sum('policies_count');
                                $emptyCats = $categories->where('policies_count', 0)->count();
                            @endphp
                            <div class="db-card">
                                <div class="db-icon" style="background: rgba(112, 18, 29, 0.06); color: #70121D;">
                                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div class="db-val">{{ $totalCats }}</div>
                                <div class="db-lbl">Categories</div>
                            </div>
                            <div class="db-card">
                                <div class="db-icon" style="background: rgba(112, 18, 29, 0.06); color: #70121D;">
                                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="db-val">{{ $totalPols }}</div>
                                <div class="db-lbl">Policies</div>
                            </div>
                            <div class="db-card">
                                <div class="db-icon" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
                                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="db-val">{{ $emptyCats }}</div>
                                <div class="db-lbl">Unfiled</div>
                            </div>
                        </div>

                        {{-- User tips --}}
                        <div style="border-top: 1px solid #ede8e0; padding-top: 1.25rem; text-align: left;">
                            <div style="display: flex; gap: 0.6rem; align-items: flex-start; margin-bottom: 0.6rem;">
                                <i class="bi bi-check2-circle text-[#70121D]" style="font-size: 1.15rem; flex-shrink: 0;"></i>
                                <p style="font-size: 0.85rem; color: #6b5e52; margin: 0; line-height: 1.4;">Select a category on the left menu sidebar to <strong>rename</strong> details or <strong>remove</strong> empty entries.</p>
                            </div>
                            <div style="display: flex; gap: 0.6rem; align-items: flex-start; margin-bottom: 0.85rem;">
                                <i class="bi bi-info-circle text-[#8a6a1e]" style="font-size: 1.15rem; flex-shrink: 0;"></i>
                                <p style="font-size: 0.85rem; color: #6b5e52; margin: 0; line-height: 1.4;">Green dots <span class="status-dot active-status"></span> show categories with active policies. Amber dots <span class="status-dot empty-status"></span> highlight categories without policies.</p>
                            </div>
                            <div style="display: flex; gap: 0.6rem; align-items: flex-start; padding: 0.65rem 0.85rem; background: #fffdf5; border: 1px dashed rgba(217, 119, 6, 0.25); border-radius: 8px;">
                                <i class="bi bi-exclamation-triangle-fill text-[#d97706]" style="font-size: 1.05rem; flex-shrink: 0; margin-top: 1px;"></i>
                                <p style="font-size: 0.825rem; color: #7c5a3c; margin: 0; line-height: 1.45;">
                                    <strong>Duplication Precaution:</strong> Please watch out for duplicate directories as categories lack a unique code identifier (like OIE-POL). Try to reuse existing folders first to prevent human error and keep lists clean.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Edit Panel --}}
                <div id="panel-edit" class="panel-section" style="width: 100%; max-width: 520px; display: none; background: #fff; border: 1px solid #ede8e0; border-radius: 20px; padding: 2rem 2.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.025);">
                    {{-- Category Tag instead of plain red block --}}
                    <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(112,18,29,0.08); color: #70121D; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; padding: 0.35rem 0.85rem; border-radius: 99px; margin-bottom: 2rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                        <span style="width: 6px; height: 6px; background: #70121D; border-radius: 50%;"></span>
                        Category Setup
                    </div>

                    <form id="edit-form" action="" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #9d9288; margin-bottom: 0.6rem;">Category Name</label>
                            <input type="text" name="name" id="edit-name" maxlength="48" required autocomplete="off" class="cat-input">
                            <p style="text-align: right; font-size: 0.8rem; color: #b0a89a; margin: 0.3rem 0 0;" id="edit-char">0/48</p>
                            <div id="edit-warning" style="display: none; margin-top: 0.75rem; padding: 0.6rem 0.85rem; background: #fffbeb; border-left: 3px solid #d97706; border-radius: 6px; color: #b45309; font-size: 0.825rem; font-weight: 500; line-height: 1.45; text-align: left;">
                                ⚠️ A similar category already exists: "<strong id="edit-warning-match"></strong>". You might not need to rename it to this.
                            </div>
                        </div>

                        {{-- Stats row --}}
                        <div style="display: flex; gap: 0.75rem; margin-bottom: 1.75rem;">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value" id="stat-count">—</div>
                                    <div class="stat-label">Policies filed</div>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon alt">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value" id="stat-created" style="font-size: 1rem; font-weight: 700;">—</div>
                                    <div class="stat-label">Created</div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="save-btn" disabled class="btn-save" style="background: #cec5b8;">
                            Save changes
                        </button>
                    </form>

                    {{-- Danger Zone --}}
                    <div style="margin-top: 2.25rem; padding-top: 1.5rem; border-top: 1px solid #ede8e0;">
                        <p style="font-size: 0.6rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #b0a89a; margin: 0 0 0.875rem;">Danger Zone</p>
                        <div class="danger-row">
                            <div>
                                <p style="font-size: 0.875rem; font-weight: 700; color: #1c1209; margin: 0;">Delete this category</p>
                                <p style="font-size: 0.75rem; color: #9d9288; margin: 3px 0 0;" id="delete-label">This category has no policies.</p>
                            </div>
                            <form id="delete-form" action="" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this category? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="delete-btn" class="btn-delete">
                                    <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- New Category Panel --}}
                <div id="panel-new" class="panel-section" style="width: 100%; max-width: 520px; display: none; background: #fff; border: 1px solid #ede8e0; border-radius: 20px; padding: 2rem 2.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                    {{-- Category Tag instead of plain red block --}}
                    <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(112,18,29,0.08); color: #70121D; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; padding: 0.35rem 0.85rem; border-radius: 99px; margin-bottom: 2rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                        <span style="width: 6px; height: 6px; background: #70121D; border-radius: 50%;"></span>
                        Create Category
                    </div>

                    <form action="{{ route('iso.management.policy-categories.store') }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #9d9288; margin-bottom: 0.6rem;">
                                Category Name <span style="color: #c0392b;">*</span>
                            </label>
                            <input type="text" name="name" id="new-name" maxlength="48" required autocomplete="off"
                                   placeholder="e.g. Academic Policies" class="cat-input">
                            <p style="text-align: right; font-size: 0.8rem; color: #b0a89a; margin: 0.3rem 0 0;" id="new-char">0/48</p>
                            <div id="new-warning" style="display: none; margin-top: 0.75rem; padding: 0.6rem 0.85rem; background: #fffbeb; border-left: 3px solid #d97706; border-radius: 6px; color: #b45309; font-size: 0.825rem; font-weight: 500; line-height: 1.45; text-align: left;">
                                ⚠️ A similar category already exists: "<strong id="new-warning-match"></strong>". You might not need to create this.
                            </div>
                            <small style="display: block; font-size: 0.75rem; color: #9d9288; margin-top: 0.45rem; line-height: 1.35; text-align: left;">
                                * Note: Categories lack a unique code identifier. Check if a similar directory exists before adding.
                            </small>
                        </div>

                        <button type="submit" class="btn-save" style="background: #70121D; margin-bottom: 0.75rem;">
                            Create Category
                        </button>
                        <button type="button" onclick="showEmpty()"
                                style="width: 100%; padding: 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600; color: #7d6f63; background: #ede8e0; border: none; cursor: pointer; transition: background 0.15s;"
                                onmouseover="this.style.background='#e0d8ce';" onmouseout="this.style.background='#ede8e0';">
                            Cancel
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
</div>

<script>
    let originalName = '';

    function showPanel(id) {
        document.getElementById('panel-empty').style.display = 'none';
        document.getElementById('panel-edit').style.display = 'none';
        document.getElementById('panel-new').style.display = 'none';
        const el = document.getElementById(id);
        el.style.display = id === 'panel-empty' ? 'flex' : 'block';
    }

    function showEmpty() {
        clearActive();
        showPanel('panel-empty');
        document.getElementById('panel-empty').style.display = 'flex';
        document.getElementById('panel-empty').style.flexDirection = 'column';
        document.getElementById('panel-empty').style.alignItems = 'center';
        document.getElementById('panel-empty').style.justifyContent = 'center';
    }

    // Bind clean redirect/focus logic
    function showNewPanel() {
        clearActive();
        document.getElementById('new-name').value = '';
        document.getElementById('new-char').textContent = '0/48';
        document.getElementById('new-warning').style.display = 'none';
        showPanel('panel-new');
        setTimeout(() => document.getElementById('new-name').focus(), 50);
    }

    function clearActive() {
        document.querySelectorAll('.cat-item').forEach(el => {
            el.classList.remove('active');
        });
    }

    function selectCategory(id) {
        clearActive();

        const item = document.getElementById('cat-item-' + id);
        item.classList.add('active');

        const name    = item.dataset.name;
        const count   = parseInt(item.dataset.count);
        const created = item.dataset.created;

        originalName = name;

        document.getElementById('edit-name').value = name;
        document.getElementById('edit-char').textContent = name.length + '/48';
        document.getElementById('stat-count').textContent = count;
        document.getElementById('stat-created').textContent = created;
        document.getElementById('edit-form').action = item.dataset.updateUrl;
        document.getElementById('delete-form').action = item.dataset.deleteUrl;
        document.getElementById('edit-warning').style.display = 'none';

        const deleteBtn = document.getElementById('delete-btn');
        const deleteLabel = document.getElementById('delete-label');
        if (count > 0) {
            deleteBtn.disabled = true;
            deleteLabel.textContent = 'Reassign ' + count + (count === 1 ? ' policy' : ' policies') + ' before deleting.';
        } else {
            deleteBtn.disabled = false;
            deleteLabel.textContent = 'This category has no policies.';
        }

        updateSaveBtn();
        showPanel('panel-edit');
    }

    function updateSaveBtn() {
        const current = document.getElementById('edit-name').value.trim();
        const btn = document.getElementById('save-btn');
        const changed = current.length > 0 && current !== originalName;
        btn.disabled = !changed;
        btn.style.background = changed ? '#70121D' : '#cec5b8';
    }

    function normalizeCategoryName(name) {
        let str = name.toLowerCase().trim();
        // Remove special characters, keep only alphanumerics and spaces
        str = str.replace(/[^a-z0-9\s]/g, '');
        // Collapse multiple spaces
        str = str.replace(/\s+/g, ' ');
        
        // Split into words, filter out "policy" / "policies"
        const words = str.split(' ');
        const filtered = [];
        for (let i = 0; i < words.length; i++) {
            let w = words[i];
            if (w !== 'policy' && w !== 'policies') {
                // Remove trailing 's' for simple singularization (e.g. Academics -> Academic)
                if (w.length > 3 && w.endsWith('s')) {
                    w = w.slice(0, -1);
                }
                filtered.push(w);
            }
        }
        
        // Fallback to original words if everything was filtered out
        if (filtered.length === 0) {
            return words.join(' ');
        }
        
        return filtered.join(' ');
    }

    function checkFuzzyDuplicate(inputVal, excludeName = '') {
        const normalizedInput = normalizeCategoryName(inputVal);
        if (!normalizedInput) return null;

        const items = document.querySelectorAll('.cat-item');
        for (let i = 0; i < items.length; i++) {
            const name = items[i].dataset.name;
            if (name.toLowerCase().trim() === excludeName.toLowerCase().trim()) {
                continue;
            }
            if (normalizeCategoryName(name) === normalizedInput) {
                return name;
            }
        }
        return null;
    }

    document.getElementById('edit-name').addEventListener('input', function () {
        document.getElementById('edit-char').textContent = this.value.length + '/48';
        updateSaveBtn();

        const warning = document.getElementById('edit-warning');
        const warningMatch = document.getElementById('edit-warning-match');
        const match = checkFuzzyDuplicate(this.value, originalName);
        if (match) {
            warningMatch.textContent = match;
            warning.style.display = 'block';
        } else {
            warning.style.display = 'none';
        }
    });

    document.getElementById('new-name').addEventListener('input', function () {
        document.getElementById('new-char').textContent = this.value.length + '/48';

        const warning = document.getElementById('new-warning');
        const warningMatch = document.getElementById('new-warning-match');
        const match = checkFuzzyDuplicate(this.value);
        if (match) {
            warningMatch.textContent = match;
            warning.style.display = 'block';
        } else {
            warning.style.display = 'none';
        }
    });

    // Live search
    document.getElementById('search-categories').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.cat-item').forEach(el => {
            el.style.display = el.dataset.name.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    // Sort
    document.getElementById('sort-categories').addEventListener('change', function () {
        const list = document.getElementById('category-list');
        const items = Array.from(list.querySelectorAll('.cat-item'));
        items.sort((a, b) => {
            const nA = a.dataset.name.toLowerCase(), nB = b.dataset.name.toLowerCase();
            const cA = parseInt(a.dataset.count), cB = parseInt(b.dataset.count);
            if (this.value === 'name_asc')   return nA.localeCompare(nB);
            if (this.value === 'name_desc')  return nB.localeCompare(nA);
            if (this.value === 'count_desc') return cB - cA;
            if (this.value === 'count_asc')  return cA - cB;
            return 0;
        });
        items.forEach(el => list.appendChild(el));
    });
</script>
</x-app-layout>
