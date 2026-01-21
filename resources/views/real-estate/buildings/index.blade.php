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
                    <a href="{{ route('real-estate.buildings.show', $building) }}" class="text-decoration-none">
                        <div class="card building-card h-100 border-2 shadow-sm">
                            <!-- Building Image - Top -->
                            <div class="position-relative">
                                @if($building->image)
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; overflow: hidden; border-radius: 0.375rem 0.375rem 0 0;">
                                        <img src="{{ Storage::url($building->image) }}" alt="{{ $building->name }}"
                                             class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: contain; padding: 10px;">
                                    </div>
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; border-radius: 0.375rem 0.375rem 0 0;">
                                        <i class="bi bi-building fs-1 text-muted"></i>
                                    </div>
                                @endif

                                <!-- Status Badge -->
                                <span class="badge status-{{ $building->status }} position-absolute top-0 end-0 m-3">
                                    {{ ucfirst($building->status) }}
                                </span>
                            </div>

                            <!-- Building Data - Bottom -->
                            <div class="card-body">
                                <h5 class="card-title mb-2 fw-bold text-dark">{{ $building->name }}</h5>

                                <p class="card-text text-muted small mb-3">
                                    <i class="bi bi-geo-alt me-1"></i> {{ Str::limit($building->address, 50) }}
                                </p>

                                @php
                                    $totalFlats = $building->units->where('unit_type', 'flat')->count();
                                    $occupiedFlats = $building->units->where('unit_type', 'flat')->where('status', 'rented')->count();
                                    $subsidiaryUnits = $building->units->where('unit_type', 'flat')->count(); // Assuming flats are subsidiary
                                    $nonSubsidiaryUnits = $building->units->whereNotIn('unit_type', ['flat'])->count();

                                    // Calculate expenses by category
                                    $ewaExpenses = $building->ewaBills->sum('amount');
                                    $maintenanceExpenses = $building->expenses->where('category', 'maintenance')->sum('amount');
                                    $municipalityExpenses = $building->expenses->where('category', 'municipality')->sum('amount');
                                @endphp

                                <!-- Primary Stats -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="stat-box p-2 rounded text-center">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="bi bi-house-door text-primary me-1"></i>
                                                <small class="text-muted fw-semibold">Flats</small>
                                            </div>
                                            <div class="fw-bold text-dark">{{ $occupiedFlats }}/{{ $totalFlats }}</div>
                                            <small class="text-muted" style="font-size: 0.65rem;">Occupied</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-box p-2 rounded text-center">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="bi bi-building text-success me-1"></i>
                                                <small class="text-muted fw-semibold">Units</small>
                                            </div>
                                            <div class="fw-bold text-dark">{{ $subsidiaryUnits }}/{{ $nonSubsidiaryUnits }}</div>
                                            <small class="text-muted" style="font-size: 0.65rem;">Sub/Non-Sub</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Expense Stats -->
                                <div class="expense-section mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-cash-stack text-warning me-2"></i>
                                        <small class="text-muted fw-semibold">Monthly Expenses</small>
                                    </div>

                                    <div class="expense-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-lightning-charge text-info me-2"></i>
                                            <small class="text-dark">EWA</small>
                                        </div>
                                        <span class="badge bg-info">{{ number_format($ewaExpenses, 0) }} BD</span>
                                    </div>

                                    <div class="expense-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-building-gear text-success me-2"></i>
                                            <small class="text-dark">Municipality</small>
                                        </div>
                                        <span class="badge bg-success">{{ number_format($municipalityExpenses, 0) }} BD</span>
                                    </div>

                                    <div class="expense-item d-flex justify-content-between align-items-center p-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-tools text-warning me-2"></i>
                                            <small class="text-dark">Maintenance</small>
                                        </div>
                                        <span class="badge bg-warning">{{ number_format($maintenanceExpenses, 0) }} BD</span>
                                    </div>
                                </div>

                                <div class="text-center pt-2 border-top">
                                    <small class="text-muted text-capitalize">
                                        <i class="bi bi-tag me-1"></i>{{ $building->property_type }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
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

@push('styles')
<style>
    .building-card {
        transition: all 0.3s ease;
        border: 2px solid #e0e0e0 !important;
        cursor: pointer;
    }

    .building-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        border-color: #007bff !important;
    }

    .building-card:hover .card-title {
        color: #007bff !important;
    }

    /* Stat boxes */
    .stat-box {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .building-card:hover .stat-box {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-color: #90caf9;
        transform: scale(1.05);
    }

    /* Expense items */
    .expense-section {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .expense-item {
        background: white;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .expense-item:hover {
        background: #f0f7ff;
        border-color: #90caf9;
        transform: translateX(3px);
    }

    .expense-item .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        font-weight: 600;
    }

    /* Icons animation */
    .building-card:hover i {
        transform: scale(1.1);
        transition: transform 0.3s ease;
    }
</style>
@endpush
@endsection

