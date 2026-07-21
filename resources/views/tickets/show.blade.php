<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase text-teal-700">{{ $ticket->ticket_code }}</p>
                <h1 class="text-2xl font-semibold text-stone-950">{{ $ticket->title }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('update', $ticket)
                    <a href="{{ route('tickets.edit', $ticket) }}" class="inline-flex items-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 shadow-sm hover:border-teal-300 hover:text-teal-700">Edit</a>
                @endcan
                @can('delete', $ticket)
                    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Archive this ticket?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center rounded-md bg-red-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-800">Archive</button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <h2 class="text-base font-semibold text-stone-950">Report Detail</h2>
                    <div class="mt-5 space-y-5 text-sm">
                        <div>
                            <p class="font-medium text-stone-500">Description</p>
                            <p class="mt-1 whitespace-pre-line leading-6 text-stone-800">{{ $ticket->description }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-stone-500">Location</p>
                            <p class="mt-1 text-stone-800">{{ $ticket->location }}</p>
                        </div>
                        @if ($ticket->resolution_note)
                            <div>
                                <p class="font-medium text-stone-500">Resolution Note</p>
                                <p class="mt-1 whitespace-pre-line leading-6 text-stone-800">{{ $ticket->resolution_note }}</p>
                            </div>
                        @endif
                    </div>
                </section>

                <aside class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-stone-950">Ticket Info</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="font-medium text-stone-500">Category</dt>
                            <dd class="mt-1 text-stone-800">
                                {{ $ticket->category->name }}
                                @if ($ticket->category->trashed())
                                    <span class="text-xs text-stone-500">(archived)</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Priority</dt>
                            <dd class="mt-1"><x-priority-badge :priority="$ticket->priority" /></dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Status</dt>
                            <dd class="mt-1"><x-status-badge :status="$ticket->status" /></dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Requester</dt>
                            <dd class="mt-1 text-stone-800">{{ $ticket->requester->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Assigned Technician</dt>
                            <dd class="mt-1 text-stone-800">{{ $ticket->technician?->name ?? 'Belum ditugaskan' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Created</dt>
                            <dd class="mt-1 text-stone-800">{{ $ticket->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-stone-500">Updated</dt>
                            <dd class="mt-1 text-stone-800">{{ $ticket->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                        @if ($ticket->resolved_at)
                            <div>
                                <dt class="font-medium text-stone-500">Resolved</dt>
                                <dd class="mt-1 text-stone-800">{{ $ticket->resolved_at->format('d M Y H:i') }}</dd>
                            </div>
                        @endif
                        @if ($ticket->closed_at)
                            <div>
                                <dt class="font-medium text-stone-500">Closed</dt>
                                <dd class="mt-1 text-stone-800">{{ $ticket->closed_at->format('d M Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
