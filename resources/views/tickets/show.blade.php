<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase text-teal-700">{{ $ticket->ticket_code }}</p>
                <h1 class="break-words text-2xl font-semibold text-stone-950">{{ $ticket->title }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('update', $ticket)
                    <a href="{{ route('tickets.edit', $ticket) }}" class="inline-flex items-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 shadow-sm hover:border-teal-300 hover:text-teal-700">Edit</a>
                @endcan
                @can('delete', $ticket)
                    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Archive this ticket?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center rounded-md bg-red-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-800">Archive</button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            @if ($errors->has('workflow'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first('workflow') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    @include('tickets.partials.report-detail')
                    @include('tickets.partials.workflow-actions')
                    @include('tickets.partials.attachments')
                    @include('tickets.partials.comments')
                    @include('tickets.partials.status-timeline')
                </div>

                <aside class="h-fit rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                    @include('tickets.partials.ticket-info')
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
