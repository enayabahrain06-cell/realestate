@extends('layouts.real-estate.dashboard')

@section('title', 'Agents')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Agents</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $agents->total() }}</h4>
                                <p class="mb-0">Total Agents</p>
                            </div>
                            <i class="bi bi-people-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $agents->where('is_active', true)->count() }}</h4>
                                <p class="mb-0">Active</p>
                            </div>
                            <i class="bi bi-person-check-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $agents->sum('commissions_count') }}</h4>
                                <p class="mb-0">Total Deals</p>
                            </div>
                            <i class="bi bi-graph-up-arrow fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agents Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Agents</h5>
                    <a href="{{ route('agents.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Agent
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search agents..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('agents.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Contact</th>
                            <th>License #</th>
                            <th>Commission Rate</th>
                            <th>Deals Closed</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle p-2 me-2">
                                        <i class="bi bi-person-fill text-white"></i>
                                    </div>
                                    <a href="{{ route('agents.show', $agent) }}" class="text-decoration-none">
                                        {{ $agent->name }}
                                    </a>
                                </div>
                            </td>
                            <td>
                                <small>{{ $agent->email ?: '-' }}</small><br>
                                <small class="text-muted">{{ $agent->phone ?: '-' }}</small>
                            </td>
                            <td>{{ $agent->license_number ?: '-' }}</td>
                            <td>{{ $agent->commission_rate ? $agent->commission_rate . '%' : '-' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $agent->commissions_count }}</span>
                            </td>
                            <td>
                                @if($agent->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('agents.show', $agent) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('agents.edit', $agent) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('agents.destroy', $agent) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No agents found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $agents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

