<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase text-teal-700">Inventory</p>
            <h1 class="text-2xl font-semibold text-stone-950">Create Asset</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('assets.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="asset_code" value="Asset Code" />
                            <x-text-input id="asset_code" name="asset_code" class="mt-1 block w-full" value="{{ old('asset_code') }}" required maxlength="50" />
                            <x-input-error :messages="$errors->get('asset_code')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name') }}" required maxlength="150" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="asset_category_id" value="Category" />
                            <select id="asset_category_id" name="asset_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('asset_category_id') === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('asset_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="condition" value="Condition" />
                            <select id="condition" name="condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select condition</option>
                                @foreach ($conditions as $condition)
                                    <option value="{{ $condition->value }}" @selected(old('condition') === $condition->value)>{{ $condition->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-3">
                        <div>
                            <x-input-label for="brand" value="Brand" />
                            <x-text-input id="brand" name="brand" class="mt-1 block w-full" value="{{ old('brand') }}" maxlength="100" />
                            <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="model" value="Model" />
                            <x-text-input id="model" name="model" class="mt-1 block w-full" value="{{ old('model') }}" maxlength="100" />
                            <x-input-error :messages="$errors->get('model')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="serial_number" value="Serial Number" />
                            <x-text-input id="serial_number" name="serial_number" class="mt-1 block w-full" value="{{ old('serial_number') }}" maxlength="100" />
                            <x-input-error :messages="$errors->get('serial_number')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="location" value="Location" />
                        <x-text-input id="location" name="location" class="mt-1 block w-full" value="{{ old('location') }}" required maxlength="150" />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-stone-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-teal-700 shadow-sm focus:ring-teal-600" @checked(old('is_active', '1') === '1')>
                        Active
                    </label>
                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('assets.index') }}" class="text-sm font-medium text-stone-600 hover:text-stone-950">Cancel</a>
                        <x-primary-button>Create Asset</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
