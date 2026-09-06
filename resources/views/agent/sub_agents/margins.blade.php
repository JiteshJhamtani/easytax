@extends('layouts.agent')

@section('title', 'Margin Earnings & Refund Ledger | EasyTax')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>
        .chq-hero {
            background-color: var(--green-light);
            padding: 2.2rem 2.5rem 5rem;
            border-bottom: 1px solid #e2efe9;
        }
        .chq-hero-flex {
            max-width: 1300px;
            margin: 0 auto;
        }
        .chq-hero-title h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--slate);
            margin: 0 0 0.25rem;
        }
        .chq-hero-title p {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin: 0;
        }
        .chq-main {
            max-width: 1300px;
            margin: -3.5rem auto 3rem;
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }
        .kpi-card {
            background: #ffffff;
            border-radius: var(--radius);
            padding: 1.3rem 1.4rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 1.1rem;
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .kpi-val {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--slate);
            line-height: 1.1;
        }
        .kpi-lbl {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }
        .card-box {
            background: #ffffff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
<div class="chq-wrapper">
    <div class="chq-hero">
        <div class="chq-hero-flex d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="chq-hero-title">
                <h1>Agency Margin Earnings & Payout Ledger</h1>
                <p>Complete financial audit of extra margins earned from your team's filings and manual bank disbursements.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#bankDetailsModal" style="border-radius: 8px;">
                    <i class="fas fa-university mr-1"></i> Payout Bank Details
                </button>
                <a href="{{ route('agent.team-pricing.index') }}" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 8px;">
                    <i class="fas fa-tags mr-1"></i> Team Pricing
                </a>
                <a href="{{ route('agent.sub-agents.index') }}" class="btn btn-light border font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Team
                </a>
            </div>
        </div>
    </div>

    <div class="chq-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- KPI Cards Grid --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fef3c7; color:#d97706;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="kpi-val text-warning">₹{{ number_format($stats['accrued_amount'], 2) }}</div>
                    <div class="kpi-lbl">Accrued (Pending Payout)</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <div class="kpi-val text-success">₹{{ number_format($stats['settled_amount'], 2) }}</div>
                    <div class="kpi-lbl">Disbursed to Bank</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#ede9fe; color:#7c3aed;">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <div class="kpi-val text-primary">₹{{ number_format($stats['total_earned'], 2) }}</div>
                    <div class="kpi-lbl">Lifetime Margin Earned</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <div>
                    <div class="kpi-val">{{ number_format($stats['payouts_count']) }}</div>
                    <div class="kpi-lbl">Payout Disbursements</div>
                </div>
            </div>
        </div>

        {{-- Bank Details Quick Banner --}}
        <div class="card-box mb-4 p-3 bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 border">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 rounded bg-light text-primary font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-landmark"></i>
                </div>
                <div>
                    <div class="font-weight-bold text-dark mb-0" style="font-size: 0.95rem;">
                        Receiving Bank Account:
                        @if($parent->bank_account_number)
                            <span class="text-primary font-monospace">{{ $parent->bank_name ?? 'Bank' }} &bull; A/C: {{ Str::mask($parent->bank_account_number, '*', -4) }}</span>
                            @if($parent->bank_ifsc)
                                <span class="badge badge-light border text-muted ml-1">IFSC: {{ $parent->bank_ifsc }}</span>
                            @endif
                        @elseif($parent->bank_upi_id)
                            <span class="text-primary font-monospace">UPI: {{ $parent->bank_upi_id }}</span>
                        @else
                            <span class="text-danger font-weight-bold">Not configured yet</span>
                        @endif
                    </div>
                    <small class="text-muted">Company Admin uses these details to disburse your accrued team margins manually.</small>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-primary font-weight-bold" data-toggle="modal" data-target="#bankDetailsModal">
                <i class="fas fa-pen mr-1"></i> Update Bank / UPI
            </button>
        </div>

        {{-- Tabbed Card Box --}}
        <div class="card-box">
            <div class="border-bottom bg-light px-3 pt-2">
                <ul class="nav nav-tabs border-bottom-0 font-weight-bold" id="marginTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active text-dark" id="accruals-tab" data-toggle="tab" href="#accrualsPane" role="tab">
                            <i class="fas fa-list-alt mr-1 text-primary"></i> Sub-Agent Accruals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" id="payouts-tab" data-toggle="tab" href="#payoutsPane" role="tab">
                            <i class="fas fa-receipt mr-1 text-success"></i> Payout Disbursements
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content p-3">
                {{-- TAB 1: ACCRUALS --}}
                <div class="tab-pane fade show active" id="accrualsPane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="marginLedgerTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Sub-Agent</th>
                                    <th>Service</th>
                                    <th>App Ref</th>
                                    <th>Sub-Agent Paid</th>
                                    <th>Company Share</th>
                                    <th>Your Extra Margin</th>
                                    <th>Status</th>
                                    <th>Payout Settlement</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: PAYOUTS --}}
                <div class="tab-pane fade" id="payoutsPane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="marginPayoutsTable">
                            <thead>
                                <tr>
                                    <th>Voucher #</th>
                                    <th>Disbursed Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Bank UTR / Transaction No</th>
                                    <th>Payment Date</th>
                                    <th>Applications</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bank Details Modal --}}
