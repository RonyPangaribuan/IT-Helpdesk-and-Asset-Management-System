<section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between gap-4">
        <h2 class="text-base font-semibold text-stone-950">Attachments</h2>
        <span class="text-sm text-stone-500">{{ $ticket->attachments->count() }} {{ Str::plural('file', $ticket->attachments->count()) }}</span>
    </div>

    <div class="mt-5 space-y-3">
        @forelse ($ticket->attachments as $attachment)
            <div class="flex flex-col gap-2 border-b border-stone-100 pb-3 last:border-b-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <a href="{{ route('ticket-attachments.download', $attachment) }}" class="break-all text-sm font-semibold text-teal-700 hover:text-teal-800">{{ $attachment->original_name }}</a>
                    <p class="mt-1 text-xs text-stone-500">
                        Uploaded by {{ $attachment->uploader->name }} / {{ $attachment->created_at->format('d M Y H:i') }} / {{ number_format($attachment->file_size / 1024, 1) }} KB
                    </p>
                </div>
                <a href="{{ route('ticket-attachments.download', $attachment) }}" class="inline-flex items-center rounded-md border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 shadow-sm hover:border-teal-300 hover:text-teal-700">Download</a>
            </div>
        @empty
            <p class="text-sm text-stone-600">No attachments uploaded.</p>
        @endforelse
    </div>

    @can('uploadAttachment', $ticket)
        <form method="POST" action="{{ route('tickets.attachments.store', $ticket) }}" enctype="multipart/form-data" class="mt-6 space-y-3">
            @csrf
            <div>
                <x-input-label for="attachments" value="Upload Attachments" />
                <input id="attachments" name="attachments[]" type="file" multiple accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full rounded-md border border-gray-300 text-sm text-stone-700 file:mr-4 file:border-0 file:bg-stone-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-stone-700 hover:file:bg-stone-200">
                <p class="mt-2 text-xs text-stone-500">JPG, PNG, or PDF. Maximum 5 files, 5 MB each.</p>
                <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
            </div>
            <x-primary-button>Upload Files</x-primary-button>
        </form>
    @endcan
</section>
