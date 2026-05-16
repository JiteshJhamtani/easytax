@extends('layouts.agent')

@section('title', 'Application Manager | EasyTax')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/applications.css') }}">
    
    <style>
        /* ── THEME VARIABLES & RESET ── */
        .content-body { 
            padding: 0 !important; 
            background-color: #F8F9FA !important; 
        }
        
        .chq-wrapper {
            --brand-green: #1E9C5D;
            --brand-green-hover: #157a48;
            --brand-mint: #EDF7F4;
            --brand-slate: #2E3D4E;
            --text-dark: #333333;
            --text-muted: #7a8799;
            --border-color: #e8ecf0;
            --card-shadow: 0 4px 20px rgba(0,0,0,0.03);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .chq-wrapper * { box-sizing: border-box; }

        /* ── HERO SECTION ── */
        .chq-hero {
            background-color: var(--brand-mint);
            padding: 2.5rem 3rem 6rem; /* Deep padding for the overlap effect */
            border-bottom: 1px solid #e2efe9;
        }

        .chq-hero-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .chq-hero-title h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0 0 0.4rem;
            letter-spacing: -0.02em;
        }
        .chq-hero-title p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin: 0;
        }

        .btn-new-app {
            background-color: var(--brand-green);
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.7rem 1.4rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-new-app:hover {
            background-color: var(--brand-green-hover);
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30,156,93,0.2);
        }

        /* ── MAIN LAYOUT ── */
        .chq-main {
            max-width: 1400px;
            margin: -3.5rem auto 3rem; /* Pulls content up */
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }

        /* ── METRICS GRID ── */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            transition: transform 0.2s;
        }
        .metric-card:hover { transform: translateY(-2px); }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        
        /* Icon Colors */
        .icon-blue { background-color: #E6F1FB; color: #185FA5; }
        .icon-orange { background-color: #FAEEDA; color: #BA7517; }
        .icon-red { background-color: #FEE2E2; color: #DC2626; }
        .icon-green { background-color: var(--brand-mint); color: var(--brand-green); }

        .metric-data { display: flex; flex-direction: column; }
        .metric-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem; }
        .metric-value { font-size: 1.6rem; font-weight: 800; color: var(--text-dark); line-height: 1; }

        /* ── DATA TABLE CARD ── */
        .data-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .data-card-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); }

        /* ── TABS ── */
        .applications-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
        .applications-tabs::-webkit-scrollbar { height: 4px; }
        .applications-tabs::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

        .app-filter {
            background: transparent;
            border: 1px solid transparent;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }
        .app-filter:hover { color: var(--text-dark); background: #f3f4f6; }
        .app-filter.active { background: var(--brand-mint); color: var(--brand-green); }

        /* ── ADVANCED FILTERS TOOLBAR ── */
        .advanced-filters-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            background: #f9fafb;
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            align-items: flex-end;
        }

        .filter-group { flex: 1; min-width: 140px; display: flex; flex-direction: column; }
        .filter-group label { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .filter-input {
            width: 100%; padding: 0.6rem 0.8rem; border-radius: 8px; border: 1px solid #d1d5db;
            font-family: inherit; font-size: 0.85rem; color: var(--text-dark); background: #fff;
            transition: border-color 0.2s; height: 38px;
        }
        .filter-input:focus { border-color: var(--brand-green); outline: none; box-shadow: 0 0 0 3px rgba(30,156,93,0.15); }
        
        .filter-actions { flex-shrink: 0; }
        #resetFilters {
            height: 38px; width: 38px; border-radius: 8px; border: 1px solid #d1d5db;
            background: #fff; color: var(--text-muted); cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center;
        }
        #resetFilters:hover { background: #f3f4f6; color: var(--text-dark); }

        /* ── DATATABLES OVERRIDES ── */
        .data-card-body { padding: 1rem 2rem 2rem; }
        
        table.dataTable { border-collapse: collapse !important; width: 100% !important; margin-top: 1rem !important; }
        table.dataTable thead th {
            border-top: none !important;
            border-bottom: 2px solid var(--border-color) !important;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 0.75rem !important;
            white-space: nowrap;
        }
        table.dataTable tbody td {
            padding: 1.2rem 0.75rem !important;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6 !important;
            font-size: 0.9rem;
            color: var(--text-dark);
            font-weight: 500;
        }
        table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
        
        /* Pagination Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px !important; border: none !important; padding: 0.4rem 0.8rem !important; margin: 0 2px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--brand-green) !important; color: #fff !important; font-weight: 700; border: none !important; box-shadow: 0 2px 6px rgba(30,156,93,0.3);
        }
        
        @media (max-width: 991px) {
            .chq-hero { padding: 2rem 1.5rem 5rem; }
            .chq-hero-flex { flex-direction: column; align-items: flex-start; }
            .chq-main { padding: 0 1rem; margin-top: -3rem; }
            .data-card-header, .data-card-body { padding: 1.5rem 1rem; }
            .advanced-filters-toolbar { flex-direction: column; }
            .filter-group { width: 100%; }
        }

        /* ── DESKTOP FIT TABLE STYLES ── */
        .data-card { overflow: visible !important; }
        
        .compact-table {
            width: 100% !important;
            table-layout: auto !important;
        }
        
        .compact-table th, 
        .compact-table td {
            white-space: normal !important;
            vertical-align: middle !important;
            word-break: break-word;
        }

        /* Protect Action Buttons on Desktop */
        .compact-table th:last-child,
        .compact-table td:last-child {
            min-width: 100px;
            white-space: nowrap !important;
            text-align: right;
        }

        /* ==========================================================================
           🔥 MORPH TABLE INTO CARDS ON MOBILE & TABLET (Max 1024px) 🔥
           ========================================================================== */
        @media screen and (max-width: 1024px) {
            
            .data-card-body { padding: 1rem !important; }
            .table-responsive { overflow-x: visible !important; -webkit-overflow-scrolling: auto; }
            
            table.compact-table, 
            table.compact-table tbody, 
            table.compact-table tr, 
            table.compact-table td {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important; 
                white-space: normal !important; 
            }

            table.compact-table thead {
                display: none !important;
            }

            table.compact-table tbody tr {
                margin-bottom: 1.25rem !important;
                border: 1px solid var(--border-color) !important;
                border-radius: 12px !important;
                padding: 1rem !important;
                background: #ffffff !important;
                box-shadow: var(--card-shadow) !important;
            }

            table.compact-table tbody td {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 0.6rem 0 !important;
                border-bottom: 1px dashed var(--border-color) !important;
                text-align: right !important; 
                border-top: none !important;
            }
            
            table.compact-table tbody td:last-child {
                border-bottom: none !important;
                padding-bottom: 0 !important;
                margin-top: 0.5rem;
                justify-content: flex-end !important;
            }

            table.compact-table tbody td::before {
                font-weight: 700;
                color: var(--text-muted);
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                text-align: left;
                margin-right: 1rem;
            }
            
            /* Fix hover odd shadows */
            table.compact-table tbody tr:nth-of-type(odd) td {
                box-shadow: none !important;
                background-color: transparent !important;
            }

            /* --- BASE COLUMNS --- */
            table.compact-table tbody td:nth-child(1)::before { content: "App ID"; }
            table.compact-table tbody td:nth-child(2)::before { content: "Service Type"; }
            table.compact-table tbody td:nth-child(3)::before { content: "Status"; }
            table.compact-table tbody td:nth-child(4)::before { content: "Payment"; }
            table.compact-table tbody td:nth-child(5)::before { content: "Amount"; }

            /* --- STANDARD TABLE --- */
            table.compact-table:not(.is-itr) tbody td:nth-child(6)::before { content: "Date Submitted"; }
            table.compact-table:not(.is-itr) tbody td:nth-child(7)::before { content: "Actions"; display: none; }

            /* --- ITR FILING TABLE --- */
            table.compact-table.is-itr tbody td:nth-child(6)::before { content: "ACK NO"; }
            table.compact-table.is-itr tbody td:nth-child(7)::before { content: "COMPUTATION"; }
            table.compact-table.is-itr tbody td:nth-child(8)::before { content: "BALANCE SHEET"; }
            table.compact-table.is-itr tbody td:nth-child(9)::before { content: "Date Submitted"; }
            table.compact-table.is-itr tbody td:nth-child(10)::before { content: "Actions"; display: none; }
        }
    </style>
