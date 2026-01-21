<?php

namespace App\Http\Controllers\RealEstate;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnitController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Unit::with(['building', 'floor']);

        if ($request->building_id) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->unit_type) {
            $query->where('unit_type', $request->unit_type);
        }

        if ($request->min_price) {
            $query->where('rent_amount', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('rent_amount', '<=', $request->max_price);
        }

        $units = $query->paginate(20);
        $buildings = Building::where('status', 'active')->get();

        return view('real-estate.units.index', compact('units', 'buildings'));
    }

    public function create(Request $request)
    {
        $buildingId = $request->building_id;
        $floorId = $request->floor_id;
        
        $buildings = Building::where('status', 'active')->get();
        $floors = collect();

        if ($buildingId) {
            $floors = Floor::where('building_id', $buildingId)->get();
        }

        return view('real-estate.units.create', compact('buildings', 'floors', 'buildingId', 'floorId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:real_estate_buildings,id',
            'floor_id' => 'required|exists:real_estate_floors,id',
            'unit_number' => 'required|string|max:50',
            'unit_type' => 'required|in:flat,office,commercial,warehouse,parking',
            'size_sqft' => 'required|numeric|min:1',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,reserved,rented,maintenance,blocked',
            'description' => 'nullable|string',
            'features' => 'nullable|array'
        ]);

        Unit::create($validated);

        return redirect()->route('real-estate.units.index')
            ->with('success', 'Unit created successfully!');
    }

    public function show(Unit $unit)
    {
        $unit->load(['building', 'floor', 'activeLease.tenant', 'bookings', 'histories.changedBy']);

        return view('real-estate.units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $buildings = Building::where('status', 'active')->get();
        $floors = Floor::where('building_id', $unit->building_id)->get();

        return view('real-estate.units.edit', compact('unit', 'buildings', 'floors'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:real_estate_buildings,id',
            'floor_id' => 'required|exists:real_estate_floors,id',
            'unit_number' => 'required|string|max:50',
            'unit_type' => 'required|in:flat,office,commercial,warehouse,parking',
            'size_sqft' => 'required|numeric|min:1',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,reserved,rented,maintenance,blocked',
            'description' => 'nullable|string',
            'features' => 'nullable|array'
        ]);

        $unit->update($validated);

        return redirect()->route('real-estate.units.show', $unit)
            ->with('success', 'Unit updated successfully!');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->leases()->where('status', 'active')->exists()) {
            return redirect()->route('real-estate.units.index')
                ->with('error', 'Cannot delete unit with active lease.');
        }

        $unit->delete();

        return redirect()->route('real-estate.units.index')
            ->with('success', 'Unit deleted successfully!');
    }

    /**
     * Visual unit selection view for a specific floor
     */
    public function floorPlan(Building $building, $floorNumber = null)
    {
        $building->load(['floors.units']);

        if (!$floorNumber) {
            $floorNumber = $building->floors->min('floor_number');
        }

        $floor = $building->floors->where('floor_number', $floorNumber)->first();

        if (!$floor) {
            return redirect()->route('real-estate.buildings.show', $building)
                ->with('error', 'Floor not found.');
        }

        $floor->load(['units']);

        // Group units by status for the visual display
        $unitsByStatus = [
            'available' => $floor->units->where('status', 'available'),
            'reserved' => $floor->units->where('status', 'reserved'),
            'rented' => $floor->units->where('status', 'rented'),
            'maintenance' => $floor->units->where('status', 'maintenance'),
        ];

        $stats = [
            'total' => $floor->units->count(),
            'available' => $unitsByStatus['available']->count(),
            'rented' => $unitsByStatus['rented']->count(),
            'reserved' => $unitsByStatus['reserved']->count(),
            'maintenance' => $unitsByStatus['maintenance']->count(),
        ];

        return view('real-estate.units.floor-plan', compact('building', 'floor', 'unitsByStatus', 'stats'));
    }

    /**
     * Lock a unit temporarily during selection process
     */
    public function lock(Request $request, Unit $unit)
    {
        $sessionId = session()->getId() ?? Str::random(40);

        if ($unit->lock($sessionId, 15)) {
            return response()->json([
                'success' => true,
                'message' => 'Unit locked for 15 minutes.',
                'locked_until' => $unit->locked_until->toIso8601String()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unit is not available for locking.'
        ], 422);
    }

    /**
     * Unlock a unit
     */
    public function unlock(Request $request, Unit $unit)
    {
        if ($unit->unlock()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit unlocked.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to unlock this unit.'
        ], 422);
    }

    /**
     * Bulk create units for a floor
     */
    public function bulkCreate(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:real_estate_buildings,id',
            'floor_id' => 'required|exists:real_estate_floors,id',
            'unit_type' => 'required|in:flat,office,commercial,warehouse,parking',
            'start_number' => 'required|integer',
            'end_number' => 'required|integer|gte:start_number',
            'rent_amount' => 'required|numeric|min:0',
            'size_sqft' => 'required|numeric|min:1',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            for ($i = $request->start_number; $i <= $request->end_number; $i++) {
                Unit::create([
                    'building_id' => $request->building_id,
                    'floor_id' => $request->floor_id,
                    'unit_number' => (string) $i,
                    'unit_type' => $request->unit_type,
                    'size_sqft' => $request->size_sqft,
                    'bedrooms' => $request->bedrooms,
                    'bathrooms' => $request->bathrooms,
                    'rent_amount' => $request->rent_amount,
                    'status' => Unit::STATUS_AVAILABLE,
                ]);
            }
        });

        return redirect()->route('real-estate.buildings.show', $request->building_id)
            ->with('success', 'Units created successfully!');
    }

    /**
     * Bulk update status for multiple units
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'unit_ids' => 'required|array|min:1',
            'unit_ids.*' => 'exists:real_estate_units,id',
            'status' => 'required|in:available,reserved,rented,maintenance,blocked',
            'reason' => 'nullable|string|max:255'
        ]);

        $units = Unit::whereIn('id', $request->unit_ids)->get();
        $count = 0;

        foreach ($units as $unit) {
            $previousStatus = $unit->status;
            $unit->update(['status' => $request->status]);
            $count++;
        }

        return redirect()->route('real-estate.units.index')
            ->with('success', "Status updated for {$count} units.");
    }

    /**
     * Bulk increase rent for multiple units
     */
    public function bulkRentIncrease(Request $request)
    {
        $request->validate([
            'unit_ids' => 'required|array|min:1',
            'unit_ids.*' => 'exists:real_estate_units,id',
            'increase_type' => 'required|in:percentage,fixed',
            'increase_value' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255'
        ]);

        $units = Unit::whereIn('id', $request->unit_ids)->get();
        $count = 0;

        foreach ($units as $unit) {
            $previousRent = $unit->rent_amount;

            if ($request->increase_type === 'percentage') {
                $newRent = $unit->rent_amount * (1 + ($request->increase_value / 100));
            } else {
                $newRent = $unit->rent_amount + $request->increase_value;
            }

            $unit->update(['rent_amount' => round($newRent, 2)]);
            $count++;
        }

        return redirect()->route('real-estate.units.index')
            ->with('success', "Rent increased for {$count} units.");
    }
}

