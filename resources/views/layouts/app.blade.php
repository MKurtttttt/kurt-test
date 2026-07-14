@php
    use App\Helpers\pagetitle;
    use App\Models\Employee;

    // Retrieve user information and current page title
    $userInfo  = Employee::where('emp_id', Auth::user()->id)->first();
    $curr_page = pagetitle::getCurrentPageTitle();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if($curr_page != '')
            <title>{{ $curr_page }}</title>
        @else
            <title>{{ config('app.name') }}</title>
        @endif

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Responsive font size for medium viewports */
            @media (max-width: 820px) {
                body {
                    font-size: 0.7rem;
                }
            }

            body {
                background-color: #edf2f7;
            }

            /* Sidebar styling */
            .sidebar {
                position: fixed;
                left: 0;
                height: 100%;
                width: 20%;
            }

            /* Content styling */
            .content {
                background-color: #edf2f7;
                position: absolute;
                top: 10%;
                left: 20%;
                width: 80%;
                overflow-x: auto;
                z-index: 4;
            }

            /* Header styling */
            .header {
                position: fixed;
                left: 20%;
                width: 80%;
                height: 10%;
                background-color: white;
                box-shadow: 4px 3px 5px rgba(0, 0, 0, 0.1);
                display: flex;
                z-index: 5;
            }

            .page-title {
                height: 100%;
                display: flex;
                align-items: center;
                float: left;
            }

            /* User image styling */
            .user-image img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
            }

            /* Logged user details styling */
            .logged-user {
                margin: 0 1rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100%;
            }

            .logged-user h3 {
                line-height: 20px;
                font-size: 15px;
                color: #100F0D;
                font-weight: 700;
            }

            .logged-user h4 {
                opacity: 50%;
                color: #100F0D;
                font-size: 13px;
                line-height: 10px;
            }

            /* Dropdown styling */
            .dropdown {
                position: fixed;
                right: 0;
                display: flex;
                align-items: center;
                z-index: 7;
                height: 10%;
            }

            .logout {
                display: flex;
                align-items: center;
                padding: 0 2rem 0 0;
            }

            /* Container to position dropdown menu relative to the button */
            .dropdown-container {
                position: relative;
                display: inline-block;
            }

            /* Dropdown button styling */
            .dropdown-btn {
                background: none;
                border: none;
                cursor: pointer;
            }

            /* Dropdown menu styling */
            .dropdown-toggle {
                position: absolute;
                top: 100%; /* Position directly below the container */
                right: 0;
                margin-top: 0.5rem; /* Spacing between icon and dropdown */
                width: 140px;
                height: 100px;
                background-color: #fdfdfd;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                display: grid;
                grid-template-rows: 50% 50%;
                z-index: 5;
            }

            .hide {
                display: none;
            }

            .dt-row {
                width: 100%;
                height: 100%;
            }

            .dt-row:hover {
                background-color: #FFF0ED;
            }

            .dt-row form,
            .dt-row button {
                width: 100%;
                height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .dt-row form img {
                width: 20px;
                height: 20px;
                margin: 0 0.5rem;
            }

            .dt-row span {
                font-size: 1rem;
            }

            .all-clickable * {
                pointer-events: auto !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Main Content -->
        <div class="content">
            <div class="main-content">
                {{ $slot }}
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <div class="sidebar">
            @php
                $currentPath = request()->path();
                $user = Auth::user()
            @endphp

            @if (Str::contains($currentPath, 'hau_ep') || (Auth::user()->role === 'Employee') || (Auth::user()->role === 'ISO Document Handler') || (Auth::user()->role === 'IDC Admin'))
                @if (Str::contains($currentPath, 'emp-acad-module'))
                    @include('layouts.emp_acad')
                @else
                    @include('layouts.portal_navigation')
                @endif
            @elseif (Str::contains($currentPath, 'admin'))
                @include('layouts.admin_nav')
            @elseif (Str::contains($currentPath, 'manage-emps'))
                @include('layouts.manage_emps')
            @elseif (Str::contains($currentPath, 'emp-acad-module'))
                @include('layouts.emp_acad')
            @else
                @include('layouts.navigation')
            @endif
        </div>

        <!-- Header -->
        <div class="header">
            <div class="page-title">
                <h1 class="text-xl text-red-900 font-bold pl-4">{{ $curr_page }}</h1>
            </div>
        </div>

        <!-- Dropdown Menu for User Settings and Logout -->
        <div class="dropdown">
            <div class="logged-user">
                <!-- Additional user details can be added here if needed -->
            </div>
            <div class="logout">
                <div class="dropdown-container">
                    @if (Auth::user()->user->profile_picture)
                        <button class="dropdown-btn">
                            <img src="{{asset('storage/profile_pictures/' . Auth::user()->user->profile_picture)}}" class ="w-11 h-11 rounded-full" alt="User Icon" >
                        </button>
                    @else
                        <button class="dropdown-btn">
                            <img src="{{ asset('images/blankdp.jpg') }}" alt="User Icon" width="50" height="50">
                        </button>
                    @endif

                    <div class="dropdown-toggle hide">
                        <!-- Settings Option -->
                        <div class="dt-row">
                            <form method="GET" action="{{ route('profile.edit') }}">
                                @csrf
                                <button type="submit">
                                    <img src="{{ asset('images/icons/icon-settings.png') }}" alt="Settings Icon">
                                    <span> Settings </span>
                                </button>
                            </form>
                        </div>
                        <!-- Logout Option -->
                        <div class="dt-row">
                            <form method="POST" action="{{ route('logout') }}" onsubmit="handleLogout(event)">
                                @csrf
                                <button type="submit">
                                    <img src="{{ asset('images/icons/icon-logout.png') }}" alt="Logout Icon">
                                    <span> Logout </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dropdown Toggle Script -->
        <script>
            let dropdownBtn = document.querySelector('.dropdown-btn');
            let dropdownBox = document.querySelector('.dropdown-toggle');
            let header      = document.querySelector('.header');
            let content     = document.querySelector('.content');

            // Hide dropdown when clicking on header or content
            header.addEventListener('click', () => {
                dropdownBox.classList.add('hide');
            });

            content.addEventListener('click', () => {
                dropdownBox.classList.add('hide');
            });

            // Toggle dropdown visibility on button click
            dropdownBtn.addEventListener('click', (event) => {
                // Prevent the event from bubbling up to the body, which might hide the dropdown immediately.
                event.stopPropagation();
                dropdownBox.classList.toggle('hide');
            });

            // Hide dropdown when clicking outside of it
            document.body.addEventListener('click', (event) => {
                if (!dropdownBox.contains(event.target) && !dropdownBtn.contains(event.target)) {
                    dropdownBox.classList.add('hide');
                }
            });

            // Handle logout and redirect to main page
            function handleLogout(event) {
                event.preventDefault();
                
                // Submit the logout form
                fetch(event.target.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new URLSearchParams(new FormData(event.target))
                }).then(() => {
                    // Redirect to main page after logout
                    window.location.href = '/';
                }).catch(() => {
                    // Fallback: redirect to main page even if fetch fails
                    window.location.href = '/';
                });
            }
        </script>
    </body>
</html>
