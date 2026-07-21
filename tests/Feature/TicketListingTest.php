<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_all_tickets(): void
    {
        $admin = User::factory()->admin()->create();
        $first = Ticket::factory()->create();
        $second = Ticket::factory()->create();

        $this->actingAs($admin)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($first->ticket_code)
            ->assertSee($second->ticket_code);
    }

    public function test_requester_only_sees_own_tickets(): void
    {
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $ownTicket = Ticket::factory()->forRequester($requester)->create();
        $otherTicket = Ticket::factory()->forRequester($otherRequester)->create();

        $this->actingAs($requester)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($ownTicket->ticket_code)
            ->assertDontSee($otherTicket->ticket_code);
    }

    public function test_technician_only_sees_assigned_tickets(): void
    {
        $technician = User::factory()->technician()->create();
        $otherTechnician = User::factory()->technician()->create();
        $assigned = Ticket::factory()->assignedTo($technician)->create();
        $otherAssigned = Ticket::factory()->assignedTo($otherTechnician)->create();
        $unassigned = Ticket::factory()->create();

        $this->actingAs($technician)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($assigned->ticket_code)
            ->assertDontSee($otherAssigned->ticket_code)
            ->assertDontSee($unassigned->ticket_code);
    }

    public function test_unauthorized_users_cannot_open_ticket_detail(): void
    {
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($otherRequester)->create();

        $this->actingAs($requester)
            ->get(route('tickets.show', $ticket))
            ->assertForbidden();

        $this->actingAs($technician)
            ->get(route('tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_search_by_ticket_code_works(): void
    {
        $admin = User::factory()->admin()->create();
        $match = Ticket::factory()->create(['title' => 'Printer jam']);
        $miss = Ticket::factory()->create(['title' => 'Network down']);

        $this->actingAs($admin)
            ->get(route('tickets.index', ['q' => $match->ticket_code]))
            ->assertOk()
            ->assertSee($match->ticket_code)
            ->assertDontSee($miss->ticket_code);
    }

    public function test_search_by_title_works(): void
    {
        $admin = User::factory()->admin()->create();
        $match = Ticket::factory()->create(['title' => 'Projector lamp failure']);
        $miss = Ticket::factory()->create(['title' => 'Wi-Fi password issue']);

        $this->actingAs($admin)
            ->get(route('tickets.index', ['q' => 'Projector lamp']))
            ->assertOk()
            ->assertSee($match->ticket_code)
            ->assertDontSee($miss->ticket_code);
    }

    public function test_status_filter_works(): void
    {
        $admin = User::factory()->admin()->create();
        $open = Ticket::factory()->status(TicketStatus::Open)->create();
        $resolved = Ticket::factory()->status(TicketStatus::Resolved)->create([
            'resolution_note' => 'Done',
            'resolved_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('tickets.index', ['status' => TicketStatus::Resolved->value]))
            ->assertOk()
            ->assertSee($resolved->ticket_code)
            ->assertDontSee($open->ticket_code);
    }

    public function test_priority_filter_works(): void
    {
        $admin = User::factory()->admin()->create();
        $critical = Ticket::factory()->priority(TicketPriority::Critical)->create();
        $low = Ticket::factory()->priority(TicketPriority::Low)->create();

        $this->actingAs($admin)
            ->get(route('tickets.index', ['priority' => TicketPriority::Critical->value]))
            ->assertOk()
            ->assertSee($critical->ticket_code)
            ->assertDontSee($low->ticket_code);
    }

    public function test_category_filter_works(): void
    {
        $admin = User::factory()->admin()->create();
        $hardware = TicketCategory::factory()->create(['name' => 'Hardware']);
        $network = TicketCategory::factory()->create(['name' => 'Network']);
        $hardwareTicket = Ticket::factory()->for($hardware, 'category')->create();
        $networkTicket = Ticket::factory()->for($network, 'category')->create();

        $this->actingAs($admin)
            ->get(route('tickets.index', ['ticket_category_id' => $hardware->id]))
            ->assertOk()
            ->assertSee($hardwareTicket->ticket_code)
            ->assertDontSee($networkTicket->ticket_code);
    }

    public function test_pagination_preserves_filters(): void
    {
        $admin = User::factory()->admin()->create();
        $category = TicketCategory::factory()->create();

        Ticket::factory()
            ->count(11)
            ->for($category, 'category')
            ->priority(TicketPriority::High)
            ->create();

        $this->actingAs($admin)
            ->get(route('tickets.index', [
                'ticket_category_id' => $category->id,
                'priority' => TicketPriority::High->value,
            ]))
            ->assertOk()
            ->assertSee('ticket_category_id='.$category->id, false)
            ->assertSee('priority=high', false);
    }
}
