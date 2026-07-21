<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'DelDesk Admin',
                'email' => 'admin@deldesk.test',
                'role' => User::ROLE_ADMIN,
                'phone' => '0812-0000-0001',
            ],
            [
                'name' => 'Budi Technician',
                'email' => 'technician1@deldesk.test',
                'role' => User::ROLE_TECHNICIAN,
                'phone' => '0812-0000-0002',
            ],
            [
                'name' => 'Sari Technician',
                'email' => 'technician2@deldesk.test',
                'role' => User::ROLE_TECHNICIAN,
                'phone' => '0812-0000-0003',
            ],
            [
                'name' => 'Alya Requester',
                'email' => 'requester1@deldesk.test',
                'role' => User::ROLE_REQUESTER,
                'phone' => '0812-0000-0004',
            ],
            [
                'name' => 'Raka Requester',
                'email' => 'requester2@deldesk.test',
                'role' => User::ROLE_REQUESTER,
                'phone' => '0812-0000-0005',
            ],
            [
                'name' => 'Mira Requester',
                'email' => 'requester3@deldesk.test',
                'role' => User::ROLE_REQUESTER,
                'phone' => '0812-0000-0006',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    ...$user,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ],
            );
        }
    }
}
