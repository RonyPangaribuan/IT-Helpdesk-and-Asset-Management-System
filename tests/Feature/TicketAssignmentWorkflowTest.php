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

class TicketAssignmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_requester_and_technician_cannot_assign_ticket(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = $this->createOpenTicket($requester);

        $this->post(route('tickets.assign', $ticket), ['technician_id' => $technician->id])
            ->assertRedirect('/login');

        $this->actingAs($requester)
            ->post(route('tickets.assign', $ticket), ['technician_id' => $technician->id])
            ->assertForbidden();

        $this->actingAs($technician)
            ->post(route('tickets.assign', $ticket), ['technician_id' => $technician->id])
            ->assertForbidden();
    }

    public function test_admin_can_assign_active_technician(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create(['name' => 'Budi Technician']);
        $ticket = $this->createOpenTicket($requester);

        $this->actingAs($admin)
            ->post(route('tickets.assign', $ticket), ['technician_id' => $technician->id])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $latestHistory = $ticket->statusHistories()->get()->last();

        $this->assertSame($technician->id, $ticket->technician_id);
        $this->assertSame(TicketStatus::Assigned, $ticket->status);
        $this->assertNotNull($latestHistory);
        $this->assertSame(TicketStatus::Open, $latestHistory->old_status);
        $this->assertSame(TicketStatus::Assigned, $latestHistory->new_status);
        $this->assertSame('Assigned to Budi Technician', $latestHistory->note);
    }

    public function test_admin_cannot_assign_requester_admin_or_inactive_technician(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $inactiveTechnician = User::factory()->technician()->create(['is_active' => false]);
        $ticket = $this->createOpenTicket($requester);

        foreach ([$requester, $admin, $inactiveTechnician] as $candidate) {
            $this->actingAs($admin)
                ->from(route('tickets.show', $ticket))
                ->post(route('tickets.assign', $ticket), ['technician_id' => $candidate->id])
                ->assertRedirect(route('tickets.show', $ticket))
                ->assertSessionHasErrors('technician_id');
        }

        $ticket->refresh();

        $this->assertNull($ticket->technician_id);
        $this->assertSame(TicketStatus::Open, $ticket->status);
    }

    public function test_admin_cannot_assign_ticket_that_is_already_assigned(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $firstTechnician = User::factory()->technician()->create();
        $secondTechnician = User::factory()->technician()->create();
        $ticket = $this->createOpenTicket($requester);

        $this->actingAs($admin)
            ->post(route('tickets.assign', $ticket), ['technician_id' => $firstTechnician->id])
            ->assertRedirect(route('tickets.show', $ticket));

        $historyCount = $ticket->statusHistories()->count();

        $this->actingAs($admin)
            ->post(route('tickets.assign', $ticket->refresh()), ['technician_id' => $secondTechnician->id])
            ->assertForbidden();

        $ticket->refresh();

        $this->assertSame($firstTechnician->id, $ticket->technician_id);
        $this->assertSame($historyCount, $ticket->statusHistories()->count());
    }

    public function test_assigned_ticket_appears_only_on_assigned_technician_list(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $otherTechnician = User::factory()->technician()->create();
        $ticket = $this->createOpenTicket($requester);

        app(TicketWorkflowService::class)->assign($ticket, $admin, $technician);

        $this->actingAs($technician)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($ticket->ticket_code);

        $this->actingAs($otherTechnician)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertDontSee($ticket->ticket_code);

        $this->actingAs($requester)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($ticket->ticket_code);

        $this->actingAs($admin)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($ticket->ticket_code);
    }

    private function createOpenTicket(User $requester): Ticket
    {
        return app(TicketWorkflowService::class)->createTicket($requester, [
            'title' => 'Computer cannot access shared drive',
            'description' => 'The shared drive cannot be accessed from this workstation.',
            'ticket_category_id' => TicketCategory::factory()->create()->id,
            'priority' => TicketPriority::Medium,
            'location' => 'Room 204',
        ]);
    }
}
