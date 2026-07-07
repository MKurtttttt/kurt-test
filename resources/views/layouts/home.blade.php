<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'Office of Institutional Effectiveness — HAU Portal for institutional data, planning, and quality assurance.')">
    <title>@yield('title', 'HAU Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Resource Hints -->
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preload" as="image" href="{{ asset('images/new-bg.jpg') }}">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Google Fonts — combined into a single request -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    @stack('head')
    <style>
        body {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        /* Full Background Image */
        .bg-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset("images/new-bg.jpg") }}?v=1');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            background-attachment: fixed;
            z-index: -2;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 1;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            overflow-x: clip;
        }
        
        /* Glassmorphism Header */
        .glass-header {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin: 1.5rem auto;
            max-width: 1200px;
            position: sticky;
            top: 1.5rem;
            z-index: 50;
        }

        .nav-link {
            transition: all 0.2s ease;
            color: #111827; /* dark gray */
        }
        .nav-link:hover {
            color: #70121D; /* HAU Red */
        }
        .active-nav-link {
            background: rgba(255, 255, 255, 0.85) !important;
            color: #70121D !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }
        
        /* Golden Gradient Button */
        .btn-gold-gradient {
            background: linear-gradient(135deg, #c5a059 0%, #d4af37 50%, #aa7c11 100%);
            color: #2b2b2b;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        .btn-gold-gradient:hover {
            background: linear-gradient(135deg, #d4af37 0%, #e5c158 50%, #c5a059 100%);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            transform: translateY(-1px);
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        /* Mobile specific adjustments */
        @media (max-width: 768px) {
            .glass-header {
                margin: 0;
                border-radius: 0;
                top: 0;
            }
            .nav-mobile {
                flex-wrap: wrap;
            }
            .mobile-menu {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
            }
        }

        /* === Page title scrim === */
        .bg-scrim {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* Cinematic radial+linear combo:
               - dark at very top center (sky/building area where titles live)
               - bleeds gently to left/right edges
               - completely transparent below 60vh so the rest of the page image shows naturally */
            background:
                radial-gradient(
                    ellipse 120% 55% at 50% 0%,
                    rgba(15, 6, 2, 0.62) 0%,
                    rgba(15, 6, 2, 0.38) 35%,
                    transparent 70%
                );
            z-index: -1;
            pointer-events: none;
        }

        /* Shared hero title helper — use on any h1 in a page header */
        .page-hero-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #ffffff;
            /* layered shadow: tight for sharpness, wider for depth */
            text-shadow:
                0 1px 2px rgba(0,0,0,0.9),
                0 3px 8px  rgba(0,0,0,0.7),
                0 8px 24px rgba(0,0,0,0.45);
            letter-spacing: -0.01em;
        }

        /* Shared hero subtitle helper */
        .page-hero-sub {
            color: rgba(255, 255, 255, 0.92);
            text-shadow:
                0 1px 3px rgba(0,0,0,0.8),
                0 3px 10px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>
    <!-- Background Layer -->
    <div class="bg-image"></div>

    <!-- Dark top scrim: fades from near-black at top to transparent, giving titles contrast against the bright building/sky -->
    <div class="bg-scrim" aria-hidden="true"></div>
    
    <div class="content-wrapper">
        <!-- Floating Glassmorphism Navigation Bar -->
        <nav class="w-full sm:w-[95%] glass-header px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Left Side: Logo -->
                <div class="flex items-center">
                    <a href="/" class="hover:scale-105 transition-transform duration-200 block">
                        <img src="{{ asset('images/logo-long.png') }}?v=2" alt="HAU Logo" class="object-contain filter drop-shadow-md" style="height: 56px; max-width: 100%; transform: scale(1.35); transform-origin: left center;">
                    </a>
                </div>
                
                <!-- Center: Navigation Links -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="/" class="nav-link px-3 py-2 rounded-md text-[15px] font-medium {{ Request::is('/') ? 'active-nav-link' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('sharepoint.public') }}" class="nav-link px-3 py-2 rounded-md text-[15px] font-medium {{ Request::is('sharepoint*') ? 'active-nav-link' : '' }}">
                        SharePoint Sites
                    </a>
                    <a href="{{ route('orgchart') }}" class="nav-link px-3 py-2 rounded-md text-[15px] font-medium {{ Request::is('org-chart*') ? 'active-nav-link' : '' }}">
                        Organizational Chart
                    </a>
                    <a href="{{ route('information-hub.public') }}" class="nav-link px-3 py-2 rounded-md text-[15px] font-medium {{ Request::is('information-hub*') ? 'active-nav-link' : '' }}">
                        Information Hub
                    </a>
                </div>
                
                <!-- Right Side: Login Button -->
                <div class="flex items-center">
                    <a href="{{ route('login') }}" class="btn-gold-gradient px-6 py-2 rounded-lg text-sm transition-all duration-200">
                        Login
                    </a>
                    
                    <!-- Mobile menu button -->
                    <button type="button" class="md:hidden ml-3 mobile-menu-btn text-gray-800 hover:text-red-900 p-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-bars text-xl drop-shadow-md"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Navigation Menu -->
            <div class="md:hidden mobile-menu hidden border-t border-gray-200/50 shadow-lg rounded-b-lg">
                <div class="px-4 pt-3 pb-4 space-y-2">
                    <a href="/" class="block px-4 py-3 rounded-lg text-base font-medium text-gray-800 hover:bg-white/60 {{ Request::is('/') ? 'bg-white/60' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('sharepoint.public') }}" class="block px-4 py-3 rounded-lg text-base font-medium text-gray-800 hover:bg-white/60 {{ Request::is('sharepoint*') ? 'bg-white/60' : '' }}">
                        SharePoint Sites
                    </a>
                    <a href="{{ route('orgchart') }}" class="block px-4 py-3 rounded-lg text-base font-medium text-gray-800 hover:bg-white/60 {{ Request::is('org-chart*') ? 'bg-white/60' : '' }}">
                        Organizational Chart
                    </a>
                    <a href="{{ route('information-hub.public') }}" class="block px-4 py-3 rounded-lg text-base font-medium text-gray-800 hover:bg-white/60 {{ Request::is('information-hub*') ? 'bg-white/60' : '' }}">
                        Information Hub
                    </a>
                </div>
            </div>
        </nav>
        
        <!-- Main Content Area -->
        <div class="main-content">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <footer class="w-full text-white" style="background: linear-gradient(180deg, #70121D 0%, #5a0e17 100%); box-shadow: 0 -4px 24px rgba(0,0,0,0.25);">
            <!-- Gold accent top line -->
            <div style="height: 3px; background: linear-gradient(90deg, transparent, #c5a059, #d4af37, #c5a059, transparent);"></div>

            <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 py-8">
                <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-8">

                    <!-- Left: Logo + tagline -->
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <img src="{{ asset('images/logo-long-white.png') }}?v=1" alt="Holy Angel University Logo"
                            loading="lazy"
                            style="height: 44px; max-width: 220px; object-fit: contain;">
                        <div class="hidden md:block" style="width: 1px; height: 32px; background: rgba(255,255,255,0.2);"></div>
                        <p style="font-size: 0.72rem; color: rgba(255,255,255,0.55); letter-spacing: 0.06em; text-transform: uppercase; font-weight: 500; text-align: center;">
                            Office of Institutional Effectiveness
                        </p>
                    </div>

                    <!-- Center: Nav links -->
                    <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:0.5rem 0;">
                        <a href="{{ route('sharepoint.public') }}"
                            style="font-size:0.8rem; color:rgba(255,255,255,0.7); text-decoration:none; font-weight:500; transition:color 0.2s; margin-right:1.5rem;"
                            onmouseover="this.style.color='#d4af37';" onmouseout="this.style.color='rgba(255,255,255,0.7)';">
                            SharePoint Sites
                        </a>
                        <a href="{{ route('orgchart') }}"
                            style="font-size:0.8rem; color:rgba(255,255,255,0.7); text-decoration:none; font-weight:500; transition:color 0.2s; margin-right:1.5rem;"
                            onmouseover="this.style.color='#d4af37';" onmouseout="this.style.color='rgba(255,255,255,0.7)';">
                            Organizational Chart
                        </a>
                        <a href="{{ route('information-hub.public') }}"
                            style="font-size:0.8rem; color:rgba(255,255,255,0.7); text-decoration:none; font-weight:500; transition:color 0.2s; margin-right:1.5rem;"
                            onmouseover="this.style.color='#d4af37';" onmouseout="this.style.color='rgba(255,255,255,0.7)';">
                            Information Hub
                        </a>
                        <a href="{{ route('login') }}"
                            style="font-size:0.8rem; color:rgba(255,255,255,0.7); text-decoration:none; font-weight:500; transition:color 0.2s;"
                            onmouseover="this.style.color='#d4af37';" onmouseout="this.style.color='rgba(255,255,255,0.7)';">
                            Login
                        </a>
                    </div>

                </div>

                <!-- Divider -->
                <div style="height: 1px; background: rgba(255,255,255,0.12); margin: 1.5rem 0;"></div>

                <!-- Bottom: Copyright -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p style="font-size: 0.72rem; color: rgba(255,255,255,0.45);">
                        &copy; {{ date('Y') }} Holy Angel University &mdash; Office of Institutional Effectiveness. All rights reserved.
                    </p>
                    <p style="font-size: 0.72rem; color: rgba(255,255,255,0.3);">
                        HAU-OIE Portal
                    </p>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const mobileMenu = document.querySelector('.mobile-menu');
            const mobileMenuIcon = mobileMenuBtn?.querySelector('i');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    mobileMenu.classList.toggle('hidden');
                    
                    if (mobileMenu.classList.contains('hidden')) {
                        mobileMenuIcon.className = 'fas fa-bars text-xl drop-shadow-md';
                    } else {
                        mobileMenuIcon.className = 'fas fa-times text-xl drop-shadow-md';
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
