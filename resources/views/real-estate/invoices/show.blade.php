@extends('layouts.real-estate.dashboard')

@section('title', 'Invoice Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
    <li class="breadcrumb-item active">#{{ $invoice->id }}</li>
@endsection

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1><i class="bi bi-receipt me-2"></i>Invoice #{{ $invoice->id }}</h1>
            <p>View invoice details.</p>
        </div>
        <div class="col-auto">
            @if($invoice->status === 'pending')
                <a href="{{ route('invoices.pay', $invoice->id) }}" class="btn btn-success">
                    <i class="bi bi-credit-card me-1"></i>Pay Now
                </a>
            @else
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i>Paid
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Invoice Details</h5>
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
            </div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="info-card p-3 bg-light rounded">
                            <small class="text-muted text-uppercase d-block mb-1">Amount</small>
                            <span class="fw-bold fs-4">${{ number_format($invoice->amount, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card p-3 bg-light rounded">
                            <small class="text-muted text-uppercase d-block mb-1">Due Date</small>
                            <span class="fw-bold">{{ $invoice->due_date->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="mb-3">Tenant Information</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-card p-3 bg-light rounded">
                            <small class="text-muted text-uppercase d-block mb-1">Tenant Name</small>
                            <span class="fw-bold">{{ $invoice->tenant->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card p-3 bg-light rounded">
                            <small class="text-muted text-uppercase d-block mb-1">Created At</small>
                            <span class="fw-bold">{{ $invoice->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Invoices
                    </a>
                    @if($invoice->status === 'pending')
                        <a href="{{ route('invoices.pay', $invoice->id) }}" class="btn btn-success">
                            <i class="bi bi-credit-card me-1"></i>Pay Invoice
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

