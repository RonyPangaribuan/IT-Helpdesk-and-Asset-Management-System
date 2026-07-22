<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Admin"
            title="Create User"
            description="Create requester, technician, or administrator accounts from a controlled admin form."
        />
    </x-slot>

    <div class="mx-auto max-w-4xl">
        <x-section-card title="User account" description="Public registration remains requester-only; elevated roles are created here.">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-8">
                @csrf

                <section class="space-y-5">
                    <h2 class="text-base font-semibold text-slate-950">Identity</h2>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" name="name" class="mt-1" value="{{ old('name') }}" required maxlength="150" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="mt-1" value="{{ old('email') }}" required maxlength="255" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="Phone" />
                            <x-text-input id="phone" name="phone" class="mt-1" value="{{ old('phone') }}" maxlength="30" />
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
                                <option value="">Select role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <label class="mt-6 inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-700 shadow-sm focus:ring-indigo-600" @checked((bool) old('is_active', true))>
                            Active account
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <h2 class="text-base font-semibold text-slate-950">Security</h2>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="password" value="Initial Password" />
                            <x-text-input id="password" name="password" type="password" class="mt-1" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Confirm Password" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1" required autocomplete="new-password" />
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('admin.users.index') }}" class="app-button-secondary">Cancel</a>
                    <x-primary-button>Create User</x-primary-button>
                </div>
            </form>
        </x-section-card>
    </div>
</x-app-layout>
