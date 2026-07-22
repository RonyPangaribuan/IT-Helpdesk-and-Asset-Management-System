@props([
    'title' => null,
    'description' => null,
    'padding' => 'p-5 sm:p-6',
])

<section {{ $attributes->merge(['class' => 'app-card']) }}>
    @if ($title || $description || isset($actions))
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-start sm:justify-between sm:px-6">
            <div>
                @if ($title)
                    <h2 class="text-base font-semibold text-slate-950">{{ $title }}</h2>
                @endif

                @if ($description)
                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $description }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex shrink-0 flex-wrap gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</section>
