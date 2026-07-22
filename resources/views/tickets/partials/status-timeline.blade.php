<x-section-card title="Status Timeline" description="Read-only history of important workflow changes.">
    <ol class="space-y-6">
        @forelse ($ticket->statusHistories as $history)
            @php
                $isReassignment = $history->old_status && $history->new_status && $history->old_status === $history->new_status;
            @endphp
            <li class="relative pl-8">
                <span class="absolute left-0 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-white ring-2 ring-indigo-200" aria-hidden="true">
                    <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                </span>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-950">
                        @if ($isReassignment)
                            Technician reassigned
                        @elseif ($history->old_status)
                            Status changed
                        @else
                            Ticket created
                        @endif
                    </p>
                    <p class="mt-1 text-xs text-slate-500">{{ $history->changedBy->name }} / {{ $history->created_at->format('d M Y H:i') }}</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @if ($isReassignment)
                            <span class="text-sm font-medium text-slate-700">Assignment updated</span>
                        @elseif ($history->old_status)
                            <x-status-badge :status="$history->old_status" />
                            <span class="text-sm text-slate-400">to</span>
                            <x-status-badge :status="$history->new_status" />
                        @else
                            <x-status-badge :status="$history->new_status" />
                        @endif
                    </div>
                    @if ($history->note)
                        <p class="mt-3 whitespace-pre-line break-words text-sm leading-6 text-slate-700">{{ $history->note }}</p>
                    @endif
                </div>
            </li>
        @empty
            <li>
                <x-empty-state title="No status history recorded." description="Workflow history will appear after ticket creation or status changes." />
            </li>
        @endforelse
    </ol>
</x-section-card>
