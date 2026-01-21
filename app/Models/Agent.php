<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_agents';

    protected $fillable = [
        'user_id',
        'employee_id',
        'full_name',
        'email',
        'phone',
        'whatsapp',
        'profile_photo',
        'commission_rate',
        'commission_type',
        'base_salary',
        'target_monthly_deals',
        'department',
        'license_number',
        'license_expiry',
        'date_joined',
        'status',
        'bio',
        'specializations',
        'languages',
        'total_sales',
        'total_commission_earned',
        'properties_sold'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:4',
        'base_salary' => 'decimal:2',
        'target_monthly_deals' => 'integer',
        'license_expiry' => 'date',
        'date_joined' => 'date',
        'total_sales' => 'decimal:2',
        'total_commission_earned' => 'decimal:2',
        'properties_sold' => 'integer',
        'specializations' => 'array',
        'languages' => 'array'
    ];

    public const COMMISSION_TYPE_PERCENTAGE = 'percentage';
    public const COMMISSION_TYPE_FIXED = 'fixed';
    public const COMMISSION_TYPE_TIERED = 'tiered';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_ON_LEAVE = 'on_leave';
    public const STATUS_TERMINATED = 'terminated';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unitAssignments()
    {
        return $this->hasMany(AgentUnitAssignment::class);
    }

    public function assignedUnits()
    {
        return $this->belongsToMany(Unit::class, 'real_estate_agent_unit_assignments')
                    ->withPivot(['assigned_at', 'notes']);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_agent_id');
    }

    public function getActiveLeadsCountAttribute(): int
    {
        return $this->leads()->active()->count();
    }

    public function getThisMonthDealsAttribute(): int
    {
        return $this->commissions()
                    ->where('status', Commission::STATUS_PAID)
                    ->whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->count();
    }

    public function getThisMonthCommissionAttribute(): float
    {
        return $this->commissions()
                    ->where('status', Commission::STATUS_PAID)
                    ->whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('amount');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->orderByDesc('total_commission_earned')->limit($limit);
    }

    public function calculateCommission(float $dealAmount, int $tier = 1): float
    {
        if ($this->commission_type === self::COMMISSION_TYPE_FIXED) {
            return $this->commission_rate;
        }

        // Percentage-based commission
        $rate = match($tier) {
            1 => $this->commission_rate,
            2 => $this->commission_rate * 1.2, // 20% bonus for tier 2
            3 => $this->commission_rate * 1.5, // 50% bonus for tier 3
            default => $this->commission_rate
        };

        return $dealAmount * $rate;
    }
}

