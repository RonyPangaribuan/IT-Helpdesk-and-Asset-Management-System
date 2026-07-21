<?php

namespace Tests\Unit;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Exceptions\InvalidTicketTransitionException;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketWorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_transition_map_identifies_valid_and_invalid_transitions(): void
    {
        $this->assertTrue(TicketStatus::Open->canTransitionTo(TicketStatus::Assigned));
        $this->assertTrue(TicketStatus::Assigned->canTransitionTo(TicketStatus::InProgress));
        $this->assertFalse(TicketStatus::Open->canTransitionTo(TicketStatus::InProgress));
        $this->assertFalse(TicketStatus::Assigned->canTransitionTo(TicketStatus::Closed));
    }

    public function test_cancelled_and_closed_are_terminal_states(): void
    {
        $this->assertTrue(TicketStatus::Cancelled->isTerminal());
        $this->assertTrue(TicketStatus::Closed->isTerminal());
        $this->assertFalse(TicketStatus::Open->isTerminal());
    }

    public function test_assignment_requires_open_status(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->assignedTo($technician)->create();

        $this->expectException(InvalidTicketTransitionException::class);

        app(TicketWorkflowService::class)->assign($ticket, $admin, User::factory()->technician()->create());
    }

    public function test_start_work_requires_assigned_status(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->open()->create();

        $this->expectException(InvalidTicketTransitionException::class);

        app(TicketWorkflowService::class)->startWork($ticket, $technician);
    }

    public function test_service_creates_history_with_status_update(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = app(TicketWorkflowService::class)->createTicket($requester, [
            'title' => 'Cannot access shared network folder',
            'description' => 'The shared folder cannot be reached from this workstation.',
            'ticket_category_id' => TicketCategory::factory()->create()->id,
            'priority' => TicketPriority::High,
            'location' => 'HR Department',
        ]);

        app(TicketWorkflowService::class)->assign($ticket, $admin, $technician);

        $ticket->refresh();
        $latestHistory = $ticket->statusHistories()->get()->last();

        $this->assertSame(TicketStatus::Assigned, $ticket->status);
        $this->assertNotNull($latestHistory);
        $this->assertSame(TicketStatus::Open, $latestHistory->old_status);
        $this->assertSame(TicketStatus::Assigned, $latestHistory->new_status);
    }
}
