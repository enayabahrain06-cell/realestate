@extends('layouts.real-estate.dashboard')

@section('title', 'Bulk Add Floors - ' . $building->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item"><a href="{{ route('real-estate.buildings.show', $building) }}">{{ $building->name }}</a></li>
    <li class="breadcrumb-item active">Bulk Add Floors</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-layers-half text-primary me-2"></i>Bulk Add Floors</h1>
                <p class="text-muted small mb-0">Create multiple floors at once for {{ $building->name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.buildings.show', $building) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Building
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Bulk Floor Creation</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Building:</strong> {{ $building->name }}
                    </div>

                    <form action="{{ route('real-estate.floors.bulk-store', $building) }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start_floor" class="form-label fw-semibold">Start Floor Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="number" class="form-control @error('start_floor') is-invalid @enderror"
                                           id="start_floor" name="start_floor" value="{{ old('start_floor', 0) }}"
                                           min="0" required placeholder="e.g., 0">
                                </div>
                                <small class="text-muted">Starting floor number (e.g., 0 for ground floor)</small>
                                @error('start_floor')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_floor" class="form-label fw-semibold">End Floor Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="number" class="form-control @error('end_floor') is-invalid @enderror"
                                           id="end_floor" name="end_floor" value="{{ old('end_floor', 5) }}"
                                           min="0" required placeholder="e.g., 5">
                                </div>
                                <small class="text-muted">Ending floor number (inclusive)</small>
                                @error('end_floor')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="alert alert-light border">
                                    <i class="bi bi-calculator me-2"></i>
                                    <strong>Floors to be created:</strong>
                                    <span id="floor-count" class="text-primary fw-bold">0</span>
                                    <span id="floor-range" class="text-muted ms-2"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="units_per_floor" class="form-label fw-semibold">Units Per Floor *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-door-open"></i></span>
                                    <input type="number" class="form-control @error('units_per_floor') is-invalid @enderror"
                                           id="units_per_floor" name="units_per_floor" value="{{ old('units_per_floor', 4) }}"
                                           min="1" required placeholder="e.g., 4">
                                </div>
                                <small class="text-muted">Number of units on each floor</small>
                                @error('units_per_floor')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Total Units</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-grid-3x3"></i></span>
                                    <input type="text" class="form-control bg-light" id="total-units" readonly value="0">
                                </div>
                                <small class="text-muted">Total units across all floors</small>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description (Optional)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="3"
                                          placeholder="Description for all floors (optional)">{{ old('description') }}</textarea>
                                <small class="text-muted">This description will be applied to all created floors</small>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Floor Plan Layout (Optional)</label>
                                <p class="text-muted small mb-2">Select unit types available on these floors</p>
                                <div class="row g-2">
                                    @php $unitTypes = ['flat', 'office', 'commercial', 'warehouse', 'parking']; @endphp
                                    @foreach($unitTypes as $type)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="floor_plan[]" value="{{ $type }}"
                                                       id="floor_plan_{{ $type }}"
                                                       {{ in_array($type, old('floor_plan', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label text-capitalize" for="floor_plan_{{ $type }}">
                                                    {{ $type }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-check-circle me-1"></i> Create Floors
                            </button>
                            <a href="{{ route('real-estate.buildings.show', $building) }}" class="btn btn-outline-secondary">
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
                        <li class="mb-2">Use 0 for ground floor, 1 for first floor, etc.</li>
                        <li class="mb-2">All floors will be created with the same number of units.</li>
                        <li class="mb-2">You can edit individual floors after creation.</li>
                        <li class="mb-2">Existing floor numbers will be skipped automatically.</li>
                        <li class="mb-2">Add units to floors after creation.</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-building text-info me-2"></i>Building Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Name:</strong>
                        {{ $building->name }}
                    </div>
                    <div class="mb-2">
                        <strong>Current Floors:</strong>
                        {{ $building->floors->count() }}
                    </div>
                    <div class="mb-2">
                        <strong>Total Units:</strong>
                        {{ $building->units->count() }}
                    </div>
                    <div class="mb-0">
                        <strong>Property Type:</strong>
                        <span class="text-capitalize">{{ $building->property_type }}</span>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-list-check text-success me-2"></i>Example</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Scenario:</strong></p>
                    <ul class="mb-0 ps-3">
                        <li>Start Floor: <strong>0</strong></li>
                        <li>End Floor: <strong>5</strong></li>
                        <li>Units Per Floor: <strong>4</strong></li>
                    </ul>
                    <hr>
                    <p class="mb-2"><strong>Result:</strong></p>
                    <ul class="mb-0 ps-3">
                        <li>6 floors created (0, 1, 2, 3, 4, 5)</li>
                        <li>24 total unit slots (6 × 4)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function calculateFloors() {
        const startFloor = parseInt(document.getElementById('start_floor').value) || 0;
        const endFloor = parseInt(document.getElementById('end_floor').value) || 0;
        const unitsPerFloor = parseInt(document.getElementById('units_per_floor').value) || 0;

        if (endFloor >= startFloor) {
            const floorCount = endFloor - startFloor + 1;
            const totalUnits = floorCount * unitsPerFloor;

            document.getElementById('floor-count').textContent = floorCount;
            document.getElementById('floor-range').textContent = `(Floor ${startFloor} to ${endFloor})`;
            document.getElementById('total-units').value = totalUnits;
        } else {
            document.getElementById('floor-count').textContent = '0';
            document.getElementById('floor-range').textContent = '';
            document.getElementById('total-units').value = '0';
        }
    }

    // Calculate on page load
    document.addEventListener('DOMContentLoaded', calculateFloors);

    // Calculate on input change
    document.getElementById('start_floor').addEventListener('input', calculateFloors);
    document.getElementById('end_floor').addEventListener('input', calculateFloors);
    document.getElementById('units_per_floor').addEventListener('input', calculateFloors);
</script>
@endpush
@endsection
