<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'real_estate_tenants';

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'id_number',
        'id_type',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'employer',
        'monthly_income',
        'notes',
        'status'
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2'
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_BLACKLISTED = 'blacklisted';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLeases()
    {
        return $this->leases()->where('status', 'active');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function tenantHistory()
    {
        return $this->hasMany(UnitTenantHistory::class);
    }

    public function lateFees()
    {
        return $this->hasMany(LateFee::class);
    }

    public function getActiveLeaseCountAttribute(): int
    {
        return $this->activeLeases()->count();
    }
}

