@php
    $user = auth()->user();
    $canAssign = $user?->can('assign', $ticket) ?? false;
    $canReassign = $user?->can('reassign', $ticket) ?? false;
    $canStartWork = $user?->can('startWork', $ticket) ?? false;
    $canCancel = $user?->can('cancel', $ticket) ?? false;
    $canResolve = $user?->can('resolve', $ticket) ?? false;
    $canClose = $user?->can('close', $ticket) ?? false;
    $canReopen = $user?->can('reopen', $ticket) ?? false;
    $hasAction = $canAssign || $canReassign || $canStartWork || $canCancel || $canResolve || $canClose || $canReopen;
@endphp

<x-section-card
    title="{{ $canClose || $canReopen ? 'Resolution Ready' : 'Next Action' }}"
    description="Actions are shown only when your role and the current ticket status allow them."
>
    @if (! $hasAction)
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
            @if ($ticket->status === App\Enums\TicketStatus::Closed)
                This ticket has been completed and is now read-only.
            @elseif ($ticket->status === App\Enums\TicketStatus::Cancelled)
                This ticket was cancelled and can no longer be processed.
            @else
                No workflow action is available for your role at this status.
            @endif
        </div>
    @else
        <div class="space-y-6">
            @can('assign', $ticket)
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="space-y-3">
                    @csrf
                    <div>
                        <x-input-label for="technician_id_assign" value="Assign Technician" />
                        <select id="technician_id_assign" name="technician_id" class="app-input mt-1" required>
                            <option value="">Select active technician</option>
                            @foreach ($technicians as $technician)
                                <option value="{{ $technician->id }}" @selected((int) old('technician_id') === $technician->id)>{{ $technician->name }}</option>
                            @endforeach
                        </select>
                        <p class="app-help">Open tickets become Assigned after an administrator chooses an active technician.</p>
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
                        <select id="technician_id_reassign" name="technician_id" class="app-input mt-1" required>
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
                <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4 text-sm text-indigo-900">
                    This ticket is assigned to you and ready to be worked on.
                </div>
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
                        <textarea id="resolution_note" name="resolution_note" rows="5" class="app-input mt-1" required>{{ old('resolution_note') }}</textarea>
                        <p class="app-help">Explain what was fixed, replaced, configured, or verified.</p>
                        <x-input-error :messages="$errors->get('resolution_note')" class="mt-2" />
                    </div>
                    <x-primary-button>Resolve Ticket</x-primary-button>
                </form>
            @endcan

            @can('close', $ticket)
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm leading-6 text-emerald-900">
                    Review the technician's resolution. Close the ticket if the issue is solved, or reopen it if the issue persists.
                </div>
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
                        <textarea id="reopen_reason" name="reason" rows="4" class="app-input mt-1" required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>
                    <button type="submit" class="app-button-warning">Reopen Ticket</button>
                </form>
            @endcan

            @can('cancel', $ticket)
                <form method="POST" action="{{ route('tickets.cancel', $ticket) }}" class="space-y-3" onsubmit="return confirm('Cancel this ticket?');">
                    @csrf
                    @method('PATCH')
                    <div>
                        <x-input-label for="cancel_reason" value="Cancellation Reason" />
                        <textarea id="cancel_reason" name="reason" rows="4" class="app-input mt-1" required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>
                    <button type="submit" class="app-button-danger">Cancel Ticket</button>
                </form>
            @endcan
        </div>
    @endif
</x-section-card>
