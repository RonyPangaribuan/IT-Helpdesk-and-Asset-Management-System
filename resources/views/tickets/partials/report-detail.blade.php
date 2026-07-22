<section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
    <h2 class="text-base font-semibold text-stone-950">Report Detail</h2>
    <div class="mt-5 space-y-5 text-sm">
        <div>
            <p class="font-medium text-stone-500">Description</p>
            <p class="mt-1 whitespace-pre-line leading-6 text-stone-800">{{ $ticket->description }}</p>
        </div>
        <div>
            <p class="font-medium text-stone-500">Location</p>
            <p class="mt-1 text-stone-800">{{ $ticket->location }}</p>
        </div>
        @if ($ticket->resolution_note)
            <div>
                <p class="font-medium text-stone-500">Resolution Note</p>
                <p class="mt-1 whitespace-pre-line leading-6 text-stone-800">{{ $ticket->resolution_note }}</p>
            </div>
        @endif
    </div>
</section>
