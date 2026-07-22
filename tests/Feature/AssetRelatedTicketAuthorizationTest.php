<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AssetRelatedTicketAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_all_related_tickets_for_asset(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $firstTechnician = User::factory()->technician()->create();
        $secondTechnician = User::factory()->technician()->create();
        $asset = Asset::factory()->good()->create();
        $firstTicket = Ticket::factory()->forRequester($requester)->assignedTo($firstTechnician)->withAsset($asset)->create(['title' => 'First related ticket']);
        $secondTicket = Ticket::factory()->forRequester($requester)->assignedTo($secondTechnician)->withAsset($asset)->create(['title' => 'Second related ticket']);
        $unassignedTicket = Ticket::factory()->forRequester($requester)->withAsset($asset)->create(['title' => 'Unassigned related ticket']);

        $this->actingAs($admin)
            ->get(route('assets.show', $asset))
            ->assertOk()
            ->assertSee($firstTicket->ticket_code)
            ->assertSee($secondTicket->ticket_code)
            ->assertSee($unassignedTicket->ticket_code);
    }

    public function test_technician_only_sees_related_tickets_assigned_to_them(): void
    {
        $requester = User::factory()->requester()->create();
        $firstTechnician = User::factory()->technician()->create();
        $secondTechnician = User::factory()->technician()->create();
        $asset = Asset::factory()->good()->create();
        $ownTicket = Ticket::factory()->forRequester($requester)->assignedTo($firstTechnician)->withAsset($asset)->create(['title' => 'Own related ticket']);
        $otherTicket = Ticket::factory()->forRequester($requester)->assignedTo($secondTechnician)->withAsset($asset)->create(['title' => 'Other related ticket']);

        $this->actingAs($firstTechnician)
            ->get(route('assets.show', $asset))
            ->assertOk()
            ->assertSee($ownTicket->ticket_code)
            ->assertDontSee($otherTicket->ticket_code)
            ->assertDontSee('Other related ticket');
    }

    public function test_requester_cannot_open_asset_detail_directly(): void
    {
        $requester = User::factory()->requester()->create();
        $asset = Asset::factory()->good()->create();

        $this->actingAs($requester)
            ->get(route('assets.show', $asset))
            ->assertForbidden();
    }

    public function test_asset_detail_uses_eager_loading_for_related_tickets(): void
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
            ->get(route('assets.show', $asset))
            ->assertOk();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertLessThan(30, $queryCount);
    }
}
