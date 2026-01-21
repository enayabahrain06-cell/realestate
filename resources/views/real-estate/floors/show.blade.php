@extends('layouts.real-estate.dashboard')

@section('title', 'Floor ' . $floor->floor_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.floors.index') }}">Floors</a></li>
    <li class="breadcrumb-item active">Floor {{ $floor->floor_number }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-layers text-primary me-2"></i>Floor {{ $floor->floor_number }}
                </h1>
                <p class="text-muted small mb-0">
                    <i class="bi bi-building me-1"></i>{{ $floor->building->name }}
                </p>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('real-estate.floors.edit', $floor) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('real-estate.floors.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Units</h6>
                            <h3 class="mb-0">{{ $stats['total_units'] }}</h3>
                        </div>
                        <i class="bi bi-grid-3x3 fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Available</h6>
                            <h3 class="mb-0">{{ $stats['available'] }}</h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Rented</h6>
                            <h3 class="mb-0">{{ $stats['rented'] }}</h3>
                        </div>
                        <i class="bi bi-key fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Reserved</h6>
                            <h3 class="mb-0">{{ $stats['reserved'] }}</h3>
                        </div>
                        <i class="bi bi-bookmark fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Units Grid -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-grid-3x3 text-success me-2"></i>Units on this Floor</h5>
                    <a href="{{ route('real-estate.units.create', ['building_id' => $floor->building_id, 'floor_id' => $floor->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Unit
                    </a>
                </div>
                <div class="card-body">
                    @if($floor->units->count() > 0)
                        <div class="row g-3">
                            @foreach($floor->units as $unit)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="card-title mb-1">#{{ $unit->unit_number }}</h5>
                                            <p class="card-text small text-muted text-capitalize mb-2">{{ $unit->unit_type }}</p>
                                            <span class="badge status-{{ $unit->status }}">
                                                {{ ucfirst($unit->status) }}
                                            </span>
                                            <div class="mt-2">
                                                <small class="text-muted">{{ number_format($unit->size_sqft) }} sqft</small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-top-0">
                                            <a href="{{ route('real-estate.units.show', $unit) }}" class="btn btn-sm btn-outline-primary w-100">
                                                <i class="bi bi-eye me-1"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-grid-3x3 fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No units on this floor yet.</p>
                            <a href="{{ route('real-estate.units.create', ['building_id' => $floor->building_id, 'floor_id' => $floor->id]) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Add First Unit
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if($floor->description)
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-text-paragraph text-info me-2"></i>Description</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $floor->description }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Building Info -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-building text-primary me-2"></i>Building Info</h5>
                </div>
                <div class="card-body">
                    <h5>{{ $floor->building->name }}</h5>
                    <p class="text-muted small mb-3">{{ $floor->building->address }}</p>
                    
                    <div class="mb-2">
                        <strong>Property Type:</strong>
                        <span class="text-capitalize">{{ $floor->building->property_type }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Total Floors:</strong>
                        {{ $floor->building->total_floors }}
                    </div>
                    <div class="mb-0">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $floor->building->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($floor->building->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('real-estate.buildings.show', $floor->building) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-eye me-1"></i> View Building
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.floors.edit', $floor) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit Floor
                        </a>
                        <a href="{{ route('real-estate.units.create', ['building_id' => $floor->building_id, 'floor_id' => $floor->id]) }}" class="btn btn-outline-success">
                            <i class="bi bi-plus-circle me-1"></i> Add Unit
                        </a>
                        <a href="{{ route('real-estate.floors.index', ['building_id' => $floor->building_id]) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-layers me-1"></i> All Floors
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

