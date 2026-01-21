@extends('layouts.real-estate.dashboard')

@section('title', 'Booking Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.bookings.index') }}">Bookings</a></li>
    <li class="breadcrumb-item active">Booking #{{ $booking->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-calendar-check text-success me-2"></i>Booking #{{ $booking->id }}
                </h1>
                <p class="text-muted small mb-0">
                    {{ $booking->tenant->full_name }} - {{ $booking->unit->building->name }} #{{ $booking->unit->unit_number }}
                </p>
            </div>
            <div class="col-auto d-flex gap-2">
                @if(in_array($booking->status, ['pending', 'confirmed']))
                    <form action="{{ route('real-estate.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning" onclick="return confirm('Cancel this booking?')">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                    </form>
                @endif
                <a href="{{ route('real-estate.bookings.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Booking Details -->
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Booking Information</h5>
                        <span class="badge status-{{ $booking->status }} fs-6">{{ ucfirst($booking->status) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Booking Type</label>
                                <h5 class="mb-0 text-capitalize">{{ $booking->booking_type }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Booking Date</label>
                                <h5 class="mb-0">{{ $booking->booking_date->format('M d, Y h:i A') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Created At</label>
                                <h5 class="mb-0">{{ $booking->created_at->format('M d, Y h:i A') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">IP Address</label>
                                <h5 class="mb-0">{{ $booking->ip_address }}</h5>
                            </div>
                        </div>
                    </div>
                    
                    @if($booking->notes)
                        <div class="mt-3 pt-3 border-top">
                            <label class="text-muted small mb-1">Notes</label>
                            <p class="mb-0">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    @if($booking->status === 'pending')
                        <form action="{{ route('real-estate.bookings.confirm', $booking) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Confirm Booking
                            </button>
                        </form>
                        <form action="{{ route('real-estate.bookings.complete', $booking) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-success">
                                <i class="bi bi-check-all me-1"></i> Mark Complete
                            </button>
                        </form>
                    @elseif($booking->status === 'confirmed')
                        @if($booking->booking_type === 'rental')
                            <a href="{{ route('real-estate.leases.create', ['unit_id' => $booking->unit_id]) }}" class="btn btn-primary">
                                <i class="bi bi-file-earmark-plus me-1"></i> Create Lease
                            </a>
                        @endif
                        <form action="{{ route('real-estate.bookings.complete', $booking) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-success">
                                <i class="bi bi-check-all me-1"></i> Mark Complete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Tenant Info -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person text-info me-2"></i>Tenant</h5>
                </div>
                <div class="card-body">
                    <h5>{{ $booking->tenant->full_name }}</h5>
                    <p class="text-muted small mb-2">{{ $booking->tenant->email }}</p>
                    <p class="text-muted small mb-0">{{ $booking->tenant->phone }}</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('real-estate.tenants.show', $booking->tenant) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-eye me-1"></i> View Tenant
                    </a>
                </div>
            </div>

            <!-- Unit Info -->
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-grid-3x3 text-success me-2"></i>Unit</h5>
                </div>
                <div class="card-body">
                    <h5>#{{ $booking->unit->unit_number }}</h5>
                    <p class="text-muted small mb-2 text-capitalize">{{ $booking->unit->unit_type }}</p>
                    <p class="text-muted small mb-0">{{ $booking->unit->building->name }}</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('real-estate.units.show', $booking->unit) }}" class="btn btn-outline-success btn-sm w-100">
                        <i class="bi bi-eye me-1"></i> View Unit
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
                        <a href="{{ route('real-estate.bookings.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-1"></i> All Bookings
                        </a>
                        <a href="{{ route('real-estate.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

