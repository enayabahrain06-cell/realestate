@extends('layouts.app')

@section('title', 'Financial Reports')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Financial Reports</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Date Range Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Building</label>
                        <select name="building_id" class="form-select">
                            <option value="">All Buildings</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}" {{ $buildingId == $building->id ? 'selected' : '' }}>
                                    {{ $building->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('reports.financial') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('reports.export-financial', ['start_date' => $startDate, 'end_date' => $endDate, 'building_id' => $buildingId]) }}" 
                           class="btn btn-outline-success w-100">
                            <i class="bi bi-download"></i> Export
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if($financialSummary)
        <!-- Financial Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="mb-0">Total Revenue</h6>
                        <h3 class="mb-0">AED {{ number_format($financialSummary['revenue'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6 class="mb-0">Total Expenses</h6>
                        <h3 class="mb-0">AED {{ number_format($financialSummary['expenses'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="mb-0">Net Profit</h6>
                        <h3 class="mb-0">AED {{ number_format($financialSummary['net_profit'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="mb-0">Profit Margin</h6>
                        <h3 class="mb-0">{{ number_format($financialSummary['profit_margin'], 1) }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div class="row mb-4">
            @if($occupancyRate !== null)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Occupancy Rate</h6>
                        <h2 class="text-primary">{{ number_format($occupancyRate, 1) }}%</h2>
                    </div>
                </div>
            </div>
            @endif
            @if($costPerUnit !== null)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Cost Per Unit</h6>
                        <h2 class="text-warning">AED {{ number_format($costPerUnit, 2) }}</h2>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <div class="row">
            <!-- Revenue by Building -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Revenue by Building</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Building</th>
                                    <th>Revenue</th>
                                    <th>Occupancy Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByBuilding as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>AED {{ number_format($item['revenue'], 2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-success" style="width: {{ $item['occupancy_rate'] }}%"></div>
                                            </div>
                                            <small>{{ number_format($item['occupancy_rate'], 1) }}%</small>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Expense Breakdown -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Expense Breakdown</h5>
                    </div>
                    <div class="card-body">
                        @forelse($expenseBreakdown as $item)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $item['category'] }}</span>
                            <strong>AED {{ number_format($item['amount'], 2) }}</strong>
                        </div>
                        @empty
                        <p class="text-muted text-center">No expense data available</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Chart Placeholder -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Monthly Revenue Trend</h5>
            </div>
            <div class="card-body">
                @if(!empty($monthlyRevenue))
                <div class="chart-container" style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
                @else
                <p class="text-muted text-center">No monthly data available for the selected period</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlyRevenue = @json($monthlyRevenue);
    
    if (Object.keys(monthlyRevenue).length > 0) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: Object.keys(monthlyRevenue),
                datasets: [{
                    label: 'Revenue (AED)',
                    data: Object.values(monthlyRevenue),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'AED ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection

