<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_requester_can_create_ticket_with_private_attachments(): void
    {
        $disk = (string) config('deldesk.attachment_disk');
        Storage::fake($disk);

        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();

        $this->actingAs($requester)
            ->post(route('tickets.store'), [
                'title' => 'Monitor has a flickering image',
                'description' => 'The monitor flickers every few minutes and needs troubleshooting.',
                'ticket_category_id' => $category->id,
                'priority' => 'medium',
                'location' => 'Room B-112',
                'attachments' => [
                    UploadedFile::fake()->create('screen.png', 64, 'image/png'),
                    UploadedFile::fake()->create('diagnostic.pdf', 128, 'application/pdf'),
                ],
            ])
            ->assertRedirect();

        $ticket = Ticket::firstOrFail();
        $attachments = $ticket->attachments()->get();

        $this->assertCount(2, $attachments);

        foreach ($attachments as $attachment) {
            Storage::disk($disk)->assertExists($attachment->file_path);
            $this->assertStringStartsWith("ticket-attachments/{$ticket->id}/", $attachment->file_path);
            $this->assertNotSame($attachment->original_name, $attachment->stored_name);
            $this->assertSame($requester->id, $attachment->uploaded_by);
        }
    }

    public function test_assigned_technician_can_upload_attachments_but_admin_and_unrelated_users_cannot(): void
    {
        $disk = (string) config('deldesk.attachment_disk');
        Storage::fake($disk);

        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $otherTechnician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->assignedTo($technician)->create();

        $this->actingAs($technician)
            ->post(route('tickets.attachments.store', $ticket), [
                'attachments' => [
                    UploadedFile::fake()->create('work-note.pdf', 32, 'application/pdf'),
                ],
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $this->actingAs($admin)
            ->post(route('tickets.attachments.store', $ticket), [
                'attachments' => [
                    UploadedFile::fake()->create('admin-note.pdf', 32, 'application/pdf'),
                ],
            ])
            ->assertForbidden();

        $this->actingAs($otherRequester)
            ->post(route('tickets.attachments.store', $ticket), [
                'attachments' => [
                    UploadedFile::fake()->create('other-requester.pdf', 32, 'application/pdf'),
                ],
            ])
            ->assertForbidden();

        $this->actingAs($otherTechnician)
            ->post(route('tickets.attachments.store', $ticket), [
                'attachments' => [
                    UploadedFile::fake()->create('other-technician.pdf', 32, 'application/pdf'),
                ],
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('ticket_attachments', 1);
        Storage::disk($disk)->assertExists(TicketAttachment::firstOrFail()->file_path);
    }

    public function test_attachment_validation_rejects_unsupported_or_oversized_files(): void
    {
        Storage::fake((string) config('deldesk.attachment_disk'));

        $requester = User::factory()->requester()->create();
        $ticket = Ticket::factory()->forRequester($requester)->open()->create();

        $this->actingAs($requester)
            ->from(route('tickets.show', $ticket))
            ->post(route('tickets.attachments.store', $ticket), [
                'attachments' => [
                    UploadedFile::fake()->create('script.exe', 1, 'application/x-msdownload'),
                ],
            ])
            ->assertRedirect(route('tickets.show', $ticket))
            ->assertSessionHasErrors('attachments.0');

        $this->actingAs($requester)
            ->from(route('tickets.show', $ticket))
            ->post(route('tickets.attachments.store', $ticket), [
                'attachments' => [
                    UploadedFile::fake()->create('large.pdf', 5121, 'application/pdf'),
                ],
            ])
            ->assertRedirect(route('tickets.show', $ticket))
            ->assertSessionHasErrors('attachments.0');

        $this->assertDatabaseCount('ticket_attachments', 0);
    }

    public function test_attachment_download_is_authorized_and_uses_configured_private_storage(): void
    {
        $disk = (string) config('deldesk.attachment_disk');
        Storage::fake($disk);

        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $otherTechnician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->assignedTo($technician)->create();
        $path = "ticket-attachments/{$ticket->id}/private-note.pdf";

        Storage::disk($disk)->put($path, 'private file');

        $attachment = TicketAttachment::factory()
            ->for($ticket)
            ->uploadedBy($requester)
            ->create([
                'original_name' => 'visible-name.pdf',
                'stored_name' => 'private-note.pdf',
                'file_path' => $path,
                'mime_type' => 'application/pdf',
                'file_size' => 12,
            ]);

        $this->actingAs($requester)
            ->get(route('ticket-attachments.download', $attachment))
            ->assertOk();

        $this->actingAs($technician)
            ->get(route('ticket-attachments.download', $attachment))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('ticket-attachments.download', $attachment))
            ->assertOk();

        $this->actingAs($otherRequester)
            ->get(route('ticket-attachments.download', $attachment))
            ->assertForbidden();

        $this->actingAs($otherTechnician)
            ->get(route('ticket-attachments.download', $attachment))
            ->assertForbidden();

        $ticket->delete();

        $this->actingAs($admin)
            ->get(route('ticket-attachments.download', $attachment))
            ->assertForbidden();
    }

    public function test_attachment_upload_is_unavailable_after_ticket_is_closed_or_cancelled(): void
    {
        Storage::fake((string) config('deldesk.attachment_disk'));

        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $closedTicket = Ticket::factory()->forRequester($requester)->closed($technician)->create();
        $cancelledTicket = Ticket::factory()->forRequester($requester)->cancelled()->create();

        $this->actingAs($requester)
            ->post(route('tickets.attachments.store', $closedTicket), [
                'attachments' => [
                    UploadedFile::fake()->create('after-close.pdf', 32, 'application/pdf'),
                ],
            ])
            ->assertForbidden();

        $this->actingAs($requester)
            ->post(route('tickets.attachments.store', $cancelledTicket), [
                'attachments' => [
                    UploadedFile::fake()->create('after-cancel.pdf', 32, 'application/pdf'),
                ],
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('ticket_attachments', 0);
    }
}
