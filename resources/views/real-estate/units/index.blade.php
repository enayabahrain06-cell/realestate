@extends('layouts.real-estate.dashboard')

@section('title', 'Units')

@section('breadcrumb')
    <li class="breadcrumb-item active">Units</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-grid-3x3 text-success me-2"></i>Units</h1>
                <p class="text-muted small mb-0">Manage all property units</p>
            </div>
            <div class="col-auto d-flex gap-2">
                <a href="{{ route('real-estate.available-units') }}" class="btn btn-outline-success">
                    <i class="bi bi-check-circle me-1"></i> Available Units
                </a>
                <a href="{{ route('real-estate.units.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add Unit
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="building_id" class="form-select select2">
                        <option value="">All Buildings</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                {{ $building->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="rented" {{ request('status') === 'rented' ? 'selected' : '' }}>Rented</option>
                        <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="unit_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="flat" {{ request('unit_type') === 'flat' ? 'selected' : '' }}>Flat</option>
                        <option value="office" {{ request('unit_type') === 'office' ? 'selected' : '' }}>Office</option>
                        <option value="commercial" {{ request('unit_type') === 'commercial' ? 'selected' : '' }}>Commercial</option>
                        <option value="warehouse" {{ request('unit_type') === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                        <option value="parking" {{ request('unit_type') === 'parking' ? 'selected' : '' }}>Parking</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="min_price" class="form-control" placeholder="Min Price"
                           value="{{ request('min_price') }}">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_price" class="form-control" placeholder="Max Price"
                           value="{{ request('max_price') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div class="card mb-4 bulk-actions-bar" style="display: none;">
        <div class="card-body py-2">
            <form action="{{ route('real-estate.units.bulk-status') }}" method="POST" class="d-flex align-items-center gap-3">
                @csrf
                <span class="text-muted"><i class="bi bi-check-square me-1"></i><span class="selected-count">0</span> selected</span>
                <input type="hidden" name="unit_ids" id="selected-unit-ids">
                <select name="status" class="form-select form-select-sm" style="width: auto;" required>
                    <option value="">Change Status To...</option>
                    <option value="available">Available</option>
                    <option value="reserved">Reserved</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="blocked">Blocked</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-check-circle me-1"></i> Apply
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                    Cancel
                </button>
            </form>
        </div>
    </div>

    <!-- Units Cards Grid -->
    @if($units->count() > 0)
        <div class="row g-4">
            @foreach($units as $unit)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card unit-card h-100" onclick="openEditModal({{ $unit->id }})" style="cursor: pointer;">
                        <!-- Unit Image -->
                        <div class="unit-image-container" style="height: 200px; overflow: hidden; background: #f8f9fa;">
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

                        <div class="card-body">
                            <!-- Unit Number & Building -->
                            <h5 class="card-title mb-1">
                                <i class="bi bi-door-closed me-1 text-primary"></i>
                                Unit {{ $unit->unit_number }}
                            </h5>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-building me-1"></i>
                                {{ $unit->building->name }}
                            </p>

                            <!-- Floor Info -->
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-layers me-1"></i>
                                    Floor {{ $unit->floor->floor_number ?? 'N/A' }}
                                </small>
                            </div>

                            <!-- Unit Type -->
                            <div class="mb-2">
                                <span class="badge bg-secondary text-capitalize">
                                    {{ $unit->unit_type }}
                                </span>
                            </div>

                            <!-- Details Grid -->
                            <div class="row g-2 mb-2 small">
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
                            <div class="border-top pt-2 mt-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Rent:</span>
                                    <span class="fw-bold text-success fs-5">
                                        ${{ number_format($unit->rent_amount, 0) }}
                                        <small class="text-muted fs-6">/mo</small>
                                    </span>
                                </div>
                            </div>

                            <!-- View Type -->
                            @if($unit->view)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-eye me-1"></i>
                                        {{ $unit->view }}
                                    </small>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ $unit->created_at->format('M d, Y') }}
                                </small>
                                <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); openEditModal({{ $unit->id }})">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $units->withQueryString()->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-grid-3x3 fs-1"></i>
                </div>
                <h5>No Units Found</h5>
                <p class="text-muted">Add your first unit to get started.</p>
                <a href="{{ route('real-estate.units.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add Unit
                </a>
            </div>
        </div>
    @endif
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

@push('scripts')
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
                const form = doc.querySelector('form');

                if (form) {
                    // Update modal content with form
                    modalContent.innerHTML = form.outerHTML;

                    // Update form action to handle modal submission
                    const modalForm = modalContent.querySelector('form');
                    modalForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        submitModalForm(modalForm, unitId);
                    });

                    // Update unit number in modal title
                    const unitNumber = doc.querySelector('[name="unit_number"]')?.value || unitId;
                    document.getElementById('modal-unit-number').textContent = `#${unitNumber}`;
                } else {
                    modalContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Failed to load unit details.
                        </div>
                    `;
                }
            })
            .catch(error => {
                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading unit: ${error.message}
                    </div>
                `;
            });
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

    function toggleSelectAll() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.unit-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.unit-checkbox:checked');
        const selectedIds = Array.from(checkboxes).map(cb => cb.value);
        const count = selectedIds.length;

        document.querySelector('.selected-count').textContent = count;
        document.getElementById('selected-unit-ids').value = JSON.stringify(selectedIds);

        const bulkBar = document.querySelector('.bulk-actions-bar');
        if (count > 0) {
            bulkBar.style.display = 'block';
        } else {
            bulkBar.style.display = 'none';
        }
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.unit-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }
</script>

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
</style>
@endpush
@endsection

