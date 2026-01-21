@extends('layouts.real-estate.dashboard')

@section('title', 'Audit Logs')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Audit Logs</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $activitySummary['today']['total'] }}</h4>
                                <p class="mb-0">Today's Activity</p>
                            </div>
                            <i class="bi bi-clock-history fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $activitySummary['this_week']['total'] }}</h4>
                                <p class="mb-0">This Week</p>
                            </div>
                            <i class="bi bi-calendar-week fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $activitySummary['this_month']['total'] }}</h4>
                                <p class="mb-0">This Month</p>
                            </div>
                            <i class="bi bi-calendar-month fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Logs Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Activity Log</h5>
                    <a href="{{ route('real-estate.audit-logs.export', request()->all()) }}" class="btn btn-outline-primary">
                        <i class="bi bi-download"></i> Export
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-2">
                        <input type="text" name="user_id" class="form-control" placeholder="User ID" 
                               value="{{ $filters['user_id'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <select name="action" class="form-select">
                            <option value="">All Actions</option>
                            <option value="create" {{ ($filters['action'] ?? '') == 'create' ? 'selected' : '' }}>Create</option>
                            <option value="update" {{ ($filters['action'] ?? '') == 'update' ? 'selected' : '' }}>Update</option>
                            <option value="delete" {{ ($filters['action'] ?? '') == 'delete' ? 'selected' : '' }}>Delete</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="model_type" class="form-control" placeholder="Entity Type" 
                               value="{{ $filters['model_type'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" 
                               value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" 
                               value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                    </div>
                </form>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <small>{{ $log->created_at->format('M d, Y') }}</small><br>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @if($log->user)
                                    <a href="{{ route('real-estate.audit-logs.user-activity', $log->user_id) }}">
                                        {{ $log->user->name }}
                                    </a>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                @switch($log->action)
                                    @case('create')<span class="badge bg-success">Created</span>@break
                                    @case('update')<span class="badge bg-warning text-dark">Updated</span>@break
                                    @case('delete')<span class="badge bg-danger">Deleted</span>@break
                                    @default<span class="badge bg-secondary">{{ $log->action }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($log->model_type)
                                    <small>{{ class_basename($log->model_type) }}</small>
                                    @if($log->model_id)
                                        <small class="text-muted">#{{ $log->model_id }}</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <small>{{ Str::limit($log->description, 50) }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No audit logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Most Active Users -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Most Active Users (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                @forelse($mostActiveUsers as $item)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        @if($item['user'])
                            <strong>{{ $item['user']->name }}</strong>
                            <small class="text-muted">{{ $item['user']->email }}</small>
                        @else
                            <span class="text-muted">Unknown User</span>
                        @endif
                    </div>
                    <span class="badge bg-primary">{{ $item['activity_count'] }} actions</span>
                </div>
                @empty
                <p class="text-muted text-center">No activity recorded</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

