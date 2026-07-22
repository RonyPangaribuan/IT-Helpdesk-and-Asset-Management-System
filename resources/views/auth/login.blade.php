<x-guest-layout>
    <div class="mb-6">
        <p class="text-sm font-medium text-indigo-700">Welcome back</p>
        <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">Log in to DelDesk</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Open your role-based helpdesk workspace.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            @if (Route::has('password.request'))
                <a class="app-link text-sm" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        @if (Route::has('register'))
            <p class="mt-6 text-sm text-slate-600">
                Need requester access?
                <a href="{{ route('register') }}" class="app-link">Create an account</a>
            </p>
        @endif
    </form>
</x-guest-layout>
