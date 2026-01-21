@extends('layouts.real-estate.dashboard')

@section('title', $agent->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('agents.index') }}">Agents</a></li>
<li class="breadcrumb-item active">{{ $agent->name }}</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Header Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle p-3 me-3">
                    <i class="bi bi-person-fill text-white fs-2"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $agent->name }}</h3>
                    @if($agent->license_number)
                        <small class="text-muted">License: {{ $agent->license_number }}</small>
                    @endif
                </div>
            </div>
            <div class="btn-group">
                <a href="{{ route('agents.edit', $agent) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('agents.destroy', $agent) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                        <i class="bi bi-trash"></i> Deactivate
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Agent Details -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Email</span>
                                <strong>{{ $agent->email ?: '-' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Phone</span>
                                <strong>{{ $agent->phone ?: '-' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Commission Rate</span>
                                <strong>{{ $agent->commission_rate ? $agent->commission_rate . '%' : '-' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Status</span>
                                @if($agent->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Joined</span>
                                <strong>{{ $agent->created_at->format('M d, Y') }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Performance Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Performance ({{ ucfirst($period) }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <h4 class="text-success mb-0">{{ number_format($performance['summary']['total_commissions_earned'] ?? 0, 2) }}</h4>
                                <small class="text-muted">Total Earned</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-primary mb-0">{{ $performance['summary']['total_deals'] ?? 0 }}</h4>
                                <small class="text-muted">Deals Closed</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <h5 class="text-warning mb-0">{{ number_format($performance['summary']['pending_commissions'] ?? 0, 2) }}</h5>
                                <small class="text-muted">Pending</small>
                            </div>
                            <div class="col-6">
                                <h5 class="text-info mb-0">{{ number_format($performance['summary']['average_commission_per_deal'] ?? 0, 2) }}</h5>
                                <small class="text-muted">Avg. Commission</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                @if($agent->address)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Address</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $agent->address }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Performance Details -->
            <div class="col-md-8">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="agentTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="commissions-tab" data-bs-toggle="tab" data-bs-target="#commissions" type="button">
                            Pending Commissions
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="units-tab" data-bs-toggle="tab" data-bs-target="#units" type="button">
                            Assigned Units
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button">
                            Performance Details
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="agentTabsContent">
                    <!-- Pending Commissions -->
                    <div class="tab-pane fade show active" id="commissions" role="tabpanel">
                        @forelse($pendingCommissions as $commission)
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $commission->lease->unit->building->name ?? 'N/A' }} - Unit {{ $commission->lease->unit->unit_number ?? 'N/A' }}</h6>
                                        <small class="text-muted">
                                            Deal: AED {{ number_format($commission->deal_amount) }} | 
                                            Commission: AED {{ number_format($commission->commission_amount) }}
                                        </small>
                                    </div>
                                    <div>
                                        @switch($commission->status)
                                            @case('pending')<span class="badge bg-warning">Pending</span>@break
                                            @case('approved')<span class="badge bg-info">Approved</span>@break
                                            @case('paid')<span class="badge bg-success">Paid</span>@break
                                        @endswitch
                                        <small class="text-muted d-block">Due: {{ $commission->due_date->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info">No pending commissions</div>
                        @endforelse
                    </div>

                    <!-- Assigned Units -->
                    <div class="tab-pane fade" id="units" role="tabpanel">
                        @forelse($assignedUnits as $assignment)
                        <div class="card mb-2">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $assignment->unit->building->name ?? 'N/A' }} - Unit {{ $assignment->unit->unit_number ?? 'N/A' }}</h6>
                                    <small class="text-muted">{{ ucfirst($assignment->type) }} assignment</small>
                                </div>
                                <form action="{{ route('agents.remove-unit-assignment', [$agent, $assignment->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove assignment?')">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info">No units assigned</div>
                        @endforelse

                        <!-- Add Unit Form -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <form action="{{ route('agents.assign-unit', $agent) }}" method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <select class="form-select" name="unit_id" required>
                                            <option value="">Select Unit</option>
                                            <!-- Would be populated with available units -->
                                        </select>
                                        <button type="submit" class="btn btn-primary">Assign Unit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Details -->
                    <div class="tab-pane fade" id="performance" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h6>Performance Breakdown</h6>
                                @if(!empty($performance['by_property_type']))
                                    @foreach($performance['by_property_type'] as $type => $data)
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span>{{ ucfirst($type ?? 'N/A') }}</span>
                                        <div>
                                            <span class="me-3">{{ $data['count'] }} deals</span>
                                            <strong>AED {{ number_format($data['commission']) }}</strong>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No performance data available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

