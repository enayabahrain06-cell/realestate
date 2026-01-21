<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name',
        'description',
        'parameters',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parameters' => 'array',
    ];

    /**
     * Get the user who created the report.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Determine if the user can view any reports.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('reports.view');
    }

    /**
     * Determine if the user can view the report.
     */
    public function view(User $user)
    {
        return $user->hasPermission('reports.view');
    }

    /**
     * Determine if the user can create reports.
     */
    public function create(User $user)
    {
        return $user->hasPermission('reports.create');
    }

    /**
     * Determine if the user can update the report.
     */
    public function canUpdate(User $user)
    {
        return $user->hasPermission('reports.edit');
    }

    /**
     * Determine if the user can delete the report.
     */
    public function canDelete(User $user)
    {
        return $user->hasPermission('reports.delete');
    }
}

