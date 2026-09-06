@extends('layouts.admin')

@section('title', 'Margin Payout Voucher #' . $payout->payout_number)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Payout Voucher #{{ $payout->payout_number }}</h1>
            <p class="text-muted mb-0 mt-1 text-xs">Settled on {{ $payout->payment_date->format('d M Y') }} by {{ $payout->admin->name ?? 'Admin' }}</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary font-weight-bold mr-2"><i class="fas fa-print mr-1"></i> Print Voucher</button>
            <a href="{{ route('admin.margin-payouts.index') }}" class="btn btn-secondary font-weight-bold"><i class="fas fa-arrow-left mr-1"></i> Back to Margin Payouts</a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">
        <div class="row">
            {{-- Summary Card --}}
            <div class="col-lg-4 mb-4">
                <div class="card modern-card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                        <h5 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-file-invoice-dollar text-success mr-2"></i> Disbursement Summary
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="text-center p-3 my-3 bg-light rounded border">
                            <span class="text-xs font-weight-bold text-uppercase text-muted">Total Disbursed Margin</span>
                            <h2 class="font-weight-bold text-success mb-1">₹{{ number_format((float) $payout->amount, 2) }}</h2>
                            <span class="badge badge-success px-3 py-1 font-weight-bold"><i class="fas fa-check-circle mr-1"></i> PAID / SETTLED</span>
                        </div>

                        <ul class="list-unstyled mb-0 text-sm">
                            <li class="py-2 border-bottom d-flex justify-content-between">
                                <span class="text-muted">Parent Agency</span>
                                <span class="font-weight-bold text-dark">{{ $payout->parentAgent->name ?? 'N/A' }}</span>
                            </li>
                            <li class="py-2 border-bottom d-flex justify-content-between">
                                <span class="text-muted">Agency Code</span>
                                <span class="font-monospace font-weight-bold">{{ $payout->parentAgent->agent_code ?? '-' }}</span>
                            </li>
                            <li class="py-2 border-bottom d-flex justify-content-between">
                                <span class="text-muted">Payment Mode</span>
                                <span class="font-weight-bold text-uppercase">{{ str_replace('_', ' ', $payout->payment_method) }}</span>
                            </li>
                            <li class="py-2 border-bottom d-flex justify-content-between">
                                <span class="text-muted">UTR / Transaction Ref</span>
                                <span class="font-monospace font-weight-bold text-primary">{{ $payout->transaction_reference }}</span>
                            </li>
                            <li class="py-2 border-bottom d-flex justify-content-between">
                                <span class="text-muted">Payment Date</span>
                                <span class="font-weight-bold">{{ $payout->payment_date->format('d M Y') }}</span>
                            </li>
                            <li class="py-2 border-bottom d-flex justify-content-between">
                                <span class="text-muted">Settled By Admin</span>
                                <span class="font-weight-bold">{{ $payout->admin->name ?? 'Admin' }}</span>
                            </li>
                            <li class="py-2 d-flex justify-content-between">
                                <span class="text-muted">Total Items Settled</span>
                                <span class="badge badge-info">{{ $payout->marginLogs->count() }} filings</span>
                            </li>
                        </ul>

                        @if($payout->notes)
                            <div class="mt-3 p-2 bg-light rounded text-xs text-muted border">
                                <strong>Remarks:</strong> {{ $payout->notes }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Breakdown Table --}}
            <div class="col-lg-8 mb-4">
                <div class="card modern-card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white pt-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title font-weight-bold text-dark mb-0">
                            <i class="fas fa-list text-primary mr-2"></i> Settled Application Items
                        </h5>
                        <span class="badge badge-light border font-weight-bold">{{ $payout->marginLogs->count() }} Applications</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-sm">
                                <thead class="bg-light text-muted text-xs text-uppercase">
                                    <tr>
                                        <th class="pl-3">App #</th>
                                        <th>Sub-Agent</th>
                                        <th>Service</th>
                                        <th class="text-right">Sub-Agent Paid</th>
                                        <th class="text-right">Company Retained</th>
                                        <th class="text-right pr-3">Margin Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payout->marginLogs as $log)
                                        <tr>
                                            <td class="pl-3 font-weight-bold">
                                                <a href="{{ route('admin.applications.show', $log->application_id) }}">#{{ $log->application_id }}</a>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-dark">{{ $log->subAgent->name ?? 'Team Member' }}</div>
                                                <small class="text-muted font-monospace">{{ $log->subAgent->agent_code ?? '-' }}</small>
                                            </td>
                                            <td>{{ $log->application->service->name ?? 'Service' }}</td>
                                            <td class="text-right font-weight-bold">₹{{ number_format((float) $log->sub_agent_paid, 2) }}</td>
                                            <td class="text-right text-muted">₹{{ number_format((float) $log->company_retained, 2) }}</td>
                                            <td class="text-right pr-3 font-weight-bold text-success">+₹{{ number_format((float) $log->margin_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="5" class="text-right pr-3">Total Margin Disbursed:</td>
                                        <td class="text-right pr-3 text-success" style="font-size: 1.1rem;">₹{{ number_format((float) $payout->amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection