<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuditLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any audit logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('audit-logs.view');
    }

    /**
     * Determine whether the user can view the audit log.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasPermission('audit-logs.view');
    }

    /**
     * Determine whether the user can export audit logs.
     */
    public function export(User $user): bool
    {
        return $user->hasPermission('audit-logs.export');
    }

    /**
     * Determine whether the user can cleanup audit logs.
     */
    public function cleanup(User $user): bool
    {
        return $user->hasPermission('audit-logs.cleanup');
    }

    /**
     * Determine whether the user can delete audit logs.
     */
    public function delete(User $user, AuditLog $auditLog): bool
    {
        return $user->hasPermission('audit-logs.cleanup');
    }
}

