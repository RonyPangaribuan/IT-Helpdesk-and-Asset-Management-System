@props([
    'ticket' => null,
    'status' => null,
])

@php
    $statusValue = $status instanceof \BackedEnum
        ? $status->value
        : ($ticket?->status instanceof \BackedEnum ? $ticket->status->value : (string) ($status ?? $ticket?->status ?? 'open'));

    $stages = [
        ['key' => 'open', 'label' => 'Reported'],
        ['key' => 'assigned', 'label' => 'Assigned'],
        ['key' => 'in_progress', 'label' => 'In Progress'],
        ['key' => 'resolved', 'label' => 'Resolved'],
        ['key' => 'closed', 'label' => 'Closed'],
    ];

    $statusOrder = [
        'open' => 0,
        'assigned' => 1,
        'reopened' => 1,
        'in_progress' => 2,
        'resolved' => 3,
        'closed' => 4,
    ];

    $activeIndex = $statusOrder[$statusValue] ?? null;
    $notice = match ($statusValue) {
        'reopened' => 'This ticket was reopened and requires additional work.',
        'closed' => 'This ticket has been completed and is now read-only.',
        'cancelled' => 'This ticket was cancelled and can no longer be processed.',
        default => null,
    };
@endphp

<div {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-5']) }}>
    <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-950">Ticket workflow</h2>
            <p class="text-sm text-slate-600">Current position in the support lifecycle.</p>
        </div>
    </div>

    @if ($statusValue === 'cancelled')
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-700 ring-1 ring-red-200" aria-hidden="true">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                </svg>
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-950">Cancelled</p>
                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $notice }}</p>
            </div>
        </div>
    @else
        <ol class="grid gap-3 sm:grid-cols-5" aria-label="Ticket workflow progress">
            @foreach ($stages as $index => $stage)
                @php
                    $isComplete = $activeIndex !== null && $index < $activeIndex;
                    $isActive = $activeIndex === $index;
                @endphp
                <li @class([
                    'rounded-lg border p-3',
                    'border-indigo-200 bg-indigo-50' => $isActive,
                    'border-emerald-200 bg-emerald-50' => $isComplete,
                    'border-slate-200 bg-slate-50' => ! $isActive && ! $isComplete,
                ])>
                    <div class="flex items-center gap-2">
                        <span @class([
                            'flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-semibold ring-1 ring-inset',
                            'bg-indigo-700 text-white ring-indigo-700' => $isActive,
                            'bg-emerald-600 text-white ring-emerald-600' => $isComplete,
                            'bg-white text-slate-400 ring-slate-300' => ! $isActive && ! $isComplete,
                        ]) aria-hidden="true">
                            @if ($isComplete)
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                </svg>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </span>
                        <span @class([
                            'text-sm font-medium',
                            'text-indigo-900' => $isActive,
                            'text-emerald-900' => $isComplete,
                            'text-slate-500' => ! $isActive && ! $isComplete,
                        ])>{{ $stage['label'] }}</span>
                    </div>
                </li>
            @endforeach
        </ol>

        @if ($notice)
            <p class="mt-4 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                {{ $notice }}
            </p>
        @endif
    @endif
</div>
