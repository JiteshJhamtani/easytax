@extends('adminlte::page')

@section('title', 'Application Manager | EasyTax')

@section('content_header')
    <div class="workspace-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="workspace-title">Application Manager</h1>
            <p class="workspace-subtitle">Track, filter, and manage client submissions.</p>
        </div>
        <div>
            <a href="{{ route('services.index') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-2"></i> New Application
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="applications-workspace">

        {{-- METRICS ROW --}}
        <div class="row metrics-row mb-4">
            {{-- ... (Keep your 4 Metric Cards exactly as they are) ... --}}
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="metric-card">
                    <div class="metric-icon bg-blue-soft text-primary">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Total Volume</span>
                        <strong class="metric-value">{{ $stats->total ?? 0 }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="metric-card">
                    <div class="metric-icon bg-orange-soft text-orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Pending Review</span>
                        <strong class="metric-value">{{ $stats->pending ?? 0 }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="metric-card">
                    <div class="metric-icon bg-red-soft text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Failed Payments</span>
                        <strong class="metric-value">{{ $stats->failed ?? 0 }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="metric-card">
                    <div class="metric-icon bg-green-soft text-success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Processed This Month</span>
                        <strong class="metric-value">{{ $stats->monthly ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- DATA TABLE CARD WITH ADVANCED FILTERS --}}
        <div class="card data-card border-0">

            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">

                {{-- Quick Filter Tabs (If you still want them) --}}
                <div class="applications-tabs mb-4">
                    <button class="app-filter active" data-filter="all">All Applications</button>
                    <button class="app-filter" data-filter="pending">Pending</button>
                    <button class="app-filter" data-filter="completed">Completed</button>
                    <button class="app-filter" data-filter="failed">Failed Payments</button>
                </div>

                {{-- Advanced Filters Toolbar --}}
                <div class="advanced-filters-toolbar">
                    <div class="filter-group">
                        <label for="filterService">Service Type</label>
                        <select id="filterService" class="form-control filter-input">
                            <option value="">All Services</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filterStatus">App Status</label>
                        <select id="filterStatus" class="form-control filter-input">
                            <option value="">Any Status</option>
                            <option value="DRAFT">Draft</option>
                            <option value="SUBMITTED">Submitted</option>
                            <option value="IN_PROGRESS">In Progress</option>
                            <option value="COMPLETED">Completed</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filterPayment">Payment</label>
                        <select id="filterPayment" class="form-control filter-input">
                            <option value="">Any Payment</option>
                            <option value="SUCCESS">Success</option>
                            <option value="FAILED">Failed</option>
                            <option value="PENDING">Pending</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filterDateFrom">Date From</label>
                        <input type="date" id="filterDateFrom" class="form-control filter-input">
                    </div>

                    <div class="filter-group">
                        <label for="filterDateTo">Date To</label>
                        <input type="date" id="filterDateTo" class="form-control filter-input">
                    </div>

                    <div class="filter-actions">
                        <button id="resetFilters" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                            <i class="fas fa-undo-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive px-4 py-3">
                    <table id="applicationsTable" class="responsive-card-table table modern-data-table w-100">
                        <thead>
                            <tr>
                                <th>App ID</th>
                                <th>Service Type</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Amount</th>
                                <th>Date Submitted</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/applications.css') }}">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets/js/applications.js') }}"></script>
@stop
