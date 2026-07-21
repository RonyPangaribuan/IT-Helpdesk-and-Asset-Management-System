<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase text-teal-700">Tickets</p>
                <h1 class="text-2xl font-semibold text-stone-950">Ticket List</h1>
            </div>
            @can('create', \App\Models\Ticket::class)
                <a href="{{ route('tickets.create') }}" class="inline-flex w-fit items-center rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-800">
                    Create Ticket
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <form method="GET" action="{{ route('tickets.index') }}" class="mb-6 rounded-lg border border-stone-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <x-input-label for="q" value="Search" />
                        <x-text-input id="q" name="q" type="search" class="mt-1 block w-full" value="{{ $filters['q'] }}" placeholder="Code or title" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="priority" value="Priority" />
                        <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All priority</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->value }}" @selected($filters['priority'] === $priority->value)>{{ $priority->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="ticket_category_id" value="Category" />
                        <select id="ticket_category_id" name="ticket_category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) $filters['ticket_category_id'] === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-primary-button>Apply</x-primary-button>
                    <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-stone-600 hover:text-stone-950">Reset</a>
                </div>
            </form>

            <div class="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Priority</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Requester</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-stone-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-stone-950">{{ $ticket->ticket_code }}</td>
                                    <td class="px-4 py-4 text-sm text-stone-700">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-teal-700 hover:text-teal-900">{{ $ticket->title }}</a>
                                        <div class="mt-1 text-xs text-stone-500">{{ $ticket->created_at->format('d M Y H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-stone-700">
                                        {{ $ticket->category->name }}
                                        @if ($ticket->category->trashed())
                                            <span class="ml-1 text-xs text-stone-500">(archived)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                    <td class="px-4 py-4"><x-status-badge :status="$ticket->status" /></td>
                                    <td class="px-4 py-4 text-sm text-stone-700">{{ $ticket->requester->name }}</td>
                                    <td class="px-4 py-4 text-right text-sm">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-teal-700 hover:text-teal-900">View</a>
                                            @can('update', $ticket)
                                                <a href="{{ route('tickets.edit', $ticket) }}" class="font-medium text-stone-700 hover:text-stone-950">Edit</a>
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
                                    <td colspan="7" class="px-4 py-12 text-center text-sm text-stone-600">
                                        No tickets found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
