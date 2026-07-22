<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketResolutionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_technician_can_resolve_in_progress_ticket_with_note(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->inProgress($technician)->create();

        $this->actingAs($technician)
            ->patch(route('tickets.resolve', $ticket), [
                'resolution_note' => 'Reinstalled the driver and confirmed that printing works again.',
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $latestHistory = $ticket->statusHistories()->get()->last();

        $this->assertSame(TicketStatus::Resolved, $ticket->status);
        $this->assertSame('Reinstalled the driver and confirmed that printing works again.', $ticket->resolution_note);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertNull($ticket->closed_at);
        $this->assertNotNull($latestHistory);
        $this->assertSame(TicketStatus::InProgress, $latestHistory->old_status);
        $this->assertSame(TicketStatus::Resolved, $latestHistory->new_status);
    }

    public function test_only_assigned_technician_can_resolve_and_note_is_required(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $otherTechnician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->inProgress($technician)->create();

        $this->actingAs($otherTechnician)
            ->patch(route('tickets.resolve', $ticket), [
                'resolution_note' => 'This should not be accepted.',
            ])
            ->assertForbidden();

        $this->actingAs($requester)
            ->patch(route('tickets.resolve', $ticket), [
                'resolution_note' => 'Requester cannot resolve.',
            ])
            ->assertForbidden();

        $this->actingAs($technician)
            ->from(route('tickets.show', $ticket))
            ->patch(route('tickets.resolve', $ticket), [
                'resolution_note' => 'short',
            ])
            ->assertRedirect(route('tickets.show', $ticket))
            ->assertSessionHasErrors('resolution_note');

        $this->assertSame(TicketStatus::InProgress, $ticket->refresh()->status);
    }

    public function test_requester_or_admin_can_close_resolved_ticket_but_technician_cannot(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->resolved($technician)->create();
        $adminClosedTicket = Ticket::factory()->forRequester($requester)->resolved($technician)->create();

        $this->actingAs($technician)
            ->patch(route('tickets.close', $ticket))
            ->assertForbidden();

        $this->actingAs($requester)
            ->patch(route('tickets.close', $ticket))
            ->assertRedirect(route('tickets.show', $ticket));

        $this->actingAs($admin)
            ->patch(route('tickets.close', $adminClosedTicket))
            ->assertRedirect(route('tickets.show', $adminClosedTicket));

        $this->assertSame(TicketStatus::Closed, $ticket->refresh()->status);
        $this->assertNotNull($ticket->closed_at);
        $this->assertSame(TicketStatus::Closed, $adminClosedTicket->refresh()->status);
    }

    public function test_requester_can_reopen_resolved_ticket_and_assigned_technician_can_resume_work(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->resolved($technician)->create([
            'resolution_note' => 'Replaced the cable and confirmed the display.',
        ]);

        $this->actingAs($requester)
            ->patch(route('tickets.reopen', $ticket), [
                'reason' => 'The same display problem returned after the meeting.',
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $latestHistory = $ticket->statusHistories()->get()->last();

        $this->assertSame(TicketStatus::Reopened, $ticket->status);
        $this->assertSame($technician->id, $ticket->technician_id);
        $this->assertNull($ticket->resolution_note);
        $this->assertNull($ticket->resolved_at);
        $this->assertNull($ticket->closed_at);
        $this->assertNotNull($latestHistory);
        $this->assertSame(TicketStatus::Resolved, $latestHistory->old_status);
        $this->assertSame(TicketStatus::Reopened, $latestHistory->new_status);
        $this->assertSame('The same display problem returned after the meeting.', $latestHistory->note);

        $this->actingAs($technician)
            ->patch(route('tickets.start-work', $ticket))
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertSame(TicketStatus::InProgress, $ticket->refresh()->status);
        $this->assertSame('Resumed work', $ticket->statusHistories()->get()->last()?->note);
    }

    public function test_admin_can_reassign_reopened_ticket_to_different_technician(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $oldTechnician = User::factory()->technician()->create(['name' => 'Old Technician']);
        $newTechnician = User::factory()->technician()->create(['name' => 'New Technician']);
        $ticket = Ticket::factory()->forRequester($requester)->reopened($oldTechnician)->create();

        $this->actingAs($admin)
            ->patch(route('tickets.reassign', $ticket), [
                'technician_id' => $newTechnician->id,
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();
        $latestHistory = $ticket->statusHistories()->get()->last();

        $this->assertSame(TicketStatus::Assigned, $ticket->status);
        $this->assertSame($newTechnician->id, $ticket->technician_id);
        $this->assertNotNull($latestHistory);
        $this->assertSame(TicketStatus::Reopened, $latestHistory->old_status);
        $this->assertSame(TicketStatus::Assigned, $latestHistory->new_status);
        $this->assertSame('Reassigned from Old Technician to New Technician', $latestHistory->note);
    }

    public function test_closed_ticket_is_terminal_for_workflow_actions(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->closed($technician)->create();

        $this->actingAs($requester)
            ->patch(route('tickets.reopen', $ticket), ['reason' => 'Please reopen.'])
            ->assertForbidden();

        $this->actingAs($technician)
            ->patch(route('tickets.start-work', $ticket))
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('tickets.cancel', $ticket), ['reason' => 'Cancel after close.'])
            ->assertForbidden();

        $this->assertSame(TicketStatus::Closed, $ticket->refresh()->status);
        $this->assertSame(0, $ticket->statusHistories()->count());
    }
}
