@extends('layouts.real-estate.dashboard')

@section('title', 'Buildings')

@section('breadcrumb')
    <li class="breadcrumb-item active">Buildings</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-building text-primary me-2"></i>Buildings</h1>
                <p class="text-muted small mb-0">Manage your properties and buildings</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.buildings.create') }}" class="btn btn-primary">
                    <i class="bi bi-building me-1"></i> Add Building
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search buildings..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="property_type" class="form-select select2">
                        <option value="">All Property Types</option>
                        <option value="residential" {{ request('property_type') === 'residential' ? 'selected' : '' }}>Residential</option>
                        <option value="commercial" {{ request('property_type') === 'commercial' ? 'selected' : '' }}>Commercial</option>
                        <option value="mixed-use" {{ request('property_type') === 'mixed-use' ? 'selected' : '' }}>Mixed-Use</option>
                        <option value="warehouse" {{ request('property_type') === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                        <option value="parking" {{ request('property_type') === 'parking' ? 'selected' : '' }}>Parking</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Buildings Grid -->
    @if($buildings->count() > 0)
        <div class="row g-4">
            @foreach($buildings as $building)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 building-card">
                        <div class="card-header bg-white p-0">
                            @if($building->image)
                                <img src="{{ Storage::url($building->image) }}" alt="{{ $building->name }}" 
                                     class="w-100" style="height: 160px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                                    <i class="bi bi-building fs-1 text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0 fw-bold">{{ $building->name }}</h5>
                                <span class="badge status-{{ $building->status }}">
                                    {{ ucfirst($building->status) }}
                                </span>
                            </div>
                            
                            <p class="card-text text-muted small mb-3">
                                <i class="bi bi-geo-alt me-1"></i> {{ $building->address }}
                            </p>

                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="fw-bold text-primary">{{ $building->total_floors }}</div>
                                    <small class="text-muted">Floors</small>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold text-success">{{ $building->total_units }}</div>
                                    <small class="text-muted">Units</small>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold text-info">{{ $building->available_units }}</div>
                                    <small class="text-muted">Available</small>
                                </div>
                            </div>

                            @php
                                $occupancyRate = $building->total_units > 0 
                                    ? ($building->rented_units / $building->total_units) * 100 
                                    : 0;
                            @endphp
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Occupancy</span>
                                    <span class="fw-semibold">{{ round($occupancyRate) }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $occupancyRate }}%"></div>
                                </div>
                            </div>
                            <small class="text-muted text-capitalize">
                                <i class="bi bi-building me-1"></i>{{ $building->property_type }}
                            </small>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2">
                                <a href="{{ route('real-estate.buildings.show', $building) }}" 
                                   class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                                <a href="{{ route('real-estate.buildings.edit', $building) }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('real-estate.buildings.destroy', $building) }}" method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this building?')">
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
            {{ $buildings->withQueryString()->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-building fs-1"></i>
                </div>
                <h5>No Buildings Found</h5>
                <p class="text-muted">Get started by adding your first building.</p>
                <a href="{{ route('real-estate.buildings.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add Building
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

