@php
    $user = auth()->user();
    $canAssign = $user?->can('assign', $ticket) ?? false;
    $canReassign = $user?->can('reassign', $ticket) ?? false;
    $canStartWork = $user?->can('startWork', $ticket) ?? false;
    $canCancel = $user?->can('cancel', $ticket) ?? false;
    $canResolve = $user?->can('resolve', $ticket) ?? false;
    $canClose = $user?->can('close', $ticket) ?? false;
    $canReopen = $user?->can('reopen', $ticket) ?? false;
@endphp

@if ($canAssign || $canReassign || $canStartWork || $canCancel || $canResolve || $canClose || $canReopen)
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
                    <x-primary-button>{{ $ticket->status === App\Enums\TicketStatus::Reopened ? 'Resume Work' : 'Start Work' }}</x-primary-button>
                </form>
            @endcan

            @can('resolve', $ticket)
                <form method="POST" action="{{ route('tickets.resolve', $ticket) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <x-input-label for="resolution_note" value="Resolution Note" />
                        <textarea id="resolution_note" name="resolution_note" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('resolution_note') }}</textarea>
                        <x-input-error :messages="$errors->get('resolution_note')" class="mt-2" />
                    </div>
                    <x-primary-button>Resolve Ticket</x-primary-button>
                </form>
            @endcan

            @can('close', $ticket)
                <form method="POST" action="{{ route('tickets.close', $ticket) }}" onsubmit="return confirm('Close this resolved ticket?');">
                    @csrf
                    @method('PATCH')
                    <x-primary-button>Close Ticket</x-primary-button>
                </form>
            @endcan

            @can('reopen', $ticket)
                <form method="POST" action="{{ route('tickets.reopen', $ticket) }}" class="space-y-3" onsubmit="return confirm('Reopen this ticket for additional work?');">
                    @csrf
                    @method('PATCH')
                    <div>
                        <x-input-label for="reopen_reason" value="Reopen Reason" />
                        <textarea id="reopen_reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>
                    <x-primary-button>Reopen Ticket</x-primary-button>
                </form>
            @endcan

            @can('cancel', $ticket)
                <form method="POST" action="{{ route('tickets.cancel', $ticket) }}" class="space-y-3" onsubmit="return confirm('Cancel this ticket?');">
                    @csrf
                    @method('PATCH')
                    <div>
                        <x-input-label for="cancel_reason" value="Cancellation Reason" />
                        <textarea id="cancel_reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>
                    <button type="submit" class="inline-flex items-center rounded-md bg-red-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-800">Cancel Ticket</button>
                </form>
            @endcan
        </div>
    </section>
@endif
