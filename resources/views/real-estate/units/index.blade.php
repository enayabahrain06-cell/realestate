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

    <!-- Units Table -->
    @if($units->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="select-all" onchange="toggleSelectAll()">
                            </th>
                            <th>Unit</th>
                            <th>Building</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Rent</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $unit)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input unit-checkbox" 
                                           value="{{ $unit->id }}" onchange="updateSelection()">
                                </td>
                                <td>
                                    <div class="fw-semibold">#{{ $unit->unit_number }}</div>
                                    @if($unit->floor)
                                        <small class="text-muted">Floor {{ $unit->floor->floor_number }}</small>
                                    @endif
                                </td>
                                <td>{{ $unit->building->name }}</td>
                                <td class="text-capitalize">{{ $unit->unit_type }}</td>
                                <td>{{ number_format($unit->size_sqft) }} sqft</td>
                                <td class="fw-semibold">${{ number_format($unit->rent_amount, 2) }}</td>
                                <td>
                                    <span class="badge status-{{ $unit->status }}">
                                        {{ ucfirst($unit->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('real-estate.units.show', $unit) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('real-estate.units.edit', $unit) }}" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('real-estate.units.destroy', $unit) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="return confirm('Delete this unit?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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

@push('scripts')
<script>
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
@endpush
@endsection

