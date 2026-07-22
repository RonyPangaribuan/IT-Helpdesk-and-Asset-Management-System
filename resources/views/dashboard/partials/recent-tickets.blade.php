<div class="mt-5 overflow-x-auto">
    <table class="min-w-full divide-y divide-stone-200">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Code</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Title</th>
                @if ($showRequester)
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Requester</th>
                @endif
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Priority</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Asset</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-stone-600">Created</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-200 bg-white">
            @forelse ($tickets as $ticket)
                <tr>
                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-stone-950">{{ $ticket->ticket_code }}</td>
                    <td class="px-4 py-4 text-sm">
                        <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-teal-700 hover:text-teal-900">{{ $ticket->title }}</a>
                    </td>
                    @if ($showRequester)
                        <td class="px-4 py-4 text-sm text-stone-700">{{ $ticket->requester->name }}</td>
                    @endif
                    <td class="px-4 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                    <td class="px-4 py-4"><x-status-badge :status="$ticket->status" /></td>
                    <td class="px-4 py-4 text-sm text-stone-700">{{ $ticket->asset?->asset_code ?? 'No asset' }}</td>
                    <td class="px-4 py-4 text-sm text-stone-700">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showRequester ? 7 : 6 }}" class="px-4 py-12 text-center text-sm text-stone-600">
                        No recent tickets found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
