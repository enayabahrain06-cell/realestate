<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentUnitAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_agent_unit_assignments';

    protected $fillable = [
        'agent_id',
        'unit_id',
        'assigned_at',
        'notes',
        'is_primary'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'is_primary' => 'boolean'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

