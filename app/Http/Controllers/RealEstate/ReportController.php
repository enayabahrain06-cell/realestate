<?php

namespace App\Http\Controllers\RealEstate;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\EwaBill;
use App\Models\Commission;
use App\Services\RealEstate\FinancialService;
use App\Services\RealEstate\CommissionService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $financialService;
    protected $commissionService;

    public function __construct(
        FinancialService $financialService,
        CommissionService $commissionService
    ) {
        $this->financialService = $financialService;
        $this->commissionService = $commissionService;
        $this->authorize('viewFinancial', \App\Models\Report::class);
    }

    /**
     * Display reports dashboard.
     */
    public function index()
    {
        return view('real-estate.reports.index');
    }

    /**
     * Financial report - Revenue & Expenses.
     */
    public function financial(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $buildingId = $request->get('building_id');

        $buildings = Building::all();
        
        if ($buildingId) {
            $building = Building::findOrFail($buildingId);
            $financialSummary = $this->financialService->calculateBuildingProfitLoss(
                $buildingId, $startDate, $endDate
            );
            $occupancyRate = $this->financialService->calculateOccupancyRate($buildingId);
            $costPerUnit = $this->financialService->calculateCostPerUnit($buildingId, $startDate, $endDate);
        } else {
            $financialSummary = null;
            $occupancyRate = null;
            $costPerUnit = null;
        }

        // Monthly revenue data
        $monthlyRevenue = $this->getMonthlyRevenue($startDate, $endDate, $buildingId);
        
        // Expense breakdown
        $expenseBreakdown = $this->financialService->getExpensesByCategory($startDate, $endDate);

        // Revenue by building
        $revenueByBuilding = $this->financialService->getRevenueByBuilding($startDate, $endDate);

        return view('real-estate.reports.financial', compact(
            'buildings', 'buildingId', 'startDate', 'endDate',
            'financialSummary', 'occupancyRate', 'costPerUnit',
            'monthlyRevenue', 'expenseBreakdown', 'revenueByBuilding'
        ));
    }

    /**
     * Agent performance report.
     */
    public function agentPerformance(Request $request)
    {
        $period = $request->get('period', 'month');
        $topPerformers = $this->commissionService->getTopPerformers(10, $period);

        // Monthly commission data
        $monthlyData = $this->getMonthlyCommissionData($period);

        return view('real-estate.reports.agent-performance', compact(
            'topPerformers', 'period', 'monthlyData'
        ));
    }

    /**
     * Occupancy report.
     */
    public function occupancy(Request $request)
    {
        $buildings = Building::with(['units'])->get();

        $occupancyData = $buildings->map(function ($building) {
            $totalUnits = $building->units->count();
            $occupiedUnits = $building->units->where('status', 'rented')->count();
            
            return [
                'building' => $building,
                'total_units' => $totalUnits,
                'occupied_units' => $occupiedUnits,
                'vacant_units' => $totalUnits - $occupiedUnits,
                'occupancy_rate' => $totalUnits > 0 ? ($occupiedUnits / $totalUnits) * 100 : 0,
                'available_units' => $building->units->where('status', 'available')->count(),
                'maintenance_units' => $building->units->where('status', 'maintenance')->count(),
            ];
        });

        $overallOccupancy = $occupancyData->avg('occupancy_rate');

        return view('real-estate.reports.occupancy', compact('occupancyData', 'overallOccupancy'));
    }

    /**
     * Expense report.
     */
    public function expenses(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $buildingId = $request->get('building_id');
        $categoryId = $request->get('category_id');

        $query = Expense::with(['category', 'building'])
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($buildingId) {
            $query->where('building_id', $buildingId);
        }

        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $totalExpenses = $expenses->sum('amount');
        $byCategory = $expenses->groupBy('category.name')->map(fn($g) => $g->sum('amount'));
        $byBuilding = $expenses->groupBy('building.name')->map(fn($g) => $g->sum('amount'));

        return view('real-estate.reports.expenses', compact(
            'expenses', 'totalExpenses', 'byCategory', 'byBuilding',
            'startDate', 'endDate', 'buildingId', 'categoryId'
        ));
    }

    /**
     * EWA report.
     */
    public function ewa(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $buildingId = $request->get('building_id');

        $query = EwaBill::with(['building', 'unit'])
            ->whereBetween('bill_date', [$startDate, $endDate]);

        if ($buildingId) {
            $query->where('building_id', $buildingId);
        }

        $bills = $query->orderBy('bill_date', 'desc')->get();

        $totalBills = $bills->sum('total_amount');
        $byBuilding = $bills->groupBy('building.name')->map(fn($g) => $g->sum('total_amount'));

        return view('real-estate.reports.ewa', compact(
            'bills', 'totalBills', 'byBuilding',
            'startDate', 'endDate', 'buildingId'
        ));
    }

    /**
     * Commission report.
     */
    public function commissions(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $agentId = $request->get('agent_id');
        $status = $request->get('status');

        $query = Commission::with(['agent', 'lease.unit.building'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $commissions = $query->orderBy('created_at', 'desc')->get();

        $totalCommission = $commissions->sum('commission_amount');
        $byAgent = $commissions->groupBy('agent.name')->map(fn($g) => $g->sum('commission_amount'));

        return view('real-estate.reports.commissions', compact(
            'commissions', 'totalCommission', 'byAgent',
            'startDate', 'endDate', 'agentId', 'status'
        ));
    }

    /**
     * Export financial report.
     */
    public function exportFinancial(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $data = $this->financialService->getRevenueByBuilding($startDate, $endDate);

        $csv = \League\Csv\Writer::createFromString('');
        $csv->insertOne(['Building', 'Revenue', 'Occupancy Rate']);

        foreach ($data as $row) {
            $csv->insertOne([
                $row['name'],
                number_format($row['revenue'], 2),
                number_format($row['occupancy_rate'], 2) . '%'
            ]);
        }

        return response((string) $csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="financial-report.csv"');
    }

    /**
     * Get monthly revenue data for charts.
     */
    protected function getMonthlyRevenue($startDate, $endDate, $buildingId = null)
    {
        $query = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate]);

        if ($buildingId) {
            $query->whereHas('lease.unit.building', function ($q) use ($buildingId) {
                $q->where('id', $buildingId);
            });
        }

        $payments = $query->get()->groupBy(fn($p) => $p->payment_date->format('Y-m'));

        $data = [];
        foreach ($payments as $month => $monthlyPayments) {
            $data[$month] = $monthlyPayments->sum('amount');
        }

        return $data;
    }

    /**
     * Get monthly commission data.
     */
    protected function getMonthlyCommissionData($period)
    {
        $startDate = now()->subMonths(12)->startOfMonth();
        
        $commissions = Commission::where('created_at', '>=', $startDate)
            ->where('status', 'paid')
            ->get()
            ->groupBy(fn($c) => $c->created_at->format('Y-m'));

        $data = [];
        for ($i = 0; $i < 12; $i++) {
            $month = now()->subMonths(11 - $i)->format('Y-m');
            $data[$month] = $commissions->get($month, collect())->sum('commission_amount');
        }

        return $data;
    }
}

