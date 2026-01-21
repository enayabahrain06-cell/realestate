<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_commission_rules';

    protected $fillable = [
        'name',
        'description',
        'property_type',
        'deal_type',
        'commission_type',
        'rate',
        'fixed_amount',
        'min_deal_amount',
        'max_deal_amount',
        'tier',
        'tier_name',
        'tier_min_deals',
        'tier_max_deals',
        'tier_bonus_percentage',
        'is_active',
        'effective_from',
        'effective_until',
        'priority'
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'fixed_amount' => 'decimal:2',
        'min_deal_amount' => 'decimal:2',
        'max_deal_amount' => 'decimal:2',
        'tier' => 'integer',
        'tier_min_deals' => 'integer',
        'tier_max_deals' => 'integer',
        'tier_bonus_percentage' => 'decimal:4',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_until' => 'date'
    ];

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';
    public const TYPE_TIERED = 'tiered';

    public const DEAL_TYPE_RENTAL = 'rental';
    public const DEAL_TYPE_SALE = 'sale';
    public const DEAL_TYPE_BOTH = 'both';

    public const PROPERTY_TYPE_ALL = 'all';
    public const PROPERTY_TYPE_RESIDENTIAL = 'residential';
    public const PROPERTY_TYPE_COMMERCIAL = 'commercial';
    public const PROPERTY_TYPE_INDUSTRIAL = 'industrial';

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('effective_from')
                          ->orWhere('effective_from', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('effective_until')
                          ->orWhere('effective_until', '>=', now());
                    });
    }

    public function scopeForDealType($query, $dealType)
    {
        return $query->whereIn('deal_type', [$dealType, self::DEAL_TYPE_BOTH]);
    }

    public function scopeForPropertyType($query, $propertyType)
    {
        return $query->whereIn('property_type', [$propertyType, self::PROPERTY_TYPE_ALL]);
    }

    public function calculateCommission(float $dealAmount): float
    {
        if ($dealAmount < $this->min_deal_amount) {
            return 0;
        }

        if ($this->max_deal_amount && $dealAmount > $this->max_deal_amount) {
            return 0;
        }

        return match($this->commission_type) {
            self::TYPE_FIXED => $this->fixed_amount,
            self::TYPE_PERCENTAGE => $dealAmount * $this->rate,
            self::TYPE_TIERED => $this->calculateTieredCommission($dealAmount),
            default => 0
        };
    }

    protected function calculateTieredCommission(float $dealAmount): float
    {
        // For tiered, base rate applies to the full amount
        $baseCommission = $dealAmount * $this->rate;

        // Add tier bonus if applicable
        if ($this->tier_bonus_percentage) {
            $baseCommission += $baseCommission * $this->tier_bonus_percentage;
        }

        return $baseCommission;
    }
}

