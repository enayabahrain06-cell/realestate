<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'real_estate_bookings';

    protected $fillable = [
        'tenant_id',
        'unit_id',
        'booking_type',
        'booking_date',
        'notes',
        'status',
        'ip_address',
        'session_id'
    ];

    protected $casts = [
        'booking_date' => 'datetime'
    ];

    public const TYPE_INQUIRY = 'inquiry';
    public const TYPE_VIEWING = 'viewing';
    public const TYPE_RESERVATION = 'reservation';
    public const TYPE_RENTAL = 'rental';

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function cancel(): bool
    {
        if (!$this->canCancel()) {
            return false;
        }

        $this->update(['status' => self::STATUS_CANCELLED]);
        
        // Unlock the unit if it was reserved
        if ($this->booking_type === self::TYPE_RESERVATION) {
            $this->unit->unlock();
        }

        return true;
    }
}

