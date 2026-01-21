<?php

namespace App\Http\Controllers\RealEstate;

use App\Models\Building;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FloorController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Floor::with(['building', 'units']);

        if ($request->building_id) {
            $query->where('building_id', $request->building_id);
        }

        $floors = $query->paginate(20);
        $buildings = Building::where('status', 'active')->get();

        return view('real-estate.floors.index', compact('floors', 'buildings'));
    }

    public function create(Request $request)
    {
        $buildingId = $request->building_id;
        $buildings = Building::where('status', 'active')->get();

        return view('real-estate.floors.create', compact('buildings', 'buildingId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:real_estate_buildings,id',
            'floor_number' => 'required|integer|min:0',
            'total_units' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'floor_plan' => 'nullable|array'
        ]);

        // Check for duplicate floor number in the same building
        $exists = Floor::where('building_id', $validated['building_id'])
            ->where('floor_number', $validated['floor_number'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['floor_number' => 'This floor number already exists for this building.'])
                ->withInput();
        }

        $floor = Floor::create($validated);

        return redirect()->route('real-estate.buildings.show', $floor->building)
            ->with('success', 'Floor created successfully! You can now add units to this floor.');
    }

    public function show(Floor $floor)
    {
        $floor->load(['building', 'units']);

        $stats = [
            'total_units' => $floor->units->count(),
            'available' => $floor->units->where('status', 'available')->count(),
            'rented' => $floor->units->where('status', 'rented')->count(),
            'reserved' => $floor->units->where('status', 'reserved')->count(),
            'maintenance' => $floor->units->where('status', 'maintenance')->count(),
        ];

        return view('real-estate.floors.show', compact('floor', 'stats'));
    }

    public function edit(Floor $floor)
    {
        $buildings = Building::where('status', 'active')->get();

        return view('real-estate.floors.edit', compact('floor', 'buildings'));
    }

    public function update(Request $request, Floor $floor)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:real_estate_buildings,id',
            'floor_number' => 'required|integer|min:0',
            'total_units' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'floor_plan' => 'nullable|array'
        ]);

        // Check for duplicate floor number in the same building (excluding current floor)
        $exists = Floor::where('building_id', $validated['building_id'])
            ->where('floor_number', $validated['floor_number'])
            ->where('id', '!=', $floor->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['floor_number' => 'This floor number already exists for this building.'])
                ->withInput();
        }

        $floor->update($validated);

        return redirect()->route('real-estate.floors.show', $floor)
            ->with('success', 'Floor updated successfully!');
    }

    public function destroy(Floor $floor)
    {
        if ($floor->units()->exists()) {
            return redirect()->route('real-estate.floors.index')
                ->with('error', 'Cannot delete floor with existing units. Delete units first.');
        }

        $buildingId = $floor->building_id;
        $floor->delete();

        return redirect()->route('real-estate.buildings.show', $buildingId)
            ->with('success', 'Floor deleted successfully!');
    }

    /**
     * Bulk create units for a floor
     */
    public function bulkCreateUnits(Request $request, Floor $floor)
    {
        $request->validate([
            'start_number' => 'required|integer',
            'end_number' => 'required|integer|gte:start_number',
            'unit_type' => 'required|in:flat,office,commercial,warehouse,parking',
            'rent_amount' => 'required|numeric|min:0',
            'size_sqft' => 'required|numeric|min:1',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $floor) {
            for ($i = $request->start_number; $i <= $request->end_number; $i++) {
                $floor->units()->create([
                    'unit_number' => (string) $i,
                    'unit_type' => $request->unit_type,
                    'size_sqft' => $request->size_sqft,
                    'bedrooms' => $request->bedrooms,
                    'bathrooms' => $request->bathrooms,
                    'rent_amount' => $request->rent_amount,
                    'status' => \App\Models\Unit::STATUS_AVAILABLE,
                ]);
            }
        });

        return redirect()->route('real-estate.floors.show', $floor)
            ->with('success', 'Units created successfully!');
    }
}

