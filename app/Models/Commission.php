<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_commissions';

    protected $fillable = [
        'agent_id',
        'commission_rule_id',
        'lease_id',
        'payment_id',
        'deal_amount',
        'commission_amount',
        'commission_rate',
        'status',
        'calculated_at',
        'approved_at',
        'paid_at',
        'payment_reference',
        'notes',
        'tier_achieved'
    ];

    protected $casts = [
        'deal_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'tier_achieved' => 'integer'
    ];

    public const STATUS_CALCULATED = 'calculated';
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_DISPUTED = 'disputed';

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function commissionRule()
    {
        return $this->belongsTo(CommissionRule::class);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_CALCULATED, self::STATUS_PENDING_APPROVAL]);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function approve(): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now()
        ]);
    }

    public function markAsPaid(string $reference = null): bool
    {
        return $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
            'payment_reference' => $reference
        ]);
    }

    public function dispute(string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_DISPUTED,
            'notes' => $this->notes . "\nDispute reason: " . $reason
        ]);
    }
}

