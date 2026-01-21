<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LateFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_late_fees';

    protected $fillable = [
        'building_id',
        'lease_id',
        'tenant_id',
        'payment_id',
        'rent_amount_due',
        'days_overdue',
        'late_fee_rate',
        'late_fee_amount',
        'grace_period_days',
        'maximum_late_fee',
        'due_date',
        'calculated_at',
        'status',
        'waiver_reason',
        'waived_by',
        'waived_at',
        'notes'
    ];

    protected $casts = [
        'rent_amount_due' => 'decimal:2',
        'days_overdue' => 'integer',
        'late_fee_rate' => 'decimal:4',
        'late_fee_amount' => 'decimal:2',
        'grace_period_days' => 'integer',
        'maximum_late_fee' => 'decimal:2',
        'due_date' => 'date',
        'calculated_at' => 'datetime',
        'waived_at' => 'datetime'
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_WAIVED = 'waived';
    public const STATUS_PAID = 'paid';
    public const STATUS_DISPUTED = 'disputed';

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function waivedBy()
    {
        return $this->belongsTo(User::class, 'waived_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->where('days_overdue', '>', 0);
    }

    public function scopeWaived($query)
    {
        return $query->where('status', self::STATUS_WAIVED);
    }

    public function scopeForBuilding($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Calculate late fee based on parameters
     */
    public static function calculate(
        float $rentAmount,
        int $daysOverdue,
        float $lateFeeRate = 0.05, // 5% default
        int $gracePeriod = 5,
        ?float $maximumFee = null
    ): float {
        // Skip if within grace period
        if ($daysOverdue <= $gracePeriod) {
            return 0;
        }

        // Calculate days after grace period
        $chargeableDays = $daysOverdue - $gracePeriod;

        // Calculate fee
        $fee = $rentAmount * $lateFeeRate;

        // Apply maximum fee cap if set
        if ($maximumFee !== null && $fee > $maximumFee) {
            $fee = $maximumFee;
        }

        return round($fee, 2);
    }

    /**
     * Waive the late fee
     */
    public function waive(int $waivedByUserId, ?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_WAIVED,
            'waived_by' => $waivedByUserId,
            'waived_at' => now(),
            'waiver_reason' => $reason
        ]);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): bool
    {
        return $this->update([
            'status' => self::STATUS_PAID
        ]);
    }
}

