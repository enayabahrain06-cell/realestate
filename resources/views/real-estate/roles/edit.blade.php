@extends('layouts.real-estate.dashboard')

@section('title', 'Edit Role')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('real-estate.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('real-estate.roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">Edit: {{ $role->name }}</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Edit Role: {{ $role->name }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('real-estate.roles.update', $role) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Permissions</label>
                                @if($permissions->isNotEmpty())
                                    @foreach($permissions as $module => $modulePermissions)
                                        <div class="card mb-2">
                                            <div class="card-header py-2">
                                                <strong>{{ $module }}</strong>
                                            </div>
                                            <div class="card-body py-2">
                                                @foreach($modulePermissions as $permission)
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
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-warning">
                                        No permissions found. Please run the permissions seeder first.
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('real-estate.roles.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Role</button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <form action="{{ route('real-estate.roles.destroy', $role) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this role?');">
                            @csrf
                            @method('DELETE')
                            <div class="text-end">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Delete Role
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

