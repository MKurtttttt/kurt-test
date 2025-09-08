<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="text-center">
        @csrf

        <!-- Logo and Title -->
        <div class="mb-6">
            <img src="{{ asset('images/logo-circle.png') }}" class="login-logo-circle mx-auto" alt="Logo Circle" /> 
            <h2>Office of Institutional Effectiveness</h2> 
            <h5>Institutional Database Management Office</h5>
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <input id="email" class="block mt-1 w-full bg-gray-200 rounded-md p-2 border border-gray-100" type="email" name="email" value="{{old('email')}}" required autofocus autocomplete="username" placeholder="HAU Email" />

        </div>

    

        <!-- Password -->
        <div class="mb-4">
            <input id="password" class="block mt-1 w-full bg-gray-200 rounded-md p-2 border border-gray-100" type="password" name="password" required autocomplete="current-password" placeholder="Password" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

   

        <!-- Login Button -->
        <div class="mb-2">
            <x-primary-button class="login-btn w-full">
                <span class="text-center text-white hover:text-black">{{ __('Log in') }}</span>
            </x-primary-button>
        </div>

        @if(isset($msg)) 
                <div class="mb-8 px-4 py-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 text-center shadow-sm">
                    <span class="text-sm font-medium italic"> {{$msg}} </span>
                </div>
            @endif


        @if(isset($term_msg))
        <div class="mb-8 p-4 border border-red-500 text-red-700 bg-red-100 text-center rounded-lg">
                <span class="text-sm font-semibold"> {{$term_msg}} </span>
        </div>

        @endif
        

        <!-- Forgot Password Link -->
         <hr class="my-4">  
        <div class="mb-4 mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm text-#70121D dark:text-gray-800 hover:text-gray-900 dark:hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 block" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Back to Home Button -->
        <div class="mb-4">
            <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 hover:text-gray-900 rounded-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Home
            </a>
        </div>
    </form>
</x-guest-layout>

<style>
    /* Custom CSS for input fields */
    #email, #password {
        background-color: #D7D8DB;
        
    }

    .login-btn {
        background-color: #70121D;
    }

    .login-btn span {
        display: inline-block;
        width: 100%;
        text-align: center;
    }
</style>
