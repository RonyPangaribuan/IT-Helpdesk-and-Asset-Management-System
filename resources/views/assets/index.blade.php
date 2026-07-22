<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Inventory"
            title="Assets"
            description="Browse managed devices and connect asset history with support tickets."
        >
            <x-slot name="actions">
                @can('create', \App\Models\Asset::class)
                    <a href="{{ route('assets.create') }}" class="app-button-primary">Create Asset</a>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-flash-messages />

        <form method="GET" action="{{ route('assets.index') }}" class="app-card p-4 sm:p-5">
            <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Filter assets</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $assets->total() }} {{ Str::plural('asset', $assets->total()) }} found.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-primary-button>Apply</x-primary-button>
                    <a href="{{ route('assets.index') }}" class="app-button-secondary">Reset</a>
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-5">
                <div class="md:col-span-2">
                    <x-input-label for="q" value="Search" />
                    <x-text-input id="q" name="q" type="search" class="mt-1" value="{{ $filters['q'] }}" placeholder="Code, name, serial, or location" />
                </div>
                <div>
                    <x-input-label for="asset_category_id" value="Category" />
                    <select id="asset_category_id" name="asset_category_id" class="app-input mt-1">
                        <option value="">All category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) $filters['asset_category_id'] === $category->id)>{{ $category->name }}{{ $category->trashed() ? ' (archived)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="condition" value="Condition" />
                    <select id="condition" name="condition" class="app-input mt-1">
                        <option value="">All condition</option>
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition->value }}" @selected($filters['condition'] === $condition->value)>{{ $condition->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="active" value="Active" />
                    <select id="active" name="active" class="app-input mt-1">
                        <option value="">All active states</option>
                        <option value="1" @selected($filters['active'] === '1')>Active</option>
                        <option value="0" @selected($filters['active'] === '0')>Inactive</option>
                    </select>
                </div>
            </div>
        </form>

        <div class="app-card overflow-hidden">
            <div class="hidden overflow-x-auto md:block">
                <table class="app-table">
                    <thead class="app-table-head">
                        <tr>
                            <th class="app-table-th" scope="col">Asset</th>
                            <th class="app-table-th" scope="col">Category</th>
                            <th class="app-table-th" scope="col">Brand/Model</th>
                            <th class="app-table-th" scope="col">Location</th>
                            <th class="app-table-th" scope="col">Condition</th>
                            <th class="app-table-th" scope="col">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($assets as $asset)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4 text-sm">
                                    <p class="font-semibold text-slate-950">{{ $asset->asset_code }}</p>
                                    <a href="{{ route('assets.show', $asset) }}" class="mt-1 block font-medium text-indigo-700 hover:text-indigo-900">{{ $asset->name }}</a>
                                    @if ($asset->serial_number)
                                        <p class="mt-1 text-xs text-slate-500">{{ $asset->serial_number }}</p>
                                    @endif
                                </td>
                                <td class="app-table-td">
                                    {{ $asset->category->name }}
                                    @if ($asset->category->trashed())
                                        <span class="ml-1 text-xs text-slate-500">(archived)</span>
                                    @endif
                                </td>
                                <td class="app-table-td">{{ trim(($asset->brand ?? '').' '.($asset->model ?? '')) ?: 'Not specified' }}</td>
                                <td class="app-table-td">{{ $asset->location }}</td>
                                <td class="px-4 py-4"><x-asset-condition-badge :condition="$asset->condition" /></td>
                                <td class="px-4 py-4">
                                    @if ($asset->is_active)
                                        <span class="app-badge bg-emerald-50 text-emerald-700 ring-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>Active</span>
                                    @else
                                        <span class="app-badge bg-slate-100 text-slate-700 ring-slate-300"><span class="h-1.5 w-1.5 rounded-full bg-slate-400" aria-hidden="true"></span>Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right text-sm">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('assets.show', $asset) }}" class="app-link">View</a>
                                        @can('update', $asset)
                                            <a href="{{ route('assets.edit', $asset) }}" class="font-medium text-slate-700 hover:text-slate-950">Edit</a>
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
                                <td colspan="7" class="px-4 py-6">
                                    <x-empty-state title="No assets found." description="Try changing filters or create an asset if you are an administrator." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 p-4 md:hidden">
                @forelse ($assets as $asset)
                    <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-indigo-700">{{ $asset->asset_code }}</p>
                                <a href="{{ route('assets.show', $asset) }}" class="mt-1 block break-words text-base font-semibold text-slate-950">{{ $asset->name }}</a>
                            </div>
                            <x-asset-condition-badge :condition="$asset->condition" />
                        </div>
                        <dl class="mt-4 grid gap-2 text-sm text-slate-600">
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Category</dt>
                                <dd class="text-right text-slate-800">{{ $asset->category->name }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Brand/model</dt>
                                <dd class="text-right text-slate-800">{{ trim(($asset->brand ?? '').' '.($asset->model ?? '')) ?: 'Not specified' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Location</dt>
                                <dd class="text-right text-slate-800">{{ $asset->location }}</dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <a href="{{ route('assets.show', $asset) }}" class="app-button-secondary">View Asset</a>
                            @can('update', $asset)
                                <a href="{{ route('assets.edit', $asset) }}" class="app-link">Edit</a>
                            @endcan
                        </div>
                    </article>
                @empty
                    <x-empty-state title="No assets found." description="Try changing filters or create an asset if you are an administrator." />
                @endforelse
            </div>
        </div>

        <div>
            {{ $assets->links() }}
        </div>
    </div>
</x-app-layout>
