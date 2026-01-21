@extends('layouts.real-estate.dashboard')

@section('title', 'Leads')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Leads</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $pipelineStats['total'] ?? 0 }}</h4>
                                <p class="mb-0">Total Leads</p>
                            </div>
                            <i class="bi bi-people-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $pipelineStats['by_stage']['new'] ?? 0 }}</h4>
                                <p class="mb-0">New</p>
                            </div>
                            <i class="bi bi-star-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $pipelineStats['by_stage']['negotiation'] ?? 0 }}</h4>
                                <p class="mb-0">Negotiation</p>
                            </div>
                            <i class="bi bi-chat-dots-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ number_format($pipelineStats['conversion_rate'] ?? 0, 1) }}%</h4>
                                <p class="mb-0">Conversion Rate</p>
                            </div>
                            <i class="bi bi-graph-up-arrow fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leads Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Leads Pipeline</h5>
                    <a href="{{ route('leads.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Lead
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search leads..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Stages</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="viewing" {{ request('status') == 'viewing' ? 'selected' : '' }}>Viewing</option>
                            <option value="negotiation" {{ request('status') == 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="source" class="form-select">
                            <option value="">All Sources</option>
                            <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website</option>
                            <option value="referral" {{ request('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                            <option value="social" {{ request('source') == 'social' ? 'selected' : '' }}>Social</option>
                            <option value="ads" {{ request('source') == 'ads' ? 'selected' : '' }}>Ads</option>
                            <option value="walkin" {{ request('source') == 'walkin' ? 'selected' : '' }}>Walk-in</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="agent_id" class="form-select">
                            <option value="">All Agents</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>

                <!-- Pipeline Stages -->
                <div class="row mb-4">
                    @foreach(['new' => 'Primary', 'contacted' => 'Contacted', 'viewing' => 'Viewing', 'negotiation' => 'Negotiation'] as $stage => $label)
                    <div class="col-md-3">
                        <div class="pipeline-stage" data-stage="{{ $stage }}">
                            <div class="stage-header bg-light p-2 rounded mb-2">
                                <strong>{{ $label }}</strong>
                                <span class="badge bg-secondary float-end">{{ $pipelineStats['by_stage'][$stage] ?? 0 }}</span>
                            </div>
                            <div class="stage-leads" style="min-height: 100px;">
                                @foreach($leads->where('status', $stage)->take(5) as $lead)
                                <div class="card mb-2 cursor-pointer" onclick="location.href='{{ route('leads.show', $lead) }}'">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1">{{ $lead->name }}</h6>
                                        <p class="card-text small text-muted mb-1">
                                            @if($lead->phone){{ $lead->phone }}@endif
                                        </p>
                                        @if($lead->assignedAgent)
                                        <small class="text-primary">{{ $lead->assignedAgent->name }}</small>
                                        @else
                                        <small class="text-warning">Unassigned</small>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Leads Table -->
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Assigned Agent</th>
                            <th>Budget</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                        <tr>
                            <td>
                                <a href="{{ route('leads.show', $lead) }}" class="text-decoration-none">
                                    {{ $lead->name }}
                                </a>
                            </td>
                            <td>
                                <small>{{ $lead->email ?: '-' }}</small><br>
                                <small class="text-muted">{{ $lead->phone ?: '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($lead->source) }}</span>
                            </td>
                            <td>
                                @switch($lead->status)
                                    @case('new')<span class="badge bg-primary">New</span>@break
                                    @case('contacted')<span class="badge bg-info">Contacted</span>@break
                                    @case('viewing')<span class="badge bg-warning">Viewing</span>@break
                                    @case('negotiation')<span class="badge bg-warning text-dark">Negotiation</span>@break
                                    @case('closed')<span class="badge bg-success">Closed</span>@break
                                    @case('lost')<span class="badge bg-danger">Lost</span>@break
                                @endswitch
                            </td>
                            <td>
                                @if($lead->assignedAgent)
                                    {{ $lead->assignedAgent->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->budget_min || $lead->budget_max)
                                    {{ number_format($lead->budget_min) }} - {{ number_format($lead->budget_max) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $lead->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">No leads found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $leads->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

