<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Admin"
            title="Asset Categories"
            description="Archived categories remain visible on existing assets but cannot be selected for new records."
        >
            <x-slot name="actions">
                <a href="{{ route('admin.asset-categories.create') }}" class="app-button-primary">Create Category</a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-flash-messages />

        <div class="app-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead class="app-table-head">
                        <tr>
                            <th class="app-table-th" scope="col">Name</th>
                            <th class="app-table-th" scope="col">Description</th>
                            <th class="app-table-th" scope="col">Assets</th>
                            <th class="app-table-th" scope="col">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4 text-sm font-semibold text-slate-950">{{ $category->name }}</td>
                                <td class="max-w-md px-4 py-4 text-sm text-slate-700">{{ $category->description ?: 'No description' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-700">{{ $category->assets_count }}</td>
                                <td class="px-4 py-4">
                                    @if ($category->trashed() || ! $category->is_active)
                                        <span class="app-badge bg-slate-100 text-slate-700 ring-slate-300"><span class="h-1.5 w-1.5 rounded-full bg-slate-400" aria-hidden="true"></span>Archived</span>
                                    @else
                                        <span class="app-badge bg-emerald-50 text-emerald-700 ring-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>Active</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right text-sm">
                                    @if (! $category->trashed())
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.asset-categories.edit', $category) }}" class="app-link">Edit</a>
                                            <form method="POST" action="{{ route('admin.asset-categories.destroy', $category) }}" onsubmit="return confirm('Archive this category?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-700 hover:text-red-900">Archive</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-slate-500">Archived</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6">
                                    <x-empty-state title="No asset categories found." description="Create categories to organize managed inventory." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $categories->links() }}
        </div>
    </div>
</x-app-layout>
