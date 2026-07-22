@props([
    'showText' => true,
    'label' => 'deskIT',
    'deskClass' => 'text-indigo-700',
    'itClass' => 'text-cyan-600',
])

<span
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}
    role="img"
    aria-label="{{ $label }}"
>
    <img
        src="{{ asset('branding/deskit-mark.png') }}"
        alt=""
        width="770"
        height="892"
        class="h-full max-h-11 w-auto shrink-0 object-contain"
        aria-hidden="true"
    >

    @if ($showText)
        <span class="whitespace-nowrap text-xl font-bold leading-none tracking-normal">
            <span class="{{ $deskClass }}">desk</span><span class="{{ $itClass }}">IT</span>
        </span>
    @endif
</span>
