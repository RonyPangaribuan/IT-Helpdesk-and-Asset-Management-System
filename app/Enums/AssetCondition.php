<?php

namespace App\Enums;

enum AssetCondition: string
{
    case Good = 'good';
    case Maintenance = 'maintenance';
    case Damaged = 'damaged';
    case Retired = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Good',
            self::Maintenance => 'Maintenance',
            self::Damaged => 'Damaged',
            self::Retired => 'Retired',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
