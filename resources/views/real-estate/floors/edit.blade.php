@extends('layouts.real-estate.dashboard')

@section('title', 'Edit Floor ' . $floor->floor_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item"><a href="{{ route('real-estate.buildings.show', $floor->building) }}">{{ $floor->building->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('real-estate.floors.show', $floor) }}">Floor {{ $floor->floor_number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Floor {{ $floor->floor_number }}</h1>
                <p class="text-muted small mb-0">Update floor information</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.floors.show', $floor) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Floor
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Floor Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('real-estate.floors.update', $floor) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="building_id" class="form-label fw-semibold">Building *</label>
                                <select class="form-select @error('building_id') is-invalid @endeo"
                                        id="building_id" name="building_id" required>
                                    <option value="">Select Building</option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}"
                                                {{ $building->id == $floor->building_id ? 'selected' : '' }}>
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('building_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="floor_number" class="form-label fw-semibold">Floor Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="number" class="form-control @error('floor_number') is-invalid @endeo"
                                           id="floor_number" name="floor_number" value="{{ old('floor_number', $floor->floor_number) }}"
                                           min="0" required placeholder="e.g., 1">
                                </div>
                                @error('floor_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="total_units" class="form-label fw-semibold">Total Units *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-door-open"></i></span>
                                    <input type="number" class="form-control @error('total_units') is-invalid @endeo"
                                           id="total_units" name="total_units" value="{{ old('total_units', $floor->total_units) }}"
                                           min="1" required placeholder="e.g., 4">
                                </div>
                                @error('total_units')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @endeo"
                                          id="description" name="description" rows="3"
                                          placeholder="Describe this floor">{{ old('description', $floor->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Floor Plan Layout</label>
                                <p class="text-muted small mb-2">Define the layout configuration for this floor (optional)</p>
                                <div class="row g-2">
                                    @php $unitTypes = ['flat', 'office', 'commercial', 'warehouse', 'parking']; @endphp
                                    @php $currentFloorPlan = is_array($floor->floor_plan) ? $floor->floor_plan : []; @endphp
                                    @foreach($unitTypes as $type)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check">
                                                <input class="form-check-input floor-plan-check" type="checkbox"
                                                       name="floor_plan[]" value="{{ $type }}"
                                                       id="floor_plan_{{ $type }}"
                                                       {{ in_array($type, old('floor_plan', $currentFloorPlan)) ? 'checked' : '' }}>
                                                <label class="form-check-label text-capitalize" for="floor_plan_{{ $type }}">
                                                    {{ $type }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('floor_plan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-warning text-white me-2">
                                <i class="bi bi-check-circle me-1"></i> Update Floor
                            </button>
                            <a href="{{ route('real-estate.floors.show', $floor) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle text-info me-2"></i>Current Units</h5>
                </div>
                <div class="card-body">
                    @if($floor->units->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($floor->units as $unit)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>#{{ $unit->unit_number }}</span>
                                    <span class="badge bg-{{ $unit->status === 'available' ? 'success' : 'secondary' }}">
                                        {{ $unit->status }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No units on this floor yet.</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-info me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.floors.show', $floor) }}" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> View Floor
                        </a>
                        <a href="{{ route('real-estate.buildings.show', $floor->building) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-building me-1"></i> View Building
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

