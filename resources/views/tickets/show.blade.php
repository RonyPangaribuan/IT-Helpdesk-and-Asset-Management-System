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

            @if ($errors->has('workflow'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first('workflow') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
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

                    @if (Auth::user()->can('assign', $ticket) || Auth::user()->can('reassign', $ticket) || Auth::user()->can('startWork', $ticket) || Auth::user()->can('cancel', $ticket))
                        <section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="text-base font-semibold text-stone-950">Workflow Actions</h2>

                            <div class="mt-5 space-y-6">
                                @can('assign', $ticket)
                                    <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <x-input-label for="technician_id_assign" value="Assign Technician" />
                                            <select id="technician_id_assign" name="technician_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                <option value="">Select active technician</option>
                                                @foreach ($technicians as $technician)
                                                    <option value="{{ $technician->id }}" @selected((int) old('technician_id') === $technician->id)>{{ $technician->name }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('technician_id')" class="mt-2" />
                                        </div>
                                        <x-primary-button>Assign Ticket</x-primary-button>
                                    </form>
                                @endcan

                                @can('reassign', $ticket)
                                    <form method="POST" action="{{ route('tickets.reassign', $ticket) }}" class="space-y-3" onsubmit="return confirm('Reassign this ticket to another technician?');">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <x-input-label for="technician_id_reassign" value="Reassign Technician" />
                                            <select id="technician_id_reassign" name="technician_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                <option value="">Select different active technician</option>
                                                @foreach ($technicians as $technician)
                                                    @if ($technician->id !== $ticket->technician_id)
                                                        <option value="{{ $technician->id }}" @selected((int) old('technician_id') === $technician->id)>{{ $technician->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('technician_id')" class="mt-2" />
                                        </div>
                                        <x-primary-button>Reassign Ticket</x-primary-button>
                                    </form>
                                @endcan

                                @can('startWork', $ticket)
                                    <form method="POST" action="{{ route('tickets.start-work', $ticket) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-primary-button>Start Work</x-primary-button>
                                    </form>
                                @endcan

                                @can('cancel', $ticket)
                                    <form method="POST" action="{{ route('tickets.cancel', $ticket) }}" class="space-y-3" onsubmit="return confirm('Cancel this ticket?');">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <x-input-label for="reason" value="Cancellation Reason" />
                                            <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('reason') }}</textarea>
                                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                                        </div>
                                        <button type="submit" class="inline-flex items-center rounded-md bg-red-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-800">Cancel Ticket</button>
                                    </form>
                                @endcan
                            </div>
                        </section>
                    @endif

                    <section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                        <h2 class="text-base font-semibold text-stone-950">Status Timeline</h2>
                        <ol class="mt-5 space-y-5">
                            @forelse ($ticket->statusHistories as $history)
                                @php
                                    $isReassignment = $history->old_status && $history->new_status && $history->old_status === $history->new_status;
                                @endphp
                                <li class="border-l-2 border-stone-200 pl-4">
                                    <p class="text-xs font-medium uppercase text-stone-500">{{ $history->created_at->format('d M Y H:i') }}</p>
                                    <p class="mt-1 text-sm font-semibold text-stone-950">{{ $history->changedBy->name }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        @if ($isReassignment)
                                            <span class="text-sm font-medium text-stone-700">Technician reassigned</span>
                                        @elseif ($history->old_status)
                                            <x-status-badge :status="$history->old_status" />
                                            <span class="text-sm text-stone-400">-&gt;</span>
                                            <x-status-badge :status="$history->new_status" />
                                        @else
                                            <x-status-badge :status="$history->new_status" />
                                        @endif
                                    </div>
                                    @if ($history->note)
                                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-stone-700">{{ $history->note }}</p>
                                    @endif
                                </li>
                            @empty
                                <li class="text-sm text-stone-600">No status history recorded.</li>
                            @endforelse
                        </ol>
                    </section>
                </div>

                <aside class="h-fit rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
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
