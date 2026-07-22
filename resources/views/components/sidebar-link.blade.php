@props([
    'href',
    'active' => false,
])

@php
    $classes = $active
        ? 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-100'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950';
@endphp

<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    {{ $attributes->merge(['class' => "group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {$classes}"]) }}
>
    <span @class([
        'h-2 w-2 rounded-full',
        'bg-indigo-600' => $active,
        'bg-slate-300 group-hover:bg-indigo-300' => ! $active,
    ]) aria-hidden="true"></span>
    <span class="truncate">{{ $slot }}</span>
</a>
