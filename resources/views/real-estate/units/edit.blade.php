@extends('layouts.real-estate.dashboard')

@section('title', 'Edit Unit #' . $unit->unit_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.units.index') }}">Units</a></li>
    <li class="breadcrumb-item"><a href="{{ route('real-estate.units.show', $unit) }}">#{{ $unit->unit_number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Unit #{{ $unit->unit_number }}</h1>
                <p class="text-muted small mb-0">Update unit information</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.units.show', $unit) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Unit
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Unit Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('real-estate.units.update', $unit) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="building_id" class="form-label fw-semibold">Building *</label>
                                <select class="form-select @error('building_id') is-invalid @endeo" 
                                        id="building_id" name="building_id" required>
                                    <option value="">Select Building</option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}" 
                                                {{ $building->id == $unit->building_id ? 'selected' : '' }}>
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('building_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="floor_id" class="form-label fw-semibold">Floor *</label>
                                <select class="form-select @error('floor_id') is-invalid @endeo" 
                                        id="floor_id" name="floor_id" required>
                                    <option value="">Select Floor</option>
                                    @foreach($floors as $floor)
                                        <option value="{{ $floor->id }}" 
                                                {{ $floor->id == $unit->floor_id ? 'selected' : '' }}>
                                            Floor {{ $floor->floor_number }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('floor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="unit_number" class="form-label fw-semibold">Unit Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="text" class="form-control @error('unit_number') is-invalid @endeo" 
                                           id="unit_number" name="unit_number" value="{{ old('unit_number', $unit->unit_number) }}" 
                                           required placeholder="e.g., 101">
                                </div>
                                @error('unit_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="unit_type" class="form-label fw-semibold">Unit Type *</label>
                                <select class="form-select @error('unit_type') is-invalid @endeo" 
                                        id="unit_type" name="unit_type" required>
                                    <option value="">Select Type</option>
                                    <option value="flat" {{ old('unit_type', $unit->unit_type) === 'flat' ? 'selected' : '' }}>Flat</option>
                                    <option value="office" {{ old('unit_type', $unit->unit_type) === 'office' ? 'selected' : '' }}>Office</option>
                                    <option value="commercial" {{ old('unit_type', $unit->unit_type) === 'commercial' ? 'selected' : '' }}>Commercial</option>
                                    <option value="warehouse" {{ old('unit_type', $unit->unit_type) === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                                    <option value="parking" {{ old('unit_type', $unit->unit_type) === 'parking' ? 'selected' : '' }}>Parking</option>
                                </select>
                                @error('unit_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="size_sqft" class="form-label fw-semibold">Size (sqft) *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-fullscreen"></i></span>
                                    <input type="number" class="form-control @error('size_sqft') is-invalid @endeo" 
                                           id="size_sqft" name="size_sqft" value="{{ old('size_sqft', $unit->size_sqft) }}" 
                                           min="1" required>
                                </div>
                                @error('size_sqft')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="rent_amount" class="form-label fw-semibold">Monthly Rent ($) *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" class="form-control @error('rent_amount') is-invalid @endeo" 
                                           id="rent_amount" name="rent_amount" value="{{ old('rent_amount', $unit->rent_amount) }}" 
                                           min="0" step="0.01" required>
                                </div>
                                @error('rent_amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="deposit_amount" class="form-label fw-semibold">Deposit Amount ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" class="form-control @error('deposit_amount') is-invalid @endeo" 
                                           id="deposit_amount" name="deposit_amount" value="{{ old('deposit_amount', $unit->deposit_amount) }}" 
                                           min="0" step="0.01">
                                </div>
                                @error('deposit_amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Status *</label>
                                <select class="form-select @error('status') is-invalid @endeo" 
                                        id="status" name="status" required>
                                    <option value="available" {{ old('status', $unit->status) === 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="reserved" {{ old('status', $unit->status) === 'reserved' ? 'selected' : '' }}>Reserved</option>
                                    <option value="rented" {{ old('status', $unit->status) === 'rented' ? 'selected' : '' }}>Rented</option>
                                    <option value="maintenance" {{ old('status', $unit->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="blocked" {{ old('status', $unit->status) === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="bedrooms" class="form-label fw-semibold">Bedrooms</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-door-closed"></i></span>
                                    <input type="number" class="form-control @error('bedrooms') is-invalid @endeo" 
                                           id="bedrooms" name="bedrooms" value="{{ old('bedrooms', $unit->bedrooms) }}" 
                                           min="0">
                                </div>
                                @error('bedrooms')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="bathrooms" class="form-label fw-semibold">Bathrooms</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-droplet"></i></span>
                                    <input type="number" class="form-control @error('bathrooms') is-invalid @endeo" 
                                           id="bathrooms" name="bathrooms" value="{{ old('bathrooms', $unit->bathrooms) }}" 
                                           min="0">
                                </div>
                                @error('bathrooms')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @endeo" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Describe this unit">{{ old('description', $unit->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-warning text-white me-2">
                                <i class="bi bi-check-circle me-1"></i> Update Unit
                            </button>
                            <a href="{{ route('real-estate.units.show', $unit) }}" class="btn btn-outline-secondary">
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
                    <h5 class="mb-0"><i class="bi bi-info-circle text-info me-2"></i>Current Lease</h5>
                </div>
                <div class="card-body">
                    @if($unit->activeLease)
                        <h5>{{ $unit->activeLease->tenant->full_name }}</h5>
                        <p class="text-muted small mb-2">
                            ${{ number_format($unit->activeLease->rent_amount, 2) }}/mo<br>
                            Until {{ $unit->activeLease->end_date->format('M d, Y') }}
                        </p>
                    @else
                        <p class="text-muted mb-0">No active lease.</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-info me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.units.show', $unit) }}" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> View Unit
                        </a>
                        <a href="{{ route('real-estate.units.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list me-1"></i> All Units
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

