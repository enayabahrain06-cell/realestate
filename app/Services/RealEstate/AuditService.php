<?php

namespace App\Services\RealEstate;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an activity
     */
    public function log(array $data): AuditLog
    {
        return AuditLog::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'action' => $data['action'] ?? 'unknown',
            'model_type' => $data['model_type'] ?? null,
            'model_id' => $data['model_id'] ?? null,
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'description' => $data['description'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'metadata' => $data['metadata'] ?? null,
            'created_at' => now(), // Manually set timestamp since model has $timestamps = false
        ]);
    }

    /**
     * Log model creation
     */
    public function logCreate($model, string $description = null): AuditLog
    {
        return $this->log([
            'action' => 'create',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'new_values' => $model->getAttributes(),
            'description' => $description ?? "Created {$this->getModelName(get_class($model))} #{$model->id}"
        ]);
    }

    /**
     * Log model update
     */
    public function logUpdate($model, array $oldValues, string $description = null): AuditLog
    {
        return $this->log([
            'action' => 'update',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $model->getChanges(),
            'description' => $description ?? "Updated {$this->getModelName(get_class($model))} #{$model->id}"
        ]);
    }

    /**
     * Log model deletion
     */
    public function logDelete($model, string $description = null): AuditLog
    {
        return $this->log([
            'action' => 'delete',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $model->getAttributes(),
            'description' => $description ?? "Deleted {$this->getModelName(get_class($model))} #{$model->id}"
        ]);
    }

    /**
     * Log custom action
     */
    public function logAction(string $action, $model = null, string $description = null): AuditLog
    {
        return $this->log([
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description ?? "Performed action: {$action}"
        ]);
    }

    /**
     * Get entity history
     */
    public function getEntityHistory(string $entityType, int $entityId)
    {
        return AuditLog::where('model_type', $entityType)
            ->where('model_id', $entityId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user activity
     */
    public function getUserActivity(int $userId, int $days = 30)
    {
        return AuditLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get module activity
     */
    public function getModuleActivity(string $module, int $days = 7)
    {
        return AuditLog::where('model_type', 'like', "%{$module}%")
            ->where('created_at', '>=', now()->subDays($days))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent activity (for dashboard)
     */
    public function getRecentActivity(int $limit = 20)
    {
        return AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity by action type
     */
    public function getActivityByAction(string $action, int $days = 7)
    {
        return AuditLog::where('action', $action)
            ->where('created_at', '>=', now()->subDays($days))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get activity summary for dashboard
     */
    public function getActivitySummary(): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        return [
            'today' => [
                'total' => AuditLog::where('created_at', '>=', $today)->count(),
                'creates' => AuditLog::where('created_at', '>=', $today)->where('action', 'create')->count(),
                'updates' => AuditLog::where('created_at', '>=', $today)->where('action', 'update')->count(),
                'deletes' => AuditLog::where('created_at', '>=', $today)->where('action', 'delete')->count(),
            ],
            'this_week' => [
                'total' => AuditLog::where('created_at', '>=', $thisWeek)->count(),
                'creates' => AuditLog::where('created_at', '>=', $thisWeek)->where('action', 'create')->count(),
                'updates' => AuditLog::where('created_at', '>=', $thisWeek)->where('action', 'update')->count(),
                'deletes' => AuditLog::where('created_at', '>=', $thisWeek)->where('action', 'delete')->count(),
            ],
            'this_month' => [
                'total' => AuditLog::where('created_at', '>=', $thisMonth)->count(),
                'creates' => AuditLog::where('created_at', '>=', $thisMonth)->where('action', 'create')->count(),
                'updates' => AuditLog::where('created_at', '>=', $thisMonth)->where('action', 'update')->count(),
                'deletes' => AuditLog::where('created_at', '>=', $thisMonth)->where('action', 'delete')->count(),
            ]
        ];
    }

    /**
     * Search audit logs
     */
    public function search(array $filters)
    {
        $query = AuditLog::query()->with('user');

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['model_type'])) {
            $query->where('model_type', 'like', "%{$filters['model_type']}%");
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%");
            });
        }

        $perPage = $filters['per_page'] ?? 20;

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Cleanup old logs
     */
    public function cleanupOldLogs(int $days = 365): int
    {
        return AuditLog::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Get most active users
     */
    public function getMostActiveUsers(int $limit = 10, int $days = 30)
    {
        return AuditLog::where('created_at', '>=', now()->subDays($days))
            ->groupBy('user_id')
            ->select('user_id', \DB::raw('count(*) as activity_count'))
            ->orderByDesc('activity_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $user = User::find($item->user_id);
                return [
                    'user' => $user,
                    'activity_count' => $item->activity_count
                ];
            });
    }

    /**
     * Get model name from class
     */
    protected function getModelName(string $className): string
    {
        $parts = explode('\\', $className);
        return end($parts);
    }

    /**
     * Create a diff between old and new values
     */
    public function createDiff(array $oldValues, array $newValues): array
    {
        $changes = [];
        
        foreach ($newValues as $key => $value) {
            if (!isset($oldValues[$key]) || $oldValues[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldValues[$key] ?? null,
                    'new' => $value
                ];
            }
        }

        return $changes;
    }

    /**
     * Log changes for model events
     */
    public function logModelEvent($model, string $event, array $changes = []): AuditLog
    {
        $actionMap = [
            'created' => 'create',
            'updated' => 'update',
            'deleted' => 'delete',
        ];

        $action = $actionMap[$event] ?? $event;

        return $this->log([
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $event === 'deleted' ? $model->getOriginal() : ($changes['old'] ?? null),
            'new_values' => $event === 'created' ? $model->getAttributes() : ($changes['new'] ?? null),
            'description' => ucfirst($event) . ' ' . $this->getModelName(get_class($model))
        ]);
    }

    /**
     * Get audit trail for compliance
     */
    public function getAuditTrail(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = AuditLog::query()->with('user');

        if (!empty($filters['entity_type'])) {
            $query->where('model_type', $filters['entity_type']);
        }

        if (!empty($filters['entity_id'])) {
            $query->where('model_id', $filters['entity_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Export audit logs
     */
    public function export(array $filters = []): \Illuminate\Support\Collection
    {
        $logs = $this->getAuditTrail($filters);

        return $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'date' => $log->created_at->format('Y-m-d H:i:s'),
                'user' => $log->user ? $log->user->name : 'System',
                'action' => $log->action,
                'entity_type' => $log->model_type,
                'entity_id' => $log->model_id,
                'description' => $log->description,
                'ip_address' => $log->ip_address
            ];
        });
    }
}

