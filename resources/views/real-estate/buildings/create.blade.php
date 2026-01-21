@extends('layouts.real-estate.dashboard')

@section('title', 'Add New Building')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-building-add text-primary me-2"></i>Add New Building</h1>
                <p class="text-muted small mb-0">Register a new property or building</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.buildings.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Buildings
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Building Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('real-estate.buildings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Building Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="Enter building name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="property_type" class="form-label fw-semibold">Property Type *</label>
                                <select class="form-select @error('property_type') is-invalid @enderror" 
                                        id="property_type" name="property_type" required>
                                    <option value="">Select Property Type</option>
                                    <option value="residential" {{ old('property_type') === 'residential' ? 'selected' : '' }}>Residential</option>
                                    <option value="commercial" {{ old('property_type') === 'commercial' ? 'selected' : '' }}>Commercial</option>
                                    <option value="mixed-use" {{ old('property_type') === 'mixed-use' ? 'selected' : '' }}>Mixed-Use</option>
                                    <option value="warehouse" {{ old('property_type') === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                                    <option value="parking" {{ old('property_type') === 'parking' ? 'selected' : '' }}>Parking</option>
                                </select>
                                @error('property_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Address *</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="2" required
                                          placeholder="Enter full address">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="total_floors" class="form-label fw-semibold">Total Floors *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-layers"></i></span>
                                    <input type="number" class="form-control @error('total_floors') is-invalid @enderror" 
                                           id="total_floors" name="total_floors" value="{{ old('total_floors', 1) }}" 
                                           min="1" required>
                                </div>
                                @error('total_floors')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="latitude" class="form-label fw-semibold">Latitude</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="number" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude') }}" 
                                           step="any" placeholder="e.g., 40.7128" min="-90" max="90">
                                </div>
                                @error('latitude')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="longitude" class="form-label fw-semibold">Longitude</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="number" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude') }}" 
                                           step="any" placeholder="e.g., -74.0060" min="-180" max="180">
                                </div>
                                @error('longitude')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="Describe the building, amenities, features, etc.">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="image" class="form-label fw-semibold">Building Image</label>
                                <div class="image-upload-wrapper">
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*"
                                           onchange="previewImage(event, 'image-preview')">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="image-preview mt-2" id="image-preview" style="display: none;">
                                        <img id="preview-img" src="#" alt="Building Preview" 
                                             class="img-thumbnail" style="max-height: 200px; max-width: 100%;">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" 
                                                onclick="clearImage('image-preview', 'image')">
                                            <i class="bi bi-x-circle me-1"></i> Remove Image
                                        </button>
                                    </div>
                                    <div class="form-text small">
                                        <i class="bi bi-info-circle me-1"></i> Allowed types: JPEG, PNG, JPG, GIF, WebP. Max size: 2MB
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Amenities</label>
                                <div class="row g-2">
                                    @php $amenitiesList = ['parking', 'elevator', 'security', 'pool', 'gym', 'garden', 'playground', 'laundry', 'wifi', 'ac']; @endphp
                                    @foreach($amenitiesList as $amenity)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="amenities[]" value="{{ $amenity }}"
                                                       id="amenity_{{ $amenity }}"
                                                       {{ is_array(old('amenities')) && in_array($amenity, old('amenities')) ? 'checked' : '' }}>
                                                <label class="form-check-label text-capitalize" for="amenity_{{ $amenity }}">
                                                    {{ $amenity }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-check-circle me-1"></i> Create Building
                            </button>
                            <a href="{{ route('real-estate.buildings.index') }}" class="btn btn-outline-secondary">
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
                        <li class="mb-2">Add accurate location coordinates for map features.</li>
                        <li class="mb-2">Select appropriate property type for better management.</li>
                        <li class="mb-2">List all amenities to attract potential tenants.</li>
                        <li class="mb-2">You can add floors and units after creating the building.</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-info me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.buildings.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list me-1"></i> View All Buildings
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

@push('scripts')
<script>
    function previewImage(event, previewId) {
        const input = event.target;
        const preview = document.getElementById(previewId);
        const previewImg = preview.querySelector('#preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function clearImage(previewId, inputId) {
        const preview = document.getElementById(previewId);
        const input = document.getElementById(inputId);
        
        preview.style.display = 'none';
        previewImg.src = '#';
        input.value = '';
    }
</script>
@endpush
@endsection

