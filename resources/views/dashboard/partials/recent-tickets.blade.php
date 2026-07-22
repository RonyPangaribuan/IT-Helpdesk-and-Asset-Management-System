<div class="hidden overflow-x-auto md:block">
    <table class="app-table">
        <thead class="app-table-head">
            <tr>
                <th class="app-table-th" scope="col">Code</th>
                <th class="app-table-th" scope="col">Title</th>
                @if ($showRequester)
                    <th class="app-table-th" scope="col">Requester</th>
                @endif
                <th class="app-table-th" scope="col">Priority</th>
                <th class="app-table-th" scope="col">Status</th>
                <th class="app-table-th" scope="col">Asset</th>
                <th class="app-table-th" scope="col">Created</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($tickets as $ticket)
                <tr class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-slate-950">{{ $ticket->ticket_code }}</td>
                    <td class="px-4 py-4 text-sm">
                        <a href="{{ route('tickets.show', $ticket) }}" class="app-link">{{ $ticket->title }}</a>
                    </td>
                    @if ($showRequester)
                        <td class="app-table-td">{{ $ticket->requester->name }}</td>
                    @endif
                    <td class="px-4 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                    <td class="px-4 py-4"><x-status-badge :status="$ticket->status" /></td>
                    <td class="app-table-td">{{ $ticket->asset?->asset_code ?? 'No asset' }}</td>
                    <td class="app-table-td">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showRequester ? 7 : 6 }}" class="px-4 py-6">
                        <x-empty-state title="No recent tickets found." description="New ticket activity will appear here." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="space-y-3 md:hidden">
    @forelse ($tickets as $ticket)
        <article class="rounded-lg border border-slate-200 bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-indigo-700">{{ $ticket->ticket_code }}</p>
                    <a href="{{ route('tickets.show', $ticket) }}" class="mt-1 block break-words text-sm font-semibold text-slate-950">{{ $ticket->title }}</a>
                </div>
                <x-status-badge :status="$ticket->status" />
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                <x-priority-badge :priority="$ticket->priority" />
                <span class="app-badge bg-slate-100 text-slate-700 ring-slate-300">{{ $ticket->asset?->asset_code ?? 'No asset' }}</span>
            </div>
            <p class="mt-3 text-xs text-slate-500">{{ $ticket->created_at->format('d M Y H:i') }}</p>
        </article>
    @empty
        <x-empty-state title="No recent tickets found." description="New ticket activity will appear here." />
    @endforelse
</div>
