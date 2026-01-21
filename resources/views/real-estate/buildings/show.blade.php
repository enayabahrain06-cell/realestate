@extends('layouts.real-estate.dashboard')

@section('title', $building->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('real-estate.buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item active">{{ $building->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">
                    @if($building->image)
                        <img src="{{ Storage::url($building->image) }}" alt="{{ $building->name }}"
                             class="rounded me-2" style="height: 40px; width: 40px; object-fit: contain; background-color: #f8f9fa; padding: 2px;">
                    @else
                        <i class="bi bi-building text-primary me-2"></i>
                    @endif
                    {{ $building->name }}
                </h1>
                <p class="text-muted small mb-0">
                    <i class="bi bi-geo-alt me-1"></i> {{ $building->address }}
                </p>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('real-estate.buildings.edit', $building) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('real-estate.buildings.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Property Type</p>
                        <h5 class="mb-0 fw-bold text-capitalize">{{ $building->property_type }}</h5>
                    </div>
                    <div class="stat-icon stat-icon-primary">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Floors</p>
                            <h5 class="mb-0 fw-bold">{{ $building->floors->count() }}</h5>
                        </div>
                        <div class="stat-icon stat-icon-info">
                            <i class="bi bi-layers"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Units</p>
                            <h5 class="mb-0 fw-bold">{{ $stats['total_units'] }}</h5>
                        </div>
                        <div class="stat-icon stat-icon-success">
                            <i class="bi bi-grid-3x3"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Occupancy Rate</p>
                            @php
                                $occupancyRate = $stats['total_units'] > 0
                                    ? round(($stats['rented'] / $stats['total_units']) * 100, 2)
                                    : 0;
                            @endphp
                            <h5 class="mb-0 fw-bold">{{ $occupancyRate }}%</h5>
                        </div>
                        <div class="stat-icon stat-icon-warning">
                            <i class="bi bi-pie-chart"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Unit Status Overview -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable text-center">
                    <i class="bi bi-check-circle text-success fs-2"></i>
                    <h4 class="mt-2 mb-0 fw-bold">{{ $stats['available'] }}</h4>
                    <small class="text-muted">Available Units</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable text-center">
                    <i class="bi bi-key text-primary fs-2"></i>
                    <h4 class="mt-2 mb-0 fw-bold">{{ $stats['rented'] }}</h4>
                    <small class="text-muted">Rented Units</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable text-center">
                    <i class="bi bi-clock-history text-warning fs-2"></i>
                    <h4 class="mt-2 mb-0 fw-bold">{{ $stats['reserved'] }}</h4>
                    <small class="text-muted">Reserved Units</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#floors-section" class="text-decoration-none">
                <div class="stat-card stat-card-clickable text-center">
                    <i class="bi bi-tools text-danger fs-2"></i>
                    <h4 class="mt-2 mb-0 fw-bold">{{ $stats['maintenance'] }}</h4>
                    <small class="text-muted">Maintenance</small>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Floors and Units -->
        <div class="col-lg-8">
            <div class="card" id="floors-section">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-layers text-info me-2"></i>Floors & Units</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('real-estate.floors.bulk-create', $building) }}"
                           class="btn btn-sm btn-success">
                            <i class="bi bi-stack me-1"></i> Bulk Add Floors
                        </a>
                        <a href="{{ route('real-estate.floors.create', ['building_id' => $building->id]) }}"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-plus me-1"></i> Add Single Floor
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($building->floors->sortBy('floor_number') as $floor)
                        <div class="floor-section mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold">
                                    Floor {{ $floor->floor_number }}
                                    <span class="badge bg-secondary ms-2">{{ $floor->units->count() }} Units</span>
                                </h6>
                                <div class="btn-group btn-group-sm">
                                    <button onclick="openBulkUnitModal({{ $floor->id }}, {{ $building->id }})"
                                            class="btn btn-success">
                                        <i class="bi bi-plus-square"></i> Bulk Add Units
                                    </button>
                                    <a href="{{ route('real-estate.units.create', ['building_id' => $building->id, 'floor_id' => $floor->id]) }}"
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-plus"></i> Add Unit
                                    </a>
                                    <a href="{{ route('real-estate.floors.edit', $floor) }}"
                                       class="btn btn-outline-secondary">Edit Floor</a>
                                </div>
                            </div>

                            <div class="row g-3">
                                @forelse($floor->units->sortBy('unit_number') as $unit)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card unit-card h-100" onclick="openEditModal({{ $unit->id }})" style="cursor: pointer;">
                                            <!-- Unit Image -->
                                            <div class="unit-image-container" style="height: 150px; overflow: hidden; background: #f8f9fa;">
                                                @if($unit->image)
                                                    <img src="{{ Storage::url($unit->image) }}" alt="Unit {{ $unit->unit_number }}"
                                                         class="card-img-top" style="height: 100%; width: 100%; object-fit: cover;">
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                        <i class="bi bi-house-door fs-1 text-muted"></i>
                                                    </div>
                                                @endif
                                                <!-- Status Badge -->
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge status-{{ $unit->status }}">
                                                        {{ ucfirst($unit->status) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="card-body p-2">
                                                <!-- Unit Number -->
                                                <h6 class="card-title mb-1">
                                                    <i class="bi bi-door-closed me-1 text-primary"></i>
                                                    Unit {{ $unit->unit_number }}
                                                </h6>

                                                <!-- Unit Type -->
                                                <div class="mb-2">
                                                    <span class="badge bg-secondary text-capitalize small">
                                                        {{ $unit->unit_type }}
                                                    </span>
                                                </div>

                                                <!-- Details Grid -->
                                                <div class="row g-1 mb-2" style="font-size: 0.75rem;">
                                                    <div class="col-6">
                                                        <i class="bi bi-rulers me-1 text-muted"></i>
                                                        <strong>{{ number_format($unit->size_sqft) }}</strong> sqft
                                                    </div>
                                                    <div class="col-6">
                                                        <i class="bi bi-bed me-1 text-muted"></i>
                                                        <strong>{{ $unit->bedrooms ?? 0 }}</strong> Beds
                                                    </div>
                                                    <div class="col-6">
                                                        <i class="bi bi-droplet me-1 text-muted"></i>
                                                        <strong>{{ $unit->bathrooms ?? 0 }}</strong> Baths
                                                    </div>
                                                    <div class="col-6">
                                                        <i class="bi bi-p-square me-1 text-muted"></i>
                                                        <strong>{{ $unit->parking_spaces ?? 0 }}</strong> Parking
                                                    </div>
                                                </div>

                                                <!-- Rent Amount -->
                                                <div class="border-top pt-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-muted small">Rent:</span>
                                                        <span class="fw-bold text-success">
                                                            ${{ number_format($unit->rent_amount, 0) }}
                                                            <small class="text-muted">/mo</small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-footer bg-white border-top-0 p-2">
                                                <button class="btn btn-sm btn-outline-primary w-100" onclick="event.stopPropagation(); openEditModal({{ $unit->id }})">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-3">
                                            <p class="text-muted mb-2">No units on this floor yet.</p>
                                            <a href="{{ route('real-estate.units.create', ['building_id' => $building->id, 'floor_id' => $floor->id]) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-plus me-1"></i> Add Units
                                            </a>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-4">
                        @endif
                    @empty
                        <div class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="bi bi-layers fs-1"></i>
                            </div>
                            <h5>No Floors Added</h5>
                            <p class="text-muted">Start by adding floors to this building.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('real-estate.floors.bulk-create', $building) }}"
                                   class="btn btn-success">
                                    <i class="bi bi-stack me-1"></i> Bulk Add Floors
                                </a>
                                <a href="{{ route('real-estate.floors.create', ['building_id' => $building->id]) }}"
                                   class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Add Single Floor
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Building Details Sidebar -->
        <div class="col-lg-4">
            @if($building->description)
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-text-paragraph text-primary me-2"></i>Description</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $building->description }}</p>
                </div>
            </div>
            @endif

            @if($building->amenities && count($building->amenities) > 0)
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-star text-warning me-2"></i>Amenities</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($building->amenities as $amenity)
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-check text-success me-1"></i> {{ ucfirst($amenity) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-toggle-on text-success me-2"></i>Status</h5>
                </div>
                <div class="card-body">
                    <span class="badge status-{{ $building->status }} fs-6">
                        <i class="bi bi-circle-fill me-1"></i> {{ ucfirst($building->status) }}
                    </span>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('real-estate.units.create', ['building_id' => $building->id]) }}"
                           class="btn btn-outline-primary">
                            <i class="bi bi-plus-square me-1"></i> Add Single Unit
                        </a>
                        <a href="{{ route('real-estate.units.index', ['building_id' => $building->id]) }}"
                           class="btn btn-outline-primary">
                            <i class="bi bi-layers me-1"></i> Bulk Add Units
                        </a>
                        <hr>
                        <form action="{{ route('real-estate.buildings.destroy', $building) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this building? All floors and units will be deleted.')">
                                <i class="bi bi-trash me-1"></i> Delete Building
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Unit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Unit <span id="modal-unit-number"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-content">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Unit Creation Modal -->
<div class="modal fade" id="bulkUnitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-stack me-2"></i>
                    Bulk Add Units to Floor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('real-estate.units.bulk-create') }}" method="POST" id="bulkUnitForm">
                @csrf
                <input type="hidden" name="building_id" id="bulk_building_id">
                <input type="hidden" name="floor_id" id="bulk_floor_id">

                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Create multiple units at once with the same specifications. You can edit individual units later.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="start_number" class="form-label fw-semibold">Start Unit Number *</label>
                            <input type="number" class="form-control" id="start_number" name="start_number"
                                   required min="1" value="1" onchange="calculateBulkUnits()">
                            <small class="text-muted">First unit number in the range</small>
                        </div>

                        <div class="col-md-6">
                            <label for="end_number" class="form-label fw-semibold">End Unit Number *</label>
                            <input type="number" class="form-control" id="end_number" name="end_number"
                                   required min="1" value="10" onchange="calculateBulkUnits()">
                            <small class="text-muted">Last unit number in the range</small>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-calculator me-2"></i>
                                <strong id="unit-count-display">10 units will be created (1 to 10)</strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="unit_type" class="form-label fw-semibold">Unit Type *</label>
                            <select class="form-select" id="unit_type" name="unit_type" required>
                                <option value="flat">Flat</option>
                                <option value="office">Office</option>
                                <option value="commercial">Commercial</option>
                                <option value="warehouse">Warehouse</option>
                                <option value="parking">Parking</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="size_sqft" class="form-label fw-semibold">Size (sqft) *</label>
                            <input type="number" class="form-control" id="size_sqft" name="size_sqft"
                                   required min="1" value="1000">
                        </div>

                        <div class="col-md-6">
                            <label for="rent_amount" class="form-label fw-semibold">Monthly Rent ($) *</label>
                            <input type="number" class="form-control" id="rent_amount" name="rent_amount"
                                   required min="0" step="0.01" value="1000">
                        </div>

                        <div class="col-md-6">
                            <label for="bedrooms" class="form-label fw-semibold">Bedrooms</label>
                            <input type="number" class="form-control" id="bedrooms" name="bedrooms"
                                   min="0" value="2">
                        </div>

                        <div class="col-md-6">
                            <label for="bathrooms" class="form-label fw-semibold">Bathrooms</label>
                            <input type="number" class="form-control" id="bathrooms" name="bathrooms"
                                   min="0" value="1">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Create Units
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .unit-card {
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
    }

    .unit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-color: #0d6efd;
    }

    .unit-image-container {
        position: relative;
    }

    .status-available {
        background-color: #28a745;
        color: white;
    }

    .status-reserved {
        background-color: #ffc107;
        color: #000;
    }

    .status-rented {
        background-color: #17a2b8;
        color: white;
    }

    .status-maintenance {
        background-color: #fd7e14;
        color: white;
    }

    .status-blocked {
        background-color: #dc3545;
        color: white;
    }

    .stat-card-clickable {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .stat-card-clickable:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    /* Smooth scroll */
    html {
        scroll-behavior: smooth;
    }
</style>

<script>
    function openEditModal(unitId) {
        const modal = new bootstrap.Modal(document.getElementById('editUnitModal'));
        const modalContent = document.getElementById('modal-content');

        // Show loading
        modalContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        modal.show();

        // Load edit form via AJAX
        fetch(`/real-estate/units/${unitId}/edit`)
            .then(response => response.text())
            .then(html => {
                // Extract form content from the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Find the card body with the form
                const cardBody = doc.querySelector('.card-body');

                if (cardBody) {
                    // Update modal content with form
                    modalContent.innerHTML = cardBody.innerHTML;

                    // Update form action to handle modal submission
                    const modalForm = modalContent.querySelector('form');
                    if (modalForm) {
                        modalForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            submitModalForm(modalForm, unitId);
                        });
                    }

                    // Update unit number in modal title
                    const unitNumber = doc.querySelector('[name="unit_number"]')?.value || unitId;
                    document.getElementById('modal-unit-number').textContent = `#${unitNumber}`;
                } else {
                    modalContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Failed to load unit details. Please try again.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading unit:', error);
                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading unit: ${error.message}
                    </div>
                `;
            });
    }

    function openBulkUnitModal(floorId, buildingId) {
        const modal = new bootstrap.Modal(document.getElementById('bulkUnitModal'));
        document.getElementById('bulk_floor_id').value = floorId;
        document.getElementById('bulk_building_id').value = buildingId;

        // Reset form
        document.getElementById('bulkUnitForm').reset();
        document.getElementById('bulk_floor_id').value = floorId;
        document.getElementById('bulk_building_id').value = buildingId;

        modal.show();
    }

    function calculateBulkUnits() {
        const startNum = parseInt(document.getElementById('start_number').value) || 0;
        const endNum = parseInt(document.getElementById('end_number').value) || 0;
        const count = endNum >= startNum ? (endNum - startNum + 1) : 0;

        document.getElementById('unit-count-display').textContent =
            count > 0 ? `${count} units will be created (${startNum} to ${endNum})` : 'Enter valid range';
    }

    function submitModalForm(form, unitId) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                // Success - reload page to show updated data
                window.location.reload();
            } else {
                return response.json().then(data => {
                    throw new Error(data.message || 'Failed to update unit');
                });
            }
        })
        .catch(error => {
            // Show error
            alert('Error: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    }

    // Add smooth scroll with offset for fixed headers
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offset = 100; // Adjust based on your header height
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });

                    // Add highlight effect
                    target.classList.add('highlight-section');
                    setTimeout(() => {
                        target.classList.remove('highlight-section');
                    }, 2000);
                }
            });
        });
    });
</script>

<style>
    @keyframes highlight {
        0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0.1); }
        100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }

    .highlight-section {
        animation: highlight 2s ease-out;
    }
</style>
@endsection

