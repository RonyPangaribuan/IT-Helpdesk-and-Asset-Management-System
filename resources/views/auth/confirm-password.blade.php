<x-guest-layout>
    <div class="mb-6">
        <p class="text-sm font-medium text-indigo-700">Secure area</p>
        <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950">Confirm your password</h1>
    </div>

    <div class="mb-4 text-sm leading-6 text-slate-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
