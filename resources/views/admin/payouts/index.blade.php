@extends('layouts.admin')

@section('title', 'Agent Payouts')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="m-0 text-dark font-weight-bold">Agent Payouts</h1>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">

        {{-- ============================= --}}
        {{-- KPI ROW (Placeholders) --}}
        {{-- ============================= --}}
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card">
                    <div class="metric-icon bg-orange-soft text-orange">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Total Pending Payout</span>
                        <span class="metric-value text-dark" id="pendingAmount">₹0</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card">
                    <div class="metric-icon bg-green-soft text-success">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Total Paid</span>
                        <span class="metric-value text-dark" id="paidAmount">₹0</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card">
                    <div class="metric-icon bg-blue-soft text-primary">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Total Payouts</span>
                        <span class="metric-value text-dark" id="totalPayouts">0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- GENERATE PANEL --}}
        {{-- ============================= --}}
        <div class="card modern-card mb-4">
            <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-calculator text-primary mr-2"></i> Generate Payouts
                </h3>
            </div>

            <div class="card-body">
                <form id="generateForm">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="form-label-custom">Agent</label>
                            <select name="agent_id" class="form-control custom-input">
                                <option value="">All Agents</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="form-label-custom">Start Date</label>
                            <input type="date" name="start_date" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="form-label-custom">End Date</label>
                            <input type="date" name="end_date" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="button" id="previewBtn"
                                class="btn btn-info flex-grow-1 font-weight-bold shadow-sm">
                                <i class="fas fa-eye mr-1"></i> Preview
                            </button>
                            <button type="submit" id="generateBtn"
                                class="btn btn-success flex-grow-1 font-weight-bold shadow-sm">
                                <i class="fas fa-bolt mr-1"></i> Generate
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- PAYOUT TABLE --}}
        {{-- ============================= --}}
        <div class="card modern-card">
            <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-history text-primary mr-2"></i> Payout History
                </h3>
            </div>

            <div class="card-body">
                <table id="payoutTable" class="table table-bordered table-striped modern-table w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Agent</th>
                            <th>Amount</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    {{-- ============================= --}}
    {{-- PREVIEW MODAL --}}
    {{-- ============================= --}}
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom-0 pb-3 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold text-dark">
                        <i class="fas fa-search-dollar text-primary mr-2"></i> Payout Preview
                    </h5>
                    <button type="button" class="close text-secondary" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-0">
                    <table class="table modern-table mb-0">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4">Agent</th>
                                <th>Applications</th>
                                <th class="px-4">Commission</th>
                            </tr>
                        </thead>
                        <tbody id="previewBody">
                            {{-- Injected via JS --}}
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer bg-light border-top-0 px-4 py-3">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/applications.css') }}">
    <style>
        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.4rem;
            letter-spacing: 0.5px;
        }

        .custom-input {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: all 0.2s;
        }

        .custom-input:focus {
            border-color: #0044b2;
            box-shadow: 0 0 0 3px rgba(0, 68, 178, 0.15);
            outline: none;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        /* Custom SweetAlert adjustments for modern look */
        div:where(.swal2-container) div:where(.swal2-popup) {
            border-radius: 12px;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Helper: SweetAlert Toast Notifications
            |--------------------------------------------------------------------------
            */
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Helper: Parse Laravel Validation Errors
            |--------------------------------------------------------------------------
            */
            function handleAjaxError(xhr) {
                let errorMsg = 'An unexpected error occurred.';

                // Check for Laravel validation errors (422)
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    errorMsg = '<ul class="text-left mb-0 pl-3">';
                    for (let field in errors) {
                        errorMsg += `<li>${errors[field][0]}</li>`; // Get the first error for each field
                    }
                    errorMsg += '</ul>';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    // Check for general custom exception messages
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Action Failed',
                    html: errorMsg,
                    confirmButtonColor: '#0044b2',
                    confirmButtonText: 'Understood'
                });
            }

            /*
            |--------------------------------------------------------------------------
            | DataTable
            |--------------------------------------------------------------------------
            */
            const table = $('#payoutTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.payouts.table') }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'agent',
                        className: 'font-weight-bold text-dark'
                    },
                    {
                        data: 'amount',
                        className: 'text-success font-weight-bold'
                    },
                    {
                        data: 'period'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [5, 'desc']
                ] // Order by created_at descending by default
            });

            /*
            |--------------------------------------------------------------------------
            | Preview
            |--------------------------------------------------------------------------
            */
            $('#previewBtn').click(function() {
                let $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...');

                $.post("{{ route('admin.payouts.preview') }}", $('#generateForm').serialize())
                    .done(function(data) {
                        let html = '';

                        if (data.length === 0) {
                            html =
                                '<tr><td colspan="3" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 opacity-50 d-block"></i>No pending commissions found for this period.</td></tr>';
                        } else {
                            data.forEach(function(row) {
                                html += `
                            <tr>
                                <td class="px-4 font-weight-bold text-dark">${row.agent_id}</td>
                                <td><span class="badge badge-info px-2 py-1">${row.applications} apps</span></td>
                                <td class="px-4 text-success font-weight-bold">₹${row.amount}</td>
                            </tr>
                        `;
                            });
                        }

                        $('#previewBody').html(html);
                        $('#previewModal').modal('show');
                    })
                    .fail(function(xhr) {
                        handleAjaxError(xhr);
                    })
                    .always(function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-eye mr-1"></i> Preview');
                    });
            });

            /*
            |--------------------------------------------------------------------------
            | Generate
            |--------------------------------------------------------------------------
            */
            $('#generateForm').submit(function(e) {
                e.preventDefault();

                let $btn = $('#generateBtn');
                $btn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i> Generating...');

                $.post("{{ route('admin.payouts.generate') }}", $(this).serialize())
                    .done(function(res) {
                        table.ajax.reload();

                        // Show beautiful success Toast
                        Toast.fire({
                            icon: 'success',
                            title: res.count + ' payout(s) generated successfully!'
                        });

                        $('#generateForm')[0].reset();
                    })
                    .fail(function(xhr) {
                        handleAjaxError(xhr);
                    })
                    .always(function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-bolt mr-1"></i> Generate');
                    });
            });

            /*
            |--------------------------------------------------------------------------
            | Mark as Paid
            |--------------------------------------------------------------------------
            */
            $('#payoutTable').on('click', '.markPaid', function(e) {
                e.preventDefault();
                let payoutId = $(this).data('id');

                // Beautiful Confirmation Dialog instead of basic confirm()
                Swal.fire({
                    title: 'Settle Payout?',
                    text: "Are you sure you want to mark this payout as paid? This cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#00b259',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fas fa-check-circle mr-1"></i> Yes, Mark Paid'
                }).then((result) => {
                    if (result.isConfirmed) {

                        // Construct URL correctly based on your typical Laravel resource routing
                        // Update this URL string if your web.php route looks different!
                        $.post(`/admin/payouts/${payoutId}/mark-paid`)
                            .done(function(res) {
                                table.ajax.reload(null, false);

                                Toast.fire({
                                    icon: 'success',
                                    title: 'Payout has been marked as paid.'
                                });
                            })
                            .fail(function(xhr) {
                                handleAjaxError(xhr);
                            });
                    }
                });
            });

        });
    </script>
@endsection
