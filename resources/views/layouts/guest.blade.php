<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OIE-HAU') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {  
            background-image: url("{{ asset('images/bg-white.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .left {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hau-banner {
            width: 370px;
            height: 370px;
        }

        .login-logo-circle {
            width: 100px;
            height: 100px;
        }

        h2, h5 {
            color: #70121D;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: bolder;
        }

        h5 {
            margin-top: -0.5rem;
            font-size: 1rem;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
        <!-- Image Section -->
        <div class="hidden md:flex bg-cover bg-center justify-center items-center left"
            style="background-image: url('{{ asset('images/hau-bg.png') }}');">
            <img src="{{ asset('images/logo-circle.png') }}" class="hau-banner" alt="HAU Logo">
        </div>

        <!-- Form Section -->
        <div class="flex flex-col justify-center items-center p-6">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
