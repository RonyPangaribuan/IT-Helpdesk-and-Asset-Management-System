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
        <dt class="font-medium text-stone-500">Related Asset</dt>
        <dd class="mt-1 text-stone-800">
            @if ($ticket->asset)
                <div class="space-y-1">
                    <div>
                        @can('view', $ticket->asset)
                            <a href="{{ route('assets.show', $ticket->asset) }}" class="font-medium text-teal-700 hover:text-teal-900">{{ $ticket->asset->asset_code }}</a>
                        @else
                            <span class="font-medium">{{ $ticket->asset->asset_code }}</span>
                        @endcan
                        @if ($ticket->asset->trashed())
                            <span class="text-xs text-stone-500">(archived)</span>
                        @elseif (! $ticket->asset->is_active)
                            <span class="text-xs text-stone-500">(inactive)</span>
                        @endif
                    </div>
                    <div>{{ $ticket->asset->name }}</div>
                    <div class="text-xs text-stone-500">{{ $ticket->asset->category->name }} - {{ $ticket->asset->location }}</div>
                    <x-asset-condition-badge :condition="$ticket->asset->condition" />
                </div>
            @else
                No related asset
            @endif
        </dd>
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
