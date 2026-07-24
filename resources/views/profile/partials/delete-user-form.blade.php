<section class="space-y-6">
    <p class="text-sm leading-6 text-slate-600">
        {{ __('Deactivating your account signs you out and prevents future login. Existing ticket history remains available to authorized administrators for support records.') }}
    </p>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Deactivate Account') }}</x-danger-button>

    <x-modal
        name="confirm-user-deletion"
        title="Confirm account deactivation"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-slate-950">
                {{ __('Are you sure you want to deactivate your account?') }}
            </h2>

            <p class="mt-2 text-sm leading-6 text-slate-600">
                {{ __('Please enter your password to confirm. This will close your current session and require an administrator to reactivate your account later.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Deactivate Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
