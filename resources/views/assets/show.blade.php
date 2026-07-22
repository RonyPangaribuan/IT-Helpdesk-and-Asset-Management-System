<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :eyebrow="$asset->asset_code"
            :title="$asset->name"
            description="Asset information, operating condition, and related ticket history."
        >
            <x-slot name="actions">
                <x-asset-condition-badge :condition="$asset->condition" />
                @if ($asset->is_active)
                    <span class="app-badge bg-emerald-50 text-emerald-700 ring-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>Active</span>
                @else
                    <span class="app-badge bg-slate-100 text-slate-700 ring-slate-300"><span class="h-1.5 w-1.5 rounded-full bg-slate-400" aria-hidden="true"></span>Inactive</span>
                @endif
                @can('update', $asset)
                    <a href="{{ route('assets.edit', $asset) }}" class="app-button-secondary">Edit</a>
                @endcan
                @can('delete', $asset)
                    <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Archive this asset?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="app-button-danger">Archive</button>
                    </form>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-flash-messages />

        <div class="grid gap-6 lg:grid-cols-3">
            <x-section-card class="lg:col-span-2" title="Asset Information" description="Core identity and technical details.">
                <dl class="grid gap-5 text-sm md:grid-cols-2">
                    <div>
                        <dt class="font-medium text-slate-500">Asset Code</dt>
                        <dd class="mt-1 text-slate-900">{{ $asset->asset_code }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500">Category</dt>
                        <dd class="mt-1 text-slate-900">
                            {{ $asset->category->name }}
                            @if ($asset->category->trashed())
                                <span class="text-xs text-slate-500">(archived)</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500">Brand</dt>
                        <dd class="mt-1 text-slate-900">{{ $asset->brand ?: 'Not specified' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500">Model</dt>
                        <dd class="mt-1 text-slate-900">{{ $asset->model ?: 'Not specified' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500">Serial Number</dt>
                        <dd class="mt-1 break-all text-slate-900">{{ $asset->serial_number ?: 'Not specified' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500">Location</dt>
                        <dd class="mt-1 text-slate-900">{{ $asset->location }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500">Created</dt>
                        <dd class="mt-1 text-slate-900">{{ $asset->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500">Updated</dt>
                        <dd class="mt-1 text-slate-900">{{ $asset->updated_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-slate-100 pt-5 text-sm">
                    <p class="font-medium text-slate-500">Description</p>
                    <p class="mt-2 whitespace-pre-line break-words leading-7 text-slate-800">{{ $asset->description ?: 'No description' }}</p>
                </div>
            </x-section-card>

            <x-section-card title="Inventory Status" description="How this asset can be used in ticket intake.">
                <dl class="space-y-5 text-sm">
                    <div>
                        <dt class="font-medium text-slate-500">Condition</dt>
                        <dd class="mt-1"><x-asset-condition-badge :condition="$asset->condition" /></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500">Ticket selectable</dt>
                        <dd class="mt-1 text-slate-900">{{ $asset->isSelectableForTickets() ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-500">Related tickets</dt>
                        <dd class="mt-1 text-slate-900">{{ $relatedTickets->total() }}</dd>
                    </div>
                </dl>
            </x-section-card>
        </div>

        <x-section-card title="Related Tickets" description="Ticket history connected to this asset and visible to your role.">
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead class="app-table-head">
                        <tr>
                            <th class="app-table-th" scope="col">Code</th>
                            <th class="app-table-th" scope="col">Title</th>
                            <th class="app-table-th" scope="col">Requester</th>
                            <th class="app-table-th" scope="col">Priority</th>
                            <th class="app-table-th" scope="col">Status</th>
                            <th class="app-table-th" scope="col">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($relatedTickets as $ticket)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-slate-950">{{ $ticket->ticket_code }}</td>
                                <td class="px-4 py-4 text-sm">
                                    @can('view', $ticket)
                                        <a href="{{ route('tickets.show', $ticket) }}" class="app-link">{{ $ticket->title }}</a>
                                    @else
                                        <span class="text-slate-700">{{ $ticket->title }}</span>
                                    @endcan
                                </td>
                                <td class="app-table-td">{{ $ticket->requester->name }}</td>
                                <td class="px-4 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                <td class="px-4 py-4"><x-status-badge :status="$ticket->status" /></td>
                                <td class="app-table-td">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6">
                                    <x-empty-state title="No related tickets found." description="Tickets linked to this asset will appear here." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $relatedTickets->links() }}
            </div>
        </x-section-card>
    </div>
</x-app-layout>
