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

class TicketReassignmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reassign_assigned_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $oldTechnician = User::factory()->technician()->create(['name' => 'Budi Technician']);
        $newTechnician = User::factory()->technician()->create(['name' => 'Sari Technician']);
        $ticket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $oldTechnician);

        $this->actingAs($admin)
            ->patch(route('tickets.reassign', $ticket), ['technician_id' => $newTechnician->id])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $latestHistory = $ticket->statusHistories()->get()->last();

        $this->assertSame($newTechnician->id, $ticket->technician_id);
        $this->assertSame(TicketStatus::Assigned, $ticket->status);
        $this->assertNotNull($latestHistory);
        $this->assertSame(TicketStatus::Assigned, $latestHistory->old_status);
        $this->assertSame(TicketStatus::Assigned, $latestHistory->new_status);
        $this->assertSame('Reassigned from Budi Technician to Sari Technician', $latestHistory->note);
    }

    public function test_reassignment_requires_active_different_technician_and_assigned_status(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $oldTechnician = User::factory()->technician()->create();
        $inactiveTechnician = User::factory()->technician()->create(['is_active' => false]);
        $ticket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $oldTechnician);

        $this->actingAs($admin)
            ->from(route('tickets.show', $ticket))
            ->patch(route('tickets.reassign', $ticket), ['technician_id' => $inactiveTechnician->id])
            ->assertRedirect(route('tickets.show', $ticket))
            ->assertSessionHasErrors('technician_id');

        $this->actingAs($admin)
            ->from(route('tickets.show', $ticket))
            ->patch(route('tickets.reassign', $ticket), ['technician_id' => $oldTechnician->id])
            ->assertRedirect(route('tickets.show', $ticket))
            ->assertSessionHasErrors('workflow');

        app(TicketWorkflowService::class)->startWork($ticket->refresh(), $oldTechnician);

        $this->actingAs($admin)
            ->patch(route('tickets.reassign', $ticket->refresh()), ['technician_id' => User::factory()->technician()->create()->id])
            ->assertForbidden();
    }

    public function test_reassignment_updates_technician_visibility(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $oldTechnician = User::factory()->technician()->create();
        $newTechnician = User::factory()->technician()->create();
        $ticket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $oldTechnician);

        $this->actingAs($admin)
            ->patch(route('tickets.reassign', $ticket), ['technician_id' => $newTechnician->id]);

        $this->actingAs($oldTechnician)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertDontSee($ticket->ticket_code);

        $this->actingAs($newTechnician)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($ticket->ticket_code);
    }

    private function createOpenTicket(User $requester): Ticket
    {
        return app(TicketWorkflowService::class)->createTicket($requester, [
            'title' => 'Projector HDMI signal not detected',
            'description' => 'The projector cannot detect HDMI input from the laptop.',
            'ticket_category_id' => TicketCategory::factory()->create()->id,
            'priority' => TicketPriority::High,
            'location' => 'Room A-204',
        ]);
    }
}
