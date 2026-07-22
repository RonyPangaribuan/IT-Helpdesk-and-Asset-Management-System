<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_requester_and_technician_cannot_access_user_management(): void
    {
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();

        $this->get(route('admin.users.index'))->assertRedirect('/login');

        $this->actingAs($requester)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($technician)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_user_list(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Viewer']);
        User::factory()->requester()->create(['name' => 'Requester Listed']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSeeText('Users')
            ->assertSeeText('Admin Viewer')
            ->assertSeeText('Requester Listed');
    }

    public function test_user_search_role_filter_and_active_filter_work(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->requester()->active()->create(['name' => 'Alpha Requester', 'email' => 'alpha@example.test']);
        User::factory()->requester()->inactive()->create(['name' => 'Inactive Requester', 'email' => 'inactive@example.test']);
        User::factory()->technician()->active()->create(['name' => 'Beta Technician', 'email' => 'beta@example.test']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['q' => 'alpha']))
            ->assertOk()
            ->assertSeeText('Alpha Requester')
            ->assertDontSeeText('Beta Technician');

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['role' => User::ROLE_TECHNICIAN]))
            ->assertOk()
            ->assertSeeText('Beta Technician')
            ->assertDontSeeText('Alpha Requester');

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['active' => '0']))
            ->assertOk()
            ->assertSeeText('Inactive Requester')
            ->assertDontSeeText('Alpha Requester');
    }

    public function test_admin_can_create_requester_technician_and_administrator(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([User::ROLE_REQUESTER, User::ROLE_TECHNICIAN, User::ROLE_ADMIN] as $index => $role) {
            $this->actingAs($admin)
                ->post(route('admin.users.store'), $this->validCreatePayload([
                    'name' => ucfirst($role).' Demo',
                    'email' => $role.$index.'@example.test',
                    'role' => $role,
                ]))
                ->assertRedirect();

            $this->assertDatabaseHas('users', [
                'email' => $role.$index.'@example.test',
                'role' => $role,
                'is_active' => true,
            ]);
        }
    }

    public function test_duplicate_email_invalid_role_and_password_confirmation_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->requester()->create(['email' => 'duplicate@example.test']);

        $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), $this->validCreatePayload(['email' => $existing->email]))
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors('email');

        $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), $this->validCreatePayload(['email' => 'invalid-role@example.test', 'role' => 'manager']))
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors('role');

        $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                ...$this->validCreatePayload(['email' => 'mismatch@example.test']),
                'password_confirmation' => 'different-password',
            ])
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors('password');
    }

    public function test_admin_created_password_is_hashed(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), $this->validCreatePayload([
                'email' => 'hashed@example.test',
                'password' => 'initial-secret',
                'password_confirmation' => 'initial-secret',
            ]))
            ->assertRedirect();

        $user = User::where('email', 'hashed@example.test')->firstOrFail();

        $this->assertNotSame('initial-secret', $user->password);
        $this->assertTrue(Hash::check('initial-secret', $user->password));
    }

    public function test_admin_can_update_user_and_empty_password_keeps_old_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->requester()->create(['password' => 'old-password']);
        $oldPassword = $user->password;

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $user), $this->validUpdatePayload($user, [
                'name' => 'Updated Requester',
                'phone' => '0812-1111-2222',
                'password' => '',
                'password_confirmation' => '',
            ]))
            ->assertRedirect(route('admin.users.edit', $user));

        $user->refresh();

        $this->assertSame('Updated Requester', $user->name);
        $this->assertSame('0812-1111-2222', $user->phone);
        $this->assertSame($oldPassword, $user->password);
    }

    public function test_valid_optional_password_updates_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->requester()->create(['password' => 'old-password']);

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $user), $this->validUpdatePayload($user, [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]))
            ->assertRedirect(route('admin.users.edit', $user));

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_admin_cannot_deactivate_or_demote_self(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->patch(route('admin.users.update', $admin), $this->validUpdatePayload($admin, ['is_active' => '0']))
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('is_active');

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->patch(route('admin.users.update', $admin), $this->validUpdatePayload($admin, ['role' => User::ROLE_REQUESTER]))
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('role');
    }

    public function test_last_active_admin_cannot_be_deactivated_or_changed_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->patch(route('admin.users.update', $admin), $this->validUpdatePayload($admin, ['is_active' => '0']))
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('is_active');

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->patch(route('admin.users.update', $admin), $this->validUpdatePayload($admin, ['role' => User::ROLE_TECHNICIAN]))
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('role');
    }

    public function test_technician_with_active_assigned_ticket_cannot_be_deactivated(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        Ticket::factory()->forRequester($requester)->assignedTo($technician)->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $technician))
            ->patch(route('admin.users.update', $technician), $this->validUpdatePayload($technician, ['is_active' => '0']))
            ->assertRedirect(route('admin.users.edit', $technician))
            ->assertSessionHasErrors('is_active');
    }

    public function test_users_with_relational_role_dependencies_cannot_change_to_invalid_role(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        Ticket::factory()->forRequester($requester)->open()->create();
        Ticket::factory()->forRequester(User::factory()->requester()->create())->closed($technician)->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $requester))
            ->patch(route('admin.users.update', $requester), $this->validUpdatePayload($requester, ['role' => User::ROLE_TECHNICIAN]))
            ->assertRedirect(route('admin.users.edit', $requester))
            ->assertSessionHasErrors('role');

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $technician))
            ->patch(route('admin.users.update', $technician), $this->validUpdatePayload($technician, ['role' => User::ROLE_REQUESTER]))
            ->assertRedirect(route('admin.users.edit', $technician))
            ->assertSessionHasErrors('role');
    }

    public function test_delete_user_route_is_not_available(): void
    {
        $this->assertFalse(Route::has('admin.users.destroy'));
    }

    public function test_direct_user_management_urls_are_protected(): void
    {
        $requester = User::factory()->requester()->create();
        $managedUser = User::factory()->requester()->create();

        $this->actingAs($requester)
            ->get(route('admin.users.edit', $managedUser))
            ->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validCreatePayload(array $overrides = []): array
    {
        return [
            'name' => 'Managed User',
            'email' => 'managed@example.test',
            'phone' => '0812-9999-0000',
            'role' => User::ROLE_REQUESTER,
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_active' => '1',
            ...$overrides,
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validUpdatePayload(User $user, array $overrides = []): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'is_active' => $user->is_active ? '1' : '0',
            'password' => '',
            'password_confirmation' => '',
            ...$overrides,
        ];
    }
}
