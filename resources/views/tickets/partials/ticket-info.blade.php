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
