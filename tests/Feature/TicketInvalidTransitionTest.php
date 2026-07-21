<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Exceptions\InvalidTicketTransitionException;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketInvalidTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_transitions_do_not_change_database_or_create_history(): void
    {
        $actor = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $service = app(TicketWorkflowService::class);

        $cases = [
            [TicketStatus::Open, TicketStatus::InProgress, null],
            [TicketStatus::Open, TicketStatus::Resolved, null],
            [TicketStatus::Assigned, TicketStatus::Closed, $technician],
            [TicketStatus::InProgress, TicketStatus::Open, $technician],
            [TicketStatus::Cancelled, TicketStatus::Open, null],
            [TicketStatus::Closed, TicketStatus::Open, null],
        ];

        foreach ($cases as [$from, $to, $assignedTechnician]) {
            $ticket = Ticket::factory()
                ->forRequester($requester)
                ->create([
                    'status' => $from,
                    'technician_id' => $assignedTechnician?->id,
                ]);

            $historyCount = $ticket->statusHistories()->count();

            try {
                $service->transitionStatus($ticket, $actor, $to, 'Invalid transition attempt.');

                $this->fail("Transition {$from->value} to {$to->value} should be rejected.");
            } catch (InvalidTicketTransitionException) {
                $ticket->refresh();

                $this->assertSame($from, $ticket->status);
                $this->assertSame($historyCount, $ticket->statusHistories()->count());
            }
        }
    }
}
