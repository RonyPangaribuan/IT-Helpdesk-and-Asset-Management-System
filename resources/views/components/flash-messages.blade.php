@if (session('success'))
    <div class="mb-6 flex gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status" aria-live="polite">
        <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
        </svg>
        <div>
            <p class="font-semibold">Success</p>
            <p class="mt-1">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 flex gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.3 4.3h3.4L21 18H3L10.3 4.3z" />
        </svg>
        <div>
            <p class="font-semibold">Action needed</p>
            <p class="mt-1">{{ session('error') }}</p>
        </div>
    </div>
@endif
