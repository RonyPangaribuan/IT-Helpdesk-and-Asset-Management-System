<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Models\Asset;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_requester_dashboard_counts_and_recent_tickets_are_scoped_to_the_requester(): void
    {
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $asset = Asset::factory()->good()->create(['asset_code' => 'AST-REQ-001']);

        Ticket::factory()->forRequester($requester)->withAsset($asset)->open()->create(['title' => 'Requester open ticket']);
        Ticket::factory()->forRequester($requester)->inProgress($technician)->create(['title' => 'Requester in progress ticket']);
        Ticket::factory()->forRequester($requester)->resolved($technician)->create(['title' => 'Requester resolved ticket']);
        Ticket::factory()->forRequester($otherRequester)->open()->create(['title' => 'Other requester ticket']);

        $this->actingAs($requester)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Requester Dashboard')
            ->assertSeeTextInOrder(['Total My Tickets', '3'])
            ->assertSeeTextInOrder(['Open', '1'])
            ->assertSeeTextInOrder(['In Progress', '1'])
            ->assertSeeTextInOrder(['Resolved', '1'])
            ->assertSeeText('Requester open ticket')
            ->assertSeeText('AST-REQ-001')
            ->assertDontSeeText('Other requester ticket');
    }

    public function test_technician_dashboard_counts_and_recent_tickets_are_scoped_to_assigned_tickets(): void
    {
        $technician = User::factory()->technician()->create();
        $otherTechnician = User::factory()->technician()->create();
        $requester = User::factory()->requester()->create();
        $asset = Asset::factory()->good()->create(['asset_code' => 'AST-TEC-001']);

        Ticket::factory()->forRequester($requester)->assignedTo($technician)->withAsset($asset)->create(['title' => 'Assigned to me']);
        Ticket::factory()->forRequester($requester)->inProgress($technician)->create(['title' => 'In progress for me']);
        Ticket::factory()->forRequester($requester)->resolved($technician)->create(['title' => 'Resolved for me']);
        Ticket::factory()->forRequester($requester)->closed($technician)->create(['title' => 'Closed for me']);
        Ticket::factory()->forRequester($requester)->assignedTo($otherTechnician)->create(['title' => 'Assigned to another technician']);
        Ticket::factory()->forRequester($requester)->open()->create(['title' => 'Unassigned ticket']);

        $this->actingAs($technician)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Technician Dashboard')
            ->assertSeeTextInOrder(['Total Assigned Tickets', '4'])
            ->assertSeeTextInOrder(['Assigned', '1'])
            ->assertSeeTextInOrder(['In Progress', '1'])
            ->assertSeeTextInOrder(['Resolved', '1'])
            ->assertSeeText('Assigned to me')
            ->assertSeeText('AST-TEC-001')
            ->assertDontSeeText('Assigned to another technician')
            ->assertDontSeeText('Unassigned ticket');
    }

    public function test_admin_dashboard_uses_actual_operational_counts_and_breakdowns(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $hardware = TicketCategory::factory()->create(['name' => 'Hardware']);
        $software = TicketCategory::factory()->create(['name' => 'Software']);
        $activeAsset = Asset::factory()->good()->create(['asset_code' => 'AST-ACTIVE-001']);
        Asset::factory()->good()->inactive()->create(['asset_code' => 'AST-INACTIVE-001']);
        Asset::factory()->retired()->create(['asset_code' => 'AST-RETIRED-001']);
        $archivedAsset = Asset::factory()->good()->create(['asset_code' => 'AST-ARCHIVED-001']);
        $archivedAsset->archive();

        Ticket::factory()->forRequester($requester)->for($hardware, 'category')->withAsset($activeAsset)->open()->priority(TicketPriority::Low)->create(['title' => 'Admin open ticket']);
        Ticket::factory()->forRequester($requester)->for($hardware, 'category')->inProgress($technician)->priority(TicketPriority::Medium)->create(['title' => 'Admin in progress ticket']);
        Ticket::factory()->forRequester($requester)->for($software, 'category')->resolved($technician)->priority(TicketPriority::High)->create(['title' => 'Admin resolved ticket']);
        Ticket::factory()->forRequester($requester)->for($software, 'category')->closed($technician)->priority(TicketPriority::Critical)->create(['title' => 'Admin closed ticket']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Administrator Dashboard')
            ->assertSeeTextInOrder(['Total Tickets', '4'])
            ->assertSeeTextInOrder(['Open', '1'])
            ->assertSeeTextInOrder(['In Progress', '1'])
            ->assertSeeTextInOrder(['Resolved', '1'])
            ->assertSeeTextInOrder(['Closed', '1'])
            ->assertSeeTextInOrder(['Active Assets', '1'])
            ->assertSeeText('Tickets by Category')
            ->assertSeeText('Hardware')
            ->assertSeeText('Software')
            ->assertSeeText('Tickets by Priority')
            ->assertSeeText('Low')
            ->assertSeeText('Medium')
            ->assertSeeText('High')
            ->assertSeeText('Critical')
            ->assertSeeText('Admin open ticket')
            ->assertSeeText('AST-ACTIVE-001');
    }

    public function test_dashboard_empty_state_and_guest_redirect(): void
    {
        $requester = User::factory()->requester()->create();

        $this->get(route('dashboard'))
            ->assertRedirect('/login');

        $this->actingAs($requester)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('No recent tickets found.')
            ->assertSeeTextInOrder(['Total My Tickets', '0']);
    }

    public function test_admin_dashboard_uses_eager_loading_for_recent_tickets(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $asset = Asset::factory()->good()->create();

        Ticket::factory()
            ->count(15)
            ->forRequester($requester)
            ->assignedTo($technician)
            ->withAsset($asset)
            ->create();

        DB::enableQueryLog();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertLessThan(35, $queryCount);
    }
}
