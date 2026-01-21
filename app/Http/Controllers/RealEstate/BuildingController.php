<?php

namespace App\Http\Controllers\RealEstate;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BuildingController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $buildings = Building::withCount(['units'])
            ->with(['units' => function($query) {
                $query->select('building_id', 'unit_type', 'status');
            }])
            ->with(['ewaBills', 'expenses'])
            ->paginate(10);

        return view('real-estate.buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('real-estate.buildings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'property_type' => 'required|in:residential,commercial,mixed-use,warehouse,parking',
            'total_floors' => 'required|integer|min:1',
            'units_per_floor' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'amenities' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'auto_create_floors' => 'nullable|boolean'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('buildings', 'public');
            $validated['image'] = $imagePath;
        }

        $building = Building::create($validated);

        // Auto-create floors if requested
        if ($request->has('auto_create_floors') && $request->auto_create_floors) {
            $totalFloors = $validated['total_floors'];
            $unitsPerFloor = $validated['units_per_floor'] ?? 4; // Default to 4 units per floor

            \Illuminate\Support\Facades\DB::transaction(function () use ($building, $totalFloors, $unitsPerFloor) {
                for ($i = 0; $i < $totalFloors; $i++) {
                    \App\Models\Floor::create([
                        'building_id' => $building->id,
                        'floor_number' => $i,
                        'total_units' => $unitsPerFloor,
                        'description' => "Floor {$i}",
                        'floor_plan' => []
                    ]);
                }
            });

            return redirect()->route('real-estate.buildings.show', $building)
                ->with('success', "Building created successfully with {$totalFloors} floors!");
        }

        return redirect()->route('real-estate.buildings.show', $building)
            ->with('success', 'Building created successfully!');
    }

    public function show(Building $building)
    {
        $building->load(['floors.units', 'units']);

        $stats = [
            'total_units' => $building->units->count(),
            'available' => $building->units->where('status', 'available')->count(),
            'rented' => $building->units->where('status', 'rented')->count(),
            'reserved' => $building->units->where('status', 'reserved')->count(),
            'maintenance' => $building->units->where('status', 'maintenance')->count(),
        ];

        return view('real-estate.buildings.show', compact('building', 'stats'));
    }

    public function edit(Building $building)
    {
        return view('real-estate.buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'property_type' => 'required|in:residential,commercial,mixed-use,warehouse,parking',
            'total_floors' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'amenities' => 'nullable|array',
            'status' => 'required|in:active,inactive'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($building->image && Storage::disk('public')->exists($building->image)) {
                Storage::disk('public')->delete($building->image);
            }

            $imagePath = $request->file('image')->store('buildings', 'public');
            $validated['image'] = $imagePath;
        }

        $building->update($validated);

        return redirect()->route('real-estate.buildings.show', $building)
            ->with('success', 'Building updated successfully!');
    }

    public function destroy(Building $building)
    {
        if ($building->units()->exists()) {
            return redirect()->route('real-estate.buildings.index')
                ->with('error', 'Cannot delete building with existing units. Delete units first.');
        }

        $building->delete();

        return redirect()->route('real-estate.buildings.index')
            ->with('success', 'Building deleted successfully!');
    }
}

