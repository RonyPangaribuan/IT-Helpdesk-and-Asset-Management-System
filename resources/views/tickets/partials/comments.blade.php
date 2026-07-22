<section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between gap-4">
        <h2 class="text-base font-semibold text-stone-950">Discussion</h2>
        <span class="text-sm text-stone-500">{{ $ticket->comments->count() }} {{ Str::plural('comment', $ticket->comments->count()) }}</span>
    </div>

    <div class="mt-5 space-y-4">
        @forelse ($ticket->comments as $comment)
            <article class="border-b border-stone-100 pb-4 last:border-b-0 last:pb-0">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-stone-950">{{ $comment->author->name }}</p>
                        <p class="text-xs uppercase text-stone-500">{{ ucfirst($comment->author->role) }} · {{ $comment->created_at->format('d M Y H:i') }}{{ $comment->wasEdited() ? ' · edited' : '' }}</p>
                    </div>
                    @can('delete', $comment)
                        <form method="POST" action="{{ route('tickets.comments.destroy', [$ticket, $comment]) }}" onsubmit="return confirm('Delete this comment?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">Delete</button>
                        </form>
                    @endcan
                </div>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-stone-800">{{ $comment->body }}</p>
                @can('update', $comment)
                    <details class="mt-3">
                        <summary class="cursor-pointer text-sm font-medium text-teal-700 hover:text-teal-800">Edit comment</summary>
                        <form method="POST" action="{{ route('tickets.comments.update', [$ticket, $comment]) }}" class="mt-3 space-y-3">
                            @csrf
                            @method('PATCH')
                            <textarea name="body" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('body', $comment->body) }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                            <x-primary-button>Update Comment</x-primary-button>
                        </form>
                    </details>
                @endcan
            </article>
        @empty
            <p class="text-sm text-stone-600">No discussion yet.</p>
        @endforelse
    </div>

    @can('comment', $ticket)
        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="mt-6 space-y-3">
            @csrf
            <div>
                <x-input-label for="comment_body" value="Add Comment" />
                <textarea id="comment_body" name="body" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('body') }}</textarea>
                <x-input-error :messages="$errors->get('body')" class="mt-2" />
            </div>
            <x-primary-button>Add Comment</x-primary-button>
        </form>
    @endcan
</section>
