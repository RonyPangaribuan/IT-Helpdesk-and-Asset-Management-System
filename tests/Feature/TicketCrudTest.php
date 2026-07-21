<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_ticket(): void
    {
        $category = TicketCategory::factory()->create();

        $this->get(route('tickets.create'))
            ->assertRedirect('/login');

        $this->post(route('tickets.store'), $this->validTicketPayload($category))
            ->assertRedirect('/login');
    }

    public function test_admin_and_technician_cannot_create_ticket(): void
    {
        $category = TicketCategory::factory()->create();
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->technician()->create();

        $this->actingAs($admin)
            ->get(route('tickets.create'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('tickets.store'), $this->validTicketPayload($category))
            ->assertForbidden();

        $this->actingAs($technician)
            ->post(route('tickets.store'), $this->validTicketPayload($category))
            ->assertForbidden();
    }

    public function test_requester_can_create_valid_ticket(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();

        $response = $this->actingAs($requester)
            ->post(route('tickets.store'), $this->validTicketPayload($category));

        $ticket = Ticket::first();

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertSame($requester->id, $ticket->requester_id);
        $this->assertNull($ticket->technician_id);
        $this->assertSame(TicketStatus::Open, $ticket->status);
        $this->assertMatchesRegularExpression('/^TCK-\d{4}-\d{6}$/', $ticket->ticket_code);
    }

    public function test_invalid_ticket_input_is_rejected(): void
    {
        $requester = User::factory()->requester()->create();

        $this->actingAs($requester)
            ->post(route('tickets.store'), [
                'title' => '',
                'description' => 'short',
                'ticket_category_id' => 999,
                'priority' => 'urgent',
                'location' => '',
            ])
            ->assertSessionHasErrors(['title', 'description', 'ticket_category_id', 'priority', 'location']);

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_ticket_creation_ignores_forbidden_input_fields(): void
    {
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $category = TicketCategory::factory()->create();

        $this->actingAs($requester)
            ->post(route('tickets.store'), [
                ...$this->validTicketPayload($category),
                'requester_id' => $otherRequester->id,
                'technician_id' => $technician->id,
                'ticket_code' => 'TCK-1999-999999',
                'status' => TicketStatus::Closed->value,
                'resolution_note' => 'Injected resolution.',
                'resolved_at' => now()->subDay()->toDateTimeString(),
                'closed_at' => now()->toDateTimeString(),
            ]);

        $ticket = Ticket::firstOrFail();

        $this->assertSame($requester->id, $ticket->requester_id);
        $this->assertNull($ticket->technician_id);
        $this->assertNotSame('TCK-1999-999999', $ticket->ticket_code);
        $this->assertSame(TicketStatus::Open, $ticket->status);
        $this->assertNull($ticket->resolution_note);
        $this->assertNull($ticket->resolved_at);
        $this->assertNull($ticket->closed_at);
    }

    public function test_ticket_code_is_unique(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();

        $this->actingAs($requester)->post(route('tickets.store'), $this->validTicketPayload($category, ['title' => 'First issue']));
        $this->actingAs($requester)->post(route('tickets.store'), $this->validTicketPayload($category, ['title' => 'Second issue']));

        $codes = Ticket::query()->pluck('ticket_code');

        $this->assertCount(2, $codes);
        $this->assertCount(2, $codes->unique());
    }

    public function test_requester_can_update_own_open_unassigned_ticket(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $newCategory = TicketCategory::factory()->create();
        $ticket = Ticket::factory()->forRequester($requester)->for($category, 'category')->create();

        $this->actingAs($requester)
            ->patch(route('tickets.update', $ticket), $this->validTicketPayload($newCategory, [
                'title' => 'Updated title',
                'priority' => TicketPriority::Critical->value,
            ]))
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();

        $this->assertSame('Updated title', $ticket->title);
        $this->assertSame($newCategory->id, $ticket->ticket_category_id);
        $this->assertSame(TicketPriority::Critical, $ticket->priority);
    }

    public function test_requester_cannot_update_other_users_ticket_or_assigned_ticket(): void
    {
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $category = TicketCategory::factory()->create();
        $otherTicket = Ticket::factory()->forRequester($otherRequester)->for($category, 'category')->create();
        $assignedTicket = Ticket::factory()->forRequester($requester)->assignedTo($technician)->for($category, 'category')->create();

        $this->actingAs($requester)
            ->patch(route('tickets.update', $otherTicket), $this->validTicketPayload($category))
            ->assertForbidden();

        $this->actingAs($requester)
            ->patch(route('tickets.update', $assignedTicket), $this->validTicketPayload($category))
            ->assertForbidden();
    }

    public function test_requester_cannot_change_forbidden_fields_on_update(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $category = TicketCategory::factory()->create();
        $ticket = Ticket::factory()->forRequester($requester)->for($category, 'category')->create();
        $originalCode = $ticket->ticket_code;

        $this->actingAs($requester)
            ->patch(route('tickets.update', $ticket), [
                ...$this->validTicketPayload($category, ['title' => 'Allowed update']),
                'ticket_code' => 'TCK-1999-111111',
                'technician_id' => $technician->id,
                'status' => TicketStatus::Closed->value,
                'resolution_note' => 'Injected note.',
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();

        $this->assertSame($originalCode, $ticket->ticket_code);
        $this->assertNull($ticket->technician_id);
        $this->assertSame(TicketStatus::Open, $ticket->status);
        $this->assertNull($ticket->resolution_note);
    }

    public function test_admin_can_only_update_category_and_priority_on_milestone_2(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $newCategory = TicketCategory::factory()->create();
        $ticket = Ticket::factory()->forRequester($requester)->for($category, 'category')->create([
            'title' => 'Original title',
            'location' => 'Original location',
        ]);

        $this->actingAs($admin)
            ->patch(route('tickets.update', $ticket), [
                'title' => 'Injected title',
                'description' => 'Injected description should not be used.',
                'location' => 'Injected location',
                'ticket_category_id' => $newCategory->id,
                'priority' => TicketPriority::High->value,
                'status' => TicketStatus::Closed->value,
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();

        $this->assertSame('Original title', $ticket->title);
        $this->assertSame('Original location', $ticket->location);
        $this->assertSame($newCategory->id, $ticket->ticket_category_id);
        $this->assertSame(TicketPriority::High, $ticket->priority);
        $this->assertSame(TicketStatus::Open, $ticket->status);
    }

    public function test_technician_cannot_update_ticket(): void
    {
        $technician = User::factory()->technician()->create();
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $ticket = Ticket::factory()->forRequester($requester)->assignedTo($technician)->for($category, 'category')->create();

        $this->actingAs($technician)
            ->patch(route('tickets.update', $ticket), $this->validTicketPayload($category))
            ->assertForbidden();
    }

    public function test_only_admin_can_archive_ticket_and_archived_ticket_is_hidden_from_list(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $category = TicketCategory::factory()->create();
        $ticket = Ticket::factory()->forRequester($requester)->for($category, 'category')->create();

        $this->actingAs($requester)
            ->delete(route('tickets.destroy', $ticket))
            ->assertForbidden();

        $this->actingAs($technician)
            ->delete(route('tickets.destroy', $ticket))
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('tickets.destroy', $ticket))
            ->assertRedirect(route('tickets.index'));

        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);

        $this->actingAs($admin)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertDontSee($ticket->ticket_code);
    }

    public function test_user_with_ticket_gets_validation_error_when_deleting_profile(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        Ticket::factory()->forRequester($requester)->for($category, 'category')->create();

        $this->actingAs($requester)
            ->from('/profile')
            ->delete('/profile', ['password' => 'password'])
            ->assertRedirect('/profile')
            ->assertSessionHasErrorsIn('userDeletion', 'password');

        $this->assertAuthenticatedAs($requester);
        $this->assertNotNull($requester->fresh());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validTicketPayload(TicketCategory $category, array $overrides = []): array
    {
        return [
            'title' => 'Computer cannot access shared drive',
            'description' => 'The shared drive cannot be accessed from this workstation.',
            'ticket_category_id' => $category->id,
            'priority' => TicketPriority::Medium->value,
            'location' => 'Room 204',
            ...$overrides,
        ];
    }
}
