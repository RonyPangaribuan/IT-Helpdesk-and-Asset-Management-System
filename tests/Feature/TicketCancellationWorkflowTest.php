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

class TicketCancellationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_requester_can_cancel_own_open_unassigned_ticket(): void
    {
        $requester = User::factory()->requester()->create();
        $ticket = $this->createOpenTicket($requester);

        $this->actingAs($requester)
            ->patch(route('tickets.cancel', $ticket), ['reason' => 'The issue has already been solved.'])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $latestHistory = $ticket->statusHistories()->get()->last();

        $this->assertSame(TicketStatus::Cancelled, $ticket->status);
        $this->assertNull($ticket->resolution_note);
        $this->assertNull($ticket->resolved_at);
        $this->assertNull($ticket->closed_at);
        $this->assertNotNull($latestHistory);
        $this->assertSame(TicketStatus::Open, $latestHistory->old_status);
        $this->assertSame(TicketStatus::Cancelled, $latestHistory->new_status);
        $this->assertSame('The issue has already been solved.', $latestHistory->note);
    }

    public function test_requester_cannot_cancel_other_or_assigned_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $otherTicket = $this->createOpenTicket($otherRequester);
        $assignedTicket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $technician);

        $this->actingAs($requester)
            ->patch(route('tickets.cancel', $otherTicket), ['reason' => 'Not mine.'])
            ->assertForbidden();

        $this->actingAs($requester)
            ->patch(route('tickets.cancel', $assignedTicket), ['reason' => 'Already assigned.'])
            ->assertForbidden();
    }

    public function test_admin_can_cancel_open_and_assigned_but_not_in_progress_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $openTicket = $this->createOpenTicket($requester);
        $assignedTicket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $technician);
        $inProgressTicket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $technician);
        app(TicketWorkflowService::class)->startWork($inProgressTicket, $technician);

        $this->actingAs($admin)
            ->patch(route('tickets.cancel', $openTicket), ['reason' => 'Duplicate request.'])
            ->assertRedirect(route('tickets.show', $openTicket));

        $this->actingAs($admin)
            ->patch(route('tickets.cancel', $assignedTicket), ['reason' => 'No longer needed.'])
            ->assertRedirect(route('tickets.show', $assignedTicket));

        $this->actingAs($admin)
            ->patch(route('tickets.cancel', $inProgressTicket->refresh()), ['reason' => 'Too late.'])
            ->assertForbidden();

        $this->assertSame(TicketStatus::Cancelled, $openTicket->refresh()->status);
        $this->assertSame(TicketStatus::Cancelled, $assignedTicket->refresh()->status);
        $this->assertSame(TicketStatus::InProgress, $inProgressTicket->refresh()->status);
    }

    public function test_technician_cannot_cancel_and_reason_is_required(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = app(TicketWorkflowService::class)->assign($this->createOpenTicket($requester), $admin, $technician);
        $openTicket = $this->createOpenTicket($requester);

        $this->actingAs($technician)
            ->patch(route('tickets.cancel', $ticket), ['reason' => 'Technician cannot cancel.'])
            ->assertForbidden();

        $this->actingAs($admin)
            ->from(route('tickets.show', $openTicket))
            ->patch(route('tickets.cancel', $openTicket), ['reason' => ''])
            ->assertRedirect(route('tickets.show', $openTicket))
            ->assertSessionHasErrors('reason');
    }

    public function test_cancelled_ticket_is_read_only(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $ticket = app(TicketWorkflowService::class)->createTicket($requester, [
            'title' => 'Email client keeps asking for password',
            'description' => 'The email client repeatedly asks for the account password.',
            'ticket_category_id' => $category->id,
            'priority' => TicketPriority::Medium,
            'location' => 'Faculty Office',
        ]);

        $this->actingAs($requester)
            ->patch(route('tickets.cancel', $ticket), ['reason' => 'Issue no longer occurs.']);

        $this->actingAs($requester)
            ->get(route('tickets.edit', $ticket->refresh()))
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('tickets.update', $ticket), [
                'ticket_category_id' => $category->id,
                'priority' => TicketPriority::High->value,
            ])
            ->assertForbidden();
    }

    public function test_closed_ticket_is_read_only(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $ticket = Ticket::factory()
            ->forRequester($requester)
            ->for($category, 'category')
            ->status(TicketStatus::Closed)
            ->create([
                'closed_at' => now(),
            ]);

        $this->actingAs($admin)
            ->get(route('tickets.edit', $ticket))
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('tickets.update', $ticket), [
                'ticket_category_id' => $category->id,
                'priority' => TicketPriority::High->value,
            ])
            ->assertForbidden();
    }

    private function createOpenTicket(User $requester): Ticket
    {
        return app(TicketWorkflowService::class)->createTicket($requester, [
            'title' => fake()->sentence(5),
            'description' => 'This is a valid ticket description for workflow testing.',
            'ticket_category_id' => TicketCategory::factory()->create()->id,
            'priority' => TicketPriority::Medium,
            'location' => 'Room 204',
        ]);
    }
}
