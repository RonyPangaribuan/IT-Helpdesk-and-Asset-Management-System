<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = AssetCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->keyBy('name');

        if ($categories->isEmpty()) {
            return;
        }

        $assets = [
            ['asset_code' => 'AST-LAP-001', 'name' => 'Lab Laptop 01', 'category' => 'Laptop', 'brand' => 'Lenovo', 'model' => 'ThinkPad E14', 'serial_number' => 'LAP-E14-001', 'location' => 'Computer Lab 1', 'condition' => AssetCondition::Good, 'is_active' => true],
            ['asset_code' => 'AST-LAP-002', 'name' => 'Faculty Laptop 02', 'category' => 'Laptop', 'brand' => 'Dell', 'model' => 'Latitude 5440', 'serial_number' => 'LAP-LAT-002', 'location' => 'Faculty Office', 'condition' => AssetCondition::Maintenance, 'is_active' => true],
            ['asset_code' => 'AST-DES-001', 'name' => 'Desktop Lab Workstation 01', 'category' => 'Desktop', 'brand' => 'HP', 'model' => 'ProDesk 400', 'serial_number' => 'DES-HP-001', 'location' => 'Computer Lab 1', 'condition' => AssetCondition::Good, 'is_active' => true],
            ['asset_code' => 'AST-DES-002', 'name' => 'Finance Desktop 02', 'category' => 'Desktop', 'brand' => 'Acer', 'model' => 'Veriton X', 'serial_number' => 'DES-ACR-002', 'location' => 'Finance Office', 'condition' => AssetCondition::Damaged, 'is_active' => true],
            ['asset_code' => 'AST-PRN-001', 'name' => 'Administration Printer', 'category' => 'Printer', 'brand' => 'Epson', 'model' => 'L6170', 'serial_number' => 'PRN-EPS-001', 'location' => 'Administration Office', 'condition' => AssetCondition::Maintenance, 'is_active' => true],
            ['asset_code' => 'AST-PRN-002', 'name' => 'Archive Room Scanner', 'category' => 'Printer', 'brand' => 'Canon', 'model' => 'DR-C225', 'serial_number' => 'SCN-CAN-002', 'location' => 'Archive Room', 'condition' => AssetCondition::Good, 'is_active' => true],
            ['asset_code' => 'AST-NET-001', 'name' => 'Main Router', 'category' => 'Network Device', 'brand' => 'MikroTik', 'model' => 'CCR2004', 'serial_number' => 'RTR-MTK-001', 'location' => 'Server Room', 'condition' => AssetCondition::Good, 'is_active' => true],
            ['asset_code' => 'AST-NET-002', 'name' => 'Library Access Point', 'category' => 'Network Device', 'brand' => 'Ubiquiti', 'model' => 'UniFi U6', 'serial_number' => 'AP-UBQ-002', 'location' => 'Library Floor 2', 'condition' => AssetCondition::Maintenance, 'is_active' => true],
            ['asset_code' => 'AST-PRJ-001', 'name' => 'Classroom Projector A204', 'category' => 'Projector', 'brand' => 'BenQ', 'model' => 'MW560', 'serial_number' => 'PRJ-BEN-001', 'location' => 'Room A-204', 'condition' => AssetCondition::Good, 'is_active' => true],
            ['asset_code' => 'AST-PRJ-002', 'name' => 'Classroom Projector C301', 'category' => 'Projector', 'brand' => 'Epson', 'model' => 'EB-X500', 'serial_number' => 'PRJ-EPS-002', 'location' => 'Room C-301', 'condition' => AssetCondition::Maintenance, 'is_active' => true],
            ['asset_code' => 'AST-PER-001', 'name' => 'Lab Monitor 01', 'category' => 'Peripheral', 'brand' => 'LG', 'model' => '24MP400', 'serial_number' => 'MON-LG-001', 'location' => 'Room B-112', 'condition' => AssetCondition::Damaged, 'is_active' => true],
            ['asset_code' => 'AST-PER-002', 'name' => 'Keyboard Mouse Set', 'category' => 'Peripheral', 'brand' => 'Logitech', 'model' => 'MK270', 'serial_number' => 'PER-LOG-002', 'location' => 'Computer Lab 1', 'condition' => AssetCondition::Retired, 'is_active' => false],
        ];

        foreach ($assets as $asset) {
            $category = $categories->get($asset['category']) ?? $categories->first();

            Asset::withTrashed()->updateOrCreate(
                ['asset_code' => $asset['asset_code']],
                [
                    'name' => $asset['name'],
                    'asset_category_id' => $category->id,
                    'brand' => $asset['brand'],
                    'model' => $asset['model'],
                    'serial_number' => $asset['serial_number'],
                    'location' => $asset['location'],
                    'condition' => $asset['condition'],
                    'description' => 'Demo inventory record for '.$asset['name'].'.',
                    'is_active' => $asset['condition'] === AssetCondition::Retired ? false : $asset['is_active'],
                    'deleted_at' => null,
                ],
            );
        }
    }
}
