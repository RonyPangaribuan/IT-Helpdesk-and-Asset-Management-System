@props(['condition'])

@php
    $value = $condition instanceof \BackedEnum ? $condition->value : (string) $condition;
    $label = $condition instanceof \App\Enums\AssetCondition ? $condition->label() : ucwords(str_replace('_', ' ', $value));

    $classes = match ($value) {
        'good' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'maintenance' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'damaged' => 'bg-red-50 text-red-700 ring-red-200',
        'retired' => 'bg-stone-100 text-stone-700 ring-stone-300',
        default => 'bg-stone-100 text-stone-700 ring-stone-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {$classes}"]) }}>
    {{ $label }}
</span>
