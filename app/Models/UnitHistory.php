<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_unit_histories';

    protected $fillable = [
        'unit_id',
        'action',
        'previous_rent_amount',
        'new_rent_amount',
        'previous_status',
        'new_status',
        'change_reason',
        'changed_by',
        'changed_at',
        'metadata'
    ];

    protected $casts = [
        'previous_rent_amount' => 'decimal:2',
        'new_rent_amount' => 'decimal:2',
        'changed_at' => 'datetime',
        'metadata' => 'array'
    ];

    public const ACTION_RENT_CHANGE = 'rent_change';
    public const ACTION_STATUS_CHANGE = 'status_change';
    public const ACTION_MAINTENANCE = 'maintenance';
    public const ACTION_INSPECTION = 'inspection';
    public const ACTION_FEATURE_UPDATE = 'feature_update';
    public const ACTION_PRICE_INCREASE = 'price_increase';
    public const ACTION_BULK_UPDATE = 'bulk_update';

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function scopeRentChanges($query)
    {
        return $query->where('action', self::ACTION_RENT_CHANGE);
    }

    public function scopeStatusChanges($query)
    {
        return $query->where('action', self::ACTION_STATUS_CHANGE);
    }

    public function getRentChangePercentageAttribute(): float
    {
        if (!$this->previous_rent_amount || $this->previous_rent_amount == 0) {
            return 0;
        }
        return (($this->new_rent_amount - $this->previous_rent_amount) / $this->previous_rent_amount) * 100;
    }
}

