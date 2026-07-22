@props(['priority'])

@php
    $value = $priority instanceof \BackedEnum ? $priority->value : (string) $priority;
    $label = $priority instanceof \App\Enums\TicketPriority ? $priority->label() : ucfirst($value);

    $classes = match ($value) {
        'low' => 'bg-slate-100 text-slate-700 ring-slate-300',
        'medium' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'high' => 'bg-orange-50 text-orange-700 ring-orange-200',
        'critical' => 'bg-red-50 text-red-700 ring-red-200',
        default => 'bg-slate-100 text-slate-700 ring-slate-300',
    };

    $dotClasses = match ($value) {
        'low' => 'bg-slate-400',
        'medium' => 'bg-blue-500',
        'high' => 'bg-orange-500',
        'critical' => 'bg-red-500',
        default => 'bg-slate-400',
    };
@endphp

<span {{ $attributes->merge(['class' => "app-badge {$classes}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dotClasses }}" aria-hidden="true"></span>
    {{ $label }}
</span>
