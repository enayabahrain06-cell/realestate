@extends('layouts.real-estate.dashboard')

@section('title', 'Bookings')

@section('breadcrumb')
    <li class="breadcrumb-item active">Bookings</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-calendar-check text-success me-2"></i>Bookings</h1>
                <p class="text-muted small mb-0">Manage property viewings, inquiries, and reservations</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('real-estate.bookings.create') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-plus me-1"></i> New Booking
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="booking_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="inquiry" {{ request('booking_type') === 'inquiry' ? 'selected' : '' }}>Inquiry</option>
                        <option value="viewing" {{ request('booking_type') === 'viewing' ? 'selected' : '' }}>Viewing</option>
                        <option value="reservation" {{ request('booking_type') === 'reservation' ? 'selected' : '' }}>Reservation</option>
                        <option value="rental" {{ request('booking_type') === 'rental' ? 'selected' : '' }}>Rental</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="unit_id" class="form-select select2">
                        <option value="">All Units</option>
                        @foreach(\App\Models\Unit::with('building')->get() as $unit)
                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->building->name }} - Unit {{ $unit->unit_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    @if($bookings->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tenant</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $booking->tenant->full_name }}</div>
                                    <small class="text-muted">{{ $booking->tenant->phone }}</small>
                                </td>
                                <td>
                                    <div>{{ $booking->unit->building->name }}</div>
                                    <small class="text-muted">Unit {{ $booking->unit->unit_number }}</small>
                                </td>
                                <td class="text-capitalize">{{ $booking->booking_type }}</td>
                                <td>{{ $booking->booking_date->format('M d, Y h:i A') }}</td>
                                <td>
                                    <span class="badge status-{{ $booking->status }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('real-estate.bookings.show', $booking) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($booking->status === 'pending')
                                            <form action="{{ route('real-estate.bookings.confirm', $booking) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Confirm">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if(in_array($booking->status, ['pending', 'confirmed']))
                                            <form action="{{ route('real-estate.bookings.cancel', $booking) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning" title="Cancel"
                                                        onclick="return confirm('Cancel this booking?')">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('real-estate.bookings.destroy', $booking) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    onclick="return confirm('Delete this booking?')">
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
            {{ $bookings->withQueryString()->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-calendar-event fs-1"></i>
                </div>
                <h5>No Bookings Found</h5>
                <p class="text-muted">Schedule a new booking for a property.</p>
                <a href="{{ route('real-estate.bookings.create') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-plus me-1"></i> New Booking
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

