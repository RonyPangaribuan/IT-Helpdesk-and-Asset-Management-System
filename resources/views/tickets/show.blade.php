<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :eyebrow="$ticket->ticket_code"
            :title="$ticket->title"
            description="Review the report, workflow state, discussion, attachments, and available next action."
        >
            <x-slot name="actions">
                <x-status-badge :status="$ticket->status" />
                <x-priority-badge :priority="$ticket->priority" />
                @can('update', $ticket)
                    <a href="{{ route('tickets.edit', $ticket) }}" class="app-button-secondary">Edit</a>
                @endcan
                @can('delete', $ticket)
                    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Archive this ticket?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="app-button-danger">Archive</button>
                    </form>
                @endcan
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-flash-messages />

        @if ($errors->has('workflow'))
            <div class="flex gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.3 4.3h3.4L21 18H3L10.3 4.3z" />
                </svg>
                <div>
                    <p class="font-semibold">Workflow action failed</p>
                    <p class="mt-1">{{ $errors->first('workflow') }}</p>
                </div>
            </div>
        @endif

        <x-ticket-workflow-progress :ticket="$ticket" />

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                @include('tickets.partials.report-detail')
                @include('tickets.partials.workflow-actions')
                @include('tickets.partials.attachments')
                @include('tickets.partials.comments')
                @include('tickets.partials.status-timeline')
            </div>

            <aside class="space-y-6 lg:col-span-1">
                <x-section-card title="Ticket Info" description="Ownership, asset, and lifecycle metadata.">
                    @include('tickets.partials.ticket-info')
                </x-section-card>
            </aside>
        </div>
    </div>
</x-app-layout>
