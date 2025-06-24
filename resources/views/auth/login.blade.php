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
