<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_based_sidebar_navigation_is_rendered(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->technician()->create();
        $requester = User::factory()->requester()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('aria-label="Primary navigation"', false)
            ->assertSeeText('Dashboard')
            ->assertSeeText('Tickets')
            ->assertSeeText('Assets')
            ->assertSeeText('Users')
            ->assertSeeText('Ticket Categories')
            ->assertSeeText('Asset Categories');

        $this->actingAs($technician)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Tickets')
            ->assertSeeText('Assets')
            ->assertDontSeeText('Users')
            ->assertDontSeeText('Ticket Categories')
            ->assertDontSeeText('Asset Categories')
            ->assertDontSeeText('Create Ticket');

        $this->actingAs($requester)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('My Tickets')
            ->assertSeeText('Create Ticket')
            ->assertDontSeeText('Users')
            ->assertDontSeeText('Assets')
            ->assertDontSeeText('Ticket Categories')
            ->assertDontSeeText('Asset Categories');
    }

    public function test_mobile_sidebar_markup_is_available(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="mobile-sidebar"', false)
            ->assertSee('aria-label="Open navigation menu"', false)
            ->assertSee('x-bind:aria-expanded="sidebarOpen.toString()"', false)
            ->assertSee('aria-label="Close navigation menu"', false);
    }

    public function test_account_deactivation_modal_has_accessible_dialog_markup(): void
    {
        $requester = User::factory()->requester()->create();

        $this->actingAs($requester)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false)
            ->assertSee('aria-label="Confirm account deactivation"', false);
    }

    public function test_landing_auth_and_main_pages_render_with_polished_ui(): void
    {
        config(['app.name' => 'deskIT']);

        $admin = User::factory()->admin()->create();
        $technician = User::factory()->technician()->create();
        $requester = User::factory()->requester()->create();
        $ticket = Ticket::factory()
            ->forRequester($requester)
            ->assignedTo($technician)
            ->create(['title' => 'Responsive polish ticket']);
        $asset = Asset::factory()->good()->create(['name' => 'Responsive polish asset']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('IT support, organized from report to resolution.')
            ->assertSeeText('Open deskIT')
            ->assertDontSeeText('DelDesk');

        $this->get(route('login'))
            ->assertOk()
            ->assertSeeText('Log in to deskIT');

        $this->get(route('register'))
            ->assertOk()
            ->assertSeeText('Create your deskIT account');

        $this->actingAs($requester)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSeeText('Filter tickets')
            ->assertSeeText('Responsive polish ticket');

        $this->actingAs($technician)
            ->get(route('assets.index'))
            ->assertOk()
            ->assertSeeText('Filter assets')
            ->assertSeeText('Responsive polish asset');

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSeeText('Manage access, roles, and account status');

        $this->actingAs($requester)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSeeText('Ticket workflow')
            ->assertSeeText('Responsive polish ticket');
    }

    public function test_ticket_detail_shows_visual_workflow_stages(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $ticket = Ticket::factory()
            ->forRequester($requester)
            ->resolved($technician)
            ->create(['title' => 'Resolution ready ticket']);

        $this->actingAs($requester)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSeeText('Reported')
            ->assertSeeText('Assigned')
            ->assertSeeText('In Progress')
            ->assertSeeText('Resolved')
            ->assertSeeText('Closed')
            ->assertSeeText('Resolution Ready')
            ->assertSeeText('Close Ticket')
            ->assertSeeText('Reopen Ticket');
    }
}
