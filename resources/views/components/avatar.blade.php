@props([
    'user' => null,
    'name' => null,
    'size' => 'md',
])

@php
    $displayName = $user?->name ?? $name ?? 'User';
    $parts = preg_split('/\s+/', trim($displayName)) ?: [];
    $initials = collect($parts)
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
        ->implode('');
    $initials = $initials !== '' ? $initials : 'U';
    $sizeClass = match ($size) {
        'sm' => 'h-8 w-8 text-xs',
        'lg' => 'h-12 w-12 text-sm',
        default => 'h-10 w-10 text-sm',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex {$sizeClass} shrink-0 items-center justify-center rounded-full bg-indigo-50 font-semibold text-indigo-700 ring-1 ring-indigo-100"]) }}>
    {{ $initials }}
</span>
