@props([
    'title',
    'description' => null,
    'actionHref' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center']) }}>
    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-500 ring-1 ring-slate-200" aria-hidden="true">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h8M8 15h5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5z" />
        </svg>
    </div>

    <h3 class="mt-4 text-sm font-semibold text-slate-950">{{ $title }}</h3>

    @if ($description)
        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">{{ $description }}</p>
    @endif

    @if ($actionHref && $actionLabel)
        <div class="mt-5">
            <a href="{{ $actionHref }}" class="app-button-primary">{{ $actionLabel }}</a>
        </div>
    @endif
</div>
