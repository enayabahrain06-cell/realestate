@extends('layouts.real-estate.dashboard')

@section('title', $tenant->full_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.tenants.index') }}">Tenants</a></li>
    <li class="breadcrumb-item active">{{ $tenant->full_name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 50px; height: 50px;">
                        <span class="text-primary fw-bold fs-5">{{ substr($tenant->full_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">{{ $tenant->full_name }}</h1>
                        <p class="text-muted small mb-0">Tenant Profile</p>
                    </div>
                </div>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('real-estate.tenants.edit', $tenant) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('real-estate.tenants.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-vcard text-primary me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Email</label>
                            <div class="fw-semibold">{{ $tenant->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Phone</label>
                            <div class="fw-semibold">{{ $tenant->phone }}</div>
                        </div>
                        @if($tenant->address)
                        <div class="col-12">
                            <label class="text-muted small d-block">Address</label>
                            <div>{{ $tenant->address }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Identification -->
            @if($tenant->id_number)
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card text-info me-2"></i>Identification</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small d-block">ID Type</label>
                            <div class="fw-semibold text-capitalize">{{ $tenant->id_type ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small d-block">ID Number</label>
                            <div class="fw-semibold">{{ $tenant->id_number }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Active Leases -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text text-success me-2"></i>Active Leases ({{ $activeLeases->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($activeLeases as $lease)
                        <div class="p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $lease->unit->building->name }} - Unit {{ $lease->unit->unit_number }}</div>
                                    <small class="text-muted">
                                        {{ $lease->start_date->format('M d, Y') }} - {{ $lease->end_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">${{ number_format($lease->rent_amount, 2) }}/mo</div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                            No active leases
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-currency-dollar text-success me-2"></i>Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <div class="fs-4 fw-bold text-success">${{ number_format($totalPaid, 2) }}</div>
                                <small class="text-muted">Total Paid</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                <div class="fs-4 fw-bold text-warning">
                                    {{ $tenant->payments->where('status', 'pending')->count() }}
                                </div>
                                <small class="text-muted">Pending Payments</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <div class="fs-4 fw-bold text-primary">
                                    {{ $tenant->leases->count() }}
                                </div>
                                <small class="text-muted">Total Leases</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <span class="badge status-{{ $tenant->status }} fs-6 mb-3">
                        <i class="bi bi-circle-fill me-1"></i> {{ ucfirst($tenant->status) }}
                    </span>
                </div>
            </div>

            <!-- Emergency Contact -->
            @if($tenant->emergency_contact_name)
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-telephone text-danger me-2"></i>Emergency Contact</h5>
                </div>
                <div class="card-body">
                    <div class="fw-semibold">{{ $tenant->emergency_contact_name }}</div>
                    <div class="text-muted">{{ $tenant->emergency_contact_phone }}</div>
                </div>
            </div>
            @endif

            <!-- Employer Info -->
            @if($tenant->employer)
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-briefcase text-primary me-2"></i>Employment</h5>
                </div>
                <div class="card-body">
                    <div class="fw-semibold">{{ $tenant->employer }}</div>
                    @if($tenant->monthly_income)
                        <div class="text-muted">Income: ${{ number_format($tenant->monthly_income, 2) }}/month</div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($tenant->notes)
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-sticky text-warning me-2"></i>Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $tenant->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.leases.create', ['tenant_id' => $tenant->id]) }}" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-plus me-1"></i> Create Lease
                        </a>
                        <a href="{{ route('real-estate.bookings.create', ['tenant_id' => $tenant->id]) }}" 
                           class="btn btn-outline-success">
                            <i class="bi bi-calendar-plus me-1"></i> Schedule Booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

