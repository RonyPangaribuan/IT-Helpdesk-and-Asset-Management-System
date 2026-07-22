<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Asset;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_code' => Ticket::pendingCode(),
            'requester_id' => User::factory()->requester(),
            'technician_id' => null,
            'ticket_category_id' => TicketCategory::factory(),
            'asset_id' => null,
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'location' => fake()->city().' Room '.fake()->numberBetween(100, 499),
            'priority' => fake()->randomElement(TicketPriority::cases()),
            'status' => TicketStatus::Open,
            'resolution_note' => null,
            'resolved_at' => null,
            'closed_at' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Ticket $ticket): void {
            if (str_starts_with($ticket->ticket_code, 'PENDING-')) {
                $ticket->forceFill([
                    'ticket_code' => Ticket::codeFromId($ticket->id, $ticket->created_at?->year),
                ])->save();
            }
        });
    }

    public function forRequester(User $requester): static
    {
        return $this->state(fn (array $attributes) => [
            'requester_id' => $requester->id,
        ]);
    }

    public function withAsset(Asset $asset): static
    {
        return $this->state(fn (array $attributes) => [
            'asset_id' => $asset->id,
        ]);
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_id' => null,
            'status' => TicketStatus::Open,
            'resolution_note' => null,
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function assignedTo(User $technician): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_id' => $technician->id,
            'status' => TicketStatus::Assigned,
            'resolution_note' => null,
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function inProgress(User $technician): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_id' => $technician->id,
            'status' => TicketStatus::InProgress,
            'resolution_note' => null,
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function resolved(User $technician): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_id' => $technician->id,
            'status' => TicketStatus::Resolved,
            'resolution_note' => 'The issue was resolved during troubleshooting.',
            'resolved_at' => now(),
            'closed_at' => null,
        ]);
    }

    public function closed(User $technician): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_id' => $technician->id,
            'status' => TicketStatus::Closed,
            'resolution_note' => 'The issue was resolved and confirmed by the requester.',
            'resolved_at' => now()->subHour(),
            'closed_at' => now(),
        ]);
    }

    public function reopened(User $technician): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_id' => $technician->id,
            'status' => TicketStatus::Reopened,
            'resolution_note' => null,
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function cancelled(?User $technician = null): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_id' => $technician?->id,
            'status' => TicketStatus::Cancelled,
            'resolution_note' => null,
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function priority(TicketPriority $priority): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => $priority,
        ]);
    }

    public function status(TicketStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }
}
