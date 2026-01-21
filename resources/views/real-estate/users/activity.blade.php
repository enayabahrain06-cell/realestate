@extends('layouts.real-estate.dashboard')

@section('title', 'User Activity - ' . $user->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('real-estate.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('real-estate.users.index') }}">Users</a></li>
<li class="breadcrumb-item"><a href="{{ route('real-estate.users.show', $user) }}">{{ $user->name }}</a></li>
<li class="breadcrumb-item active">Activity</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3>User Activity</h3>
                <p class="text-muted mb-0">{{ $user->name }} ({{ $user->email }})</p>
            </div>
            <div class="d-flex gap-2">
                <select class="form-select" id="days" onchange="window.location.href='?days='+this.value">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 days</option>
                    <option value="14" {{ $days == 14 ? 'selected' : '' }}>Last 14 days</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 days</option>
                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>Last 60 days</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 days</option>
                </select>
                <a href="{{ route('real-estate.users.show', $user) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Profile
                </a>
            </div>
        </div>

        <!-- Activity Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Total Actions</p>
                            <h4 class="mb-0">{{ $activity['total'] ?? 0 }}</h4>
                        </div>
                        <div class="stat-icon stat-icon-primary">
                            <i class="bi bi-activity"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Unique Actions</p>
                            <h4 class="mb-0">{{ count($activity['action_counts'] ?? []) }}</h4>
                        </div>
                        <div class="stat-icon stat-icon-success">
                            <i class="bi bi-list-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Most Active Day</p>
                            <h4 class="mb-0">{{ $activity['most_active_day'] ?? 'N/A' }}</h4>
                        </div>
                        <div class="stat-icon stat-icon-warning">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Avg. per Day</p>
                            <h4 class="mb-0">{{ number_format($activity['daily_average'] ?? 0, 1) }}</h4>
                        </div>
                        <div class="stat-icon stat-icon-info">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Breakdown -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Activity Breakdown</h5>
            </div>
            <div class="card-body">
                @if(isset($activity['action_counts']) && count($activity['action_counts']) > 0)
                    <div class="row">
                        @foreach($activity['action_counts'] as $action => $count)
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3 text-center">
                                    <h5 class="mb-1">{{ $count }}</h5>
                                    <small class="text-muted text-capitalize">{{ str_replace('_', ' ', $action) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                        No activity recorded in the selected period
                    </div>
                @endif
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Activity Timeline</h5>
            </div>
            <div class="card-body">
                @if(isset($activity['logs']) && count($activity['logs']) > 0)
                    <div class="timeline">
                        @foreach($activity['logs'] as $log)
                            <div class="timeline-item d-flex mb-4 pb-3 border-bottom">
                                <div class="timeline-marker bg-primary me-3" style="width: 12px; height: 12px; border-radius: 50%; margin-top: 6px;"></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge bg-primary text-capitalize mb-2">
                                                {{ str_replace('_', ' ', $log->action) }}
                                            </span>
                                            @if($log->description)
                                                <p class="mb-1">{{ $log->description }}</p>
                                            @endif
                                            @if($log->properties && is_array($log->properties))
                                                <small class="text-muted">
                                                    @foreach($log->properties as $key => $value)
                                                        <span class="me-3">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ is_array($value) ? json_encode($value) : $value }}</span>
                                                    @endforeach
                                                </small>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $log->created_at->format('M d, Y H:i:s') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                        No activity recorded in the selected period
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

