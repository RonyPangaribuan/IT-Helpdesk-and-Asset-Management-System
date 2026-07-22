<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<TicketAttachment>
 */
class TicketAttachmentFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (TicketAttachment $attachment): void {
            $disk = (string) config('deskit.attachment_disk', 'local');

            if (! Storage::disk($disk)->exists($attachment->file_path)) {
                Storage::disk($disk)->put($attachment->file_path, 'deskIT test attachment.');
            }
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $storedName = Str::uuid()->toString().'.pdf';

        return [
            'ticket_id' => Ticket::factory(),
            'uploaded_by' => User::factory(),
            'original_name' => 'supporting-document.pdf',
            'stored_name' => $storedName,
            'file_path' => 'ticket-attachments/'.fake()->numberBetween(1, 9999).'/'.$storedName,
            'mime_type' => 'application/pdf',
            'file_size' => 128000,
        ];
    }

    public function uploadedBy(User $uploader): static
    {
        return $this->state(fn (array $attributes) => [
            'uploaded_by' => $uploader->id,
        ]);
    }
}
