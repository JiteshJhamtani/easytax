@extends('layouts.admin')

@section('title', 'Application Manager | EasyTax')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>
        /* ── PAGE HEADER ── */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: var(--slate-dark); margin: 0 0 0.25rem 0; letter-spacing: -0.02em; }
        .page-subtitle { font-size: 0.9rem; color: var(--text-muted); margin: 0; }

        /* ── EXPORT DROPDOWN ── */
        .export-btn {
            background-color: var(--surface); color: var(--slate-dark); font-weight: 700; padding: 0.5rem 1.25rem;
            border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 0.5rem;
            transition: all 0.2s; font-size: 0.85rem; cursor: pointer;
        }
        .export-btn:hover, .export-btn[aria-expanded="true"] { background-color: var(--ink-100); color: var(--slate-dark); }
        .dropdown-menu { border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 0.5rem; min-width: 240px; }
        .dropdown-item { font-size: 0.85rem; font-weight: 600; color: var(--slate); border-radius: 6px; padding: 0.6rem 1rem; display: flex; align-items: center; gap: 0.75rem; transition: all 0.15s; }
        .dropdown-item:hover { background-color: var(--ink-100); color: var(--slate-dark); }
        .dropdown-item i { width: 16px; text-align: center; }

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
        .kpi-red    .kpi-icon { background: #FEE2E2; color: #DC2626; }

        /* ── FILTER BAR ── */
        .filter-bar {
            background: var(--surface); border-radius: 12px; border: 1px solid var(--border);
            padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; align-items: flex-end; gap: 1rem;
        }
        .filter-group { display: flex; flex-direction: column; gap: 0.4rem; flex: 1; min-width: 150px; }
        .filter-label { font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: var(--text-muted); }
        .filter-control {
            font-size: 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-weight: 600; color: var(--slate);
            padding: 0.45rem 0.8rem; background: #fafbfc; transition: all 0.2s; height: 40px; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .filter-control:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(30,156,93,0.15); background: #fff; }
        
        .btn-reset {
            background: #fff; border: 1px solid var(--border); color: var(--text-muted); height: 40px; width: 40px;
            border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;
        }
        .btn-reset:hover { background: #FEE2E2; color: #DC2626; border-color: #fca5a5; }

        /* ── DATA TABLE CARD ── */
        .table-card { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        .dataTables_wrapper { padding: 1.5rem; }
        table.dataTable { border-collapse: collapse !important; width: 100% !important; margin-bottom: 1rem !important; border-bottom: 1px solid var(--border); }
        table.dataTable thead th { background: #f8fafc; color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--border) !important; border-top: none !important; padding: 1rem; white-space: nowrap; }
        table.dataTable tbody td { padding: 1rem; vertical-align: middle; color: var(--text); font-size: 0.9rem; border-bottom: 1px solid var(--ink-100); }
        table.dataTable tbody tr:hover { background: #f8fafc; }
        
        .dataTables_info { color: var(--text-muted); font-size: 0.85rem; font-weight: 500; }
        .page-item.active .page-link { background: var(--slate-dark); border-color: var(--slate-dark); color: #fff; border-radius: 6px; }
        .page-link { color: var(--slate); border: 1px solid var(--border); border-radius: 6px; margin: 0 2px; font-size: 0.85rem; font-weight: 600; }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/applications.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Global Application Manager</h1>
            <p class="page-subtitle">Monitor, filter, and manage all agent submissions.</p>
        </div>
        <div>
            {{-- THE NEW EXPORT DROPDOWN --}}
            <div class="dropdown d-inline-block">
                <button class="export-btn dropdown-toggle" type="button" id="exportMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-export text-primary"></i> Export Data
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportMenu">
                    {{-- Option 1: Standard --}}
                    <a class="dropdown-item" href="{{ route('admin.applications.export') }}">
                        <i class="fas fa-table text-secondary"></i> Standard Excel Export
                    </a>
                    <div class="dropdown-divider"></div>
                    {{-- Option 2: Completed Forms --}}
                    <a class="dropdown-item" href="{{ route('admin.applications.export', ['filter' => 'completed_forms']) }}">
                        <i class="fas fa-check-circle text-success"></i> Export Completed (Form Data)
                    </a>
                    {{-- Option 3: Pending Only --}}
                    <a class="dropdown-item" href="{{ route('admin.applications.export', ['filter' => 'pending_only']) }}">
                        <i class="fas fa-hourglass-half text-warning"></i> Export Pending Applications
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- METRICS ROW --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats->total ?? 0 }}</div>
                    <div class="kpi-label">Total Volume</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-orange">
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats->pending ?? 0 }}</div>
                    <div class="kpi-label">Pending Review</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats->completed ?? 0 }}</div>
                    <div class="kpi-label">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="kpi-card kpi-red">
                <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats->failed ?? 0 }}</div>
                    <div class="kpi-label">Failed Payments</div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <div class="filter-group">
            <label class="filter-label" for="filterAgent">Assigned Agent</label>
            <select id="filterAgent" class="filter-control">
                <option value="">All Agents</option>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterService">Service Type</label>
            <select id="filterService" class="filter-control">
                <option value="">All Services</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterStatus">App Status</label>
            <select id="filterStatus" class="filter-control">
                <option value="">Any Status</option>
                <option value="DRAFT">Draft</option>
                <option value="SUBMITTED">Submitted</option>
                <option value="IN_PROGRESS">In Progress</option>
                <option value="COMPLETED">Completed</option>
                <option value="CANCELLED">Cancelled</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterPayment">Payment</label>
            <select id="filterPayment" class="filter-control">
                <option value="">Any Payment</option>
                <option value="SUCCESS">Success</option>
                <option value="FAILED">Failed</option>
                <option value="PENDING">Pending</option>
                <option value="REFUNDED">Refunded</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterDateFrom">Date From</label>
            <input type="date" id="filterDateFrom" class="filter-control">
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterDateTo">Date To</label>
            <input type="date" id="filterDateTo" class="filter-control">
        </div>

        <div>
            <button id="resetFilters" class="btn-reset" title="Clear Filters">
                <i class="fas fa-undo-alt"></i>
            </button>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="table-card">
        <table id="applicationsTable" class="table w-100">
           <thead>
                <tr>
                    <th>App ID</th>
                    <th>Agent</th>
                    <th>Service Type</th>
                    <th>Primary Data</th> <th>Status</th>
                    <th>Payment</th>
                    <th>Amount</th>
                    <th>Date Submitted</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets/js/admin-applications.js') }}"></script>
@endsection