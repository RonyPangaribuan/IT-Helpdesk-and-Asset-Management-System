<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AssetCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_requester_and_technician_cannot_access_asset_category_management(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();

        $this->get(route('admin.asset-categories.index'))
            ->assertRedirect('/login');

        $this->actingAs($requester)
            ->get(route('admin.asset-categories.index'))
            ->assertForbidden();

        $this->actingAs($technician)
            ->get(route('admin.asset-categories.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_archive_asset_category(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.asset-categories.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.asset-categories.store'), [
                'name' => '  Tablet  ',
                'description' => '  Shared tablet devices.  ',
            ])
            ->assertRedirect(route('admin.asset-categories.index'));

        $category = AssetCategory::firstOrFail();

        $this->assertSame('Tablet', $category->name);
        $this->assertSame('Shared tablet devices.', $category->description);

        $this->actingAs($admin)
            ->from(route('admin.asset-categories.create'))
            ->post(route('admin.asset-categories.store'), [
                'name' => 'Tablet',
                'description' => 'Duplicate category.',
            ])
            ->assertRedirect(route('admin.asset-categories.create'))
            ->assertSessionHasErrors('name');

        $this->actingAs($admin)
            ->patch(route('admin.asset-categories.update', $category), [
                'name' => 'Tablet Devices',
                'description' => '',
            ])
            ->assertRedirect(route('admin.asset-categories.index'));

        $this->assertDatabaseHas('asset_categories', [
            'id' => $category->id,
            'name' => 'Tablet Devices',
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.asset-categories.destroy', $category))
            ->assertRedirect(route('admin.asset-categories.index'));

        $this->assertSoftDeleted('asset_categories', ['id' => $category->id]);
        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_archived_category_is_not_available_for_new_asset_but_existing_asset_still_displays_it(): void
    {
        $admin = User::factory()->admin()->create();
        $category = AssetCategory::factory()->create(['name' => 'Legacy Devices']);
        $asset = Asset::factory()->for($category, 'category')->create(['name' => 'Legacy Switch']);

        $category->archive();

        $this->actingAs($admin)
            ->get(route('assets.create'))
            ->assertOk()
            ->assertDontSee('Legacy Devices');

        $this->actingAs($admin)
            ->get(route('assets.show', $asset))
            ->assertOk()
            ->assertSee('Legacy Devices')
            ->assertSee('(archived)');
    }

    public function test_force_delete_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('admin.asset-categories.force-delete'));
        $this->assertFalse(Route::has('admin.asset-categories.restore'));
    }
}
