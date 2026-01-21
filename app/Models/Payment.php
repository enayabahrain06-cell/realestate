<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'real_estate_payments';

    protected $fillable = [
        'lease_id',
        'tenant_id',
        'unit_id',
        'amount',
        'payment_type',
        'payment_method',
        'transaction_id',
        'notes',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public const TYPE_RENT = 'rent';
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_LATE_FEE = 'late_fee';
    public const TYPE_MAINTENANCE = 'maintenance';
    public const TYPE_OTHER = 'other';

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING && 
               $this->created_at->addDays(7)->isPast();
    }
}

