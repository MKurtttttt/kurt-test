@extends('layouts.home')

@section('title', 'Office of Institutional Effectiveness')

@section('content')
    <!-- Hero Section -->
    <div class="flex flex-col items-center justify-center pt-10 md:pt-14 px-4 relative z-10 w-full" style="min-height: 78vh;">
        
        <!-- Hero Title -->
        <h1 class="page-hero-title text-3xl md:text-5xl lg:text-6xl text-center mb-10 md:mb-14 drop-shadow-lg">
            Office of Institutional Effectiveness
        </h1>

        <!-- Welcome Glass Card -->
        <div class="rounded-2xl p-8 mb-10 w-full max-w-2xl text-white shadow-2xl relative overflow-hidden" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,0.45); box-shadow: 0 12px 48px rgba(0,0,0,0.22); border-radius: 1rem;">
            
            <!-- Decorative top accent line -->
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl" style="background: linear-gradient(90deg, #c5a059, #d4af37, #c5a059);"></div>

            <!-- Gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 pt-2">
                
                <!-- Left side: Welcome & Login -->
                <div class="flex-1 text-center md:text-left">
                    <div class="inline-block text-xs font-bold uppercase tracking-[0.18em] mb-3 px-3 py-1 rounded-full" style="background: rgba(197,160,89,0.25); color: #f0d080; border: 1px solid rgba(197,160,89,0.35);">HAU — OIE</div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-3 drop-shadow-md" style="font-family: 'Playfair Display', serif;">Welcome to OIE</h2>
                    <p class="text-sm md:text-base font-light mb-8 leading-relaxed" style="color: rgba(255,255,255,0.85);">
                        Access the central hub for institutional data, planning and review, and quality assurance.
                    </p>
                    
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="background: linear-gradient(135deg, #c5a059 0%, #d4af37 50%, #aa7c11 100%); color: #2b1a00; box-shadow: 0 4px 18px rgba(212,175,55,0.45); border-radius: 0.75rem;">
                        <i class="fas fa-lock mr-2 text-xs opacity-75"></i>
                        <span>Login for Full Dashboard</span>
                        <i class="fas fa-arrow-right ml-2 text-xs opacity-75 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Right side: Visitor count -->
                <div class="flex flex-col items-center justify-center p-5 rounded-2xl w-full md:w-auto md:min-w-[140px]" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 1rem;">
                    <i class="fas fa-eye text-2xl mb-2" style="color: rgba(255,255,255,0.7);"></i>
                    <div class="text-4xl md:text-5xl font-bold text-white mb-1" style="font-family: 'Playfair Display', serif; text-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                        {{ $count }}
                    </div>
                    <div class="text-xs font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.65);">
                        @if ($period === "daily")
                            Visitor{{ $count !== 1 ? 's' : '' }} Today
                        @else 
                            {{ ucfirst($period) }} Visitors
                        @endif 
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <!-- Quick Access Section -->
    <div class="w-full relative z-10" style="background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, #ffffff 10%);">
        <div class="max-w-6xl mx-auto px-6 sm:px-8 lg:px-10" style="padding-top: 4.2rem; padding-bottom: 4.8rem;">

            <!-- Section Label -->
            <div class="flex items-center gap-4" style="margin-bottom: 2rem;">
                <div class="h-px flex-1" style="background: linear-gradient(90deg, transparent, #e8dfc7);"></div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] px-4 py-1.5 rounded-full" style="background: rgba(197,160,89,0.1); color: #8a6a1e; border: 1px solid rgba(197,160,89,0.3);">Quick Access</span>
                <div class="h-px flex-1" style="background: linear-gradient(90deg, #e8dfc7, transparent);"></div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                @php
                $quickCards = [
                    ['icon' => 'fas fa-folder-open', 'title' => 'SharePoint Resources', 'route' => route('sharepoint.public'), 'desc' => 'Browse department and office SharePoint sites'],
                    ['icon' => 'fas fa-sitemap', 'title' => 'Organizational Chart', 'route' => route('orgchart'), 'desc' => 'View the current OIE organizational structure'],
                    ['icon' => 'fas fa-book-open', 'title' => 'Information Hub', 'route' => route('information-hub.public'), 'desc' => 'Access institutional knowledge and resources'],
                    ['icon' => 'far fa-window-maximize', 'title' => 'Full Dashboard', 'route' => route('login'), 'desc' => 'Login to access the full management system'],
                ];
                @endphp

                @foreach($quickCards as $card)
                <a href="{{ $card['route'] }}" class="quick-card group flex flex-col rounded-2xl overflow-hidden border border-[#ede8d8] bg-white shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1" style="border-radius: 1rem;">
                    <!-- Card Icon Area -->
                    <div class="p-6 pb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, rgba(197,160,89,0.15) 0%, rgba(212,175,55,0.1) 100%); border: 1px solid rgba(197,160,89,0.25); border-radius: 0.75rem;">
                            <i class="{{ $card['icon'] }} text-xl" style="color: #c5a059;"></i>
                        </div>
                        <h4 class="text-base font-bold text-gray-800 mb-1 leading-snug">{{ $card['title'] }}</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $card['desc'] }}</p>
                    </div>
                    <!-- Card Footer -->
                    <div class="mt-auto px-6 py-3 flex items-center justify-between text-xs font-semibold" style="background: linear-gradient(90deg, rgba(197,160,89,0.08), rgba(212,175,55,0.12)); border-top: 1px solid rgba(197,160,89,0.2); color: #8a6a1e;">
                        <span>Open</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>
                @endforeach

            </div>
        </div>
    </div>
@endsection
