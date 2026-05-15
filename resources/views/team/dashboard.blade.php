@extends('layouts.admin')
@section('title', 'My Assigned Tasks | EasyTax')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>
        /* ── PAGE HEADER ── */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: var(--slate-dark); margin: 0 0 0.25rem 0; letter-spacing: -0.02em; }
        .page-subtitle { font-size: 0.9rem; color: var(--text-muted); margin: 0; }

        /* ── KPI CARDS ── */
        .kpi-card {
            display: flex; align-items: center; padding: 1.25rem 1.5rem; border-radius: 16px;
            background: var(--surface); border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s; height: 100%;
        }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; margin-right: 1rem; }
        .kpi-body { flex: 1; }
        .kpi-value { font-size: 1.3rem; font-weight: 800; color: var(--slate-dark); line-height: 1.2; }
        .kpi-label { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; }

        .kpi-blue   .kpi-icon { background: #dbeafe; color: #2563eb; } 
        .kpi-orange .kpi-icon { background: #fff7ed; color: #ea580c; }
        .kpi-green  .kpi-icon { background: #dcfce7; color: #16a34a; }

        /* ── DATA TABLE CARD ── */
        .table-card { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        .dataTables_wrapper { padding: 1.5rem; }
        table.dataTable { border-collapse: collapse !important; width: 100% !important; margin-bottom: 1rem !important; border-bottom: 1px solid var(--border); }
        table.dataTable thead th { background: #f8fafc; color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--border) !important; border-top: none !important; padding: 1rem; white-space: nowrap; }
        table.dataTable tbody td { padding: 1rem; vertical-align: middle; color: var(--text); font-size: 0.9rem; border-bottom: 1px solid var(--ink-100); }
        table.dataTable tbody tr:hover { background: #f8fafc; }

        .btn-custom-view { background-color: #ff6b00; border-color: #ff6b00; color: #ffffff; }
        .btn-custom-view:hover { background-color: #e56000; border-color: #e56000; color: #ffffff; }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">💼 My Assigned Applications</h1>
            <p class="page-subtitle">Process and update your assigned applications.</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge px-3 py-2" style="background-color: #dbeafe; color: #1e40af; border-radius: 50px; font-size: 0.8rem; font-weight: 700;">
                <i class="fas fa-headset mr-1"></i> Support Helpline: +91 77259 81022
            </span>
        </div>
    </div>
  
    {{-- METRICS ROW (Calculated directly from their assigned tasks) --}}
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">  
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $applications->count() }}</div>
                    <div class="kpi-label">Total Assigned</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-orange">
                <div class="kpi-icon"><i class="fas fa-spinner"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">
                        {{ $applications->filter(fn($app) => in_array(strtolower($app->status->value ?? $app->status), ['submitted', 'pending', 'in_progress']))->count() }}
                    </div>
                    <div class="kpi-label">Active / Pending</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">
                        {{ $applications->filter(fn($app) => strtolower($app->status->value ?? $app->status) === 'completed')->count() }}
                    </div>
                    <div class="kpi-label">Completed</div>
                </div>
            </div>
        </div>
    </div>

    {{-- NEW: FINANCIAL METRICS ROW (Now styled perfectly like the row above!) --}}
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="letter-spacing: 1px; font-size: 0.8rem;">My Financial Summary</h6>
        </div>
        <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">  
            <div class="kpi-card">
                <div class="kpi-icon" style="background: #f3e8ff; color: #9333ea;"><i class="fas fa-wallet"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">₹{{ number_format($totalEarned) }}</div>
                    <div class="kpi-label">Total Earned</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: #dcfce7; color: #16a34a;"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">₹{{ number_format($totalPaid) }}</div>
                    <div class="kpi-label">Total Received</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: #fee2e2; color: #dc2626;"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value {{ $balanceDue > 0 ? 'text-danger' : '' }}">₹{{ number_format($balanceDue) }}</div>
                    <div class="kpi-label">Balance Due</div>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="table-card">
        <table id="teamTable" class="table w-100">
           <thead>
                <tr>
                    <th>App ID</th>
                    <th>Service Type</th>
                    <th>Status</th>
                    <th>Date Assigned</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $app)
                <tr>
                    <td class="font-weight-bold text-muted">#{{ $app->id }}</td>
                    <td class="font-weight-bold text-dark">{{ $app->service->name ?? 'Unknown Service' }}</td>
                    <td>
                        @php 
                            $status = strtolower($app->status->value ?? $app->status ?? 'unknown'); 
                            $badgeClass = match($status) {
                                'completed' => 'badge-success',
                                'in_progress', 'processing' => 'badge-info',
                                'rejected' => 'badge-danger',
                                default => 'badge-warning'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2 py-1 text-uppercase">{{ str_replace('_', ' ', $status) }}</span>
                    </td>
                    <td class="text-muted">{{ $app->updated_at->format('d M, Y') }}</td>
                    <td class="text-right">
                        <a href="{{ route('team.applications.show', $app->id) }}" class="btn btn-sm btn-custom-view shadow-sm" style="border-radius: 6px; font-weight: 600;">
                            view </i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#teamTable').DataTable({
                "order": [[ 0, "desc" ]], // Sort by ID descending  
                "language": {
                    "emptyTable": "You have no assigned tasks at the moment! 🎉"
                }
            });
        });
    </script>
@endsection