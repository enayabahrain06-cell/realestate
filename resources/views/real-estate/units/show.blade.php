@extends('layouts.real-estate.dashboard')

@section('title', 'Unit #' . $unit->unit_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.units.index') }}">Units</a></li>
    <li class="breadcrumb-item active">#{{ $unit->unit_number }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-grid-3x3 text-success me-2"></i>Unit #{{ $unit->unit_number }}
                </h1>
                <p class="text-muted small mb-0">
                    {{ $unit->building->name }} - Floor {{ $unit->floor->floor_number }}
                </p>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('real-estate.units.edit', $unit) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('real-estate.units.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Unit Details -->
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Unit Details</h5>
                        <span class="badge status-{{ $unit->status }} fs-6">{{ ucfirst($unit->status) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Unit Number</label>
                                <h5 class="mb-0">#{{ $unit->unit_number }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Unit Type</label>
                                <h5 class="mb-0 text-capitalize">{{ $unit->unit_type }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Size</label>
                                <h5 class="mb-0">{{ number_format($unit->size_sqft) }} sqft</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Monthly Rent</label>
                                <h5 class="mb-0 text-success">${{ number_format($unit->rent_amount, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Deposit</label>
                                <h5 class="mb-0">${{ number_format($unit->deposit_amount, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Bedrooms / Bathrooms</label>
                                <h5 class="mb-0">{{ $unit->bedrooms }} / {{ $unit->bathrooms }}</h5>
                            </div>
                        </div>
                    </div>
                    
                    @if($unit->description)
                        <div class="mt-3 pt-3 border-top">
                            <label class="text-muted small mb-1">Description</label>
                            <p class="mb-0">{{ $unit->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Lease -->
            @if($unit->activeLease)
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Active Lease</h5>
                </div>
                <div class="card-body">
                    @php $lease = $unit->activeLease; @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Tenant</label>
                                <h5 class="mb-0">{{ $lease->tenant->full_name }}</h5>
                                <small class="text-muted">{{ $lease->tenant->email }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Lease Period</label>
                                <h5 class="mb-0">{{ $lease->start_date->format('M d, Y') }} - {{ $lease->end_date->format('M d, Y') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Monthly Rent</label>
                                <h5 class="mb-0 text-success">${{ number_format($lease->rent_amount, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Days Remaining</label>
                                <h5 class="mb-0">{{ $lease->days_remaining }} days</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('real-estate.leases.show', $lease) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i> View Lease
                    </a>
                </div>
            </div>
            @endif

            <!-- Unit History -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Unit History</h5>
                </div>
                <div class="card-body">
                    @if($unit->histories && $unit->histories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                        <th>Changed By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unit->histories()->orderBy('changed_at', 'desc')->take(10)->get() as $history)
                                        <tr>
                                            <td>{{ $history->changed_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @switch($history->action)
                                                    @case('rent_change')
                                                        <span class="badge bg-info">Rent Change</span>
                                                        @break
                                                    @case('status_change')
                                                        <span class="badge bg-warning">Status Change</span>
                                                        @break
                                                    @case('maintenance')
                                                        <span class="badge bg-secondary">Maintenance</span>
                                                        @break
                                                    @case('feature_update')
                                                        <span class="badge bg-primary">Update</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $history->action)) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($history->previous_rent_amount && $history->new_rent_amount)
                                                    ${{ number_format($history->previous_rent_amount, 2) }} → ${{ number_format($history->new_rent_amount, 2) }}
                                                @elseif($history->previous_status && $history->new_status)
                                                    {{ ucfirst($history->previous_status) }} → {{ ucfirst($history->new_status) }}
                                                @else
                                                    {{ $history->change_reason ?: 'No details' }}
                                                @endif
                                            </td>
                                            <td>{{ $history->changedBy ? $history->changedBy->full_name : 'System' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($unit->histories->count() > 10)
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    View All {{ $unit->histories->count() }} Records
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-clock-history fs-1"></i>
                            <p class="mb-0 mt-2">No history records yet.</p>
                            <small>History will be recorded when unit details change.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Building Info -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-building text-primary me-2"></i>Building</h5>
                </div>
                <div class="card-body">
                    <h5>{{ $unit->building->name }}</h5>
                    <p class="text-muted small mb-3">{{ $unit->building->address }}</p>
                    <a href="{{ route('real-estate.buildings.show', $unit->building) }}" class="btn btn-outline-primary btn-sm w-100">
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
                        <a href="{{ route('real-estate.units.edit', $unit) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit Unit
                        </a>
                        @if($unit->status === 'available')
                            <a href="{{ route('real-estate.bookings.create', ['unit_id' => $unit->id]) }}" class="btn btn-outline-success">
                                <i class="bi bi-calendar-plus me-1"></i> Create Booking
                            </a>
                            <a href="{{ route('real-estate.leases.create', ['unit_id' => $unit->id]) }}" class="btn btn-outline-success">
                                <i class="bi bi-file-earmark-plus me-1"></i> Create Lease
                            </a>
                        @endif
                        <a href="{{ route('real-estate.floors.show', $unit->floor) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-layers me-1"></i> View Floor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

