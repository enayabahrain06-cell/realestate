<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_leads';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'whatsapp',
        'nationality',
        'preferred_location',
        'budget_min',
        'budget_max',
        'property_type',
        'bedrooms_required',
        'unit_size_min',
        'unit_size_max',
        'source',
        'assigned_agent_id',
        'status',
        'priority',
        'notes',
        'interest_level',
        'desired_move_in_date',
        'follow_up_date',
        'converted_to_tenant_at'
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'unit_size_min' => 'decimal:2',
        'unit_size_max' => 'decimal:2',
        'desired_move_in_date' => 'date',
        'follow_up_date' => 'date',
        'converted_to_tenant_at' => 'datetime'
    ];

    // Status Pipeline
    public const STATUS_NEW = 'new';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_VIEWING = 'viewing';
    public const STATUS_NEGOTIATION = 'negotiation';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_LOST = 'lost';

    // Priority Levels
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // Sources
    public const SOURCE_WALK_IN = 'walk_in';
    public const SOURCE_WEBSITE = 'website';
    public const SOURCE_REFERRAL = 'referral';
    public const SOURCE_SOCIAL_MEDIA = 'social_media';
    public const SOURCE_PROPERTY_PORTAL = 'property_portal';
    public const SOURCE_OTHER = 'other';

    public function assignedAgent()
    {
        return $this->belongsTo(Agent::class, 'assigned_agent_id');
    }

    public function interactions()
    {
        return $this->hasMany(LeadInteraction::class);
    }

    public function reminders()
    {
        return $this->hasMany(LeadReminder::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_NEW => 'primary',
            self::STATUS_CONTACTED => 'info',
            self::STATUS_VIEWING => 'warning',
            self::STATUS_NEGOTIATION => 'orange',
            self::STATUS_CLOSED => 'success',
            self::STATUS_LOST => 'danger',
            default => 'secondary'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_MEDIUM => 'info',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_URGENT => 'danger',
            default => 'secondary'
        };
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_NEW,
            self::STATUS_CONTACTED,
            self::STATUS_VIEWING,
            self::STATUS_NEGOTIATION
        ]);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function scopeAssigned($query, $agentId)
    {
        return $query->where('assigned_agent_id', $agentId);
    }

    public function scopeNeedsFollowUp($query)
    {
        return $query->whereNotNull('follow_up_date')
                    ->where('follow_up_date', '<=', now())
                    ->whereNotIn('status', [self::STATUS_CLOSED, self::STATUS_LOST]);
    }

    public function markAsClosed(?int $tenantId = null): bool
    {
        return $this->update([
            'status' => self::STATUS_CLOSED,
            'converted_to_tenant_at' => now(),
            'converted_to_tenant_id' => $tenantId
        ]);
    }

    public function markAsLost(?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_LOST,
            'notes' => $this->notes . "\nLost reason: " . ($reason ?? 'Not specified')
        ]);
    }
}

