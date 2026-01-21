@extends('layouts.real-estate.dashboard')

@section('title', 'Users Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('real-estate.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Users Management</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('real-estate.users.export') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-download"></i> Export
                </a>
                <a href="{{ route('real-estate.users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Create User
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('real-estate.users.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Name, email, mobile..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">All Roles</option>
                            @php
                            $roles = [
                                'super_admin' => 'Super Admin',
                                'property_manager' => 'Property Manager',
                                'accountant' => 'Accountant',
                                'agent' => 'Agent',
                                'viewer' => 'Viewer',
                            ];
                            @endphp
                            @foreach($roles as $slug => $name)
                                <option value="{{ $slug }}" {{ ($filters['role'] ?? '') === $slug ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="verified" {{ ($filters['status'] ?? '') === 'verified' ? 'selected' : '' }}>
                                Verified
                            </option>
                            <option value="unverified" {{ ($filters['status'] ?? '') === 'unverified' ? 'selected' : '' }}>
                                Unverified
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2">Filter</button>
                        <a href="{{ route('real-estate.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Users ({{ $users->total() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Contact</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px; color: #fff; font-weight: 600;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            @if($user->full_name)
                                                <small class="text-muted d-block">{{ $user->full_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $user->email }}</div>
                                    @if($user->mobile)
                                        <small class="text-muted">{{ $user->mobile }}</small>
                                    @endif
                                </td>
                                <td>
                                    @forelse($user->realEstateRoles as $role)
                                        <span class="badge bg-{{ $role->slug === 'super_admin' ? 'danger' : 'primary' }} mb-1">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="badge bg-secondary">No Role</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('real-estate.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('real-estate.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->id !== auth()->id() && !$user->isSuperAdmin())
                                            <form action="{{ route('real-estate.users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        No users found
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
            <div class="card-footer">
                {{ $users->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

