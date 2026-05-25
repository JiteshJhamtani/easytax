@extends('adminlte::page')

@section('title', 'Agent Workspace | EasyTax')

@section('content_header')
    <div class="workspace-header">
        <div>
            <h1 class="workspace-title">Welcome back, {{ Auth::user()->name ?? 'Agent' }}</h1>
            <p class="workspace-subtitle">Here is what's happening with your applications today.</p>
        </div>
        <div class="workspace-actions">
            <a href="{{ route('services.index') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-2"></i> New Application
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="dashboard-container">

        {{-- KPI ROW --}}
        <div class="row kpi-row">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="kpi-card">
                    <div class="kpi-icon icon-blue">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="kpi-info">
                        <div class="kpi-label">Total Applications</div>
                        <div class="kpi-value">{{ $stats->total_applications ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="kpi-card">
                    <div class="kpi-icon icon-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kpi-info">
                        <div class="kpi-label">Completed</div>
                        <div class="kpi-value">{{ $stats->completed_applications ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="kpi-card">
                    <div class="kpi-icon icon-orange">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="kpi-info">
                        <div class="kpi-label">In Progress</div>
                        <div class="kpi-value">{{ $stats->pending_applications ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="kpi-card">
                    <div class="kpi-icon icon-purple">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="kpi-info">
                        <div class="kpi-label">Total Commission</div>
                        <div class="kpi-value amount">₹{{ number_format($stats->total_commission ?? 0, 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHART + COMMISSION --}}
        <div class="row mt-3 align-items-start">
            <div class="col-lg-8 col-md-12">
                <div class="card dashboard-card">
                    <div class="card-header border-0">
                        <h3 class="card-title font-weight-bold text-dark">Application Velocity</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="applicationsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card dashboard-card bg-gradient-primary payout-card">
                    <div class="card-header border-0">
                        <h3 class="card-title text-white font-weight-bold">Payout Summary</h3>
                    </div>
                    <div class="card-body commission-summary">
                        <div class="payout-circle">
                            <span class="payout-label">Pending Clearance</span>
                            <strong class="payout-amount">₹{{ number_format($stats->pending_commission ?? 0, 2) }}</strong>
                        </div>

                        <div class="commission-breakdown mt-4">
                            <div class="commission-row">
                                <span class="text-white-50">Total Earned (YTD)</span>
                                <strong class="text-white">₹{{ number_format($stats->total_commission ?? 0, 2) }}</strong>
                            </div>
                            <div class="commission-row">
                                <span class="text-white-50">Successfully Paid</span>
                                <strong class="text-white">₹{{ number_format($stats->paid_commission ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RECENT APPLICATIONS --}}
        <div class="card dashboard-card mt-3">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark">Recent Activity</h3>
                <a href="#" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>

            <div class="card-body p-0 table-responsive">
                <table class="responsive-card-table table table-hover modern-table mb-0">
                    <thead>
                        <tr>
                            <th>App ID</th>
                            <th>Service Type</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentApplications as $app)
                            <tr>
                                <td><span class="text-muted font-weight-bold">#{{ $app->id }}</span></td>
                                <td class="font-weight-bold text-dark">{{ $app->service->name }}</td>
                                <td>{{ $app->created_at->format('d M, Y') }} <span
                                        class="text-muted small d-block">{{ $app->created_at->format('h:i A') }}</span>
                                </td>
                                <td>
                                    @php
                                        // Example badge logic, adjust to your actual enums
                                        $statusClass = match (strtolower($app->status->value)) {
                                            'completed' => 'badge-success-soft',
                                            'pending' => 'badge-warning-soft',
                                            'rejected' => 'badge-danger-soft',
                                            default => 'badge-primary-soft',
                                        };
                                    @endphp
                                    <span class="badge badge-modern {{ $statusClass }}">
                                        {{ $app->status->value }}
                                    </span>
                                </td>
                                <td>
                                    @if (strtolower($app->payment_status->value) == 'paid')
                                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i> Paid</span>
                                    @else
                                        <span class="text-warning"><i class="fas fa-clock mr-1"></i> Pending</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold">
                                    ₹{{ number_format($app->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No recent applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.dashboardData = {
            months: {!! json_encode($monthlyApplications->pluck('month') ?? []) !!},
            totals: {!! json_encode($monthlyApplications->pluck('total') ?? []) !!}
        };
    </script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
@stop
