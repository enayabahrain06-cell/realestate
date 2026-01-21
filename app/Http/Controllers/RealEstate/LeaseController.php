<?php

namespace App\Http\Controllers\RealEstate;

use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaseController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Lease::with(['tenant', 'unit.building']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->building_id) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('building_id', $request->building_id);
            });
        }

        if ($request->tenant_id) {
            $query->where('tenant_id', $request->tenant_id);
        }

        $leases = $query->paginate(15);
        $buildings = Building::where('status', 'active')->get();
        $tenants = Tenant::where('status', 'active')->get();

        return view('real-estate.leases.index', compact('leases', 'buildings', 'tenants'));
    }

    public function create(Request $request)
    {
        $unitId = $request->unit_id;
        $unit = null;
        
        if ($unitId) {
            $unit = Unit::with(['building', 'floor'])->findOrFail($unitId);
        }

        $tenants = Tenant::where('status', 'active')->get();
        $buildings = Building::where('status', 'active')->get();

        return view('real-estate.leases.create', compact('tenants', 'buildings', 'unit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'unit_id' => 'required|exists:units,id',
            'lease_type' => 'required|in:single_unit,full_floor,full_building',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:monthly,quarterly,annually',
            'late_payment_fee' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
            'status' => 'required|in:active,expired,terminated,pending'
        ]);

        // Check if unit is already rented with active lease
        $existingLease = Lease::where('unit_id', $validated['unit_id'])
            ->where('status', 'active')
            ->exists();

        if ($existingLease && $validated['status'] === 'active') {
            return back()->withErrors(['unit_id' => 'This unit already has an active lease.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $request) {
            $lease = Lease::create($validated);

            // If lease is active, update unit status
            if ($validated['status'] === 'active') {
                Unit::where('id', $validated['unit_id'])->update(['status' => Unit::STATUS_RENTED]);
            }
        });

        return redirect()->route('real-estate.leases.show', Lease::latest()->first())
            ->with('success', 'Lease created successfully!');
    }

    public function show(Lease $lease)
    {
        $lease->load(['tenant', 'unit.building', 'unit.floor', 'payments']);

        $stats = [
            'total_paid' => $lease->payments()->where('status', 'completed')->sum('amount'),
            'pending_payments' => $lease->payments()->where('status', 'pending')->count(),
            'days_remaining' => $lease->days_remaining,
            'is_expired' => $lease->is_expired,
        ];

        return view('real-estate.leases.show', compact('lease', 'stats'));
    }

    public function edit(Lease $lease)
    {
        $lease->load(['unit.building']);
        $tenants = Tenant::where('status', 'active')->get();

        return view('real-estate.leases.edit', compact('lease', 'tenants'));
    }

    public function update(Request $request, Lease $lease)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'unit_id' => 'required|exists:units,id',
            'lease_type' => 'required|in:single_unit,full_floor,full_building',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:monthly,quarterly,annually',
            'late_payment_fee' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
            'status' => 'required|in:active,expired,terminated,pending'
        ]);

        DB::transaction(function () use ($validated, $lease) {
            $lease->update($validated);

            // Update unit status based on lease status
            if ($validated['status'] === 'active') {
                Unit::where('id', $validated['unit_id'])->update(['status' => Unit::STATUS_RENTED]);
            } elseif ($validated['status'] === 'expired' || $validated['status'] === 'terminated') {
                Unit::where('id', $validated['unit_id'])->update(['status' => Unit::STATUS_AVAILABLE]);
            }
        });

        return redirect()->route('real-estate.leases.show', $lease)
            ->with('success', 'Lease updated successfully!');
    }

    public function destroy(Lease $lease)
    {
        $unitId = $lease->unit_id;
        $lease->delete();

        // Make unit available
        Unit::where('id', $unitId)->update(['status' => Unit::STATUS_AVAILABLE]);

        return redirect()->route('real-estate.leases.index')
            ->with('success', 'Lease deleted successfully!');
    }

    /**
     * Terminate a lease
     */
    public function terminate(Request $request, Lease $lease)
    {
        $request->validate([
            'cancellation_notes' => 'nullable|string'
        ]);

        $lease->update([
            'status' => Lease::STATUS_TERMINATED,
            'cancellation_notes' => $request->cancellation_notes,
            'terminated_at' => now()
        ]);

        // Make unit available
        $lease->unit->update(['status' => Unit::STATUS_AVAILABLE]);

        return redirect()->route('real-estate.leases.show', $lease)
            ->with('success', 'Lease terminated successfully!');
    }

    /**
     * Bulk rent entire floor or building
     */
    public function bulkRent(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'lease_type' => 'required|in:full_floor,full_building',
            'building_id' => 'required|exists:real_estate_buildings,id',
            'floor_id' => 'nullable|exists:real_estate_floors,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:monthly,quarterly,annually',
            'terms' => 'nullable|string',
        ]);

        // Get units to rent
        $unitQuery = Unit::where('building_id', $request->building_id);
        
        if ($request->floor_id) {
            $unitQuery->where('floor_id', $request->floor_id);
        }

        $units = $unitQuery->where('status', Unit::STATUS_AVAILABLE)->get();

        if ($units->isEmpty()) {
            return back()->withErrors(['error' => 'No available units found for the selected criteria.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $units) {
            foreach ($units as $unit) {
                Lease::create([
                    'tenant_id' => $request->tenant_id,
                    'unit_id' => $unit->id,
                    'lease_type' => $request->lease_type,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'rent_amount' => $request->rent_amount,
                    'deposit_amount' => $request->deposit_amount,
                    'payment_frequency' => $request->payment_frequency,
                    'terms' => $request->terms,
                    'status' => Lease::STATUS_ACTIVE,
                ]);

                $unit->update(['status' => Unit::STATUS_RENTED]);
            }
        });

        return redirect()->route('real-estate.leases.index')
            ->with('success', $units->count() . ' units rented successfully!');
    }
}

