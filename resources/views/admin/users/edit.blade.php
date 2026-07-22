<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :eyebrow="ucfirst($managedUser->role)"
            title="Edit User"
            description="Adjust profile, role, active state, or reset password while preserving account history."
        />
    </x-slot>

    <div class="mx-auto max-w-4xl">
        <x-flash-messages />

        <x-section-card title="User account" description="Changing role or active state is validated by user-management rules.">
            <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="space-y-8" onsubmit="return confirm('Save changes to this user account?');">
                @csrf
                @method('PATCH')

                <section class="space-y-5">
                    <h2 class="text-base font-semibold text-slate-950">Identity</h2>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" name="name" class="mt-1" value="{{ old('name', $managedUser->name) }}" required maxlength="150" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="mt-1" value="{{ old('email', $managedUser->email) }}" required maxlength="255" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="Phone" />
                            <x-text-input id="phone" name="phone" class="mt-1" value="{{ old('phone', $managedUser->phone) }}" maxlength="30" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <h2 class="text-base font-semibold text-slate-950">Role and Access</h2>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="role" value="Role" />
                            <select id="role" name="role" class="app-input mt-1" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" @selected(old('role', $managedUser->role) === $role)>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-amber-900">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-amber-300 text-indigo-700 shadow-sm focus:ring-indigo-600" @checked((bool) old('is_active', $managedUser->is_active))>
                                Active account
                            </label>
                            <p class="mt-2 text-xs leading-5 text-amber-800">Inactive users cannot sign in. If already signed in, they will be logged out on the next protected request.</p>
                            <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <h2 class="text-base font-semibold text-slate-950">Security</h2>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        Leave password fields blank to keep the current password.
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="password" value="New Password" />
                            <x-text-input id="password" name="password" type="password" class="mt-1" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Confirm New Password" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1" autocomplete="new-password" />
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('admin.users.index') }}" class="app-button-secondary">Cancel</a>
                    <x-primary-button>Save Changes</x-primary-button>
                </div>
            </form>
        </x-section-card>
    </div>
</x-app-layout>
