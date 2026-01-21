@extends('layouts.real-estate.dashboard')

@section('title', 'My Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">My Profile</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-person-circle text-primary me-2"></i>
                    My Profile
                </h1>
                <p class="text-muted small mb-0">View and manage your profile information</p>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('real-estate.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person text-primary me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Full Name</label>
                            <p class="fw-bold mb-0">{{ $user->full_name ?? $user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Email</label>
                            <p class="fw-bold mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Mobile</label>
                            <p class="fw-bold mb-0">{{ $user->mobile ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Gender</label>
                            <p class="fw-bold mb-0 text-capitalize">{{ $user->gender ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Birthdate</label>
                            <p class="fw-bold mb-0">{{ $user->birthdate ? $user->birthdate->format('F j, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Age</label>
                            <p class="fw-bold mb-0">{{ $user->age ?? 'N/A' }} years old</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Blood Type</label>
                            <p class="fw-bold mb-0">{{ $user->blood_type ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nationality</label>
                            <p class="fw-bold mb-0">{{ $user->nationality ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Guardian Information -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-shield-check text-success me-2"></i>Guardian</h5>
                </div>
                <div class="card-body">
                    @if($relationship)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-person-check fs-4 text-primary"></i>
                            </div>
                            <div>
                                <p class="fw-bold mb-0">{{ $relationship->guardian->full_name ?? $relationship->guardian->name }}</p>
                                <small class="text-muted">{{ $relationship->guardian->email }}</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Relationship Type</span>
                            <span class="fw-bold">{{ ucfirst($relationship->relationship_type) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Billing Contact</span>
                            <span class="fw-bold">
                                @if($relationship->is_billing_contact)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </span>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-shield-exclamation fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">No guardian assigned</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Additional Info -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle text-info me-2"></i>Additional Details</h5>
                </div>
                <div class="card-body">
                    @if($user->horoscope)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Horoscope</span>
                        <span class="fw-bold">{{ $user->horoscope }}</span>
                    </div>
                    @endif
                    @if($user->lifeStage)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Life Stage</span>
                        <span class="fw-bold">{{ $user->lifeStage }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Member Since</span>
                        <span class="fw-bold">{{ $user->created_at->format('F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit Profile
                        </a>
                        <a href="{{ route('family.dashboard') }}" class="btn btn-outline-primary">
                            <i class="bi bi-people me-1"></i> Family Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