@endsection

@section('content')
<div class="chq-wrapper">

    {{-- ── HERO SECTION ── --}}
    <header class="chq-hero">
        <div class="chq-hero-flex">
            <div class="chq-hero-title">
                <h1>Application Manager</h1>
                <p>Track, filter, and manage client submissions.</p>
            </div>
           <div>
                @if($type === 'itr-filing')
                    <a href="{{ route('services.show', 'itr-filing') }}" class="btn-new-app">
                        <i class="fas fa-plus"></i> New ITR Application
                    </a>
                @elseif($type === 'gst-registration')
                    <a href="{{ route('services.show', 'gst-registration') }}" class="btn-new-app">
                        <i class="fas fa-plus"></i> New GST Registration
                    </a>
                @elseif($type === 'gst-return-filing')
                    <a href="{{ route('services.show', 'gst-return-filing') }}" class="btn-new-app">
                        <i class="fas fa-plus"></i> New GST Return
                    </a>
                @else
                    <a href="{{ route('services.index') }}" class="btn-new-app">
                        <i class="fas fa-plus"></i> New Application
                    </a>
                @endif
            </div>
        </div>
    </header>

    <div class="chq-main">
        
        {{-- ── METRICS GRID ── --}}
       <div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon icon-blue">
            <i class="fas fa-file-alt fa-lg"></i>
        </div>
        <div class="metric-data">
            <span class="metric-label">Total Volume</span>
            <strong class="metric-value">{{ $stats->total ?? 0 }}</strong>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon icon-orange">
            <i class="fas fa-hourglass-half fa-lg"></i>
        </div>
        <div class="metric-data">
            <span class="metric-label">Pending Review</span>
            <strong class="metric-value">{{ $stats->pending ?? 0 }}</strong>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon icon-red">
            <i class="fas fa-times-circle fa-lg"></i>
        </div>
        <div class="metric-data">
            <span class="metric-label">Failed Payments</span>
            <strong class="metric-value">{{ $stats->failed ?? 0 }}</strong>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon icon-green">
            <i class="fas fa-calendar-check fa-lg"></i>
        </div>
        <div class="metric-data">
            <span class="metric-label">Processed This Month</span>
            <strong class="metric-value">{{ $stats->monthly ?? 0 }}</strong>
        </div>
    </div>
