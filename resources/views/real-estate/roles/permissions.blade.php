@extends('layouts.real-estate.dashboard')

@section('title', 'Role Permissions: ' . $role->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('real-estate.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('real-estate.roles.index') }}">Roles</a></li>
<li class="breadcrumb-item"><a href="{{ route('real-estate.roles.show', $role) }}">{{ $role->name }}</a></li>
<li class="breadcrumb-item active">Permissions</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Manage Permissions: {{ $role->name }}</h3>
                        <a href="{{ route('real-estate.roles.show', $role) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Role
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('real-estate.roles.permissions.update', $role) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            @if($permissions->isNotEmpty())
                                @foreach($permissions as $module => $modulePermissions)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <strong>{{ $module }}</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($modulePermissions as $permission)
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $permission->id }}" 
                                                                   id="perm_{{ $permission->id }}"
                                                                   {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Save Permissions
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    No permissions found. Please run the permissions seeder first.
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

