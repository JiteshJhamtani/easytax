@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="m-0 text-dark font-weight-bold">
            <i class="fas fa-chart-line text-primary mr-2"></i> Analytics Dashboard
        </h1>
        <span class="text-muted font-weight-bold" style="font-size: 0.9rem;">
            <i class="far fa-calendar-alt mr-1"></i> {{ now()->format('d M Y, h:i A') }}
        </span>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">

    {{-- ═══════ ROW 1 — KPI Cards ═══════ --}}
    <div class="row mb-4">
        <div class="col-lg col-md-4 col-sm-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fas fa-file-alt"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($kpis['total_applications']) }}</div>
                    <div class="kpi-label">Total Applications</div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-sm-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fas fa-rupee-sign"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">₹{{ number_format($kpis['total_revenue'], 2) }}</div>
                    <div class="kpi-label">Total Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-sm-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-purple">
                <div class="kpi-icon"><i class="fas fa-coins"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">₹{{ number_format($kpis['total_commission'], 2) }}</div>
                    <div class="kpi-label">Commission Generated</div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-sm-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-teal">
                <div class="kpi-icon"><i class="fas fa-users"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($kpis['total_agents']) }}</div>
                    <div class="kpi-label">Total Agents</div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-sm-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-orange">
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($kpis['pending_applications']) }}</div>
                    <div class="kpi-label">Pending Applications</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ ROW 2 — Charts ═══════ --}}
    <div class="row mb-4">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="card modern-card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-chart-bar text-primary mr-2"></i> Monthly Applications
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div style="position: relative; height: 260px;">
                        <canvas id="applicationsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card modern-card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-chart-area text-success mr-2"></i> Monthly Revenue
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div style="position: relative; height: 260px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ ROW 3 — Top Agents & Top Services ═══════ --}}
    <div class="row mb-4">
        <div class="col-lg-7 mb-4 mb-lg-0">
            <div class="card modern-card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-trophy text-warning mr-2"></i> Top 10 Agents
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Agent</th>
                                    <th class="text-center">Apps</th>
                                    <th class="text-right">Revenue</th>
                                    <th class="text-right">Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topAgents as $i => $agent)
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <span class="font-weight-bold text-dark">{{ $agent->name }}</span>
                                        <br><code class="text-muted" style="font-size:0.78rem;">{{ $agent->agent_code }}</code>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info px-2 py-1">{{ $agent->applications_count }}</span>
                                    </td>
                                    <td class="text-right text-success font-weight-bold">₹{{ number_format($agent->total_revenue, 2) }}</td>
                                    <td class="text-right text-primary font-weight-bold">₹{{ number_format($agent->commission_earned, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card modern-card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-concierge-bell text-info mr-2"></i> Top 10 Services
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service</th>
                                    <th class="text-center">Apps</th>
                                    <th class="text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topServices as $i => $svc)
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ $i + 1 }}</td>
                                    <td class="font-weight-bold text-dark">{{ $svc->name }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-info px-2 py-1">{{ $svc->applications_count }}</span>
                                    </td>
                                    <td class="text-right text-success font-weight-bold">₹{{ number_format($svc->revenue, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════ ROW 4 — Recent Applications ═══════ --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card modern-card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-history text-secondary mr-2"></i> Recent Applications
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Agent</th>
                                    <th>Service</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-right">Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentApplications as $app)
                                <tr>
                                    <td class="font-weight-bold text-dark">#{{ $app->id }}</td>
                                    <td>{{ $app->agent->name ?? '—' }}</td>
                                    <td>{{ $app->service->name ?? '—' }}</td>
                                    <td class="text-right font-weight-bold">₹{{ number_format($app->amount, 2) }}</td>
                                    <td class="text-center">
                                        @php
                                            $statusColors = [
                                                'DRAFT'      => 'badge-secondary-soft',
                                                'SUBMITTED'  => 'badge-info-soft',
                                                'PROCESSING' => 'badge-warning-soft',
                                                'COMPLETED'  => 'badge-success-soft',
                                                'REJECTED'   => 'badge-danger-soft',
                                            ];
                                            $badgeClass = $statusColors[$app->status->value ?? $app->status] ?? 'badge-secondary-soft';
                                        @endphp
                                        <span class="custom-badge {{ $badgeClass }}">{{ $app->status->value ?? $app->status }}</span>
                                    </td>
                                    <td class="text-right text-muted">{{ $app->submitted_at?->format('d M Y') ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No applications yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('css')
<style>
    /* ── KPI Cards ── */
    .kpi-card {
        display: flex;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .kpi-icon {
        width: 54px; height: 54px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        margin-right: 1rem;
    }
    .kpi-body { flex: 1; }
    .kpi-value { font-size: 1.5rem; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .kpi-label { font-size: 0.78rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

    .kpi-blue   .kpi-icon { background: #dbeafe; color: #2563eb; }
    .kpi-green  .kpi-icon { background: #dcfce7; color: #16a34a; }
    .kpi-purple .kpi-icon { background: #f3e8ff; color: #9333ea; }
    .kpi-teal   .kpi-icon { background: #ccfbf1; color: #0d9488; }
    .kpi-orange .kpi-icon { background: #fff7ed; color: #ea580c; }

    /* ── Modern Card ── */
    .modern-card {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.02) !important;
    }

    /* ── Tables ── */
    .modern-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0 !important;
        border-top: none !important;
        padding: 0.85rem 0.75rem;
    }
    .modern-table tbody td {
        padding: 0.85rem 0.75rem;
        vertical-align: middle;
        color: #475569;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .modern-table tbody tr { transition: background 0.15s; }
    .modern-table tbody tr:hover { background: #f8fafc; }

    /* ── Status Badges ── */
    .custom-badge {
        display: inline-flex; align-items: center;
        padding: 0.3rem 0.75rem; border-radius: 50px;
        font-size: 0.75rem; font-weight: 700; letter-spacing: 0.3px;
    }
    .badge-success-soft   { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-info-soft      { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .badge-warning-soft   { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-danger-soft    { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-secondary-soft { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    const labels = @json($chartLabels);
    const appData = @json($chartApplications);
    const revData = @json($chartRevenue);

    const gridColor = 'rgba(226,232,240,0.6)';
    const fontFamily = "'Segoe UI', Roboto, 'Helvetica Neue', sans-serif";

    // ── Applications Chart ──
    new Chart(document.getElementById('applicationsChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Applications',
                data: appData,
                backgroundColor: 'rgba(37,99,235,0.75)',
                borderColor: 'rgba(37,99,235,1)',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1e293b', titleFont: { family: fontFamily }, bodyFont: { family: fontFamily } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: fontFamily, size: 11 }, color: '#94a3b8' } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { font: { family: fontFamily, size: 11 }, color: '#94a3b8', precision: 0 } }
            }
        }
    });

    // ── Revenue Chart ──
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (₹)',
                data: revData,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.08)',
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#16a34a',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                borderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { family: fontFamily },
                    bodyFont: { family: fontFamily },
                    callbacks: { label: ctx => '₹' + ctx.parsed.y.toLocaleString('en-IN', { minimumFractionDigits: 2 }) }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: fontFamily, size: 11 }, color: '#94a3b8' } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { font: { family: fontFamily, size: 11 }, color: '#94a3b8', callback: v => '₹' + v.toLocaleString() } }
            }
        }
    });
</script>
@endsection
