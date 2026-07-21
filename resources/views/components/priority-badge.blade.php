@props(['priority'])

@php
    $value = $priority instanceof \BackedEnum ? $priority->value : (string) $priority;
    $label = $priority instanceof \App\Enums\TicketPriority ? $priority->label() : ucfirst($value);

    $classes = match ($value) {
        'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'medium' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'high' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'critical' => 'bg-red-50 text-red-700 ring-red-200',
        default => 'bg-stone-100 text-stone-700 ring-stone-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {$classes}"]) }}>
    {{ $label }}
</span>
