<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketStatusHistory>
 */
class TicketStatusHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'changed_by' => User::factory(),
            'old_status' => null,
            'new_status' => TicketStatus::Open,
            'note' => 'Ticket created',
            'created_at' => now(),
        ];
    }

    public function transition(?TicketStatus $oldStatus, TicketStatus $newStatus, ?string $note = null): static
    {
        return $this->state(fn (array $attributes) => [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note,
        ]);
    }
}
