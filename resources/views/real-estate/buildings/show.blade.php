@extends('layouts.real-estate.dashboard')

@section('title', $building->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item active">{{ $building->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">
                    @if($building->image)
                        <img src="{{ Storage::url($building->image) }}" alt="{{ $building->name }}"
                             class="rounded me-2" style="height: 40px; width: 40px; object-fit: contain; background-color: #f8f9fa; padding: 2px;">
                    @else
                        <i class="bi bi-building text-primary me-2"></i>
                    @endif
                    {{ $building->name }}
                </h1>
                <p class="text-muted small mb-0">
                    <i class="bi bi-geo-alt me-1"></i> {{ $building->address }}
                </p>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('real-estate.buildings.edit', $building) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('real-estate.buildings.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Property Type</p>
                        <h5 class="mb-0 fw-bold text-capitalize">{{ $building->property_type }}</h5>
                    </div>
                    <div class="stat-icon stat-icon-primary">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Floors</p>
                            <h5 class="mb-0 fw-bold">{{ $building->floors->count() }}</h5>
                        </div>
                        <div class="stat-icon stat-icon-info">
                            <i class="bi bi-layers"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Units</p>
                            <h5 class="mb-0 fw-bold">{{ $stats['total_units'] }}</h5>
                        </div>
                        <div class="stat-icon stat-icon-success">
                            <i class="bi bi-grid-3x3"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Occupancy Rate</p>
                            @php
                                $occupancyRate = $stats['total_units'] > 0
                                    ? round(($stats['rented'] / $stats['total_units']) * 100, 2)
                                    : 0;
                            @endphp
                            <h5 class="mb-0 fw-bold">{{ $occupancyRate }}%</h5>
                        </div>
                        <div class="stat-icon stat-icon-warning">
                            <i class="bi bi-pie-chart"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Unit Status Overview -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable text-center">
                    <i class="bi bi-check-circle text-success fs-2"></i>
                    <h4 class="mt-2 mb-0 fw-bold">{{ $stats['available'] }}</h4>
                    <small class="text-muted">Available Units</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable text-center">
                    <i class="bi bi-key text-primary fs-2"></i>
                    <h4 class="mt-2 mb-0 fw-bold">{{ $stats['rented'] }}</h4>
                    <small class="text-muted">Rented Units</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable text-center">
                    <i class="bi bi-clock-history text-warning fs-2"></i>
                    <h4 class="mt-2 mb-0 fw-bold">{{ $stats['reserved'] }}</h4>
                    <small class="text-muted">Reserved Units</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable text-center">
                    <i class="bi bi-tools text-danger fs-2"></i>
                    <h4 class="mt-2 mb-0 fw-bold">{{ $stats['maintenance'] }}</h4>
                    <small class="text-muted">Maintenance</small>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Floors and Units -->
        <div class="col-lg-8">
            <div class="card" id="floors-section">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-layers text-info me-2"></i>Floors & Units</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('real-estate.floors.bulk-create', $building) }}"
                           class="btn btn-sm btn-success">
                            <i class="bi bi-stack me-1"></i> Bulk Add Floors
                        </a>
                        <a href="{{ route('real-estate.floors.create', ['building_id' => $building->id]) }}"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-plus me-1"></i> Add Single Floor
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($building->floors->sortBy('floor_number') as $floor)
                        <div class="floor-section mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold">
                                    Floor {{ $floor->floor_number }}
                                    <span class="badge bg-secondary ms-2">{{ $floor->units->count() }} Units</span>
                                </h6>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('real-estate.floors.show', $floor) }}"
                                       class="btn btn-outline-primary">View Units</a>
                                    <a href="{{ route('real-estate.floors.edit', $floor) }}"
                                       class="btn btn-outline-secondary">Edit</a>
                                </div>
                            </div>

                            <div class="row g-2">
                                @forelse($floor->units->sortBy('unit_number') as $unit)
                                    <div class="col-6 col-sm-4 col-md-3">
                                        <a href="{{ route('real-estate.units.show', $unit) }}"
                                           class="unit-tile text-decoration-none p-2 border rounded d-block text-center
                                                  {{ $unit->status === 'available' ? 'bg-success bg-opacity-10 border-success' :
                                                     ($unit->status === 'rented' ? 'bg-primary bg-opacity-10 border-primary' :
                                                     ($unit->status === 'reserved' ? 'bg-warning bg-opacity-10 border-warning' :
                                                     'bg-danger bg-opacity-10 border-danger')) }}">
                                            <div class="fw-bold">{{ $unit->unit_number }}</div>
                                            <small class="text-capitalize {{ $unit->status === 'available' ? 'text-success' :
                                                                             ($unit->status === 'rented' ? 'text-primary' :
                                                                             ($unit->status === 'reserved' ? 'text-warning' :
                                                                             'text-danger')) }}">
                                                {{ $unit->status }}
                                            </small>
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-3">
                                            <p class="text-muted mb-2">No units on this floor yet.</p>
                                            <a href="{{ route('real-estate.units.create', ['building_id' => $building->id, 'floor_id' => $floor->id]) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-plus me-1"></i> Add Units
                                            </a>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-4">
                        @endif
                    @empty
                        <div class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="bi bi-layers fs-1"></i>
                            </div>
                            <h5>No Floors Added</h5>
                            <p class="text-muted">Start by adding floors to this building.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('real-estate.floors.bulk-create', $building) }}"
                                   class="btn btn-success">
                                    <i class="bi bi-stack me-1"></i> Bulk Add Floors
                                </a>
                                <a href="{{ route('real-estate.floors.create', ['building_id' => $building->id]) }}"
                                   class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Add Single Floor
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Building Details Sidebar -->
        <div class="col-lg-4">
            @if($building->description)
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-text-paragraph text-primary me-2"></i>Description</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $building->description }}</p>
                </div>
            </div>
            @endif

            @if($building->amenities && count($building->amenities) > 0)
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-star text-warning me-2"></i>Amenities</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($building->amenities as $amenity)
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-check text-success me-1"></i> {{ ucfirst($amenity) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-toggle-on text-success me-2"></i>Status</h5>
                </div>
                <div class="card-body">
                    <span class="badge status-{{ $building->status }} fs-6">
                        <i class="bi bi-circle-fill me-1"></i> {{ ucfirst($building->status) }}
                    </span>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.units.create', ['building_id' => $building->id]) }}"
                           class="btn btn-outline-primary">
                            <i class="bi bi-plus-square me-1"></i> Add Single Unit
                        </a>
                        <a href="{{ route('real-estate.units.index', ['building_id' => $building->id]) }}"
                           class="btn btn-outline-primary">
                            <i class="bi bi-layers me-1"></i> Bulk Add Units
                        </a>
                        <hr>
                        <form action="{{ route('real-estate.buildings.destroy', $building) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this building? All floors and units will be deleted.')">
                                <i class="bi bi-trash me-1"></i> Delete Building
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .unit-tile {
        transition: all 0.2s ease;
    }
    .unit-tile:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .stat-card-clickable {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .stat-card-clickable:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    /* Smooth scroll */
    html {
        scroll-behavior: smooth;
    }
</style>

<script>
    // Add smooth scroll with offset for fixed headers
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offset = 100; // Adjust based on your header height
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });

                    // Add highlight effect
                    target.classList.add('highlight-section');
                    setTimeout(() => {
                        target.classList.remove('highlight-section');
                    }, 2000);
                }
            });
        });
    });
</script>

<style>
    @keyframes highlight {
        0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0.1); }
        100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }

    .highlight-section {
        animation: highlight 2s ease-out;
    }
</style>
@endsection

