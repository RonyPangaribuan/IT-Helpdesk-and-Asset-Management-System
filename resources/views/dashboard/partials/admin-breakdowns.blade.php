<div class="mt-8 grid gap-6 lg:grid-cols-2">
    <section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-semibold text-stone-950">Tickets by Category</h2>
        <div class="mt-5 space-y-3">
            @forelse ($categoryBreakdown as $category)
                <div class="flex items-center justify-between gap-4 rounded-md bg-stone-50 px-4 py-3 text-sm">
                    <span class="font-medium text-stone-800">
                        {{ $category->name }}
                        @if ($category->trashed())
                            <span class="text-xs text-stone-500">(archived)</span>
                        @endif
                    </span>
                    <span class="font-semibold text-stone-950">{{ $category->tickets_count }}</span>
                </div>
            @empty
                <p class="text-sm text-stone-600">No category data yet.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-semibold text-stone-950">Tickets by Priority</h2>
        <div class="mt-5 space-y-3">
            @forelse ($priorityBreakdown as $row)
                <div class="flex items-center justify-between gap-4 rounded-md bg-stone-50 px-4 py-3 text-sm">
                    <x-priority-badge :priority="$row['priority']" />
                    <span class="font-semibold text-stone-950">{{ $row['count'] }}</span>
                </div>
            @empty
                <p class="text-sm text-stone-600">No priority data yet.</p>
            @endforelse
        </div>
    </section>
</div>
