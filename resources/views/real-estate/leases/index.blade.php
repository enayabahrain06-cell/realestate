@extends('layouts.real-estate.dashboard')

@section('title', 'Leases')

@section('breadcrumb')
    <li class="breadcrumb-item active">Leases</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-file-earmark-text text-primary me-2"></i>Leases</h1>
                <p class="text-muted small mb-0">Manage all lease agreements</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.leases.create') }}" class="btn btn-primary">
                    <i class="bi bi-file-earmark-plus me-1"></i> New Lease
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="building_id" class="form-select select2">
                        <option value="">All Buildings</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                {{ $building->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tenant_id" class="form-select select2">
                        <option value="">All Tenants</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                {{ $tenant->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leases Table -->
    @if($leases->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tenant</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>Period</th>
                            <th>Rent</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leases as $lease)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $lease->tenant->full_name }}</div>
                                    <small class="text-muted">{{ $lease->tenant->email }}</small>
                                </td>
                                <td>
                                    <div>{{ $lease->unit->building->name }}</div>
                                    <small class="text-muted">Unit {{ $lease->unit->unit_number }}</small>
                                </td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $lease->lease_type) }}</td>
                                <td>
                                    <small>
                                        {{ $lease->start_date->format('M d, Y') }} - 
                                        {{ $lease->end_date->format('M d, Y') }}
                                    </small>
                                </td>
                                <td class="fw-semibold">${{ number_format($lease->rent_amount, 2) }}/{{ $lease->payment_frequency }}</td>
                                <td>
                                    <span class="badge status-{{ $lease->status }}">
                                        {{ ucfirst($lease->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('real-estate.leases.show', $lease) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('real-estate.leases.edit', $lease) }}" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('real-estate.leases.destroy', $lease) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="return confirm('Delete this lease?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $leases->withQueryString()->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-file-earmark-text fs-1"></i>
                </div>
                <h5>No Leases Found</h5>
                <p class="text-muted">Create your first lease agreement.</p>
                <a href="{{ route('real-estate.leases.create') }}" class="btn btn-primary">
                    <i class="bi bi-file-earmark-plus me-1"></i> New Lease
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

