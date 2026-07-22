@php
    $description = match ($card['label']) {
        'Open' => 'Needs review',
        'Assigned' => 'Ready to start',
        'In Progress' => 'Currently active',
        'Resolved' => 'Awaiting confirmation',
        'Closed' => 'Completed work',
        'Active Assets' => 'Selectable inventory',
        default => 'Current scope',
    };
@endphp

<a href="{{ $card['href'] }}" class="group app-card block p-5 hover:border-indigo-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $card['value'] }}</p>
            <p class="mt-2 text-xs leading-5 text-slate-500">{{ $description }}</p>
        </div>
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100 group-hover:bg-indigo-100" aria-hidden="true">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
            </svg>
        </span>
    </div>
</a>
