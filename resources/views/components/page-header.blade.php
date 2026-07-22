@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'breadcrumbs' => null,
])

<div {{ $attributes->merge(['class' => 'flex min-w-0 flex-col gap-4 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div class="min-w-0">
        @if ($breadcrumbs)
            <nav class="mb-2 flex flex-wrap items-center gap-2 text-xs text-slate-500" aria-label="Breadcrumb">
                {{ $breadcrumbs }}
            </nav>
        @endif

        @if ($eyebrow)
            <p class="text-sm font-medium text-indigo-700">{{ $eyebrow }}</p>
        @endif

        <h1 class="mt-1 break-words text-2xl font-semibold tracking-tight text-slate-950 sm:text-3xl">
            {{ $title }}
        </h1>

        @if ($description)
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                {{ $description }}
            </p>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
