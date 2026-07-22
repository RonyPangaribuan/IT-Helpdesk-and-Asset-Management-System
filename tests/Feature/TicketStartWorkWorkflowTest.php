<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStartWorkWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_technician_can_start_work(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $technician);

        $this->actingAs($technician)
            ->patch(route('tickets.start-work', $ticket))
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $latestHistory = $ticket->statusHistories()->get()->last();

        $this->assertSame(TicketStatus::InProgress, $ticket->status);
        $this->assertSame($technician->id, $ticket->technician_id);
        $this->assertNotNull($latestHistory);
        $this->assertSame(TicketStatus::Assigned, $latestHistory->old_status);
        $this->assertSame(TicketStatus::InProgress, $latestHistory->new_status);
        $this->assertSame('Started work', $latestHistory->note);
    }

    public function test_only_assigned_technician_can_start_work(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $otherTechnician = User::factory()->technician()->create();
        $ticket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $technician);

        $this->actingAs($otherTechnician)
            ->patch(route('tickets.start-work', $ticket))
            ->assertForbidden();

        $this->actingAs($requester)
            ->patch(route('tickets.start-work', $ticket))
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('tickets.start-work', $ticket))
            ->assertForbidden();

        $ticket->refresh();

        $this->assertSame(TicketStatus::Assigned, $ticket->status);
    }

    public function test_unassigned_open_ticket_and_duplicate_start_work_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $openTicket = $this->createOpenTicket($requester);

        $this->actingAs($technician)
            ->patch(route('tickets.start-work', $openTicket))
            ->assertForbidden();

        $assignedTicket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $technician);

        $this->actingAs($technician)
            ->patch(route('tickets.start-work', $assignedTicket))
            ->assertRedirect(route('tickets.show', $assignedTicket));

        $historyCount = $assignedTicket->statusHistories()->count();

        $this->actingAs($technician)
            ->patch(route('tickets.start-work', $assignedTicket->refresh()))
            ->assertForbidden();

        $this->assertSame($historyCount, $assignedTicket->statusHistories()->count());
    }

    private function createOpenTicket(User $requester): Ticket
    {
        return app(TicketWorkflowService::class)->createTicket($requester, [
            'title' => 'Printer output has faded text',
            'description' => 'The printer output is faded and unreadable.',
            'ticket_category_id' => TicketCategory::factory()->create()->id,
            'priority' => TicketPriority::Medium,
            'location' => 'Administration Office',
        ]);
    }
}
