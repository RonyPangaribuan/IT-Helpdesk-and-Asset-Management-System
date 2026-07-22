<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketAttachmentRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Services\TicketAttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketAttachmentController extends Controller
{
    public function store(StoreTicketAttachmentRequest $request, Ticket $ticket, TicketAttachmentService $attachments): RedirectResponse
    {
        $attachments->storeForTicket($ticket, $request->user(), (array) $request->file('attachments', []));

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Attachment uploaded.');
    }

    public function download(TicketAttachment $attachment): StreamedResponse
    {
        $this->authorize('download', $attachment);

        abort_unless(Storage::disk('local')->exists($attachment->file_path), 404);

        return Storage::disk('local')->download($attachment->file_path, $attachment->original_name);
    }
}
