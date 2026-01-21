<?php

namespace App\Services\RealEstate;

use App\Models\Building;
use App\Models\Unit;
use App\Models\Payment;
use App\Models\EwaBill;
use App\Models\Expense;
use App\Models\Lease;
use App\Models\LateFee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    /**
     * Calculate Profit & Loss for a building
     */
    public function calculateBuildingProfitLoss(int $buildingId, $startDate, $endDate): array
    {
        $revenue = $this->calculateTotalRevenue($buildingId, $startDate, $endDate);
        $expenses = $this->calculateTotalExpenses($buildingId, $startDate, $endDate);
        
        return [
            'building_id' => $buildingId,
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'revenue' => $revenue,
            'expenses' => $expenses,
            'net_profit' => $revenue - $expenses,
            'profit_margin' => $revenue > 0 ? (($revenue - $expenses) / $revenue) * 100 : 0
        ];
    }

    /**
     * Calculate total revenue for a building
     */
    public function calculateTotalRevenue(int $buildingId, $startDate, $endDate): float
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // Rent payments
        $rentPayments = Payment::whereHas('lease.unit.building', function ($query) use ($buildingId) {
            $query->where('id', $buildingId);
        })
        ->whereBetween('payment_date', [$start, $end])
        ->where('status', 'completed')
        ->sum('amount');

        return $rentPayments;
    }

    /**
     * Calculate total expenses for a building
     */
    public function calculateTotalExpenses(int $buildingId, $startDate, $endDate): float
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // EWA bills
        $ewaBills = EwaBill::where('building_id', $buildingId)
            ->whereBetween('bill_date', [$start, $end])
            ->sum('total_amount');

        // Other expenses
        $otherExpenses = Expense::where('building_id', $buildingId)
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        return $ewaBills + $otherExpenses;
    }

    /**
     * Calculate occupancy rate for a building
     */
    public function calculateOccupancyRate(int $buildingId): float
    {
        $totalUnits = Unit::where('building_id', $buildingId)->count();
        
        if ($totalUnits === 0) {
            return 0;
        }

        $occupiedUnits = Unit::where('building_id', $buildingId)
            ->where('status', 'rented')
            ->count();

        return ($occupiedUnits / $totalUnits) * 100;
    }

    /**
     * Calculate monthly revenue for a building
     */
    public function calculateMonthlyRevenue(int $buildingId, int $month, int $year): float
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        return Payment::whereHas('lease.unit.building', function ($query) use ($buildingId) {
            $query->where('id', $buildingId);
        })
        ->whereBetween('payment_date', [$start, $end])
        ->where('status', 'completed')
        ->sum('amount');
    }

    /**
     * Calculate net profit for a building
     */
    public function calculateNetProfit(int $buildingId, $startDate, $endDate): float
    {
        $revenue = $this->calculateTotalRevenue($buildingId, $startDate, $endDate);
        $expenses = $this->calculateTotalExpenses($buildingId, $startDate, $endDate);

        return $revenue - $expenses;
    }

    /**
     * Calculate cost per unit for a building
     */
    public function calculateCostPerUnit(int $buildingId, $startDate, $endDate): float
    {
        $totalUnits = Unit::where('building_id', $buildingId)->count();
        
        if ($totalUnits === 0) {
            return 0;
        }

        $totalExpenses = $this->calculateTotalExpenses($buildingId, $startDate, $endDate);

        return $totalExpenses / $totalUnits;
    }

    /**
     * Calculate EWA to Rent ratio for a building
     */
    public function calculateEwaToRentRatio(int $buildingId, $startDate, $endDate): float
    {
        $totalRent = $this->calculateTotalRevenue($buildingId, $startDate, $endDate);
        $totalEwa = EwaBill::where('building_id', $buildingId)
            ->whereBetween('bill_date', [Carbon::parse($startDate), Carbon::parse($endDate)])
            ->sum('total_amount');

        if ($totalRent === 0) {
            return 0;
        }

        return ($totalEwa / $totalRent) * 100;
    }

    /**
     * Process rent payment
     */
    public function processRentPayment(int $leaseId, float $amount, string $method): Payment
    {
        return DB::transaction(function () use ($leaseId, $amount, $method) {
            $lease = Lease::findOrFail($leaseId);
            
            $payment = Payment::create([
                'lease_id' => $leaseId,
                'payment_date' => now(),
                'amount' => $amount,
                'method' => $method,
                'status' => 'completed',
                'reference' => 'PAY-' . strtoupper(uniqid()),
                'notes' => 'Rent payment processed via ' . $method
            ]);

            // Update lease last payment date
            $lease->update(['last_payment_date' => now()]);

            return $payment;
        });
    }

    /**
     * Calculate late fee for a lease
     */
    public function calculateLateFee(int $leaseId): float
    {
        $lease = Lease::findOrFail($leaseId);
        
        // Check if rent is overdue
        $dueDate = Carbon::parse($lease->start_date)->addMonth();
        $gracePeriod = config('real-estate.late_fee_grace_period', 5);
        
        if (now()->lessThanOrEqualTo($dueDate->addDays($gracePeriod))) {
            return 0;
        }

        // Calculate late fee (default 5% of rent)
        $lateFeePercentage = config('real-estate.late_fee_percentage', 5);
        $lateFeeAmount = ($lease->rent_amount * $lateFeePercentage) / 100;

        // Check if late fee already applied for this period
        $existingLateFee = LateFee::where('lease_id', $leaseId)
            ->where('payment_id', null)
            ->whereDate('created_at', '>=', $dueDate->subDays($gracePeriod))
            ->first();

        if ($existingLateFee) {
            return $existingLateFee->amount;
        }

        return $lateFeeAmount;
    }

    /**
     * Apply late fees for all overdue leases
     */
    public function applyLateFees(): array
    {
        $applied = [];
        $gracePeriod = config('real-estate.late_fee_grace_period', 5);
        
        $overdueLeases = Lease::where('status', 'active')
            ->whereDate('start_date', '<', now()->subDays($gracePeriod)->subMonth())
            ->get();

        foreach ($overdueLeases as $lease) {
            $lateFeeAmount = $this->calculateLateFee($lease->id);
            
            if ($lateFeeAmount > 0) {
                LateFee::create([
                    'lease_id' => $lease->id,
                    'amount' => $lateFeeAmount,
                    'due_date' => now(),
                    'status' => 'pending'
                ]);
                
                $applied[] = $lease->id;
            }
        }

        return $applied;
    }

    /**
     * Get financial summary for dashboard
     */
    public function getDashboardSummary(): array
    {
        $buildings = Building::withCount(['units'])->get();
        
        $totalRevenue = Payment::where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');
        
        $totalExpenses = Expense::whereMonth('expense_date', now()->month)->sum('amount');
        
        $totalEwaBills = EwaBill::whereMonth('bill_date', now()->month)->sum('total_amount');
        
        $occupiedUnits = Unit::where('status', 'rented')->count();
        $totalUnits = Unit::count();
        $occupancyRate = $totalUnits > 0 ? ($occupiedUnits / $totalUnits) * 100 : 0;

        return [
            'total_buildings' => $buildings->count(),
            'total_units' => $totalUnits,
            'occupied_units' => $occupiedUnits,
            'occupancy_rate' => round($occupancyRate, 2),
            'monthly_revenue' => $totalRevenue,
            'monthly_expenses' => $totalExpenses,
            'monthly_ewa_bills' => $totalEwaBills,
            'net_income' => $totalRevenue - $totalExpenses - $totalEwaBills
        ];
    }

    /**
     * Get revenue breakdown by building
     */
    public function getRevenueByBuilding($startDate, $endDate): array
    {
        $buildings = Building::with(['units.leases.payments' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('payment_date', [$startDate, $endDate])
                  ->where('status', 'completed');
        }])->get();

        return $buildings->map(function ($building) {
            $revenue = $building->units->flatMap(function ($unit) {
                return $unit->leases->flatMap(function ($lease) {
                    return $lease->payments->pluck('amount');
                });
            })->sum();

            return [
                'id' => $building->id,
                'name' => $building->name,
                'revenue' => $revenue,
                'occupancy_rate' => $this->calculateOccupancyRate($building->id)
            ];
        })->toArray();
    }

    /**
     * Get expense breakdown by category
     */
    public function getExpensesByCategory($startDate, $endDate): array
    {
        return Expense::with('category')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get()
            ->groupBy('category.name')
            ->map(function ($items, $category) {
                return [
                    'category' => $category ?? 'Uncategorized',
                    'amount' => $items->sum('amount')
                ];
            })
            ->values()
            ->toArray();
    }
}