</div>

        {{-- ── DATA TABLE CARD WITH ADVANCED FILTERS ── --}}
        <div class="data-card">

            <div class="data-card-header">
                {{-- Quick Filter Tabs --}}
                <div class="applications-tabs">
                    <button class="app-filter active" data-filter="all">All Applications</button>
                    <button class="app-filter" data-filter="pending">Pending</button>
                    <button class="app-filter" data-filter="completed">Completed</button>
                    <button class="app-filter" data-filter="failed">Failed Payments</button>
                </div>

                {{-- Advanced Filters Toolbar --}}
                <div class="advanced-filters-toolbar">
                    <div class="filter-group">
                        <label for="filterService">Service Type</label>
                        <select id="filterService" class="filter-input">
                            <option value="">All Services</option>
                            @if(isset($services))
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filterStatus">App Status</label>
                        <select id="filterStatus" class="filter-input">
                            <option value="">Any Status</option>
                            <option value="DRAFT">Draft</option>
                            <option value="SUBMITTED">Submitted</option>
                            <option value="IN_PROGRESS">In Progress</option>
                            <option value="COMPLETED">Completed</option>
                            <option value="CANCELLED">Cancelled</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filterPayment">Payment</label>
                        <select id="filterPayment" class="filter-input">
                            <option value="">Any Payment</option>
                            <option value="SUCCESS">Success</option>
                            <option value="FAILED">Failed</option>
                            <option value="PENDING">Pending</option>
                            <option value="REFUNDED">Refunded</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filterDateFrom">Date From</label>
                        <input type="date" id="filterDateFrom" class="filter-input">
                    </div>

                    <div class="filter-group">
                        <label for="filterDateTo">Date To</label>
                        <input type="date" id="filterDateTo" class="filter-input">
                    </div>

                    <div class="filter-actions">
                        <button id="resetFilters" title="Clear Filters">
                            <i class="fas fa-undo-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

           <div class="data-card-body">
               <div class="table-responsive">
                    {{-- Added conditional 'is-itr' class here --}}
                    <table id="applicationsTable" class="table w-100 compact-table @if(request()->query('type') === 'itr-filing') is-itr @endif">
                        <thead>
                            <tr>
                                <th>App ID</th>
                                <th>Service Type</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Amount</th>
                                
                                {{-- Check URL param dynamically for Agent side headers --}}
                                @if(request()->query('type') === 'itr-filing')
                                    <th>ACK NO</th>
                                    <th>COMPUTATION</th>
                                    <th>BALANCE SHEET</th>
                                @endif
                                
                                <th>Date Submitted</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets/js/applications.js') }}?v={{ time() }}"></script>
@endsection