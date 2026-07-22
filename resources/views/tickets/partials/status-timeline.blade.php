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
