@extends('layouts.admin')

@section('title', 'Agency Margin Payouts')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Agency Margin Payouts</h1>
            <p class="text-muted mb-0 text-xs">Manual settlement of extra margins earned by parent agencies from sub-agent filings.</p>
        </div>
        <div>
            <a href="{{ route('admin.payouts.index') }}" class="btn btn-outline-secondary font-weight-bold shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-coins mr-1"></i> Direct Commission Payouts
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">

        {{-- KPI ROW --}}
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-3" style="border-radius: 12px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="metric-label font-weight-bold text-xs text-uppercase text-muted">Accrued Margin Liability</span>
                        <div class="metric-icon bg-orange-soft text-orange p-2 rounded" style="background:#fef3c7; color:#d97706;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                    <div class="metric-value font-weight-bold text-warning" style="font-size: 1.6rem;">₹{{ number_format($kpis['total_accrued'], 2) }}</div>
                    <div class="text-xs text-muted mt-1">Pending manual disbursement to parent agencies</div>
                </div>
            </div>

            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-3" style="border-radius: 12px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="metric-label font-weight-bold text-xs text-uppercase text-muted">Total Settled Margins</span>
                        <div class="metric-icon bg-green-soft text-success p-2 rounded" style="background:#dcfce7; color:#16a34a;">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                    <div class="metric-value font-weight-bold text-success" style="font-size: 1.6rem;">₹{{ number_format($kpis['total_settled'], 2) }}</div>
                    <div class="text-xs text-muted mt-1">Total extra margins paid out to bank/UPI</div>
                </div>
            </div>

            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-3" style="border-radius: 12px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="metric-label font-weight-bold text-xs text-uppercase text-muted">Agencies Pending Settlement</span>
                        <div class="metric-icon bg-blue-soft text-primary p-2 rounded" style="background:#e0f2fe; color:#0284c7;">
                            <i class="fas fa-users-cog"></i>
                        </div>
                    </div>
                    <div class="metric-value font-weight-bold text-dark" style="font-size: 1.6rem;">{{ number_format($kpis['pending_agencies_count']) }}</div>
                    <div class="text-xs text-muted mt-1">Agencies with accrued margin balances</div>
                </div>
            </div>
        </div>

        {{-- AGENCIES WITH ACCRUED BALANCES --}}
        <div class="card modern-card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow:hidden;">
            <div class="card-header bg-white pt-3 pb-2 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-wallet text-warning mr-2"></i> Agencies With Pending Accrued Margins
                </h5>
                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1">{{ $pendingAgencies->count() }} Agencies Due</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="pl-3">Parent Agency</th>
                                <th>Sub-Agents</th>
                                <th>Receiving Bank / UPI</th>
                                <th class="text-center">Pending Apps</th>
                                <th class="text-right">Accrued Margin</th>
                                <th class="text-center pr-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingAgencies as $agency)
                                <tr>
                                    <td class="pl-3">
                                        <div class="font-weight-bold text-dark">
                                            <a href="{{ route('admin.agents.show', $agency->id) }}">{{ $agency->name }}</a>
                                        </div>
                                        <div class="text-xs text-muted">
                                            <span class="badge badge-light border font-monospace">{{ $agency->agent_code }}</span>
                                            &bull; {{ $agency->mobile_number ?? $agency->email }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $agency->sub_agents_count }} team members</span>
                                    </td>
                                    <td>
                                        @if($agency->bank_account_number)
                                            <div class="font-weight-bold text-xs text-dark">
                                                <i class="fas fa-university text-secondary mr-1"></i>{{ $agency->bank_name ?? 'Bank' }}
                                            </div>
                                            <div class="text-xs font-monospace text-muted">
                                                A/C: {{ Str::mask($agency->bank_account_number, '*', -4) }} | IFSC: {{ $agency->bank_ifsc }}
                                            </div>
                                        @elseif($agency->bank_upi_id)
                                            <div class="text-xs font-monospace text-primary">
                                                <i class="fas fa-mobile-alt mr-1"></i>UPI: {{ $agency->bank_upi_id }}
                                            </div>
                                        @else
                                            <span class="badge badge-secondary text-xs">No Bank on File</span>
                                        @endif
                                    </td>
                                    <td class="text-center font-weight-bold">
                                        <span class="badge badge-warning text-dark px-2 py-1">{{ $agency->pending_items_count }} cases</span>
                                    </td>
                                    <td class="text-right font-weight-bold text-success" style="font-size: 1.1rem;">
                                        ₹{{ number_format((float) $agency->pending_amount, 2) }}
                                    </td>
                                    <td class="text-center pr-3">
                                        <button type="button" class="btn btn-sm btn-success font-weight-bold openSettleModalBtn shadow-sm px-3"
                                                data-agent-id="{{ $agency->id }}"
                                                data-agent-name="{{ $agency->name }}"
                                                style="border-radius: 6px;">
                                            <i class="fas fa-money-check-alt mr-1"></i> Settle Payout
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                                        All agency margins are fully settled! No pending accrued balances.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- HISTORICAL PAYOUTS TABLE --}}
        <div class="card modern-card shadow-sm border-0" style="border-radius: 12px; overflow:hidden;">
            <div class="card-header bg-white pt-3 pb-2 border-bottom">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-history text-primary mr-2"></i> Margin Disbursement History
                </h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="marginPayoutsTable" class="table table-bordered table-striped modern-table w-100">
                        <thead>
                            <tr>
                                <th>Voucher #</th>
                                <th>Parent Agency</th>
                                <th>Amount Disbursed</th>
                                <th>Payment Mode</th>
                                <th>Bank UTR / Reference No</th>
                                <th>Payment Date</th>
                                <th>Processed By</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- SETTLE PAYOUT MODAL --}}
    <div class="modal fade" id="settleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom-0 pb-3 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold text-dark">
                        <i class="fas fa-money-bill-wave text-success mr-2"></i> Settle Agency Margin Payout
                    </h5>
                    <button type="button" class="close text-secondary" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="settleForm">
                    @csrf
                    <input type="hidden" id="settleAgentId" name="agent_id">

                    <div class="modal-body px-4 py-3">
                        <div class="p-3 bg-light rounded mb-3 border d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-1" id="modalAgentName">Agency Name</h6>
                                <div class="text-xs text-muted" id="modalAgentCode">Code: -</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-muted font-weight-bold text-uppercase">Payout Destination</div>
                                <div class="font-weight-bold text-primary" id="modalBankDetails">Fetching bank details...</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="font-weight-bold text-xs text-uppercase text-muted d-flex justify-content-between align-items-center">
                                <span>Accrued Margin Items to Settle (<span id="modalItemsCount">0</span>)</span>
                                <span class="text-success font-weight-bold" style="font-size: 1.1rem;">Total: ₹<span id="modalTotalAmount">0.00</span></span>
                            </label>
                            <div style="max-height: 220px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <table class="table table-sm table-striped mb-0 text-xs">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th>App #</th>
                                            <th>Sub-Agent</th>
                                            <th>Service</th>
                                            <th class="text-right">Sub-Agent Paid</th>
                                            <th class="text-right">Company Share</th>
                                            <th class="text-right">Margin</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalItemsBody">
                                        {{-- Injected via JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-xs text-uppercase text-muted">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-control" required style="border-radius: 8px;">
                                    <option value="bank_transfer">Bank Transfer (NEFT / RTGS / IMPS)</option>
                                    <option value="upi">UPI / Virtual Payment Address</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="cash">Cash</option>
                                    <option value="other">Other / Internal</option>
                                </select>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-xs text-uppercase text-muted">Bank UTR / Transaction Ref <span class="text-danger">*</span></label>
                                <input type="text" name="transaction_reference" class="form-control font-monospace" required placeholder="e.g. HDFC000123456789 or UPI/4231..." style="border-radius: 8px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-xs text-uppercase text-muted">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-xs text-uppercase text-muted">Admin Notes / Remarks</label>
                                <input type="text" name="notes" class="form-control" placeholder="e.g. Paid via HDFC Corporate Net Banking" style="border-radius: 8px;">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top-0 px-4 py-3">
                        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="submitSettleBtn" class="btn btn-success font-weight-bold px-4">
                            <i class="fas fa-check-circle mr-1"></i> Confirm & Disburse Payout
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/applications.css') }}">
    <style>
        .gap-2 { gap: 0.5rem; }
    </style>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            var table = $('#marginPayoutsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.margin-payouts.table") }}',
                columns: [
                    { data: 'voucher', name: 'payout_number' },
                    { data: 'agent', name: 'parentAgent.name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'payment_mode', name: 'payment_method' },
                    { data: 'reference', name: 'transaction_reference' },
                    { data: 'date', name: 'payment_date' },
                    { data: 'processed_by', name: 'admin.name', orderable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                ],
                order: [[5, 'desc']]
            });

            $('.openSettleModalBtn').on('click', function() {
                let agentId = $(this).data('agent-id');
                let agentName = $(this).data('agent-name');

                $('#settleAgentId').val(agentId);
                $('#modalAgentName').text(agentName);
                $('#modalItemsBody').html('<tr><td colspan="6" class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Loading details...</td></tr>');
                $('#modalBankDetails').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                $('#settleModal').modal('show');

                $.get(`/admin/margin-payouts/agent/${agentId}/accrued`, function(res) {
                    $('#modalAgentCode').text(`Code: ${res.agent.agent_code || '-'} | Phone: ${res.agent.mobile_number || '-'}`);
                    
                    if (res.agent.bank_account_number) {
                        $('#modalBankDetails').html(`${res.agent.bank_name || 'Bank'} &bull; A/C: ${res.agent.bank_account_number} <br><small class="text-muted">IFSC: ${res.agent.bank_ifsc || '-'}</small>`);
                    } else if (res.agent.bank_upi_id) {
                        $('#modalBankDetails').html(`UPI: ${res.agent.bank_upi_id}`);
                    } else {
                        $('#modalBankDetails').html('<span class="text-danger font-weight-bold">No bank details on file</span>');
                    }

                    $('#modalTotalAmount').text(res.total_amount.toFixed(2));
                    $('#modalItemsCount').text(res.items_count);

                    let rows = '';
                    if (res.logs.length === 0) {
                        rows = '<tr><td colspan="6" class="text-center text-muted py-2">No accrued items found.</td></tr>';
                    } else {
                        res.logs.forEach(log => {
                            rows += `
                                <tr>
                                    <td class="font-weight-bold">#${log.application_id}</td>
                                    <td>${log.sub_agent_name} <span class="text-muted font-monospace">(${log.sub_agent_code})</span></td>
                                    <td>${log.service_name}</td>
                                    <td class="text-right">₹${log.sub_agent_paid.toFixed(2)}</td>
                                    <td class="text-right">₹${log.company_retained.toFixed(2)}</td>
                                    <td class="text-right text-success font-weight-bold">+₹${log.margin_amount.toFixed(2)}</td>
                                </tr>
                            `;
                        });
                    }
                    $('#modalItemsBody').html(rows);
                }).fail(function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to fetch accrued details.', 'error');
                    $('#settleModal').modal('hide');
                });
            });

            $('#settleForm').on('submit', function(e) {
                e.preventDefault();
                let agentId = $('#settleAgentId').val();
                let $btn = $('#submitSettleBtn');

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing Payout...');

                $.post(`/admin/margin-payouts/agent/${agentId}/settle`, $(this).serialize())
                    .done(function(res) {
                        $('#settleModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Payout Disbursed!',
                            text: res.message,
                            confirmButtonColor: '#16a34a',
                        }).then(() => {
                            window.location.reload();
                        });
                    })
                    .fail(function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Could not process payout.', 'error');
                    })
                    .always(function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Confirm & Disburse Payout');
                    });
            });
        });
    </script>
@endsection