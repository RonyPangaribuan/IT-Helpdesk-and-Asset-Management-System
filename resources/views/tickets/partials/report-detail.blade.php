<x-section-card title="Main Report" description="Original issue details and current resolution context.">
    <div class="space-y-6 text-sm">
        <div>
            <p class="font-medium text-slate-500">Description</p>
            <p class="mt-2 whitespace-pre-line break-words leading-7 text-slate-800">{{ $ticket->description }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="font-medium text-slate-500">Location</p>
                <p class="mt-1 text-slate-900">{{ $ticket->location }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="font-medium text-slate-500">Category</p>
                <p class="mt-1 text-slate-900">
                    {{ $ticket->category->name }}
                    @if ($ticket->category->trashed())
                        <span class="text-xs text-slate-500">(archived)</span>
                    @endif
                </p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="font-medium text-slate-500">Requester</p>
                <p class="mt-1 text-slate-900">{{ $ticket->requester->name }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="font-medium text-slate-500">Technician</p>
                <p class="mt-1 text-slate-900">{{ $ticket->technician?->name ?? 'Unassigned' }}</p>
            </div>
        </div>

        @if ($ticket->resolution_note)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="font-semibold text-emerald-900">Resolution</p>
                    @if ($ticket->resolved_at)
                        <p class="text-xs text-emerald-700">{{ $ticket->resolved_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
                <p class="mt-2 whitespace-pre-line break-words leading-7 text-emerald-900">{{ $ticket->resolution_note }}</p>
            </div>
        @endif
    </div>
</x-section-card>
