<?php

namespace App\Http\Controllers\RealEstate;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Services\RealEstate\CommissionService;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
        $this->authorizeResource(Commission::class, 'commission');
    }

    /**
     * Display a listing of commissions.
     */
    public function index(Request $request)
    {
        $query = Commission::query();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by agent
        if ($request->has('agent_id') && $request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filter by period
        if ($request->has('period') && $request->period) {
            $startDate = $this->getPeriodStartDate($request->period);
            $query->where('created_at', '>=', $startDate);
        }

        $commissions = $query->with(['agent', 'lease.unit.building'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = $this->commissionService->getCommissionStats(
            $request->period ? $this->getPeriodStartDate($request->period) : now()->startOfMonth(),
            now()
        );

        return view('real-estate.commissions.index', compact('commissions', 'stats'));
    }

    /**
     * Display the specified commission.
     */
    public function show(Commission $commission)
    {
        $commission->load(['agent', 'lease.unit.building']);
        return view('real-estate.commissions.show', compact('commission'));
    }

    /**
     * Approve a commission.
     */
    public function approve(Request $request, Commission $commission)
    {
        $this->authorize('approve', Commission::class);

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $commission = $this->commissionService->approveCommission($commission->id);
        
        if ($validated['notes']) {
            $commission->update(['notes' => $commission->notes . "\n" . $validated['notes']]);
        }

        return back()->with('success', 'Commission approved successfully.');
    }

    /**
     * Pay a commission.
     */
    public function pay(Request $request, Commission $commission)
    {
        $this->authorize('pay', Commission::class);

        $validated = $request->validate([
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $commission = $this->commissionService->payCommission($commission->id, $validated['reference'] ?? null);
        
        if ($validated['notes']) {
            $commission->update(['notes' => $commission->notes . "\n" . $validated['notes']]);
        }

        return back()->with('success', 'Commission paid successfully.');
    }

    /**
     * Cancel a commission.
     */
    public function cancel(Request $request, Commission $commission)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $this->commissionService->cancelCommission($commission->id, $validated['reason']);

        return back()->with('success', 'Commission cancelled.');
    }

    /**
     * Get pending approvals (for dashboard widget).
     */
    public function pending()
    {
        $commissions = $this->commissionService->getPendingApprovals();
        return view('real-estate.commissions.pending', compact('commissions'));
    }

    /**
     * Export commissions to CSV.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Commission::class);

        $query = Commission::query();

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('agent_id') && $request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }

        $commissions = $query->with(['agent', 'lease.unit.building'])->get();

        $csv = \League\Csv\Writer::createFromString('');
        $csv->insertOne(['ID', 'Agent', 'Deal Amount', 'Commission', 'Status', 'Due Date', 'Created']);

        foreach ($commissions as $commission) {
            $csv->insertOne([
                $commission->id,
                $commission->agent->name,
                $commission->deal_amount,
                $commission->commission_amount,
                $commission->status,
                $commission->due_date,
                $commission->created_at,
            ]);
        }

        return response((string) $csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="commissions.csv"');
    }

    /**
     * Get period start date helper.
     */
    protected function getPeriodStartDate($period)
    {
        switch ($period) {
            case 'week':
                return now()->startOfWeek();
            case 'month':
                return now()->startOfMonth();
            case 'quarter':
                return now()->startOfQuarter();
            case 'year':
                return now()->startOfYear();
            default:
                return now()->startOfMonth();
        }
    }
}

