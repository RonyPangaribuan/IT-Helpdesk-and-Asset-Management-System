<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Laptop', 'description' => 'Portable computers used by staff, labs, and classrooms.'],
            ['name' => 'Desktop', 'description' => 'Desktop workstations and lab computers.'],
            ['name' => 'Printer', 'description' => 'Printers, scanners, and multi-function devices.'],
            ['name' => 'Network Device', 'description' => 'Routers, switches, access points, and network equipment.'],
            ['name' => 'Projector', 'description' => 'Projectors and presentation display equipment.'],
            ['name' => 'Peripheral', 'description' => 'Monitors, keyboards, mice, speakers, and accessories.'],
            ['name' => 'Other', 'description' => 'Inventory items outside the listed asset categories.'],
        ];

        foreach ($categories as $category) {
            AssetCategory::withTrashed()->updateOrCreate(
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
