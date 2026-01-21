@extends('layouts.real-estate.dashboard')

@section('title', 'Tenants')

@section('breadcrumb')
    <li class="breadcrumb-item active">Tenants</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-people text-primary me-2"></i>Tenants</h1>
                <p class="text-muted small mb-0">Manage your tenants and their information</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.tenants.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Add Tenant
                </a>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="blacklisted" {{ request('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tenants Table -->
    @if($tenants->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tenant</th>
                            <th>Contact</th>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Active Leases</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <span class="text-primary fw-bold">{{ substr($tenant->full_name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $tenant->full_name }}</div>
                                            @if($tenant->employer)
                                                <small class="text-muted">{{ $tenant->employer }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $tenant->email }}</div>
                                    <small class="text-muted">{{ $tenant->phone }}</small>
                                </td>
                                <td>
                                    @if($tenant->id_number)
                                        <small class="text-capitalize">{{ $tenant->id_type ?? 'ID' }}: {{ $tenant->id_number }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge status-{{ $tenant->status }}">
                                        {{ ucfirst($tenant->status) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $activeLeases = $tenant->leases->where('status', 'active');
                                    @endphp
                                    <span class="badge bg-primary">{{ $activeLeases->count() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('real-estate.tenants.show', $tenant) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('real-estate.tenants.edit', $tenant) }}" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('real-estate.tenants.destroy', $tenant) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="return confirm('Delete this tenant?')">
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
            {{ $tenants->withQueryString()->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-people fs-1"></i>
                </div>
                <h5>No Tenants Found</h5>
                <p class="text-muted">Add your first tenant to get started.</p>
                <a href="{{ route('real-estate.tenants.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Add Tenant
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

