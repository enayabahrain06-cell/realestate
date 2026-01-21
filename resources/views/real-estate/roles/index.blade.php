@extends('layouts.real-estate.dashboard')

@section('title', 'Roles & Permissions')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('real-estate.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Roles</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Roles & Permissions</h3>
            <a href="{{ route('real-estate.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Create Role
            </a>
        </div>

        <!-- Roles Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Roles</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Description</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>
                                <strong>{{ $role->name }}</strong>
                            </td>
                            <td>{{ $role->description ?: '-' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $role->users_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $role->permissions_count }}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('real-estate.roles.show', $role) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('real-estate.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(!in_array($role->name, ['Super Admin', 'Viewer']))
                                    <form action="{{ route('real-estate.roles.destroy', $role) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure? Users with this role will be affected.')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No roles found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Permissions Overview -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Permission Modules</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                    $modules = [
                        'Buildings' => ['buildings.view', 'buildings.create', 'buildings.edit', 'buildings.delete'],
                        'Units' => ['units.view', 'units.create', 'units.edit', 'units.delete', 'units.bulk_actions'],
                        'Tenants' => ['tenants.view', 'tenants.create', 'tenants.edit', 'tenants.delete'],
                        'Leases' => ['leases.view', 'leases.create', 'leases.edit', 'leases.delete', 'leases.renew', 'leases.terminate'],
                        'Payments' => ['payments.view', 'payments.create', 'payments.edit', 'payments.approve', 'payments.export'],
                        'Leads' => ['leads.view', 'leads.create', 'leads.edit', 'leads.delete', 'leads.convert', 'leads.assign'],
                        'Agents' => ['agents.view', 'agents.create', 'agents.edit', 'agents.delete'],
                        'Commissions' => ['commissions.view', 'commissions.create', 'commissions.approve', 'commissions.pay', 'commissions.export'],
                        'Documents' => ['documents.view', 'documents.create', 'documents.edit', 'documents.delete', 'documents.download'],
                        'Reports' => ['reports.view', 'reports.export', 'reports.financial'],
                        'Audit Logs' => ['audit-logs.view', 'audit-logs.export', 'audit-logs.cleanup'],
                        'Roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'roles.assign'],
                    ];
                    @endphp
                    
                    @foreach($modules as $module => $perms)
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-header py-2">
                                <strong>{{ $module }}</strong>
                            </div>
                            <div class="card-body py-2">
                                @foreach($perms as $perm)
                                    <span class="badge bg-light text-dark mb-1">{{ str_replace('.', ' → ', $perm) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

