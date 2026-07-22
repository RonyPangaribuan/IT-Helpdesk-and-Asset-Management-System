@php
    $maxCategoryCount = max(1, (int) $categoryBreakdown->max('tickets_count'));
    $maxPriorityCount = max(1, (int) $priorityBreakdown->max('count'));
@endphp

<div class="grid gap-6 lg:grid-cols-2">
    <x-section-card title="Tickets by Category" description="Volume distribution across support areas.">
        <div class="space-y-4">
            @forelse ($categoryBreakdown as $category)
                <div>
                    <div class="mb-2 flex items-center justify-between gap-4 text-sm">
                        <span class="font-medium text-slate-800">
                            {{ $category->name }}
                            @if ($category->trashed())
                                <span class="text-xs text-slate-500">(archived)</span>
                            @endif
                        </span>
                        <span class="font-semibold text-slate-950">{{ $category->tickets_count }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100">
                        <div class="h-2 rounded-full bg-indigo-500" style="width: {{ max(6, (int) round(($category->tickets_count / $maxCategoryCount) * 100)) }}%"></div>
                    </div>
                </div>
            @empty
                <x-empty-state title="No category data yet." description="Ticket category usage appears once tickets are created." />
            @endforelse
        </div>
    </x-section-card>

    <x-section-card title="Tickets by Priority" description="Operational urgency without adding a chart library.">
        <div class="space-y-4">
            @forelse ($priorityBreakdown as $row)
                <div>
                    <div class="mb-2 flex items-center justify-between gap-4 text-sm">
                        <x-priority-badge :priority="$row['priority']" />
                        <span class="font-semibold text-slate-950">{{ $row['count'] }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100">
                        <div class="h-2 rounded-full bg-blue-500" style="width: {{ max(6, (int) round(($row['count'] / $maxPriorityCount) * 100)) }}%"></div>
                    </div>
                </div>
            @empty
                <x-empty-state title="No priority data yet." description="Priority counts appear once tickets are created." />
            @endforelse
        </div>
    </x-section-card>
</div>
