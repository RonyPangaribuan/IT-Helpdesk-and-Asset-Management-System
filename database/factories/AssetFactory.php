<?php

namespace Database\Factories;

use App\Enums\AssetCondition;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $condition = fake()->randomElement(AssetCondition::cases());

        return [
            'asset_code' => strtoupper(fake()->unique()->bothify('AST-???-###')),
            'name' => fake()->words(3, true),
            'asset_category_id' => AssetCategory::factory(),
            'brand' => fake()->optional()->company(),
            'model' => fake()->optional()->bothify('MDL-###'),
            'serial_number' => fake()->boolean(70) ? strtoupper(fake()->unique()->bothify('SN-####-????')) : null,
            'location' => fake()->city().' Room '.fake()->numberBetween(100, 499),
            'condition' => $condition,
            'description' => fake()->optional()->paragraph(),
            'is_active' => $condition === AssetCondition::Retired ? false : fake()->boolean(85),
        ];
    }

    public function good(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::Good,
            'is_active' => true,
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::Maintenance,
            'is_active' => true,
        ]);
    }

    public function damaged(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::Damaged,
            'is_active' => true,
        ]);
    }

    public function retired(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::Retired,
            'is_active' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
