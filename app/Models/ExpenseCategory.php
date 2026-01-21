<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_expense_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'is_active',
        'type'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public const TYPE_OPERATIONAL = 'operational';
    public const TYPE_MAINTENANCE = 'maintenance';
    public const TYPE_ADMINISTRATIVE = 'administrative';
    public const TYPE_MARKETING = 'marketing';
    public const TYPE_UTILITY = 'utility';
    public const TYPE_INSURANCE = 'insurance';
    public const TYPE_TAX = 'tax';
    public const TYPE_OTHER = 'other';

    public function parent()
    {
        return $this->belongsTo(ExpenseCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ExpenseCategory::class, 'parent_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getTotalExpensesAttribute(): float
    {
        return $this->expenses()->sum('amount');
    }
}

