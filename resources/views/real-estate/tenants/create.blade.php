@extends('layouts.real-estate.dashboard')

@section('title', 'Add New Tenant')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.tenants.index') }}">Tenants</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-person-plus text-info me-2"></i>Add New Tenant</h1>
                <p class="text-muted small mb-0">Register a new tenant</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.tenants.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Tenants
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Tenant Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('real-estate.tenants.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label fw-semibold">Full Name *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control @error('full_name') is-invalid @endeo" 
                                           id="full_name" name="full_name" value="{{ old('full_name') }}" 
                                           required placeholder="Enter full name">
                                </div>
                                @error('full_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @endeo" 
                                           id="email" name="email" value="{{ old('email') }}" 
                                           required placeholder="email@example.com">
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                    <input type="text" class="form-control @error('phone') is-invalid @endeo" 
                                           id="phone" name="phone" value="{{ old('phone') }}" 
                                           required placeholder="+1234567890">
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Status *</label>
                                <select class="form-select @error('status') is-invalid @endeo" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="blacklisted" {{ old('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="id_type" class="form-label fw-semibold">ID Type</label>
                                <select class="form-select @error('id_type') is-invalid @endeo" 
                                        id="id_type" name="id_type">
                                    <option value="">Select ID Type</option>
                                    <option value="passport" {{ old('id_type') === 'passport' ? 'selected' : '' }}>Passport</option>
                                    <option value="national_id" {{ old('id_type') === 'national_id' ? 'selected' : '' }}>National ID</option>
                                    <option value="drivers_license" {{ old('id_type') === 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                                    <option value="other" {{ old('id_type') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('id_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="id_number" class="form-label fw-semibold">ID Number</label>
                                <input type="text" class="form-control @error('id_number') is-invalid @endeo" 
                                       id="id_number" name="id_number" value="{{ old('id_number') }}" 
                                       placeholder="Enter ID number">
                                @error('id_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Address</label>
                                <textarea class="form-control @error('address') is-invalid @endeo" 
                                          id="address" name="address" rows="2" 
                                          placeholder="Enter full address">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="employer" class="form-label fw-semibold">Employer</label>
                                <input type="text" class="form-control @error('employer') is-invalid @endeo" 
                                       id="employer" name="employer" value="{{ old('employer') }}" 
                                       placeholder="Enter employer name">
                                @error('employer')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="monthly_income" class="form-label fw-semibold">Monthly Income ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" class="form-control @error('monthly_income') is-invalid @endeo" 
                                           id="monthly_income" name="monthly_income" value="{{ old('monthly_income') }}" 
                                           min="0" step="0.01" placeholder="e.g., 5000.00">
                                </div>
                                @error('monthly_income')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="emergency_contact_name" class="form-label fw-semibold">Emergency Contact Name</label>
                                <input type="text" class="form-control @error('emergency_contact_name') is-invalid @endeo" 
                                       id="emergency_contact_name" name="emergency_contact_name" 
                                       value="{{ old('emergency_contact_name') }}" 
                                       placeholder="Enter contact name">
                                @error('emergency_contact_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="emergency_contact_phone" class="form-label fw-semibold">Emergency Contact Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                    <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @endeo" 
                                           id="emergency_contact_phone" name="emergency_contact_phone" 
                                           value="{{ old('emergency_contact_phone') }}" 
                                           placeholder="+1234567890">
                                </div>
                                @error('emergency_contact_phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @endeo" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Additional notes about this tenant">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-info text-white me-2">
                                <i class="bi bi-check-circle me-1"></i> Create Tenant
                            </button>
                            <a href="{{ route('real-estate.tenants.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightbulb text-warning me-2"></i>Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Enter accurate contact information for communication.</li>
                        <li class="mb-2">Verify ID documents before registration.</li>
                        <li class="mb-2">Income information helps with lease approvals.</li>
                        <li class="mb-2">Emergency contact is required for safety.</li>
                        <li class="mb-2">Blacklisted tenants cannot rent units.</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-info me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.tenants.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-1"></i> View All Tenants
                        </a>
                        <a href="{{ route('real-estate.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

