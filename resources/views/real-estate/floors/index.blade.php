@extends('layouts.real-estate.dashboard')

@section('title', 'Floors')

@section('breadcrumb')
    <li class="breadcrumb-item active">Floors</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-layers text-primary me-2"></i>Floors</h1>
                <p class="text-muted small mb-0">Manage floors across all buildings</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.floors.create') }}" class="btn btn-primary">
                    <i class="bi bi-layers me-1"></i> Add Floor
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <select name="building_id" class="form-select select2">
                        <option value="">All Buildings</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                {{ $building->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($floors->count() > 0)
        <div class="row g-4">
            @foreach($floors as $floor)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">Floor {{ $floor->floor_number }}</h5>
                                <span class="badge bg-primary">{{ $floor->units->count() }} Units</span>
                            </div>
                            <small class="text-muted"><i class="bi bi-building me-1"></i>{{ $floor->building->name }}</small>
                        </div>
                        <div class="card-body">
                            @php
                                $availableUnits = $floor->units->where('status', 'available')->count();
                                $rentedUnits = $floor->units->where('status', 'rented')->count();
                                $totalUnits = $floor->units->count();
                                $occupancyRate = $totalUnits > 0 ? ($rentedUnits / $totalUnits) * 100 : 0;
                            @endphp
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $availableUnits }} Available</span>
                                <span class="text-primary"><i class="bi bi-key me-1"></i>{{ $rentedUnits }} Rented</span>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Occupancy</span>
                                    <span class="fw-semibold">{{ round($occupancyRate) }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ $occupancyRate }}%"></div>
                                </div>
                            </div>

                            @if($floor->description)
                                <p class="card-text small text-muted">{{ Str::limit($floor->description, 100) }}</p>
                            @endif
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2">
                                <a href="{{ route('real-estate.floors.show', $floor) }}" 
                                   class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="bi bi-eye me-1"></i> View Units
                                </a>
                                <a href="{{ route('real-estate.floors.edit', $floor) }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('real-estate.floors.destroy', $floor) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Delete this floor and all its units?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $floors->withQueryString()->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-layers fs-1"></i>
                </div>
                <h5>No Floors Found</h5>
                <p class="text-muted">Add floors to your buildings to organize units.</p>
                <a href="{{ route('real-estate.floors.create') }}" class="btn btn-primary">
                    <i class="bi bi-layers me-1"></i> Add Floor
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

