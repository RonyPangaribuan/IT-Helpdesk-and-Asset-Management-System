<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_category_management(): void
    {
        $this->get(route('admin.ticket-categories.index'))
            ->assertRedirect('/login');
    }

    public function test_requester_and_technician_cannot_access_category_management(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();

        $this->actingAs($requester)
            ->get(route('admin.ticket-categories.index'))
            ->assertForbidden();

        $this->actingAs($technician)
            ->get(route('admin.ticket-categories.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_category_list(): void
    {
        $admin = User::factory()->admin()->create();
        $category = TicketCategory::factory()->create(['name' => 'Hardware']);

        $this->actingAs($admin)
            ->get(route('admin.ticket-categories.index'))
            ->assertOk()
            ->assertSee($category->name);
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.ticket-categories.store'), [
                'name' => 'Security',
                'description' => 'Security incidents and access reviews.',
            ])
            ->assertRedirect(route('admin.ticket-categories.index'));

        $this->assertDatabaseHas('ticket_categories', [
            'name' => 'Security',
            'is_active' => true,
        ]);
    }

    public function test_duplicate_category_name_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        TicketCategory::factory()->create(['name' => 'Network']);

        $this->actingAs($admin)
            ->post(route('admin.ticket-categories.store'), [
                'name' => 'Network',
                'description' => 'Duplicate.',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = TicketCategory::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->patch(route('admin.ticket-categories.update', $category), [
                'name' => 'New Name',
                'description' => 'Updated description.',
            ])
            ->assertRedirect(route('admin.ticket-categories.index'));

        $this->assertDatabaseHas('ticket_categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'description' => 'Updated description.',
        ]);
    }

    public function test_admin_can_archive_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = TicketCategory::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->delete(route('admin.ticket-categories.destroy', $category))
            ->assertRedirect(route('admin.ticket-categories.index'));

        $this->assertSoftDeleted('ticket_categories', ['id' => $category->id]);
        $this->assertDatabaseHas('ticket_categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    public function test_archived_category_is_not_available_in_new_ticket_form(): void
    {
        $requester = User::factory()->requester()->create();
        $active = TicketCategory::factory()->create(['name' => 'Visible Category']);
        $archived = TicketCategory::factory()->create(['name' => 'Archived Category']);
        $archived->archive();

        $this->actingAs($requester)
            ->get(route('tickets.create'))
            ->assertOk()
            ->assertSee($active->name)
            ->assertDontSee($archived->name);
    }

    public function test_existing_ticket_can_display_archived_category(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create(['name' => 'Legacy Category']);
        $ticket = Ticket::factory()
            ->forRequester($requester)
            ->for($category, 'category')
            ->create();

        $category->archive();

        $this->actingAs($requester)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Legacy Category')
            ->assertSee('archived');
    }
}
