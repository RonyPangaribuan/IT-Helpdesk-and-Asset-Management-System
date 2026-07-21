<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Hardware', 'description' => 'Physical devices, peripherals, and computer components.'],
            ['name' => 'Software', 'description' => 'Application installation, updates, and software errors.'],
            ['name' => 'Network', 'description' => 'Connectivity, Wi-Fi, LAN, and internet access issues.'],
            ['name' => 'Account', 'description' => 'Login, access, password, and account permission issues.'],
            ['name' => 'Printer', 'description' => 'Printer setup, printing failure, and print quality issues.'],
            ['name' => 'Projector', 'description' => 'Projector, display, and meeting room presentation equipment.'],
            ['name' => 'Other', 'description' => 'General IT support requests outside the listed categories.'],
        ];

        foreach ($categories as $category) {
            TicketCategory::withTrashed()->updateOrCreate(
                ['name' => $category['name']],
                [
                    ...$category,
                    'is_active' => true,
                    'deleted_at' => null,
                ],
            );
        }
    }
}
