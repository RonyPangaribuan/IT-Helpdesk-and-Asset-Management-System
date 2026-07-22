<?php

namespace App\Models;

use App\Enums\AssetCondition;
use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_code',
        'name',
        'asset_category_id',
        'brand',
        'model',
        'serial_number',
        'location',
        'condition',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'condition' => AssetCondition::class,
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id')->withTrashed();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeSelectableForTickets(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('condition', '!=', AssetCondition::Retired->value);
    }

    public function isSelectableForTickets(): bool
    {
        return ! $this->trashed()
            && $this->is_active
            && $this->condition !== AssetCondition::Retired;
    }

    public function archive(): bool
    {
        $this->forceFill(['is_active' => false])->save();

        return (bool) $this->delete();
    }
}
