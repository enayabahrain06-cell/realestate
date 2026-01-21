<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'real_estate_units';

    protected $fillable = [
        'building_id',
        'floor_id',
        'unit_number',
        'unit_type',
        'size_sqft',
        'bedrooms',
        'bathrooms',
        'rent_amount',
        'deposit_amount',
        'status',
        'features',
        'images',
        'description',
        'locked_until',
        'locked_by'
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'rent_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'locked_until' => 'datetime'
    ];

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_RENTED = 'rented';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_BLOCKED = 'blocked';

    public const TYPE_FLAT = 'flat';
    public const TYPE_OFFICE = 'office';
    public const TYPE_COMMERCIAL = 'commercial';
    public const TYPE_WAREHOUSE = 'warehouse';
    public const TYPE_PARKING = 'parking';

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease()
    {
        return $this->leases()->where('status', 'active')->first();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function histories()
    {
        return $this->hasMany(UnitHistory::class);
    }

    public function tenantHistory()
    {
        return $this->hasMany(UnitTenantHistory::class);
    }

    public function agentAssignments()
    {
        return $this->hasMany(AgentUnitAssignment::class);
    }

    public function assignedAgents()
    {
        return $this->belongsToMany(Agent::class, 'real_estate_agent_unit_assignments');
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE && 
               (!$this->locked_until || $this->locked_until->isPast());
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function lock(string $sessionId, int $minutes = 15): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $this->update([
            'locked_until' => now()->addMinutes($minutes),
            'locked_by' => $sessionId
        ]);

        return true;
    }

    public function unlock(): bool
    {
        if ($this->locked_by === session()->getId()) {
            $this->update([
                'locked_until' => null,
                'locked_by' => null
            ]);
            return true;
        }
        return false;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'success',
            self::STATUS_RESERVED => 'warning',
            self::STATUS_RENTED => 'danger',
            self::STATUS_MAINTENANCE => 'secondary',
            self::STATUS_BLOCKED => 'dark',
            default => 'secondary'
        };
    }
}

