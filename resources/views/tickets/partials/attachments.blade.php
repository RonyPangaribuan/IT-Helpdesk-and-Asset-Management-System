<x-section-card title="Attachments" description="{{ $ticket->attachments->count() }} {{ Str::plural('file', $ticket->attachments->count()) }} uploaded.">
    <div class="space-y-3">
        @forelse ($ticket->attachments as $attachment)
            <div class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-slate-500 ring-1 ring-slate-200" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <a href="{{ route('ticket-attachments.download', $attachment) }}" class="block truncate text-sm font-semibold text-indigo-700 hover:text-indigo-900">{{ $attachment->original_name }}</a>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $attachment->mime_type }} / {{ number_format($attachment->file_size / 1024, 1) }} KB / Uploaded by {{ $attachment->uploader->name }} / {{ $attachment->created_at->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('ticket-attachments.download', $attachment) }}" class="app-button-secondary w-fit">Download</a>
            </div>
        @empty
            <x-empty-state title="No attachments uploaded." description="Supporting screenshots, photos, or PDFs will appear here." />
        @endforelse
    </div>

    @can('uploadAttachment', $ticket)
        <form method="POST" action="{{ route('tickets.attachments.store', $ticket) }}" enctype="multipart/form-data" class="mt-6 space-y-3">
            @csrf
            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4">
                <x-input-label for="attachments" value="Upload supporting files" />
                <input id="attachments" name="attachments[]" type="file" multiple accept=".jpg,.jpeg,.png,.pdf" class="mt-3 block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                <p class="app-help">JPG, PNG, or PDF. Maximum 5 MB each, up to 5 files.</p>
                <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
            </div>
            <x-primary-button>Upload Files</x-primary-button>
        </form>
    @endcan
</x-section-card>
