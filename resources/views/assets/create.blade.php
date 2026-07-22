<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Inventory"
            title="Create Asset"
            description="Register a device so future tickets can reference its location and repair history."
        />
    </x-slot>

    <div class="mx-auto max-w-4xl">
        <x-section-card title="Asset record" description="Use stable inventory identifiers and clear location details.">
            <form method="POST" action="{{ route('assets.store') }}" class="space-y-8">
                @csrf

                <section class="space-y-5">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Identity</h2>
                        <p class="mt-1 text-sm text-slate-600">Asset code and name are the primary references in ticket pages.</p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="asset_code" value="Asset Code" />
                            <x-text-input id="asset_code" name="asset_code" class="mt-1" value="{{ old('asset_code') }}" required maxlength="50" placeholder="AST-LAP-001" />
                            <x-input-error :messages="$errors->get('asset_code')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" name="name" class="mt-1" value="{{ old('name') }}" required maxlength="150" placeholder="Finance Laptop" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Classification</h2>
                        <p class="mt-1 text-sm text-slate-600">Category and condition determine whether the asset can be selected for new tickets.</p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="asset_category_id" value="Category" />
                            <select id="asset_category_id" name="asset_category_id" class="app-input mt-1" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('asset_category_id') === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('asset_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="condition" value="Condition" />
                            <select id="condition" name="condition" class="app-input mt-1" required>
                                <option value="">Select condition</option>
                                @foreach ($conditions as $condition)
                                    <option value="{{ $condition->value }}" @selected(old('condition') === $condition->value)>{{ $condition->label() }}</option>
                                @endforeach
                            </select>
                            <p class="app-help">Retired assets are saved as inactive automatically.</p>
                            <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Technical Information</h2>
                        <p class="mt-1 text-sm text-slate-600">Optional manufacturer details help technicians identify the exact device.</p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-3">
                        <div>
                            <x-input-label for="brand" value="Brand" />
                            <x-text-input id="brand" name="brand" class="mt-1" value="{{ old('brand') }}" maxlength="100" />
                            <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="model" value="Model" />
                            <x-text-input id="model" name="model" class="mt-1" value="{{ old('model') }}" maxlength="100" />
                            <x-input-error :messages="$errors->get('model')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="serial_number" value="Serial Number" />
                            <x-text-input id="serial_number" name="serial_number" class="mt-1" value="{{ old('serial_number') }}" maxlength="100" />
                            <x-input-error :messages="$errors->get('serial_number')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="space-y-5 border-t border-slate-100 pt-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Location and Status</h2>
                    </div>

                    <div>
                        <x-input-label for="location" value="Location" />
                        <x-text-input id="location" name="location" class="mt-1" value="{{ old('location') }}" required maxlength="150" />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="4" class="app-input mt-1">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-700 shadow-sm focus:ring-indigo-600" @checked(old('is_active', '1') === '1')>
                        Active asset
                    </label>
                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                </section>

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('assets.index') }}" class="app-button-secondary">Cancel</a>
                    <x-primary-button>Create Asset</x-primary-button>
                </div>
            </form>
        </x-section-card>
    </div>
</x-app-layout>
