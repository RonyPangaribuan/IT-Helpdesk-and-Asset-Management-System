@props(['status'])

@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $label = $status instanceof \App\Enums\TicketStatus ? $status->label() : ucwords(str_replace('_', ' ', $value));

    $classes = match ($value) {
        'open' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'assigned' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'in_progress' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'resolved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'closed' => 'bg-stone-100 text-stone-700 ring-stone-300',
        'reopened' => 'bg-orange-50 text-orange-700 ring-orange-200',
        'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
        default => 'bg-stone-100 text-stone-700 ring-stone-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {$classes}"]) }}>
    {{ $label }}
</span>
