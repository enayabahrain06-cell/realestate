<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_roles';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'is_system'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean'
    ];

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_PROPERTY_MANAGER = 'property_manager';
    public const ROLE_ACCOUNTANT = 'accountant';
    public const ROLE_AGENT = 'agent';
    public const ROLE_VIEWER = 'viewer';

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'real_estate_permission_role', 'role_id', 'permission_id')
                    ->withPivot(['is_custom'])
                    ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'real_estate_role_user', 'role_id', 'user_id')
                    ->withTimestamps();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions->contains('slug', $permissionSlug);
    }

    public function givePermissionTo($permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }
        $this->permissions()->syncWithoutDetaching($permission);
    }

    public function revokePermissionFrom($permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }
        $this->permissions()->detach($permission);
    }

    public function syncPermissions(array $permissions): void
    {
        $permissionIds = collect($permissions)->map(function ($permission) {
            return is_string($permission) ? Permission::where('slug', $permission)->firstOrFail()->id : $permission;
        });
        $this->permissions()->sync($permissionIds);
    }

    public function scopeSystemRoles($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustomRoles($query)
    {
        return $query->where('is_system', false);
    }

    public function isSuperAdmin(): bool
    {
        return $this->slug === self::ROLE_SUPER_ADMIN;
    }
}

