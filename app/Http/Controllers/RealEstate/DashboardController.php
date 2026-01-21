<?php

namespace App\Http\Controllers\RealEstate;

use App\Models\Building;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $stats = [
            'total_buildings' => Building::count(),
            'total_units' => Unit::count(),
            'available_units' => Unit::where('status', Unit::STATUS_AVAILABLE)->count(),
            'rented_units' => Unit::where('status', Unit::STATUS_RENTED)->count(),
            'reserved_units' => Unit::where('status', Unit::STATUS_RESERVED)->count(),
            'maintenance_units' => Unit::where('status', Unit::STATUS_MAINTENANCE)->count(),
            'total_tenants' => Tenant::where('status', 'active')->count(),
            'active_leases' => Lease::where('status', Lease::STATUS_ACTIVE)->count(),
            'pending_bookings' => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'completed_payments' => Payment::where('status', Payment::STATUS_COMPLETED)->sum('amount'),
            'pending_payments' => Payment::where('status', Payment::STATUS_PENDING)->sum('amount'),
        ];

        // Calculate occupancy rate
        $stats['occupancy_rate'] = $stats['total_units'] > 0 
            ? round(($stats['rented_units'] / $stats['total_units']) * 100, 2) 
            : 0;

        // Monthly revenue for current year
        $monthlyRevenue = Payment::where('status', Payment::STATUS_COMPLETED)
            ->whereYear('paid_at', Carbon::now()->year)
            ->selectRaw('CAST(strftime("%m", paid_at) AS INTEGER) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill in missing months with 0
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueData[$i] = $monthlyRevenue[$i] ?? 0;
        }

        // Recent activities
        $recentLeases = Lease::with(['tenant', 'unit.building'])
            ->latest()
            ->take(5)
            ->get();

        $recentBookings = Booking::with(['tenant', 'unit.building'])
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['tenant', 'lease.unit.building'])
            ->where('status', Payment::STATUS_COMPLETED)
            ->latest()
            ->take(5)
            ->get();

        // Buildings with lowest occupancy
        $buildingsWithOccupancy = Building::withCount(['units', 'units as rented_count' => function ($query) {
                $query->where('status', Unit::STATUS_RENTED);
            }])
            ->get()
            ->map(function ($building) {
                $building->occupancy_rate = $building->units_count > 0 
                    ? round(($building->rented_count / $building->units_count) * 100, 2) 
                    : 0;
                return $building;
            })
            ->sortBy('occupancy_rate')
            ->take(5);

        return view('real-estate.dashboard', compact(
            'stats',
            'revenueData',
            'recentLeases',
            'recentBookings',
            'recentPayments',
            'buildingsWithOccupancy'
        ));
    }

    public function availableUnits()
    {
        $units = Unit::with(['building', 'floor'])
            ->where('status', Unit::STATUS_AVAILABLE)
            ->orderBy('rent_amount', 'asc')
            ->paginate(20);

        return view('real-estate.units.available', compact('units'));
    }
}

