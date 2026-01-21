<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $table = 'real_estate_buildings';

    protected $fillable = [
        'name',
        'address',
        'property_type',
        'total_floors',
        'ewa_account_number',
        'description',
        'image',
        'latitude',
        'longitude',
        'amenities',
        'status'
    ];

    protected $casts = [
        'amenities' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function floors()
    {
        return $this->hasMany(Floor::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function getTotalUnitsAttribute()
    {
        return $this->units()->count();
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

    public function getOccupancyRateAttribute()
    {
        $total = $this->total_units;
        if ($total == 0) return 0;
        return round(($this->rented_units / $total) * 100, 2);
    }

    public function ewaBills()
    {
        return $this->hasMany(EwaBill::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function lateFees()
    {
        return $this->hasMany(LateFee::class);
    }

    public function getTotalRentRevenueAttribute()
    {
        // Sum of all payments from units in this building
        return $this->units()->with('payments')->get()->sum(function ($unit) {
            return $unit->payments->sum('amount');
        });
    }

    // Financial Accessors
    public function getTotalRevenueAttribute(): float
    {
        return $this->total_rent_revenue + $this->getOtherIncomeAttribute();
    }

    public function getTotalExpensesAttribute(): float
    {
        return $this->expenses()->sum('amount') + $this->ewaBills()->sum('amount');
    }

    public function getNetProfitAttribute(): float
    {
        return $this->total_revenue - $this->total_expenses;
    }

    public function getProfitMarginAttribute(): float
    {
        $revenue = $this->total_revenue;
        if ($revenue == 0) return 0;
        return round(($this->net_profit / $revenue) * 100, 2);
    }

    protected function getOtherIncomeAttribute(): float
    {
        return 0; // Can be extended for other income sources
    }
}

