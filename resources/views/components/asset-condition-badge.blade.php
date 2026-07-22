@props(['condition'])

@php
    $value = $condition instanceof \BackedEnum ? $condition->value : (string) $condition;
    $label = $condition instanceof \App\Enums\AssetCondition ? $condition->label() : ucwords(str_replace('_', ' ', $value));

    $classes = match ($value) {
        'good' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'maintenance' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'damaged' => 'bg-red-50 text-red-700 ring-red-200',
        'retired' => 'bg-slate-100 text-slate-700 ring-slate-300',
        default => 'bg-slate-100 text-slate-700 ring-slate-300',
    };

    $dotClasses = match ($value) {
        'good' => 'bg-emerald-500',
        'maintenance' => 'bg-amber-500',
        'damaged' => 'bg-red-500',
        'retired' => 'bg-slate-400',
        default => 'bg-slate-400',
    };
@endphp

<span {{ $attributes->merge(['class' => "app-badge {$classes}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dotClasses }}" aria-hidden="true"></span>
    {{ $label }}
</span>
