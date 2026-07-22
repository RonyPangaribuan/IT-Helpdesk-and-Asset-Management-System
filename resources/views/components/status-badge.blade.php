@props(['status'])

@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $label = $status instanceof \App\Enums\TicketStatus ? $status->label() : ucwords(str_replace('_', ' ', $value));

    $classes = match ($value) {
        'open' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'assigned' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'in_progress' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'resolved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'closed' => 'bg-slate-100 text-slate-700 ring-slate-300',
        'reopened' => 'bg-violet-50 text-violet-700 ring-violet-200',
        'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
        default => 'bg-slate-100 text-slate-700 ring-slate-300',
    };

    $dotClasses = match ($value) {
        'open' => 'bg-blue-500',
        'assigned' => 'bg-indigo-500',
        'in_progress' => 'bg-amber-500',
        'resolved' => 'bg-emerald-500',
        'closed' => 'bg-slate-500',
        'reopened' => 'bg-violet-500',
        'cancelled' => 'bg-red-500',
        default => 'bg-slate-400',
    };
@endphp

<span {{ $attributes->merge(['class' => "app-badge {$classes}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dotClasses }}" aria-hidden="true"></span>
    {{ $label }}
</span>
