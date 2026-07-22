<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase text-teal-700">Inventory</p>
                <h1 class="text-2xl font-semibold text-stone-950">Assets</h1>
            </div>
            @can('create', \App\Models\Asset::class)
                <a href="{{ route('assets.create') }}" class="inline-flex w-fit items-center rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-800">
                    Create Asset
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <form method="GET" action="{{ route('assets.index') }}" class="mb-6 rounded-lg border border-stone-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <x-input-label for="q" value="Search" />
                        <x-text-input id="q" name="q" type="search" class="mt-1 block w-full" value="{{ $filters['q'] }}" placeholder="Code, name, serial, or location" />
                    </div>
                    <div>
                        <x-input-label for="asset_category_id" value="Category" />
                        <select id="asset_category_id" name="asset_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) $filters['asset_category_id'] === $category->id)>{{ $category->name }}{{ $category->trashed() ? ' (archived)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="condition" value="Condition" />
                        <select id="condition" name="condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All condition</option>
                            @foreach ($conditions as $condition)
                                <option value="{{ $condition->value }}" @selected($filters['condition'] === $condition->value)>{{ $condition->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="active" value="Active" />
                        <select id="active" name="active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All active states</option>
                            <option value="1" @selected($filters['active'] === '1')>Active</option>
                            <option value="0" @selected($filters['active'] === '0')>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-primary-button>Apply</x-primary-button>
                    <a href="{{ route('assets.index') }}" class="text-sm font-medium text-stone-600 hover:text-stone-950">Reset</a>
                </div>
            </form>

            <div class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Brand/Model</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Location</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Condition</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Active</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-stone-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($assets as $asset)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-stone-950">{{ $asset->asset_code }}</td>
                                    <td class="px-4 py-4 text-sm text-stone-700">
                                        <a href="{{ route('assets.show', $asset) }}" class="font-medium text-teal-700 hover:text-teal-900">{{ $asset->name }}</a>
                                        @if ($asset->serial_number)
                                            <div class="mt-1 text-xs text-stone-500">{{ $asset->serial_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-stone-700">
                                        {{ $asset->category->name }}
                                        @if ($asset->category->trashed())
                                            <span class="ml-1 text-xs text-stone-500">(archived)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-stone-700">{{ trim(($asset->brand ?? '').' '.($asset->model ?? '')) ?: 'Not specified' }}</td>
                                    <td class="px-4 py-4 text-sm text-stone-700">{{ $asset->location }}</td>
                                    <td class="px-4 py-4"><x-asset-condition-badge :condition="$asset->condition" /></td>
                                    <td class="px-4 py-4">
                                        @if ($asset->is_active)
                                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-1 text-xs font-medium text-stone-700 ring-1 ring-inset ring-stone-300">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('assets.show', $asset) }}" class="font-medium text-teal-700 hover:text-teal-900">View</a>
                                            @can('update', $asset)
                                                <a href="{{ route('assets.edit', $asset) }}" class="font-medium text-stone-700 hover:text-stone-950">Edit</a>
                                            @endcan
                                            @can('delete', $asset)
                                                <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Archive this asset?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-medium text-red-700 hover:text-red-900">Archive</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center text-sm text-stone-600">
                                        No assets found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $assets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
