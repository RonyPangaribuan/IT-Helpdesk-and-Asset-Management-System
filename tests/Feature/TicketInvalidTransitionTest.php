<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Exceptions\InvalidTicketTransitionException;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketInvalidTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_workflow_actions_do_not_change_database_or_create_history(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $service = app(TicketWorkflowService::class);

        $cases = [
            [TicketStatus::Open, null, fn (Ticket $ticket): Ticket => $service->startWork($ticket, $technician)],
            [TicketStatus::Assigned, $technician, fn (Ticket $ticket): Ticket => $service->resolve($ticket, $technician, 'Resolved too early.')],
            [TicketStatus::InProgress, $technician, fn (Ticket $ticket): Ticket => $service->close($ticket, $admin)],
            [TicketStatus::Resolved, $technician, fn (Ticket $ticket): Ticket => $service->cancel($ticket, $admin, 'Too late to cancel.')],
            [TicketStatus::Cancelled, null, fn (Ticket $ticket): Ticket => $service->assign($ticket, $admin, $technician)],
            [TicketStatus::Closed, $technician, fn (Ticket $ticket): Ticket => $service->reopen($ticket, $requester, 'Issue returned.')],
        ];

        foreach ($cases as [$status, $assignedTechnician, $action]) {
            $ticket = $this->ticketForStatus($requester, $status, $assignedTechnician);
            $historyCount = $ticket->statusHistories()->count();

            try {
                /** @var Closure(Ticket): Ticket $action */
                $action($ticket);

                $this->fail("Workflow action for {$status->value} should be rejected.");
            } catch (InvalidTicketTransitionException) {
                $ticket->refresh();

                $this->assertSame($status, $ticket->status);
                $this->assertSame($historyCount, $ticket->statusHistories()->count());
            }
        }
    }

    private function ticketForStatus(User $requester, TicketStatus $status, ?User $technician): Ticket
    {
        $factory = Ticket::factory()->forRequester($requester);

        return match ($status) {
            TicketStatus::Assigned => $factory->assignedTo($technician)->create(),
            TicketStatus::InProgress => $factory->inProgress($technician)->create(),
            TicketStatus::Resolved => $factory->resolved($technician)->create(),
            TicketStatus::Closed => $factory->closed($technician)->create(),
            TicketStatus::Cancelled => $factory->cancelled($technician)->create(),
            default => $factory->open()->create(),
        };
    }
}
