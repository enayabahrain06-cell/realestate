@extends('layouts.real-estate.dashboard')

@section('title', 'Create New Lease')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.leases.index') }}">Leases</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-file-earmark-text text-primary me-2"></i>Create New Lease</h1>
                <p class="text-muted small mb-0">Create a new lease agreement</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.leases.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Leases
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Lease Information</h5>
                </div>
                <div class="card-body">
                    @if(isset($unit) && $unit)
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Creating lease for unit <strong>#{{ $unit->unit_number }}</strong> in 
                        <strong>{{ $unit->building->name }}</strong> (Floor {{ $unit->floor->floor_number }})
                        <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                    </div>
                    @endif

                    <form action="{{ route('real-estate.leases.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tenant_id" class="form-label fw-semibold">Tenant *</label>
                                <select class="form-select @error('tenant_id') is-invalid @endeo" 
                                        id="tenant_id" name="tenant_id" required>
                                    <option value="">Select Tenant</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" 
                                                {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->full_name }} ({{ $tenant->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="unit_id" class="form-label fw-semibold">Unit *</label>
                                <select class="form-select @error('unit_id') is-invalid @endeo" 
                                        id="unit_id" name="unit_id" required>
                                    <option value="">Select Unit</option>
                                    @foreach($buildings as $building)
                                        @if($building->units->count() > 0)
                                            <optgroup label="{{ $building->name }}">
                                                @foreach($building->units->where('status', 'available') as $u)
                                                    <option value="{{ $u->id }}" 
                                                            {{ (isset($unit) && $unit->id == $u->id) || old('unit_id') == $u->id ? 'selected' : '' }}>
                                                        #{{ $u->unit_number }} - {{ $u->unit_type }} (${{ number_format($u->rent_amount, 2) }}/mo)
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="lease_type" class="form-label fw-semibold">Lease Type *</label>
                                <select class="form-select @error('lease_type') is-invalid @endeo" 
                                        id="lease_type" name="lease_type" required>
                                    <option value="">Select Type</option>
                                    <option value="single_unit" {{ old('lease_type') === 'single_unit' ? 'selected' : '' }}>Single Unit</option>
                                    <option value="full_floor" {{ old('lease_type') === 'full_floor' ? 'selected' : '' }}>Full Floor</option>
                                    <option value="full_building" {{ old('lease_type') === 'full_building' ? 'selected' : '' }}>Full Building</option>
                                </select>
                                @error('lease_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_frequency" class="form-label fw-semibold">Payment Frequency *</label>
                                <select class="form-select @error('payment_frequency') is-invalid @endeo" 
                                        id="payment_frequency" name="payment_frequency" required>
                                    <option value="">Select Frequency</option>
                                    <option value="monthly" {{ old('payment_frequency', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('payment_frequency') === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="annually" {{ old('payment_frequency') === 'annually' ? 'selected' : '' }}>Annually</option>
                                </select>
                                @error('payment_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-semibold">Start Date *</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @endeo" 
                                       id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" 
                                       required>
                                @error('start_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-semibold">End Date *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @endeo" 
                                       id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+1 year'))) }}" 
                                       required>
                                @error('end_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="rent_amount" class="form-label fw-semibold">Monthly Rent ($) *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" class="form-control @error('rent_amount') is-invalid @endeo" 
                                           id="rent_amount" name="rent_amount" value="{{ old('rent_amount', 1000) }}" 
                                           min="0" step="0.01" required placeholder="e.g., 1500.00">
                                </div>
                                @error('rent_amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="deposit_amount" class="form-label fw-semibold">Deposit Amount ($) *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" class="form-control @error('deposit_amount') is-invalid @endeo" 
                                           id="deposit_amount" name="deposit_amount" value="{{ old('deposit_amount', 1000) }}" 
                                           min="0" step="0.01" required placeholder="e.g., 2000.00">
                                </div>
                                @error('deposit_amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="late_payment_fee" class="form-label fw-semibold">Late Payment Fee ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" class="form-control @error('late_payment_fee') is-invalid @endeo" 
                                           id="late_payment_fee" name="late_payment_fee" value="{{ old('late_payment_fee', 50) }}" 
                                           min="0" step="0.01" placeholder="e.g., 50.00">
                                </div>
                                @error('late_payment_fee')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Status *</label>
                                <select class="form-select @error('status') is-invalid @endeo" 
                                        id="status" name="status" required>
                                    <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="expired" {{ old('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="terminated" {{ old('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="terms" class="form-label fw-semibold">Terms & Conditions</label>
                                <textarea class="form-control @error('terms') is-invalid @endeo" 
                                          id="terms" name="terms" rows="4" 
                                          placeholder="Enter lease terms and conditions">{{ old('terms') }}</textarea>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-check-circle me-1"></i> Create Lease
                            </button>
                            <a href="{{ route('real-estate.leases.index') }}" class="btn btn-outline-secondary">
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
                        <li class="mb-2">Select an active tenant for the lease.</li>
                        <li class="mb-2">Ensure the unit is available before creating lease.</li>
                        <li class="mb-2">End date must be after start date.</li>
                        <li class="mb-2">Active status will immediately rent the unit.</li>
                        <li class="mb-2">Add late fees to encourage on-time payments.</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-info me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.leases.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-1"></i> View All Leases
                        </a>
                        <a href="{{ route('real-estate.tenants.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus me-1"></i> Add Tenant
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

