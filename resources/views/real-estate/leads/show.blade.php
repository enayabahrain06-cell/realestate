@extends('layouts.real-estate.dashboard')

@section('title', $lead->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
<li class="breadcrumb-item active">{{ $lead->name }}</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Header Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>{{ $lead->name }}</h3>
            <div class="btn-group">
                @if(!in_array($lead->status, ['closed', 'lost']))
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#convertModal">
                        <i class="bi bi-person-plus"></i> Convert to Tenant
                    </button>
                @endif
                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Lead Details -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lead Details</h5>
                        @switch($lead->status)
                            @case('new')<span class="badge bg-primary">New</span>@break
                            @case('contacted')<span class="badge bg-info">Contacted</span>@break
                            @case('viewing')<span class="badge bg-warning">Viewing</span>@break
                            @case('negotiation')<span class="badge bg-warning text-dark">Negotiation</span>@break
                            @case('closed')<span class="badge bg-success">Closed</span>@break
                            @case('lost')<span class="badge bg-danger">Lost</span>@break
                        @endswitch
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Email</span>
                                <strong>{{ $lead->email ?: '-' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Phone</span>
                                <strong>{{ $lead->phone ?: '-' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Source</span>
                                <strong>{{ ucfirst($lead->source) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Budget</span>
                                <strong>
                                    @if($lead->budget_min || $lead->budget_max)
                                        AED {{ number_format($lead->budget_min) }} - {{ number_format($lead->budget_max) }}
                                    @else
                                        -
                                    @endif
                                </strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Preferred Location</span>
                                <strong>{{ $lead->preferred_location ?: '-' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Unit Type</span>
                                <strong>{{ ucfirst($lead->preferred_unit_type) ?: '-' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Bedrooms</span>
                                <strong>{{ $lead->bedrooms ?: '-' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Created</span>
                                <strong>{{ $lead->created_at->format('M d, Y') }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Assigned Agent -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Assigned Agent</h5>
                    </div>
                    <div class="card-body">
                        @if($lead->assignedAgent)
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle p-3 me-3">
                                    <i class="bi bi-person-fill text-white fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $lead->assignedAgent->name }}</h6>
                                    <small class="text-muted">{{ $lead->assignedAgent->email }}</small>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-3">No agent assigned</p>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignAgentModal">
                                Assign Agent
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                @if($lead->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Notes</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $lead->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Pipeline & Actions -->
            <div class="col-md-8">
                <!-- Pipeline Progress -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Pipeline Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-3" style="height: 30px;">
                            @php
                                $stages = ['new' => 20, 'contacted' => 40, 'viewing' => 60, 'negotiation' => 80, 'closed' => 100];
                                $currentStage = $lead->status;
                                $progress = $stages[$currentStage] ?? 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%">
                                {{ ucfirst($currentStage) }}
                            </div>
                        </div>

                        @if(!in_array($lead->status, ['closed', 'lost']))
                            <div class="btn-group">
                                @foreach(['new', 'contacted', 'viewing', 'negotiation'] as $stage)
                                    @if($stage != $lead->status)
                                        <form action="{{ route('leads.update-status', $lead) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $stage }}">
                                            <button type="submit" class="btn btn-outline-{{ $stage == 'negotiation' ? 'warning' : 'secondary' }}">
                                                Move to {{ ucfirst($stage) }}
                                            </button>
                                        </form>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="leadTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="interactions-tab" data-bs-toggle="tab" data-bs-target="#interactions" type="button">
                            Interactions
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="reminders-tab" data-bs-toggle="tab" data-bs-target="#reminders" type="button">
                            Reminders
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="leadTabsContent">
                    <!-- Interactions Tab -->
                    <div class="tab-pane fade show active" id="interactions" role="tabpanel">
                        <!-- Add Interaction Form -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="{{ route('leads.add-interaction', $lead) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select class="form-select" name="type" required>
                                                <option value="">Type</option>
                                                <option value="call">Call</option>
                                                <option value="email">Email</option>
                                                <option value="meeting">Meeting</option>
                                                <option value="site_visit">Site Visit</option>
                                                <option value="follow_up">Follow-up</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="description" placeholder="Description" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary w-100">Log</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Interactions List -->
                        <div class="card">
                            <div class="card-body">
                                @forelse($interactions as $interaction)
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge bg-{{ $interaction->type == 'call' ? 'primary' : ($interaction->type == 'meeting' ? 'success' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $interaction->type)) }}
                                            </span>
                                            <strong>{{ $interaction->description }}</strong>
                                        </div>
                                        <small class="text-muted">{{ $interaction->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    @if($interaction->user)
                                        <small class="text-muted">by {{ $interaction->user->name }}</small>
                                    @endif
                                </div>
                                @empty
                                <p class="text-muted text-center">No interactions recorded</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Reminders Tab -->
                    <div class="tab-pane fade" id="reminders" role="tabpanel">
                        <!-- Add Reminder Form -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="{{ route('leads.create-reminder', $lead) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="title" placeholder="Reminder title" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" class="form-control" name="due_date" required>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="notes" placeholder="Notes">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="submit" class="btn btn-primary w-100">Add</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Reminders List -->
                        @forelse($lead->reminders()->orderBy('due_date')->get() as $reminder)
                        <div class="card mb-2 {{ $reminder->is_completed ? 'bg-light' : '' }}">
                            <div class="card-body py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $reminder->title }}</strong>
                                    <small class="text-muted d-block">Due: {{ $reminder->due_date->format('M d, Y') }}</small>
                                    @if($reminder->notes)
                                        <small class="text-muted">{{ $reminder->notes }}</small>
                                    @endif
                                </div>
                                @if(!$reminder->is_completed)
                                    <form action="{{ route('leads.complete-reminder', $reminder) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-success">Completed</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center">No reminders set</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Convert to Tenant Modal -->
<div class="modal fade" id="convertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Convert to Tenant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('leads.convert', $lead) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">This will create a new tenant record from this lead.</p>
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" name="full_name" value="{{ $lead->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $lead->email }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="{{ $lead->phone }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Number</label>
                        <input type="text" class="form-control" name="id_number">
                    </div>
                    <hr>
                    <p class="fw-bold">Lease Information (Optional)</p>
                    <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <select class="form-select" name="unit_id">
                            <option value="">Select Unit</option>
                            <!-- Units would be populated here -->
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d', strtotime('+1 year')) }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Convert</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Agent Modal -->
<div class="modal fade" id="assignAgentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('leads.assign-agent', $lead) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <select class="form-select" name="agent_id" required>
                        <option value="">Select Agent</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

