<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\TicketStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AssetCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_routes_are_authorized_by_role(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $admin = User::factory()->admin()->create();
        $asset = Asset::factory()->create();

        $this->get(route('assets.index'))
            ->assertRedirect('/login');

        $this->actingAs($requester)
            ->get(route('assets.index'))
            ->assertForbidden();

        $this->actingAs($requester)
            ->get(route('assets.show', $asset))
            ->assertForbidden();

        $this->actingAs($technician)
            ->get(route('assets.index'))
            ->assertOk();

        $this->actingAs($technician)
            ->get(route('assets.show', $asset))
            ->assertOk();

        $this->actingAs($technician)
            ->get(route('assets.create'))
            ->assertForbidden();

        $this->actingAs($technician)
            ->patch(route('assets.update', $asset), $this->validAssetPayload($asset->category))
            ->assertForbidden();

        $this->actingAs($technician)
            ->delete(route('assets.destroy', $asset))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('assets.create'))
            ->assertOk();
    }

    public function test_admin_can_create_update_and_archive_asset(): void
    {
        $admin = User::factory()->admin()->create();
        $category = AssetCategory::factory()->create();
        $newCategory = AssetCategory::factory()->create();

        $this->actingAs($admin)
            ->post(route('assets.store'), $this->validAssetPayload($category, [
                'asset_code' => ' ast-lap-900 ',
                'brand' => '',
                'model' => ' Latitude ',
                'serial_number' => ' SN-900 ',
                'description' => '',
            ]))
            ->assertRedirect();

        $asset = Asset::firstOrFail();

        $this->assertSame('AST-LAP-900', $asset->asset_code);
        $this->assertNull($asset->brand);
        $this->assertSame('Latitude', $asset->model);
        $this->assertSame('SN-900', $asset->serial_number);
        $this->assertNull($asset->description);
        $this->assertTrue($asset->is_active);

        $this->actingAs($admin)
            ->patch(route('assets.update', $asset), $this->validAssetPayload($newCategory, [
                'asset_code' => 'AST-LAP-901',
                'name' => 'Updated Laptop',
                'condition' => AssetCondition::Maintenance->value,
                'is_active' => '0',
            ]))
            ->assertRedirect(route('assets.show', $asset));

        $asset->refresh();

        $this->assertSame('AST-LAP-901', $asset->asset_code);
        $this->assertSame('Updated Laptop', $asset->name);
        $this->assertSame($newCategory->id, $asset->asset_category_id);
        $this->assertSame(AssetCondition::Maintenance, $asset->condition);
        $this->assertFalse($asset->is_active);

        $this->actingAs($admin)
            ->delete(route('assets.destroy', $asset))
            ->assertRedirect(route('assets.index'));

        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
        $this->assertFalse($asset->fresh()->is_active);
    }

    public function test_asset_validation_enforces_unique_codes_serials_and_valid_condition(): void
    {
        $admin = User::factory()->admin()->create();
        $category = AssetCategory::factory()->create();
        $existing = Asset::factory()->for($category, 'category')->create([
            'asset_code' => 'AST-EXIST-001',
            'serial_number' => 'SERIAL-001',
        ]);

        $existing->archive();

        $this->actingAs($admin)
            ->from(route('assets.create'))
            ->post(route('assets.store'), $this->validAssetPayload($category, [
                'asset_code' => 'AST-EXIST-001',
                'serial_number' => 'SERIAL-002',
            ]))
            ->assertRedirect(route('assets.create'))
            ->assertSessionHasErrors('asset_code');

        $this->actingAs($admin)
            ->from(route('assets.create'))
            ->post(route('assets.store'), $this->validAssetPayload($category, [
                'asset_code' => 'AST-NEW-001',
                'serial_number' => 'SERIAL-001',
            ]))
            ->assertRedirect(route('assets.create'))
            ->assertSessionHasErrors('serial_number');

        $this->actingAs($admin)
            ->from(route('assets.create'))
            ->post(route('assets.store'), $this->validAssetPayload($category, [
                'asset_code' => 'AST-NEW-002',
                'condition' => 'excellent',
            ]))
            ->assertRedirect(route('assets.create'))
            ->assertSessionHasErrors('condition');
    }

    public function test_retired_asset_is_forced_inactive(): void
    {
        $admin = User::factory()->admin()->create();
        $category = AssetCategory::factory()->create();

        $this->actingAs($admin)
            ->post(route('assets.store'), $this->validAssetPayload($category, [
                'condition' => AssetCondition::Retired->value,
                'is_active' => '1',
            ]));

        $asset = Asset::firstOrFail();

        $this->assertSame(AssetCondition::Retired, $asset->condition);
        $this->assertFalse($asset->is_active);
    }

    public function test_asset_search_filters_and_pagination_work(): void
    {
        $technician = User::factory()->technician()->create();
        $laptopCategory = AssetCategory::factory()->create(['name' => 'Laptop']);
        $printerCategory = AssetCategory::factory()->create(['name' => 'Printer']);

        Asset::factory()->count(12)->for($laptopCategory, 'category')->good()->create();
        $target = Asset::factory()->for($printerCategory, 'category')->maintenance()->create([
            'asset_code' => 'AST-PRN-XYZ',
            'name' => 'Administration Printer',
            'brand' => 'Epson',
            'model' => 'L6170',
            'serial_number' => 'SERIAL-XYZ',
            'location' => 'Administration Office',
            'is_active' => false,
        ]);

        $this->actingAs($technician)
            ->get(route('assets.index', ['q' => 'AST-PRN-XYZ']))
            ->assertOk()
            ->assertSee($target->asset_code)
            ->assertDontSee('No assets found.');

        $this->actingAs($technician)
            ->get(route('assets.index', ['q' => 'Administration Printer']))
            ->assertOk()
            ->assertSee($target->name);

        $this->actingAs($technician)
            ->get(route('assets.index', ['q' => 'SERIAL-XYZ']))
            ->assertOk()
            ->assertSee($target->asset_code);

        $this->actingAs($technician)
            ->get(route('assets.index', ['asset_category_id' => $printerCategory->id]))
            ->assertOk()
            ->assertSee($target->asset_code);

        $this->actingAs($technician)
            ->get(route('assets.index', ['condition' => AssetCondition::Maintenance->value]))
            ->assertOk()
            ->assertSee($target->asset_code);

        $this->actingAs($technician)
            ->get(route('assets.index', ['active' => '0']))
            ->assertOk()
            ->assertSee($target->asset_code);

        $this->actingAs($technician)
            ->get(route('assets.index', ['q' => 'AST', 'page' => 2]))
            ->assertOk()
            ->assertSee('q=AST', false);
    }

    public function test_archived_asset_is_hidden_from_list_and_does_not_delete_related_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $asset = Asset::factory()->create(['asset_code' => 'AST-ARCH-001']);
        $ticket = Ticket::factory()->withAsset($asset)->status(TicketStatus::Open)->create();

        $this->actingAs($admin)
            ->delete(route('assets.destroy', $asset))
            ->assertRedirect(route('assets.index'));

        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
        $this->assertNotNull($ticket->fresh());

        $this->actingAs($admin)
            ->get(route('assets.index'))
            ->assertOk()
            ->assertDontSee('AST-ARCH-001');
    }

    public function test_force_delete_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('assets.force-delete'));
        $this->assertFalse(Route::has('assets.restore'));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validAssetPayload(AssetCategory $category, array $overrides = []): array
    {
        return [
            'asset_code' => 'AST-TEST-001',
            'name' => 'Test Asset',
            'asset_category_id' => $category->id,
            'brand' => 'Dell',
            'model' => 'Latitude 5440',
            'serial_number' => 'SERIAL-TEST-001',
            'location' => 'Room 204',
            'condition' => AssetCondition::Good->value,
            'description' => 'A valid test asset.',
            'is_active' => '1',
            ...$overrides,
        ];
    }
}
