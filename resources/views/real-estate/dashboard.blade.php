@extends('layouts.real-estate.dashboard')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Overview</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">Real Estate Dashboard</h1>
                <p class="text-muted small mb-0">Overview of your property management system</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.buildings.create') }}" class="btn btn-primary">
                    <i class="bi bi-building me-1"></i> Add Building
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card h-100">
                <a href="{{ route('real-estate.buildings.index') }}" class="text-decoration-none text-dark d-block h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Buildings</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_buildings'] }}</h3>
                        </div>
                        <div class="stat-icon stat-icon-primary">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Total Units</p>
                        <h3 class="mb-0 fw-bold">{{ $stats['total_units'] }}</h3>
                    </div>
                    <div class="stat-icon stat-icon-info">
                        <i class="bi bi-grid-3x3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Available Units</p>
                        <h3 class="mb-0 fw-bold text-success">{{ $stats['available_units'] }}</h3>
                    </div>
                    <div class="stat-icon stat-icon-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Occupancy Rate</p>
                        <h3 class="mb-0 fw-bold">{{ $stats['occupancy_rate'] }}%</h3>
                    </div>
                    <div class="stat-icon stat-icon-warning">
                        <i class="bi bi-pie-chart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="stat-card text-center">
                <div class="stat-icon stat-icon-success mx-auto mb-2">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <p class="text-muted small mb-1">Revenue</p>
                <h5 class="mb-0 fw-bold">${{ number_format($stats['completed_payments'] ?? 0, 2) }}</h5>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center">
                <div class="stat-icon stat-icon-primary mx-auto mb-2">
                    <i class="bi bi-people"></i>
                </div>
                <p class="text-muted small mb-1">Tenants</p>
                <h5 class="mb-0 fw-bold">{{ $stats['total_tenants'] }}</h5>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center">
                <div class="stat-icon stat-icon-info mx-auto mb-2">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <p class="text-muted small mb-1">Active Leases</p>
                <h5 class="mb-0 fw-bold">{{ $stats['active_leases'] }}</h5>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center">
                <div class="stat-icon stat-icon-warning mx-auto mb-2">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <p class="text-muted small mb-1">Pending Bookings</p>
                <h5 class="mb-0 fw-bold">{{ $stats['pending_bookings'] }}</h5>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center">
                <div class="stat-icon stat-icon-danger mx-auto mb-2">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <p class="text-muted small mb-1">Maintenance</p>
                <h5 class="mb-0 fw-bold">{{ $stats['maintenance_units'] }}</h5>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center">
                <div class="stat-icon stat-icon-secondary mx-auto mb-2">
                    <i class="bi bi-clock-history"></i>
                </div>
                <p class="text-muted small mb-1">Reserved</p>
                <h5 class="mb-0 fw-bold">{{ $stats['reserved_units'] }}</h5>
            </div>
        </div>
    </div>

    <!-- Charts and Alerts Row -->
    <div class="row g-4 mb-4">
        <!-- Monthly Revenue Chart -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Monthly Revenue ({{ now()->year }})</h5>
                    <span class="badge bg-primary">{{ now()->format('F') }}</span>
                </div>
                <div class="card-body">
                    @php
                        $maxRevenue = max($revenueData);
                    @endphp
                    <div class="d-flex align-items-end" style="height: 200px;">
                        @foreach($revenueData as $month => $revenue)
                            @php
                                $height = $maxRevenue > 0 ? ($revenue / $maxRevenue * 150) : 5;
                                $height = max($height, 5);
                            @endphp
                            <div class="flex-fill text-center mx-1">
                                <div class="revenue-bar mb-2" style="height: {{ $height }}px; min-height: 5px;"
                                     data-bs-toggle="tooltip" 
                                     title="${{ number_format($revenue, 2) }}">
                                </div>
                                <span class="small text-muted d-block">{{ date('M', mktime(0, 0, 0, $month, 1)) }}</span>
                                <div class="small fw-bold">${{ number_format($revenue, 0) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Buildings with Low Occupancy -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Buildings Needing Attention</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($buildingsWithOccupancy as $building)
                        <div class="p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $building->name }}</div>
                                    <small class="text-muted">{{ $building->property_type ?? 'Commercial' }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold {{ $building->occupancy_rate < 50 ? 'text-danger' : 'text-warning' }}">
                                        {{ $building->occupancy_rate }}%
                                    </div>
                                    <small class="text-muted">{{ $building->rented_count }}/{{ $building->units_count }} units</small>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar {{ $building->occupancy_rate < 50 ? 'bg-danger' : 'bg-warning' }}" 
                                     role="progressbar" 
                                     style="width: {{ $building->occupancy_rate }}%" 
                                     aria-valuenow="{{ $building->occupancy_rate }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                            All buildings have good occupancy!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-4 mb-4">
        <!-- Recent Leases -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Recent Leases</h5>
                    <a href="{{ route('real-estate.leases.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentLeases as $lease)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $lease->tenant->full_name ?? 'N/A' }}</div>
                                    <small class="text-muted">
                                        {{ $lease->unit->building->name ?? 'N/A' }} - Unit {{ $lease->unit->unit_number ?? 'N/A' }}
                                    </small>
                                </div>
                                <span class="badge status-{{ $lease->status }}">
                                    {{ ucfirst($lease->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                            No recent leases
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar-check text-success me-2"></i>Recent Bookings</h5>
                    <a href="{{ route('real-estate.bookings.index') }}" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentBookings as $booking)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $booking->tenant->full_name ?? 'N/A' }}</div>
                                    <small class="text-muted">
                                        {{ $booking->unit->building->name ?? 'N/A' }} - {{ $booking->booking_type ?? 'Viewing' }}
                                    </small>
                                </div>
                                <span class="badge status-{{ $booking->status }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                            No recent bookings
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-currency-dollar text-success me-2"></i>Recent Payments</h5>
                    <a href="#" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentPayments as $payment)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $payment->tenant->full_name ?? 'N/A' }}</div>
                                    <small class="text-muted">
                                        {{ $payment->payment_type ?? 'Rent' }} - {{ $payment->lease->unit->building->name ?? '' }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</div>
                                    <small class="text-muted">{{ $payment->paid_at ? $payment->paid_at->diffForHumans() : 'Pending' }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                            No recent payments
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('real-estate.buildings.create') }}" class="quick-action-card">
                                <i class="bi bi-building text-primary"></i>
                                <div class="fw-semibold">Add New Building</div>
                                <small class="text-muted">Register a new property</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('real-estate.units.index') }}" class="quick-action-card">
                                <i class="bi bi-grid-3x3 text-success"></i>
                                <div class="fw-semibold">Manage Units</div>
                                <small class="text-muted">View and edit units</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('real-estate.tenants.create') }}" class="quick-action-card">
                                <i class="bi bi-person-plus text-info"></i>
                                <div class="fw-semibold">Add New Tenant</div>
                                <small class="text-muted">Register a tenant</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('real-estate.available-units') }}" class="quick-action-card">
                                <i class="bi bi-search text-warning"></i>
                                <div class="fw-semibold">View Available Units</div>
                                <small class="text-muted">Browse vacant units</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
@endsection

