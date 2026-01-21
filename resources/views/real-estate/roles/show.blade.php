@extends('layouts.real-estate.dashboard')

@section('title', 'Role: ' . $role->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('real-estate.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('real-estate.roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">{{ $role->name }}</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Role Details: {{ $role->name }}</h3>
                        <div>
                            <a href="{{ route('real-estate.roles.edit', $role) }}" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('real-estate.roles.permissions', $role) }}" class="btn btn-info">
                                <i class="bi bi-key"></i> Permissions
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Basic Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 150px;">Name</th>
                                        <td>{{ $role->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $role->description ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Users Count</th>
                                        <td>{{ $role->users->count() }}</td>
                                    </tr>
                                    <tr>
                                        <th>Permissions Count</th>
                                        <td>{{ $role->permissions->count() }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Assigned Users</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($role->users->isNotEmpty())
                                            <ul class="list-group">
                                                @foreach($role->users as $user)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        {{ $user->name }}
                                                        <span class="badge bg-primary rounded-pill">{{ $user->email }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted mb-0">No users assigned to this role.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Permissions</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($role->permissions->isNotEmpty())
                                            @php
                                                $permissionsByModule = $role->permissions->groupBy('module');
                                            @endphp
                                            @foreach($permissionsByModule as $module => $modulePermissions)
                                                <div class="mb-3">
                                                    <strong>{{ $module }}</strong>
                                                    <div class="mt-1">
                                                        @foreach($modulePermissions as $permission)
                                                            <span class="badge bg-info mb-1">{{ $permission->name }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted mb-0">No permissions assigned to this role.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

