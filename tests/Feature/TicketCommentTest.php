<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_visible_participants_can_comment_on_active_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->assignedTo($technician)->create();

        foreach ([$requester, $technician, $admin] as $actor) {
            $this->actingAs($actor)
                ->post(route('tickets.comments.store', $ticket), [
                    'body' => 'Comment from '.$actor->role,
                ])
                ->assertRedirect(route('tickets.show', $ticket));
        }

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $requester->id,
            'body' => 'Comment from requester',
        ]);
        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $technician->id,
            'body' => 'Comment from technician',
        ]);
        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'body' => 'Comment from admin',
        ]);
    }

    public function test_users_outside_ticket_visibility_cannot_comment(): void
    {
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $otherTechnician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->assignedTo($technician)->create();

        $this->actingAs($otherRequester)
            ->post(route('tickets.comments.store', $ticket), ['body' => 'I should not see this ticket.'])
            ->assertForbidden();

        $this->actingAs($otherTechnician)
            ->post(route('tickets.comments.store', $ticket), ['body' => 'I should not see this ticket.'])
            ->assertForbidden();

        $this->assertDatabaseMissing('ticket_comments', [
            'ticket_id' => $ticket->id,
            'body' => 'I should not see this ticket.',
        ]);
    }

    public function test_comment_author_can_edit_and_admin_can_delete_comment(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()->forRequester($requester)->assignedTo($technician)->create();
        $comment = TicketComment::factory()->for($ticket)->forAuthor($requester)->create([
            'body' => 'Initial update.',
        ]);

        $this->actingAs($technician)
            ->patch(route('tickets.comments.update', [$ticket, $comment]), ['body' => 'Edited by technician.'])
            ->assertForbidden();

        $this->actingAs($requester)
            ->patch(route('tickets.comments.update', [$ticket, $comment]), ['body' => 'Requester edited update.'])
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertDatabaseHas('ticket_comments', [
            'id' => $comment->id,
            'body' => 'Requester edited update.',
        ]);

        $this->actingAs($admin)
            ->delete(route('tickets.comments.destroy', [$ticket, $comment]))
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertSoftDeleted('ticket_comments', ['id' => $comment->id]);
    }

    public function test_comments_are_unavailable_after_ticket_is_closed_or_cancelled(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $closedTicket = Ticket::factory()->forRequester($requester)->closed($technician)->create();
        $cancelledTicket = Ticket::factory()->forRequester($requester)->cancelled()->create();
        $comment = TicketComment::factory()->for($closedTicket)->forAuthor($requester)->create();

        $this->actingAs($requester)
            ->post(route('tickets.comments.store', $closedTicket), ['body' => 'Please reopen this.'])
            ->assertForbidden();

        $this->actingAs($requester)
            ->post(route('tickets.comments.store', $cancelledTicket), ['body' => 'Please reopen this.'])
            ->assertForbidden();

        $this->actingAs($requester)
            ->patch(route('tickets.comments.update', [$closedTicket, $comment]), ['body' => 'Edited after close.'])
            ->assertForbidden();
    }
}
