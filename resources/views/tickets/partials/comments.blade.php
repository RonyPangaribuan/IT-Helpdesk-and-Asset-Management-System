<x-section-card title="Discussion" description="{{ $ticket->comments->count() }} {{ Str::plural('comment', $ticket->comments->count()) }} in this thread.">
    <div class="space-y-5">
        @forelse ($ticket->comments as $comment)
            <article class="flex gap-3 border-b border-slate-100 pb-5 last:border-b-0 last:pb-0">
                <x-avatar :user="$comment->author" size="sm" />
                <div class="min-w-0 flex-1">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-950">{{ $comment->author->name }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ ucfirst($comment->author->role) }} / {{ $comment->created_at->format('d M Y H:i') }}{{ $comment->wasEdited() ? ' / edited' : '' }}
                            </p>
                        </div>
                        @can('delete', $comment)
                            <form method="POST" action="{{ route('tickets.comments.destroy', [$ticket, $comment]) }}" onsubmit="return confirm('Delete this comment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-900">Delete</button>
                            </form>
                        @endcan
                    </div>
                    <p class="mt-3 whitespace-pre-line break-words text-sm leading-7 text-slate-800">{{ $comment->body }}</p>
                    @can('update', $comment)
                        <details class="mt-3">
                            <summary class="cursor-pointer text-sm font-medium text-indigo-700 hover:text-indigo-900">Edit comment</summary>
                            <form method="POST" action="{{ route('tickets.comments.update', [$ticket, $comment]) }}" class="mt-3 space-y-3">
                                @csrf
                                @method('PATCH')
                                <x-input-label for="comment_body_{{ $comment->id }}" value="Edit Comment" class="sr-only" />
                                <textarea id="comment_body_{{ $comment->id }}" name="body" rows="3" class="app-input" required>{{ old('body', $comment->body) }}</textarea>
                                <x-input-error :messages="$errors->get('body')" class="mt-2" />
                                <x-primary-button>Update Comment</x-primary-button>
                            </form>
                        </details>
                    @endcan
                </div>
            </article>
        @empty
            <x-empty-state title="No discussion yet." description="Comments from authorized participants will appear in this conversation." />
        @endforelse
    </div>

    @can('comment', $ticket)
        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="mt-6 space-y-3">
            @csrf
            <div>
                <x-input-label for="comment_body" value="Add Comment" />
                <textarea id="comment_body" name="body" rows="4" class="app-input mt-1" required>{{ old('body') }}</textarea>
                <x-input-error :messages="$errors->get('body')" class="mt-2" />
            </div>
            <x-primary-button>Add Comment</x-primary-button>
        </form>
    @endcan
</x-section-card>
