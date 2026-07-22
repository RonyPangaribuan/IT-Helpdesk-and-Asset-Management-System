<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => User::ROLE_REQUESTER,
            'is_active' => true,
        ]);
    }

    public function test_registration_ignores_submitted_admin_or_technician_role_and_inactive_status(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_TECHNICIAN] as $index => $injectedRole) {
            $email = 'role-injection-'.$index.'@example.com';

            $response = $this->post('/register', [
                'name' => 'Role Injection',
                'email' => $email,
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => $injectedRole,
                'is_active' => false,
            ]);

            $this->assertAuthenticated();
            $response->assertRedirect(route('dashboard', absolute: false));

            $this->assertDatabaseHas('users', [
                'email' => $email,
                'role' => User::ROLE_REQUESTER,
                'is_active' => true,
            ]);

            $this->post('/logout');
        }
    }
}
