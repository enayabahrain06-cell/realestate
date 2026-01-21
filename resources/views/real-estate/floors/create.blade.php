@extends('layouts.real-estate.dashboard')

@section('title', 'Add New Floor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.floors.index') }}">Floors</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-layers-half text-primary me-2"></i>Add New Floor</h1>
                <p class="text-muted small mb-0">Create a new floor in a building</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.floors.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Floors
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
                    <form action="{{ route('real-estate.floors.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="building_id" class="form-label fw-semibold">Building *</label>
                                <select class="form-select @error('building_id') is-invalid @enderror" 
                                        id="building_id" name="building_id" required>
                                    <option value="">Select Building</option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}" 
                                                {{ old('building_id', $buildingId) == $building->id ? 'selected' : '' }}>
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
                                    <input type="number" class="form-control @error('floor_number') is-invalid @enderror" 
                                           id="floor_number" name="floor_number" value="{{ old('floor_number', 1) }}" 
                                           min="0" required placeholder="e.g., 1">
                                </div>
                                @error('floor_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Ground floor is typically 0 or 1</small>
                            </div>

                            <div class="col-md-6">
                                <label for="total_units" class="form-label fw-semibold">Total Units *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-door-open"></i></span>
                                    <input type="number" class="form-control @error('total_units') is-invalid @enderror" 
                                           id="total_units" name="total_units" value="{{ old('total_units', 1) }}" 
                                           min="1" required placeholder="e.g., 4">
                                </div>
                                @error('total_units')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Describe this floor, its purpose, features, etc.">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Floor Plan Layout</label>
                                <p class="text-muted small mb-2">Define the layout configuration for this floor (optional)</p>
                                <div class="row g-2">
                                    @php $unitTypes = ['flat', 'office', 'commercial', 'warehouse', 'parking']; @endphp
                                    @foreach($unitTypes as $type)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check">
                                                <input class="form-check-input floor-plan-check" type="checkbox" 
                                                       name="floor_plan[]" value="{{ $type }}"
                                                       id="floor_plan_{{ $type }}"
                                                       {{ is_array(old('floor_plan')) && in_array($type, old('floor_plan')) ? 'checked' : '' }}>
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
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-check-circle me-1"></i> Create Floor
                            </button>
                            <a href="{{ route('real-estate.floors.index') }}" class="btn btn-outline-secondary">
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
                    <h5 class="mb-0"><i class="bi bi-lightbulb text-warning me-2"></i>Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Select the correct building for this floor.</li>
                        <li class="mb-2">Floor numbers should be sequential within a building.</li>
                        <li class="mb-2">Set total units based on the floor's capacity.</li>
                        <li class="mb-2">You can add specific unit details after creating the floor.</li>
                        <li class="mb-2">Use the floor plan checkboxes to indicate unit types on this floor.</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-info me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.floors.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-1"></i> View All Floors
                        </a>
                        <a href="{{ route('real-estate.buildings.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-building me-1"></i> Manage Buildings
                        </a>
                        <a href="{{ route('real-estate.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-collection text-success me-2"></i">Floor Numbering Guide</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3 small">
                        <li class="mb-1"><strong>0</strong> - Ground Floor / Lobby</li>
                        <li class="mb-1"><strong>1+</strong> - Standard floors</li>
                        <li class="mb-1"><strong>-1, -2</strong> - Basement / Parking</li>
                        <li class="mb-0"><strong>100+</strong> - Penthouse / Rooftop</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

