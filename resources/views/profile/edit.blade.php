<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Account"
            title="Profile"
            description="Manage your profile information, password, and account status."
        />
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        <x-section-card title="Profile Information" description="Update your account name and email address.">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-section-card>

        <x-section-card title="Update Password" description="Use a strong password to keep your account secure.">
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-section-card>

        <x-section-card title="Deactivate Account" description="This signs you out and prevents future login until an administrator reactivates the account." class="border-red-200">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-section-card>
    </div>
</x-app-layout>
