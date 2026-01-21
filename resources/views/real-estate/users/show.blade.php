@extends('layouts.real-estate.dashboard')

@section('title', $user->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('real-estate.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('real-estate.users.index') }}">Users</a></li>
<li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>User Profile</h3>
            <div class="d-flex gap-2">
                @if($user->id !== auth()->id())
                    <form action="{{ route('real-estate.users.toggle-status', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $user->email_verified_at ? 'warning' : 'success' }}">
                            <i class="bi bi-{{ $user->email_verified_at ? 'x-circle' : 'check-circle' }}"></i>
                            {{ $user->email_verified_at ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('real-estate.users.edit', $user) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('real-estate.users.activity', $user) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-clock-history"></i> Activity
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width: 100px; height: 100px; color: #fff; font-size: 2.5rem; font-weight: 600;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        @if($user->full_name)
                            <p class="text-muted">{{ $user->full_name }}</p>
                        @endif
                        <div class="mb-3">
                            @forelse($user->realEstateRoles as $role)
                                <span class="badge bg-{{ $role->slug === 'super_admin' ? 'danger' : 'primary' }} me-1">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="badge bg-secondary">No Role</span>
                            @endforelse
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            @if($user->email_verified_at)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                            @else
                                <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="row text-center">
                            <div class="col">
                                <small class="text-muted d-block">Member Since</small>
                                <strong>{{ $user->created_at?->format('M Y') ?? 'N/A' }}</strong>
                            </div>
                            <div class="col border-start">
                                <small class="text-muted d-block">ID</small>
                                <strong>#{{ $user->id }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0 fw-bold">{{ $user->email }}</p>
                        </div>
                        @if($user->mobile)
                            <div class="mb-3">
                                <label class="text-muted small">Mobile</label>
                                <p class="mb-0 fw-bold">{{ $user->mobile }}</p>
                            </div>
                        @endif
                        @if($user->nationality)
                            <div class="mb-3">
                                <label class="text-muted small">Nationality</label>
                                <p class="mb-0 fw-bold">{{ $user->nationality }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="col-md-8">
                <!-- Personal Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Gender</label>
                                <p class="mb-0 fw-bold text-capitalize">{{ $user->gender ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Birthdate</label>
                                <p class="mb-0 fw-bold">{{ $user->birthdate?->format('F j, Y') ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Age</label>
                                <p class="mb-0 fw-bold">{{ $user->age ?? 'N/A' }} years old</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Blood Type</label>
                                <p class="mb-0 fw-bold">{{ $user->blood_type ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Horoscope</label>
                                <p class="mb-0 fw-bold">{{ $user->horoscope ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Life Stage</label>
                                <p class="mb-0 fw-bold">{{ $user->lifeStage ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles & Permissions -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Roles & Permissions</h5>
                        <a href="{{ route('real-estate.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit Roles
                        </a>
                    </div>
                    <div class="card-body">
                        @forelse($user->realEstateRoles as $role)
                            <div class="mb-3">
                                <span class="badge bg-{{ $role->slug === 'super_admin' ? 'danger' : 'primary' }} fs-6 mb-2">
                                    {{ $role->name }}
                                </span>
                                @if($role->description)
                                    <p class="text-muted small mb-2">{{ $role->description }}</p>
                                @endif
                                @if($role->permissions->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($role->permissions->take(10) as $permission)
                                            <span class="badge bg-light text-dark">{{ $permission->name }}</span>
                                        @endforeach
                                        @if($role->permissions->count() > 10)
                                            <span class="badge bg-light text-dark">+{{ $role->permissions->count() - 10 }} more</span>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-muted small mb-0">No permissions assigned</p>
                                @endif
                            </div>
                        @empty
                            <div class="alert alert-warning mb-0">
                                This user has no roles assigned. They will only have basic access.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Activity</h5>
                        <a href="{{ route('real-estate.users.activity', $user) }}" class="btn btn-sm btn-outline-secondary">
                            View All
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @forelse($user->auditLogs->take(10) as $log)
                            <div class="activity-item d-flex justify-content-between align-items-start p-3 border-bottom">
                                <div>
                                    <span class="badge bg-{{ $log->action === 'user_created' ? 'success' : 'primary' }} mb-1">
                                        {{ $log->action }}
                                    </span>
                                    <p class="mb-0 small">{{ $log->description ?? 'No description' }}</p>
                                </div>
                                <small class="text-muted">{{ $log->created_at?->format('M d, H:i') ?? 'N/A' }}</small>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                                No activity recorded yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        @if($user->id !== auth()->id() && !$user->isSuperAdmin())
            <div class="row mt-4">
                <div class="col-md-8 offset-md-4">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">Danger Zone</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Once you delete a user, there is no going back. Please be certain.</p>
                            <form action="{{ route('real-estate.users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    <i class="bi bi-trash"></i> Delete User
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

