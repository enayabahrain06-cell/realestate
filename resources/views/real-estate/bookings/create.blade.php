@extends('layouts.real-estate.dashboard')

@section('title', 'Create New Booking')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.bookings.index') }}">Bookings</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-calendar-plus text-success me-2"></i>Create New Booking</h1>
                <p class="text-muted small mb-0">Schedule a booking inquiry or viewing</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.bookings.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Bookings
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Booking Information</h5>
                </div>
                <div class="card-body">
                    @if(isset($unit) && $unit)
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Creating booking for unit <strong>#{{ $unit->unit_number }}</strong> in 
                        <strong>{{ $unit->building->name }}</strong>
                        <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                    </div>
                    @endif

                    <form action="{{ route('real-estate.bookings.store') }}" method="POST">
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
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}" 
                                                {{ (isset($unit) && $unit->id == $u->id) || old('unit_id') == $u->id ? 'selected' : '' }}>
                                            #{{ $u->unit_number }} - {{ $u->building->name }} ({{ $u->unit_type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="booking_type" class="form-label fw-semibold">Booking Type *</label>
                                <select class="form-select @error('booking_type') is-invalid @endeo" 
                                        id="booking_type" name="booking_type" required>
                                    <option value="">Select Type</option>
                                    <option value="inquiry" {{ old('booking_type') === 'inquiry' ? 'selected' : '' }}>Inquiry</option>
                                    <option value="viewing" {{ old('booking_type') === 'viewing' ? 'selected' : '' }}>Viewing</option>
                                    <option value="reservation" {{ old('booking_type') === 'reservation' ? 'selected' : '' }}>Reservation</option>
                                    <option value="rental" {{ old('booking_type') === 'rental' ? 'selected' : '' }}>Rental Application</option>
                                </select>
                                @error('booking_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="booking_date" class="form-label fw-semibold">Booking Date *</label>
                                <input type="datetime-local" class="form-control @error('booking_date') is-invalid @endeo" 
                                       id="booking_date" name="booking_date" 
                                       value="{{ old('booking_date', date('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('booking_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @endeo" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Additional notes about this booking">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="bi bi-check-circle me-1"></i> Create Booking
                            </button>
                            <a href="{{ route('real-estate.bookings.index') }}" class="btn btn-outline-secondary">
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
                    <h5 class="mb-0"><i class="bi bi-lightbulb text-warning me-2"></i>Booking Types</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2"><strong>Inquiry</strong> - General questions about the property</li>
                        <li class="mb-2"><strong>Viewing</strong> - Schedule a property tour</li>
                        <li class="mb-2"><strong>Reservation</strong> - Reserve unit for 24 hours</li>
                        <li class="mb-0"><strong>Rental</strong> - Start rental application process</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-info me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.bookings.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-1"></i> View All Bookings
                        </a>
                        <a href="{{ route('real-estate.available-units') }}" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i> Available Units
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

