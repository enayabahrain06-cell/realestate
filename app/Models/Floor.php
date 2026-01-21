<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $table = 'real_estate_floors';

    protected $fillable = [
        'building_id',
        'floor_number',
        'total_units',
        'description',
        'floor_plan'
    ];

    protected $casts = [
        'floor_plan' => 'array'
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function getAvailableUnitsAttribute()
    {
        return $this->units()->where('status', 'available')->count();
    }

    public function getRentedUnitsAttribute()
    {
        return $this->units()->where('status', 'rented')->count();
    }

    public function getReservedUnitsAttribute()
    {
        return $this->units()->where('status', 'reserved')->count();
    }
}

