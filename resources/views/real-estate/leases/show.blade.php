@extends('layouts.real-estate.dashboard')

@section('title', 'Lease Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.leases.index') }}">Leases</a></li>
    <li class="breadcrumb-item active">Lease #{{ $lease->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>Lease #{{ $lease->id }}
                </h1>
                <p class="text-muted small mb-0">
                    {{ $lease->tenant->full_name }} - {{ $lease->unit->building->name }} #{{ $lease->unit->unit_number }}
                </p>
            </div>
            <div class="col-auto d-flex gap-2">
                @if($lease->status === 'active')
                    <form action="{{ route('real-estate.leases.terminate', $lease) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Terminate this lease?')">
                            <i class="bi bi-x-circle me-1"></i> Terminate
                        </button>
                    </form>
                @endif
                <a href="{{ route('real-estate.leases.index') }}" class="btn btn-outline-secondary">
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
                            <h6 class="mb-0">Monthly Rent</h6>
                            <h3 class="mb-0">${{ number_format($lease->rent_amount, 2) }}</h3>
                        </div>
                        <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Paid</h6>
                            <h3 class="mb-0">${{ number_format($stats['total_paid'], 2) }}</h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Pending</h6>
                            <h3 class="mb-0">{{ $stats['pending_payments'] }}</h3>
                        </div>
                        <i class="bi bi-clock fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-{{ $lease->is_expired ? 'danger' : 'info' }} text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Days Remaining</h6>
                            <h3 class="mb-0">{{ $stats['days_remaining'] }}</h3>
                        </div>
                        <i class="bi bi-calendar fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Lease Details -->
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Lease Information</h5>
                        <span class="badge status-{{ $lease->status }} fs-6">{{ ucfirst($lease->status) }}</span>
                    </div>
                </div>
                <div class="card-body">
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
                                <label class="text-muted small mb-1">Unit</label>
                                <h5 class="mb-0">#{{ $lease->unit->unit_number }}</h5>
                                <small class="text-muted">{{ $lease->unit->building->name }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Lease Type</label>
                                <h5 class="mb-0 text-capitalize">{{ str_replace('_', ' ', $lease->lease_type) }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Payment Frequency</label>
                                <h5 class="mb-0 text-capitalize">{{ $lease->payment_frequency }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Start Date</label>
                                <h5 class="mb-0">{{ $lease->start_date->format('M d, Y') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">End Date</label>
                                <h5 class="mb-0">{{ $lease->end_date->format('M d, Y') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Deposit</label>
                                <h5 class="mb-0">${{ number_format($lease->deposit_amount, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Late Payment Fee</label>
                                <h5 class="mb-0">${{ number_format($lease->late_payment_fee, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    
                    @if($lease->terms)
                        <div class="mt-3 pt-3 border-top">
                            <label class="text-muted small mb-1">Terms & Conditions</label>
                            <p class="mb-0">{{ $lease->terms }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payments -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card text-success me-2"></i>Payment History</h5>
                </div>
                <div class="card-body">
                    @if($lease->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lease->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->paid_at->format('M d, Y') }}</td>
                                            <td>${{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span class="badge status-{{ $payment->status }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $payment->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0">No payments recorded yet.</p>
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
                    <h5>{{ $lease->tenant->full_name }}</h5>
                    <p class="text-muted small mb-2">{{ $lease->tenant->email }}</p>
                    <p class="text-muted small mb-0">{{ $lease->tenant->phone }}</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('real-estate.tenants.show', $lease->tenant) }}" class="btn btn-outline-primary btn-sm w-100">
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
                    <h5>#{{ $lease->unit->unit_number }}</h5>
                    <p class="text-muted small mb-2 text-capitalize">{{ $lease->unit->unit_type }}</p>
                    <p class="text-muted small mb-0">{{ number_format($lease->unit->size_sqft) }} sqft</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('real-estate.units.show', $lease->unit) }}" class="btn btn-outline-success btn-sm w-100">
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
                        <a href="{{ route('real-estate.leases.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-1"></i> All Leases
                        </a>
                        <a href="{{ route('real-estate.units.show', $lease->unit) }}" class="btn btn-outline-success">
                            <i class="bi bi-grid-3x3 me-1"></i> View Unit
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

