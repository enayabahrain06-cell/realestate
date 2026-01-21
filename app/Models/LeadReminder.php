<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadReminder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_lead_reminders';

    protected $fillable = [
        'lead_id',
        'title',
        'description',
        'reminder_date',
        'reminder_time',
        'is_completed',
        'completed_at',
        'priority',
        'created_by',
        'assigned_to',
        'recurring',
        'recurring_type',
        'recurring_until'
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'reminder_time' => 'datetime',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'recurring' => 'boolean',
        'recurring_until' => 'date'
    ];

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    public const RECURRING_DAILY = 'daily';
    public const RECURRING_WEEKLY = 'weekly';
    public const RECURRING_MONTHLY = 'monthly';

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false)
                    ->where(function ($q) {
                        $q->whereNull('reminder_date')
                          ->orWhere('reminder_date', '<=', now());
                    });
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('is_completed', false)
                    ->whereBetween('reminder_date', [now(), now()->addDays($days)]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_completed', false)
                    ->where('reminder_date', '<', now());
    }

    public function markAsCompleted(): bool
    {
        $update = ['is_completed' => true, 'completed_at' => now()];

        // Handle recurring reminders
        if ($this->recurring && $this->recurring_until && $this->recurring_until->isFuture()) {
            $nextDate = match($this->recurring_type) {
                self::RECURRING_DAILY => now()->addDay(),
                self::RECURRING_WEEKLY => now()->addWeek(),
                self::RECURRING_MONTHLY => now()->addMonth(),
                default => now()->addDay()
            };

            if ($nextDate->lte($this->recurring_until)) {
                $this->update($update + ['reminder_date' => $nextDate]);
                return true;
            }
        }

        return $this->update($update);
    }
}

