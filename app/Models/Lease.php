<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lease extends Model
{
    use HasFactory;

    protected $table = 'real_estate_leases';

    protected $fillable = [
        'tenant_id',
        'unit_id',
        'lease_type',
        'start_date',
        'end_date',
        'rent_amount',
        'deposit_amount',
        'payment_frequency',
        'late_payment_fee',
        'status',
        'terms',
        'contract_document',
        'cancellation_notes',
        'terminated_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rent_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'late_payment_fee' => 'decimal:2',
        'terminated_at' => 'datetime'
    ];

    public const TYPE_SINGLE_UNIT = 'single_unit';
    public const TYPE_FULL_FLOOR = 'full_floor';
    public const TYPE_FULL_BUILDING = 'full_building';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_TERMINATED = 'terminated';
    public const STATUS_PENDING = 'pending';

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function lateFees()
    {
        return $this->hasMany(LateFee::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->is_expired) return 0;
        return now()->diffInDays($this->end_date);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->rent_amount - $this->total_paid;
    }

    public function getEwaToRentRatioAttribute(): float
    {
        $totalRent = $this->total_paid;
        if ($totalRent == 0) return 0;
        
        $ewaAmount = $this->unit->ewaBills()
            ->whereBetween('billing_period_start', [$this->start_date, $this->end_date ?? now()])
            ->sum('amount');
            
        return round(($ewaAmount / $totalRent) * 100, 2);
    }
}

