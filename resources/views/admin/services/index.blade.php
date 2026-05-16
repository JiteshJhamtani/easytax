@extends('layouts.admin')

@section('title', 'Services Management')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <style>
        /* ── PAGE HEADER ── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--slate-dark);
            margin: 0;
            letter-spacing: -0.02em;
        }

        /* ── PREMIUM BUTTON ── */
        .btn-premium {
            background-color: var(--slate-dark);
            color: #ffffff;
            font-weight: 700;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .btn-premium:hover {
            background-color: #000000;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* ── MASTER TABLE CARD ── */
        .table-card {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            padding: 1.5rem;
            overflow: hidden;
        }

        /* ── DATATABLES OVERRIDES ── */
        .dataTables_wrapper .row { align-items: center; }
        
        .dataTables_filter label { font-weight: 600; color: var(--text-muted); font-size: 0.85rem; width: 100%; }
        .dataTables_filter input {
            border: 1px solid var(--border); border-radius: 8px;
            padding: 0.4rem 0.75rem; margin-left: 0.5rem; outline: none; transition: all 0.2s;
        }
        .dataTables_filter input:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(30,156,93,0.15); }

        .dataTables_length label { font-weight: 600; color: var(--text-muted); font-size: 0.85rem; }
        .dataTables_length select {
            border: 1px solid var(--border); border-radius: 6px;
            padding: 0.3rem 1.5rem 0.3rem 0.5rem; margin: 0 0.5rem; outline: none;
        }

        table.dataTable {
    border-collapse: collapse !important; 
    margin-top: 1rem !important;
    margin-bottom: 1.5rem !important; 
    width: 100% !important; 
    min-width: 1000px !important; 
    border-bottom: 1px solid var(--border);
}
        
        table.dataTable thead th,
        table.dataTable tbody td {
    white-space: nowrap !important;
    word-wrap: normal !important; 
    word-break: keep-all !important; 
}
        
        table.dataTable thead th {
            background: #f8fafc; color: var(--text-muted); font-size: 0.75rem;
            text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--border) !important;
            border-top: none !important; padding: 1rem 0.75rem; vertical-align: middle; font-weight: 700;
        }
        table.dataTable tbody td {
            padding: 1rem 0.75rem; vertical-align: middle; color: var(--text);
            font-size: 0.9rem; border-bottom: 1px solid var(--ink-100);
        }
        table.dataTable tbody tr { transition: background 0.2s; }
        table.dataTable tbody tr:hover { background: #f8fafc; }

        .dataTables_info { color: var(--text-muted); font-size: 0.85rem; font-weight: 500; }
        .page-item.active .page-link { background-color: var(--slate-dark); border-color: var(--slate-dark); color: white; border-radius: 6px; }
        .page-link {
            color: var(--slate); border: 1px solid var(--border); border-radius: 6px;
            margin: 0 3px; font-size: 0.85rem; font-weight: 600;
        }
        .page-link:hover { background: var(--ink-100); color: var(--slate-dark); }

        /* ── CUSTOM BADGES ── */
        .custom-badge {
            display: inline-flex; align-items: center; padding: 0.35rem 0.85rem;
            border-radius: 50px; font-size: 0.75rem; font-weight: 700;
            letter-spacing: 0.05em; text-transform: uppercase;
        }
        .badge-success-soft { background-color: var(--green-light); color: var(--green-dark); }
        .badge-danger-soft { background-color: #FEE2E2; color: #DC2626; }
        .badge-info-soft { background-color: #DBEAFE; color: #1E40AF; }
        
        .code-tag {
            background: var(--ink-100); color: var(--slate); padding: 0.3rem 0.6rem;
            border-radius: 6px; font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem; font-weight: 700; border: 1px solid var(--border);
            white-space: nowrap !important;
            display: inline-block !important;
        }

        /* 🔥 BULLETPROOF MOBILE OVERRIDES 🔥 */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column !important;
                align-items: flex-start !important;
            }
            .btn-premium {
                width: 100% !important;
                justify-content: center !important;
                margin-top: 15px !important;
            }
            .table-card {
                padding: 1rem !important;
            }
            
            /* Overriding DataTables specific mobile grid */
            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter,
            div.dataTables_wrapper div.dataTables_info,
            div.dataTables_wrapper div.dataTables_paginate {
                text-align: left !important;
            }
            
            div.dataTables_wrapper div.dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
                margin-top: 8px !important;
                display: block !important;
            }
            
            .dataTables_wrapper .row > div {
                margin-bottom: 15px !important;
            }
        


        /* 🔥 MORPH TABLE INTO CARDS ON MOBILE 🔥 */
          /* 1. Force the table to behave like standard blocks instead of a rigid grid */
        table.dataTable, 
        table.dataTable tbody, 
        table.dataTable tr, 
        table.dataTable td {
            display: block !important;
            width: 100% !important;
            min-width: 0 !important; /* Removes our previous scroll fix */
            white-space: normal !important; 
        }

        /* 2. Hide the original top headers completely */
        table.dataTable thead {
            display: none !important;
        }

        /* 3. Style every Row as a beautiful Card */
        table.dataTable tbody tr {
            margin-bottom: 1.25rem !important;
            border: 1px solid var(--border) !important;
            border-radius: 12px !important;
            padding: 1rem !important;
            background: var(--surface) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
        }

        /* 4. Style the Cells inside the card (Flexbox for Label + Value) */
        table.dataTable tbody td {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 0.6rem 0 !important;
            border-bottom: 1px dashed var(--ink-100) !important;
            text-align: right !important; /* Forces values to the right */
        }
        
        /* Remove the dashed line from the last item (Actions) */
        table.dataTable tbody td:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
            margin-top: 0.5rem;
            justify-content: flex-end !important; /* Push buttons to the right */
        }

        /* 5. Inject the Column Labels using CSS pseudo-elements! */
        table.dataTable tbody td::before {
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-align: left;
        }

        /* Map each column to its name based on the order in your DataTables JS */
        table.dataTable tbody td:nth-child(1)::before { content: "ID"; }
        table.dataTable tbody td:nth-child(2)::before { content: "Name"; }
        table.dataTable tbody td:nth-child(3)::before { content: "Slug"; }
        table.dataTable tbody td:nth-child(4)::before { content: "Price"; }
        table.dataTable tbody td:nth-child(5)::before { content: "Commission"; }
        table.dataTable tbody td:nth-child(6)::before { content: "Apps"; }
        table.dataTable tbody td:nth-child(7)::before { content: "Status"; }
        table.dataTable tbody td:nth-child(8)::before { content: "Action"; display: none; /* Hides the word 'Action' so just buttons show */ }
}
      
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Services Management</h1>
        <a href="{{ route('admin.services.create') }}" class="btn-premium">
            <i class="fas fa-plus-circle"></i> Add New Service
        </a>
    </div>

    <div class="table-card">
        <table id="servicesTable" class="table w-100">
            <thead>
                <tr>
                    <th class="pl-3">ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th class="text-right">Price</th>
                    <th class="text-center">Commission</th>
                    <th class="text-center">Apps</th>
                    <th class="text-center">Status</th>
                    <th class="text-center pr-3">Action</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

   <script>
    $(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#servicesTable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.services.datatable') }}',
            language: {
                search: "",
                searchPlaceholder: "Search services..."
            },
            columns: [
                { data: 'id', className: 'pl-3 font-weight-bold text-dark' },
                { data: 'name', className: 'font-weight-bold text-dark' },
                { 
                    data: 'slug', 
                    render: function(data) { return `<span class="code-tag">${data}</span>`; }
                },
                { 
                    data: 'price', 
                    className: 'text-right font-weight-bold',
                    render: function(data) { 
                        let amount = data ? parseFloat(data) : 0;
                        return `<span style="color: var(--green);">₹${amount.toFixed(2)}</span>`; 
                    }
                },
                { 
                    data: 'commission_display', 
                    className: 'text-center font-weight-bold', 
                    searchable: false, 
                    render: function(data){ return `<span style="color: var(--slate);">${data}</span>`; } 
                },
                { 
                    data: 'applications_count', 
                    className: 'text-center',
                    searchable: false,  
                    render: function(data) { 
                        return data ? `<span class="custom-badge badge-info-soft">${data}</span>` : '<span class="text-muted">-</span>'; 
                    }
                },
                { 
                    data: 'active', 
                    className: 'text-center',
                    searchable: false, 
                    render: function(data) { 
                        if (data) { return '<span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i> Active</span>'; }
                        return '<span class="custom-badge badge-danger-soft"><i class="fas fa-ban mr-1"></i> Inactive</span>'; 
                    }
                },
                { data: 'action', className: 'text-center pr-3', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endsection