@extends('layouts.real-estate.dashboard')

@section('title', 'Available Units')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Available Units</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-check-circle text-success me-2"></i>Available Units</h1>
                <p class="text-muted small mb-0">Browse all available units for rent</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if($units->count() > 0)
        <div class="row g-4">
            @foreach($units as $unit)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 unit-card">
                        <div class="card-header bg-white p-0">
                            <div class="bg-success bg-opacity-10 d-flex align-items-center justify-content-center" 
                                 style="height: 120px;">
                                <i class="bi bi-grid-3x3 fs-1 text-success"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title mb-0 fw-bold">{{ $unit->building->name }}</h5>
                                    <small class="text-muted">Unit {{ $unit->unit_number }}</small>
                                </div>
                                <span class="badge bg-success">Available</span>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold">{{ $unit->size_sqft }}</div>
                                        <small class="text-muted">sqft</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold">{{ $unit->bedrooms ?? '-' }}</div>
                                        <small class="text-muted">Beds</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold">{{ $unit->bathrooms ?? '-' }}</div>
                                        <small class="text-muted">Baths</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="fw-bold fs-5 text-primary">${{ number_format($unit->rent_amount, 2) }}</div>
                                <small class="text-muted">/month</small>
                            </div>
                            
                            @if($unit->features && count($unit->features) > 0)
                                <div class="mt-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach(array_slice($unit->features, 0, 3) as $feature)
                                            <span class="badge bg-light text-dark">{{ $feature }}</span>
                                        @endforeach
                                        @if(count($unit->features) > 3)
                                            <span class="badge bg-light text-dark">+{{ count($unit->features) - 3 }} more</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2">
                                <a href="{{ route('real-estate.units.show', $unit) }}" 
                                   class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="bi bi-eye me-1"></i> View Details
                                </a>
                                <a href="{{ route('real-estate.bookings.create', ['unit_id' => $unit->id]) }}" 
                                   class="btn btn-success btn-sm flex-grow-1">
                                    <i class="bi bi-calendar-plus me-1"></i> Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $units->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="text-success mb-3">
                    <i class="bi bi-check-circle fs-1"></i>
                </div>
                <h5>No Available Units</h5>
                <p class="text-muted">Currently, there are no units available for rent.</p>
                <a href="{{ route('real-estate.dashboard') }}" class="btn btn-primary">
                    Back to Dashboard
                </a>
            </div>
        </div>
    @endif
</div>

<style>
    .unit-card {
        transition: all 0.3s ease;
    }
    .unit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endsection