<div class="modal fade" id="bankDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom-0 pt-4 px-4 pb-2">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-university text-primary mr-2"></i> Payout Bank & UPI Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('agent.margin-ledger.update-bank') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <p class="text-xs text-muted mb-3">Provide valid banking or UPI credentials. Admin verifies these details before disbursing your margin settlements.</p>
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-xs text-uppercase text-muted">Account Holder Name</label>
                        <input type="text" name="bank_account_holder" class="form-control" value="{{ old('bank_account_holder', $parent->bank_account_holder) }}" placeholder="e.g. Rahul Sharma" style="border-radius: 8px;">
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold text-xs text-uppercase text-muted">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $parent->bank_name) }}" placeholder="e.g. HDFC Bank" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold text-xs text-uppercase text-muted">IFSC Code</label>
                            <input type="text" name="bank_ifsc" class="form-control font-monospace text-uppercase" value="{{ old('bank_ifsc', $parent->bank_ifsc) }}" placeholder="e.g. HDFC0001234" style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-xs text-uppercase text-muted">Bank Account Number</label>
                        <input type="text" name="bank_account_number" class="form-control font-monospace" value="{{ old('bank_account_number', $parent->bank_account_number) }}" placeholder="e.g. 50100234567890" style="border-radius: 8px;">
                    </div>

                    <div class="text-center my-2 text-muted text-xs font-weight-bold">&mdash; OR &mdash;</div>

                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-xs text-uppercase text-muted">UPI ID / VPA</label>
                        <input type="text" name="bank_upi_id" class="form-control font-monospace" value="{{ old('bank_upi_id', $parent->bank_upi_id) }}" placeholder="e.g. rahul@okhdfcbank" style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 px-4 py-3">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4">Save Details</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    // Tab 1: Accruals Table
    $('#marginLedgerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("agent.margin-ledger.data") }}',
        columns: [
            { data: 'date', name: 'created_at' },
            { data: 'sub_agent', name: 'sub_agent' },
            { data: 'service', name: 'service' },
            { data: 'app_ref', name: 'application_id' },
            { data: 'sub_paid', name: 'sub_agent_paid', searchable: false },
            { data: 'company_share', name: 'company_retained', searchable: false },
            { data: 'margin_amount', name: 'margin_amount', searchable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'payout_info', name: 'payout_reference', orderable: false },
        ],
        order: [[0, 'desc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search ledger...",
        }
    });

    // Tab 2: Payouts Table (Initialized when tab is shown or upfront)
    var payoutsTable = $('#marginPayoutsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("agent.margin-ledger.payouts") }}',
        columns: [
            { data: 'voucher', name: 'payout_number' },
            { data: 'amount', name: 'amount', searchable: false },
            { data: 'mode', name: 'payment_method' },
            { data: 'reference', name: 'transaction_reference' },
            { data: 'date', name: 'payment_date' },
            { data: 'items', name: 'margin_logs_count', searchable: false, orderable: false },
        ],
        order: [[4, 'desc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search payouts...",
        }
    });

    // Ensure responsive resize when switching tabs
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
});
</script>
@endsection
