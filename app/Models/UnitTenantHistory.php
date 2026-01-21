<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitTenantHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_unit_tenant_history';

    protected $fillable = [
        'unit_id',
        'tenant_id',
        'lease_id',
        'action',
        'move_in_date',
        'move_out_date',
        'rent_amount',
        'deposit_amount',
        'termination_reason',
        'notes'
    ];

    protected $casts = [
        'move_in_date' => 'date',
        'move_out_date' => 'date',
        'rent_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2'
    ];

    public const ACTION_MOVE_IN = 'move_in';
    public const ACTION_MOVE_OUT = 'move_out';
    public const ACTION_RENEWAL = 'renewal';
    public const ACTION_TRANSFER = 'transfer';

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function scopeMoveIns($query)
    {
        return $query->where('action', self::ACTION_MOVE_IN);
    }

    public function scopeMoveOuts($query)
    {
        return $query->where('action', self::ACTION_MOVE_OUT);
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function getDurationDaysAttribute(): int
    {
        if (!$this->move_out_date) {
            return now()->diffInDays($this->move_in_date);
        }
        return $this->move_in_date->diffInDays($this->move_out_date);
    }
}

