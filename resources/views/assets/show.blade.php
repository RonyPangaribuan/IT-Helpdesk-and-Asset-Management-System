<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase text-teal-700">{{ $asset->asset_code }}</p>
                <h1 class="text-2xl font-semibold text-stone-950">{{ $asset->name }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('update', $asset)
                    <a href="{{ route('assets.edit', $asset) }}" class="inline-flex items-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 shadow-sm hover:border-teal-300 hover:text-teal-700">Edit</a>
                @endcan
                @can('delete', $asset)
                    <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Archive this asset?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center rounded-md bg-red-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-800">Archive</button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <h2 class="text-base font-semibold text-stone-950">Asset Detail</h2>
                    <dl class="mt-5 grid gap-5 text-sm md:grid-cols-2">
                        <div>
                            <dt class="font-medium text-stone-500">Asset Code</dt>
                            <dd class="mt-1 text-stone-800">{{ $asset->asset_code }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Category</dt>
                            <dd class="mt-1 text-stone-800">
                                {{ $asset->category->name }}
                                @if ($asset->category->trashed())
                                    <span class="text-xs text-stone-500">(archived)</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Brand</dt>
                            <dd class="mt-1 text-stone-800">{{ $asset->brand ?: 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Model</dt>
                            <dd class="mt-1 text-stone-800">{{ $asset->model ?: 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Serial Number</dt>
                            <dd class="mt-1 text-stone-800">{{ $asset->serial_number ?: 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Location</dt>
                            <dd class="mt-1 text-stone-800">{{ $asset->location }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Condition</dt>
                            <dd class="mt-1"><x-asset-condition-badge :condition="$asset->condition" /></dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Active Status</dt>
                            <dd class="mt-1">
                                @if ($asset->is_active)
                                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-1 text-xs font-medium text-stone-700 ring-1 ring-inset ring-stone-300">Inactive</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Created</dt>
                            <dd class="mt-1 text-stone-800">{{ $asset->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Updated</dt>
                            <dd class="mt-1 text-stone-800">{{ $asset->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 text-sm">
                        <p class="font-medium text-stone-500">Description</p>
                        <p class="mt-1 whitespace-pre-line leading-6 text-stone-800">{{ $asset->description ?: 'No description' }}</p>
                    </div>
                </section>

                <aside class="h-fit rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-stone-950">Inventory Status</h2>
                    <div class="mt-5 space-y-4 text-sm">
                        <div>
                            <p class="font-medium text-stone-500">Ticket selectable</p>
                            <p class="mt-1 text-stone-800">{{ $asset->isSelectableForTickets() ? 'Yes' : 'No' }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-stone-500">Related tickets</p>
                            <p class="mt-1 text-stone-800">{{ $relatedTickets->total() }}</p>
                        </div>
                    </div>
                </aside>
            </div>

            <section class="mt-6 rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-stone-950">Related Tickets</h2>
                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Requester</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Priority</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($relatedTickets as $ticket)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-stone-950">{{ $ticket->ticket_code }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        @can('view', $ticket)
                                            <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-teal-700 hover:text-teal-900">{{ $ticket->title }}</a>
                                        @else
                                            <span class="text-stone-700">{{ $ticket->title }}</span>
                                        @endcan
                                    </td>
                                    <td class="px-4 py-4 text-sm text-stone-700">{{ $ticket->requester->name }}</td>
                                    <td class="px-4 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                    <td class="px-4 py-4"><x-status-badge :status="$ticket->status" /></td>
                                    <td class="px-4 py-4 text-sm text-stone-700">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-sm text-stone-600">
                                        No related tickets found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $relatedTickets->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
