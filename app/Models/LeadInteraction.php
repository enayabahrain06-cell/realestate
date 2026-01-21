<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadInteraction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_lead_interactions';

    protected $fillable = [
        'lead_id',
        'interaction_type',
        'subject',
        'notes',
        'duration_minutes',
        'scheduled_at',
        'completed_at',
        'outcome',
        'next_action',
        'next_action_date',
        'created_by',
        'is_follow_up'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'next_action_date' => 'date',
        'duration_minutes' => 'integer',
        'is_follow_up' => 'boolean'
    ];

    public const TYPE_PHONE_CALL = 'phone_call';
    public const TYPE_EMAIL = 'email';
    public const TYPE_WHATSAPP = 'whatsapp';
    public const TYPE_VIEWING = 'viewing';
    public const TYPE_MEETING = 'meeting';
    public const TYPE_SITE_VISIT = 'site_visit';
    public const TYPE_OTHER = 'other';

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereNotNull('scheduled_at')
                    ->where('scheduled_at', '>', now())
                    ->whereNull('completed_at');
    }

    public function markAsCompleted(string $outcome = null): bool
    {
        return $this->update([
            'completed_at' => now(),
            'outcome' => $outcome ?? 'Completed'
        ]);
    }
}

