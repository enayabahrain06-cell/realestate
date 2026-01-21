<?php

namespace App\Http\Controllers\RealEstate;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\RealEstate\AuditService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
        // Authorization is handled by the 'permission:audit-logs.view' middleware in routes
        // No additional authorize() call needed here
    }

    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        
        $logs = $this->auditService->search($filters);

        $activitySummary = $this->auditService->getActivitySummary();
        $mostActiveUsers = $this->auditService->getMostActiveUsers(10, 7);

        return view('real-estate.audit-logs.index', compact(
            'logs', 'filters', 'activitySummary', 'mostActiveUsers'
        ));
    }

    /**
     * Display the specified audit log.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('real-estate.audit-logs.show', compact('auditLog'));
    }

    /**
     * Get entity history.
     */
    public function entityHistory(Request $request)
    {
        $entityType = $request->get('entity_type');
        $entityId = $request->get('entity_id');

        if (!$entityType || !$entityId) {
            return response()->json(['error' => 'entity_type and entity_id are required'], 400);
        }

        $logs = $this->auditService->getEntityHistory($entityType, $entityId);
        return response()->json($logs);
    }

    /**
     * Get user activity.
     */
    public function userActivity(Request $request, $userId)
    {
        $days = $request->get('days', 30);
        $activity = $this->auditService->getUserActivity($userId, $days);
        
        $user = User::find($userId);
        
        return view('real-estate.audit-logs.user-activity', compact('activity', 'user', 'days'));
    }

    /**
     * Get recent activity for dashboard.
     */
    public function recent()
    {
        $limit = request('limit', 20);
        $activity = $this->auditService->getRecentActivity($limit);
        return response()->json($activity);
    }

    /**
     * Export audit logs.
     */
    public function export(Request $request)
    {
        $filters = $request->all();
        $logs = $this->auditService->getAuditTrail($filters);

        $csv = \League\Csv\Writer::createFromString('');
        $csv->insertOne(['ID', 'Date', 'User', 'Action', 'Entity Type', 'Entity ID', 'Description', 'IP Address']);

        foreach ($logs as $log) {
            $csv->insertOne([
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user ? $log->user->name : 'System',
                $log->action,
                class_basename($log->model_type),
                $log->model_id,
                $log->description,
                $log->ip_address
            ]);
        }

        return response((string) $csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="audit-logs.csv"');
    }

    /**
     * Cleanup old logs.
     */
    public function cleanup(Request $request)
    {
        $this->authorize('delete', AuditLog::class);
        
        $days = $request->get('days', 365);
        $count = $this->auditService->cleanupOldLogs($days);

        return back()->with('success', "Cleaned up {$count} old log entries.");
    }
}

