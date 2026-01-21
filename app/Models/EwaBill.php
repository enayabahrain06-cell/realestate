<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EwaBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'bill_type',
        'amount',
        'due_date',
        'billing_period_start',
        'billing_period_end',
        'status',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date'
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
