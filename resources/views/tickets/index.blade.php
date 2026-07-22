<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Tickets"
            title="Ticket List"
            description="Track, filter, and open IT support requests based on your role."
        >
            <x-slot name="actions">
                @can('create', \App\Models\Ticket::class)
                    <a href="{{ route('tickets.create') }}" class="app-button-primary">Create Ticket</a>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-flash-messages />

        <form method="GET" action="{{ route('tickets.index') }}" class="app-card p-4 sm:p-5">
            <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Filter tickets</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $tickets->total() }} {{ Str::plural('result', $tickets->total()) }} found.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-primary-button>Apply</x-primary-button>
                    <a href="{{ route('tickets.index') }}" class="app-button-secondary">Reset</a>
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-5">
                <div class="md:col-span-2">
                    <x-input-label for="q" value="Search" />
                    <x-text-input id="q" name="q" type="search" class="mt-1" value="{{ $filters['q'] }}" placeholder="Code, title, or asset" />
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="app-input mt-1">
                        <option value="">All status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="priority" value="Priority" />
                    <select id="priority" name="priority" class="app-input mt-1">
                        <option value="">All priority</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->value }}" @selected($filters['priority'] === $priority->value)>{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="ticket_category_id" value="Category" />
                    <select id="ticket_category_id" name="ticket_category_id" class="app-input mt-1">
                        <option value="">All category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) $filters['ticket_category_id'] === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="app-card overflow-hidden">
            <div class="hidden overflow-x-auto md:block">
                <table class="app-table">
                    <thead class="app-table-head">
                        <tr>
                            <th class="app-table-th" scope="col">Ticket</th>
                            <th class="app-table-th" scope="col">Category</th>
                            <th class="app-table-th" scope="col">Asset</th>
                            <th class="app-table-th" scope="col">Priority</th>
                            <th class="app-table-th" scope="col">Status</th>
                            <th class="app-table-th" scope="col">Requester</th>
                            <th class="app-table-th" scope="col">Technician</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($tickets as $ticket)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4 text-sm">
                                    <p class="font-semibold text-slate-950">{{ $ticket->ticket_code }}</p>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="mt-1 block break-words font-medium text-indigo-700 hover:text-indigo-900">{{ $ticket->title }}</a>
                                    <p class="mt-1 text-xs text-slate-500">Created {{ $ticket->created_at->format('d M Y H:i') }}</p>
                                </td>
                                <td class="app-table-td">
                                    {{ $ticket->category->name }}
                                    @if ($ticket->category->trashed())
                                        <span class="ml-1 text-xs text-slate-500">(archived)</span>
                                    @endif
                                </td>
                                <td class="app-table-td">
                                    @if ($ticket->asset)
                                        {{ $ticket->asset->asset_code }}
                                        @if ($ticket->asset->trashed())
                                            <span class="ml-1 text-xs text-slate-500">(archived)</span>
                                        @endif
                                    @else
                                        No asset
                                    @endif
                                </td>
                                <td class="px-4 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                <td class="px-4 py-4"><x-status-badge :status="$ticket->status" /></td>
                                <td class="app-table-td">{{ $ticket->requester->name }}</td>
                                <td class="app-table-td">{{ $ticket->technician?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-4 text-right text-sm">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="app-link">View</a>
                                        @can('update', $ticket)
                                            <a href="{{ route('tickets.edit', $ticket) }}" class="font-medium text-slate-700 hover:text-slate-950">Edit</a>
                                        @endcan
                                        @can('delete', $ticket)
                                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Archive this ticket?');">
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
                                <td colspan="8" class="px-4 py-6">
                                    @if (Auth::user()->isRequester())
                                        <x-empty-state title="No tickets yet" description="Create your first support request and track it from report to resolution." :action-href="route('tickets.create')" action-label="Create Ticket" />
                                    @elseif (Auth::user()->isTechnician())
                                        <x-empty-state title="No assigned tickets" description="You currently have no support requests assigned to you." />
                                    @else
                                        <x-empty-state title="No matching tickets" description="Try changing or resetting the filters." />
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 p-4 md:hidden">
                @forelse ($tickets as $ticket)
                    <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-indigo-700">{{ $ticket->ticket_code }}</p>
                                <a href="{{ route('tickets.show', $ticket) }}" class="mt-1 block break-words text-base font-semibold text-slate-950">
                                    {{ $ticket->title }}
                                </a>
                            </div>
                            <x-status-badge :status="$ticket->status" />
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <x-priority-badge :priority="$ticket->priority" />
                            <span class="app-badge bg-slate-100 text-slate-700 ring-slate-300">{{ $ticket->category->name }}</span>
                        </div>

                        <dl class="mt-4 grid gap-2 text-sm text-slate-600">
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Asset</dt>
                                <dd class="text-right text-slate-800">{{ $ticket->asset?->asset_code ?? 'No asset' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Location</dt>
                                <dd class="text-right text-slate-800">{{ $ticket->location }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Created</dt>
                                <dd class="text-right text-slate-800">{{ $ticket->created_at->format('d M Y H:i') }}</dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <a href="{{ route('tickets.show', $ticket) }}" class="app-button-secondary">View Ticket</a>
                            @can('update', $ticket)
                                <a href="{{ route('tickets.edit', $ticket) }}" class="app-link">Edit</a>
                            @endcan
                        </div>
                    </article>
                @empty
                    @if (Auth::user()->isRequester())
                        <x-empty-state title="No tickets yet" description="Create your first support request and track it from report to resolution." :action-href="route('tickets.create')" action-label="Create Ticket" />
                    @elseif (Auth::user()->isTechnician())
                        <x-empty-state title="No assigned tickets" description="You currently have no support requests assigned to you." />
                    @else
                        <x-empty-state title="No matching tickets" description="Try changing or resetting the filters." />
                    @endif
                @endforelse
            </div>
        </div>

        <div>
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
