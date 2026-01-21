@extends('layouts.real-estate.dashboard')

@section('title', 'Invoices')

@section('breadcrumb')
    <li class="breadcrumb-item active">Invoices</li>
@endsection

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1><i class="bi bi-receipt me-2"></i>Invoices</h1>
            <p>View and manage your invoices.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('invoices.pay-all') }}" class="btn btn-success" 
               onclick="return confirm('Pay all pending invoices?')">
                <i class="bi bi-credit-card me-1"></i>Pay All Pending
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($invoices->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Tenant</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>
                                    <span class="fw-bold">#{{ $invoice->id }}</span>
                                </td>
                                <td>{{ $invoice->tenant->name ?? 'N/A' }}</td>
                                <td>${{ number_format($invoice->amount, 2) }}</td>
                                <td>
                                    @switch($invoice->status)
                                        @case('paid')
                                            <span class="badge bg-success">Paid</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                            @break
                                        @case('overdue')
                                            <span class="badge bg-danger">Overdue</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $invoice->due_date->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('invoices.show', $invoice->id) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($invoice->status === 'pending')
                                            <a href="{{ route('invoices.pay', $invoice->id) }}" 
                                               class="btn btn-outline-success" title="Pay">
                                                <i class="bi bi-credit-card"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $invoices->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                No invoices found.
            </div>
        @endif
    </div>
</div>
@endsection

