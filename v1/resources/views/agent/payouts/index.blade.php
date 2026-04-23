@extends('adminlte::page')

@section('title', 'My Payouts')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="m-0 text-dark font-weight-bold">Payout History</h1>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">

        {{-- ============================= --}}
        {{-- QUICK KPI SUMMARY --}}
        {{-- ============================= --}}
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100"
                    style="border-radius: 12px; padding: 1.5rem; display: flex; align-items: center;">
                    <div class="metric-icon"
                        style="width: 54px; height: 54px; border-radius: 12px; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 1.25rem;">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div>
                        <span
                            style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Lifetime
                            Earnings</span>
                        {{-- Note: Pass $lifetimeEarnings from your controller --}}
                        <h3 class="m-0 font-weight-bold text-dark" style="font-size: 1.8rem;">
                            ₹{{ number_format($lifetimeEarnings ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3 mb-md-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100"
                    style="border-radius: 12px; padding: 1.5rem; display: flex; align-items: center;">
                    <div class="metric-icon"
                        style="width: 54px; height: 54px; border-radius: 12px; background: #e0e7ff; color: #3730a3; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 1.25rem;">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div>
                        <span
                            style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Last
                            Payout</span>
                        {{-- Note: Pass $lastPayoutAmount from your controller --}}
                        <h3 class="m-0 font-weight-bold text-dark" style="font-size: 1.8rem;">
                            ₹{{ number_format($lastPayoutAmount ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- PAYOUTS TABLE --}}
        {{-- ============================= --}}
        <div class="card modern-card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Processed Payouts
                </h3>
            </div>

            <div class="card-body">
                <table id="payoutTable" class="table table-bordered table-striped modern-table w-100">
                    <thead>
                        <tr>
                            <th class="pl-3">Payout ID</th>
                            <th class="text-right">Amount</th>
                            <th>Period</th>
                            <th class="text-center">Status</th>
                            <th class="pr-3">Paid Date</th>
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
            white-space: nowrap;
        }

        .badge-success-soft {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-warning-soft {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
        }

        /* Pagination & DataTable Overrides */
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
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#payoutTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('agent.payouts.table') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        className: 'pl-3 font-weight-bold text-dark',
                        render: function(data) {
                            return '#' + data;
                        }
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        className: 'text-right text-success font-weight-bold', // Right-aligned
                        render: function(data) {
                            return '₹' + parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'period',
                        name: 'period',
                        className: 'text-muted'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function(data) {
                            // Check if status string contains 'paid' or 'success'
                            if (data && data.toString().toLowerCase().includes('paid')) {
                                return '<span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i> Paid</span>';
                            }
                            return '<span class="custom-badge badge-warning-soft"><i class="fas fa-clock mr-1"></i> Pending</span>';
                        }
                    },
                    {
                        data: 'paid_at',
                        name: 'paid_at',
                        className: 'pr-3',
                        render: function(data) {
                            // If no date is provided, show a nice empty state rather than a blank box
                            if (!data) {
                                return '<span class="text-muted font-italic"><i class="fas fa-minus"></i></span>';
                            }
                            return `<span class="font-weight-medium text-dark">${data}</span>`;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ] // Order by Payout ID descending by default (newest first)
            });

        });
    </script>
@endsection
