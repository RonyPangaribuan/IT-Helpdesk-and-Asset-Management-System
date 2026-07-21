<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase text-teal-700">Admin</p>
                <h1 class="text-2xl font-semibold text-stone-950">Ticket Categories</h1>
            </div>
            <a href="{{ route('admin.ticket-categories.create') }}" class="inline-flex w-fit items-center rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-800">
                Create Category
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Tickets</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-stone-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="px-4 py-4 text-sm font-medium text-stone-950">{{ $category->name }}</td>
                                    <td class="px-4 py-4 text-sm text-stone-700">{{ $category->description ?: 'No description' }}</td>
                                    <td class="px-4 py-4 text-sm text-stone-700">{{ $category->tickets_count }}</td>
                                    <td class="px-4 py-4">
                                        @if ($category->trashed() || ! $category->is_active)
                                            <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-1 text-xs font-medium text-stone-700 ring-1 ring-inset ring-stone-300">Archived</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm">
                                        @if (! $category->trashed())
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('admin.ticket-categories.edit', $category) }}" class="font-medium text-teal-700 hover:text-teal-900">Edit</a>
                                                <form method="POST" action="{{ route('admin.ticket-categories.destroy', $category) }}" onsubmit="return confirm('Archive this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-medium text-red-700 hover:text-red-900">Archive</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-stone-500">Archived</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-sm text-stone-600">
                                        No ticket categories found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
