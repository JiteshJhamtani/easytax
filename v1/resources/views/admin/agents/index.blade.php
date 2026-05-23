@extends('adminlte::page')

@section('title', 'Agents Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="m-0 text-dark font-weight-bold">Agents Management</h1>
        <a href="{{ route('admin.agents.create') }}" class="btn btn-primary font-weight-bold shadow-sm">
            <i class="fas fa-user-plus mr-1"></i> Add New Agent
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">

        <div class="card modern-card shadow-sm border-0">

            <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-users text-primary mr-2"></i> Registered Agents
                </h3>
            </div>

            <div class="card-body">
                <table id="agentsTable" class="table table-bordered table-striped modern-table w-100">
                    <thead>
                        <tr>
                            <th class="pl-3">ID</th>
                            <th>Agent Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-center">Applications</th>
                            <th class="text-right">Commission</th>
                            <th class="text-right">Payouts</th>
                            <th class="text-center">Status</th>
                            <th class="text-center pr-3">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>

    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <style>
        /* Base Card Styling */
        .modern-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }

        /* Modern Table Styling */
        table.dataTable.modern-table {
            border-collapse: collapse !important;
            margin-top: 0.5rem !important;
            margin-bottom: 1.5rem !important;
            width: 100% !important;
        }

        .modern-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .modern-table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            color: #475569;
            font-size: 0.95rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .modern-table tbody tr {
            transition: background 0.2s;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        /* Custom Soft Badges */
        .custom-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            
        }

        .badge-success-soft {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-danger-soft {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Agent Code Badge */
        .agent-code-tag {
            background: #f1f5f9;
            color: #334155;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
        }

        /* Pagination Controls */
        .dataTables_wrapper .row:last-child {
            align-items: center;
            padding-top: 0.5rem;
        }

        .dataTables_info {
            color: #64748b;
            font-size: 0.875rem;
        }

        .page-item.active .page-link {
            background-color: #0044b2;
            border-color: #0044b2;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 68, 178, 0.2);
        }

        .page-link {
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin: 0 2px;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function() {

            // Ensure CSRF for any subsequent AJAX calls inside DataTables (like deleting)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#agentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.agents.datatable') }}',
                columns: [{
                        data: 'id',
                        className: 'pl-3 font-weight-bold text-dark'
                    },
                    {
                        data: 'agent_code',
                        render: function(data) {
                            return `<span class="agent-code-tag">${data}</span>`;
                        }
                    },
                    {
                        data: 'name',
                        className: 'font-weight-bold text-dark'
                    },
                    {
                        data: 'email',
                        className: 'text-muted'
                    },
                    {
                        data: 'applications',
                        className: 'text-center font-weight-medium',
                        render: function(data) {
                            return data ?
                                `<span class="badge badge-info px-2 py-1">${data}</span>` :
                                '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'commission',
                        className: 'text-right text-success font-weight-bold',
                        render: function(data) {
                            // Formatting raw number to currency on the client side
                            let amount = data ? parseFloat(data) : 0;
                            return '₹' + amount.toFixed(2);
                        }
                    },
                    {
                        data: 'payouts',
                        className: 'text-right text-primary font-weight-bold',
                        render: function(data) {
                            let amount = data ? parseFloat(data) : 0;
                            return '₹' + amount.toFixed(2);
                        }
                    },
                    {
                        data: 'is_active',
                        className: 'text-center',
                        render: function(data) {
                        console.log(data);

                            // Assumes your backend returns something like 'active' or 'inactive'

                            if (data) {
                                return '<span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i> Active</span>';
                            }
                            return '<span class="custom-badge badge-danger-soft"><i class="fas fa-ban mr-1"></i> Inactive</span>';
                        }
                    },
                    {
                        data: 'action',
                        className: 'text-center pr-3',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ] // Load newest agents first
            });

        });
    </script>
@endsection
