<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_dashboard_renders_role_specific_placeholder(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->technician()->create();
        $requester = User::factory()->requester()->create();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Administrator Dashboard');

        $this->actingAs($technician)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Technician Dashboard');

        $this->actingAs($requester)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Requester Dashboard');
    }

    public function test_role_dashboard_routes_are_restricted(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->technician()->create();
        $requester = User::factory()->requester()->create();

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk();

        $this->actingAs($technician)
            ->get('/admin/dashboard')
            ->assertForbidden();

        $this->actingAs($requester)
            ->get('/technician/dashboard')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/requester/dashboard')
            ->assertForbidden();
    }

    public function test_demo_seeded_accounts_can_authenticate(): void
    {
        $this->seed(DemoUserSeeder::class);

        foreach ([
            'admin@deskit.test',
            'technician1@deskit.test',
            'technician2@deskit.test',
            'requester1@deskit.test',
            'requester2@deskit.test',
            'requester3@deskit.test',
        ] as $email) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'password',
            ]);

            $this->assertAuthenticated();
            $response->assertRedirect(route('dashboard', absolute: false));

            $this->post('/logout');
        }
    }
}
