<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class TicketAttachmentService
{
    private readonly string $disk;

    public function __construct(private readonly TicketWorkflowService $workflow)
    {
        $this->disk = (string) config('deldesk.attachment_disk', 'local');
    }

    /**
     * @param  array<string, mixed>  $ticketAttributes
     * @param  array<int, UploadedFile>  $files
     */
    public function createTicketWithAttachments(User $requester, array $ticketAttributes, array $files): Ticket
    {
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($files, $requester, &$storedPaths, $ticketAttributes): Ticket {
                $ticket = $this->workflow->createTicket($requester, $ticketAttributes);
                $this->storeMany($ticket, $requester, $files, $storedPaths);

                return $ticket;
            });
        } catch (Throwable $throwable) {
            $this->deleteStoredPaths($storedPaths);

            throw $throwable;
        }
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @return Collection<int, TicketAttachment>
     */
    public function storeForTicket(Ticket $ticket, User $uploader, array $files): Collection
    {
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($files, $ticket, $uploader, &$storedPaths): Collection {
                return $this->storeMany($ticket, $uploader, $files, $storedPaths);
            });
        } catch (Throwable $throwable) {
            $this->deleteStoredPaths($storedPaths);

            throw $throwable;
        }
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, string>  $storedPaths
     * @return Collection<int, TicketAttachment>
     */
    private function storeMany(Ticket $ticket, User $uploader, array $files, array &$storedPaths): Collection
    {
        $attachments = collect();

        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $storedName = Str::uuid()->toString().'.'.$extension;
            $path = "ticket-attachments/{$ticket->id}/{$storedName}";

            Storage::disk($this->disk)->putFileAs("ticket-attachments/{$ticket->id}", $file, $storedName);
            $storedPaths[] = $path;

            $attachments->push(TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'uploaded_by' => $uploader->id,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'file_path' => $path,
                'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                'file_size' => $file->getSize() ?: 0,
            ]));
        }

        return $attachments;
    }

    /**
     * @param  array<int, string>  $storedPaths
     */
    private function deleteStoredPaths(array $storedPaths): void
    {
        foreach ($storedPaths as $path) {
            Storage::disk($this->disk)->delete($path);
        }
    }
}
