<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_permissions';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
        'action',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Module definitions
    public const MODULE_BUILDINGS = 'buildings';
    public const MODULE_FLOORS = 'floors';
    public const MODULE_UNITS = 'units';
    public const MODULE_TENANTS = 'tenants';
    public const MODULE_LEASES = 'leases';
    public const MODULE_PAYMENTS = 'payments';
    public const MODULE_LEADS = 'leads';
    public const MODULE_AGENTS = 'agents';
    public const MODULE_COMMISSIONS = 'commissions';
    public const MODULE_DOCUMENTS = 'documents';
    public const MODULE_REPORTS = 'reports';
    public const MODULE_SETTINGS = 'settings';
    public const MODULE_ROLES = 'roles';
    public const MODULE_USERS = 'users';
    public const MODULE_EXPENSES = 'expenses';
    public const MODULE_EWA = 'ewa';

    // Action definitions
    public const ACTION_VIEW = 'view';
    public const ACTION_CREATE = 'create';
    public const ACTION_EDIT = 'edit';
    public const ACTION_DELETE = 'delete';
    public const ACTION_APPROVE = 'approve';
    public const ACTION_EXPORT = 'export';
    public const ACTION_REPORT = 'report';

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'real_estate_permission_role', 'permission_id', 'role_id')
                    ->withPivot(['is_custom'])
                    ->withTimestamps();
    }

    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullPermissionAttribute(): string
    {
        return "{$this->module}.{$this->action}";
    }

    /**
     * Generate standard permissions for a module
     */
    public static function generateModulePermissions(string $module): array
    {
        $actions = [
            self::ACTION_VIEW,
            self::ACTION_CREATE,
            self::ACTION_EDIT,
            self::ACTION_DELETE,
            self::ACTION_APPROVE,
            self::ACTION_EXPORT,
            self::ACTION_REPORT
        ];

        return collect($actions)->map(function ($action) use ($module) {
            return [
                'name' => ucfirst($action) . ' ' . ucfirst($module),
                'slug' => "{$module}.{$action}",
                'description' => "Can {$action} {$module}",
                'module' => $module,
                'action' => $action,
                'is_active' => true
            ];
        })->toArray();
    }
}

