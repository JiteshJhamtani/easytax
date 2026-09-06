@extends('layouts.agent')

@section('title', 'Team Pricing & Margins | EasyTax')

@section('css')
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
        .card-box {
            background: #ffffff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .btn-brand-green {
            background-color: var(--green);
            color: #ffffff !important;
            font-weight: 700;
            border-radius: 8px;
            padding: 0.6rem 1.3rem;
            border: none;
            transition: all 0.2s;
        }
        .btn-brand-green:hover {
            background-color: var(--green-dark);
            transform: translateY(-1px);
        }
        .pricing-row-invalid {
            background-color: #fef2f2 !important;
        }
        .badge-locked {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            padding: 0.35rem 0.6rem;
            border-radius: 6px;
        }
    </style>
@endsection

@section('content')
<div class="chq-wrapper">
    <div class="chq-hero">
        <div class="chq-hero-flex d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="chq-hero-title">
                <h1>Team Service Pricing & Custom Margins</h1>
                <p>Customize the prices and commissions for your sub-agents. Any amount paid above the company minimum is refunded to you automatically upon payment confirmation.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('agent.margin-ledger.index') }}" class="btn btn-outline-success font-weight-bold" style="border-radius: 8px;">
                    <i class="fas fa-coins mr-1"></i> Margin Earnings
                </a>
                <a href="{{ route('agent.sub-agents.index') }}" class="btn btn-light border font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Team
                </a>
            </div>
        </div>
    </div>

    <div class="chq-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4">
                <i class="fas fa-check-circle mr-2"></i> {!! session('success') !!}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i> {!! session('error') !!}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="card-box p-4 mb-4">
            <form method="GET" action="{{ route('agent.team-pricing.index') }}" class="form-inline mb-0">
                <label class="font-weight-bold mr-2 text-dark">Configure Pricing For:</label>
                <select name="sub_agent_id" class="form-control mr-3" onchange="this.form.submit()">
                    <option value="">🏢 Entire Agency (Default for all sub-agents)</option>
                    @foreach($subAgents as $agent)
                        <option value="{{ $agent->id }}" {{ $selectedSubAgentId == $agent->id ? 'selected' : '' }}>
                            👤 {{ $agent->name }} ({{ $agent->agent_code }})
                        </option>
                    @endforeach
                </select>
                <span class="text-muted small">Select a specific team member to set a personalized price, or set the agency-wide standard.</span>
            </form>
        </div>

        <form method="POST" action="{{ route('agent.team-pricing.update') }}" id="pricingForm">
            @csrf
            <input type="hidden" name="sub_agent_id" value="{{ $selectedSubAgentId }}">

            <div class="card-box">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap">
                    <span class="font-weight-bold text-dark">
                        <i class="fas fa-calculator mr-1 text-primary"></i>
                        Pricing Formula: <code>Sub-Agent Pays = Price - Commission</code> &bull; <code>Your Extra Margin = Sub-Agent Pays - Company Minimum</code>
                    </span>
                    <span class="badge badge-warning text-dark px-2 py-1">
                        <i class="fas fa-shield-alt mr-1"></i> Sub-Agent payable must be &ge; Company Minimum
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 25%;">Service</th>
                                <th style="width: 15%;">Company Minimum</th>
                                <th style="width: 15%;">Sub-Agent Price (₹)</th>
                                <th style="width: 15%;">Sub-Agent Commission (₹)</th>
                                <th style="width: 15%;">Sub-Agent Pays (₹)</th>
                                <th style="width: 15%;">Your Extra Margin (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pricingRows as $index => $row)
                                <tr id="row-{{ $index }}" data-min="{{ $row['company_minimum'] }}">
                                    <td>
                                        <input type="hidden" name="pricing[{{ $index }}][service_id]" value="{{ $row['service_id'] }}">
                                        <strong>{{ $row['service_name'] }}</strong>
                                        <div class="text-muted text-xs">Base: ₹{{ number_format($row['base_price'], 2) }} &bull; Comm: ₹{{ number_format($row['base_commission'], 2) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge-locked" title="Fixed rate required by EasyTax platform">
                                            <i class="fas fa-lock mr-1 text-muted"></i> ₹{{ number_format($row['company_minimum'], 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                            name="pricing[{{ $index }}][price]" 
                                            class="form-control form-control-sm price-input" 
                                            value="{{ $row['sub_price'] }}" 
                                            data-index="{{ $index }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                            name="pricing[{{ $index }}][commission]" 
                                            class="form-control form-control-sm comm-input" 
                                            value="{{ $row['sub_commission'] }}" 
                                            data-index="{{ $index }}" required>
                                    </td>
                                    <td>
                                        <strong id="payable-{{ $index }}" class="text-dark">
                                            ₹{{ number_format($row['sub_payable'], 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <strong id="margin-{{ $index }}" class="text-success">
                                            +₹{{ number_format($row['margin'], 2) }}
                                        </strong>
                                        <div id="error-{{ $index }}" class="text-danger text-xs mt-1" style="display:none;">
                                            Below company min!
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
                    <span id="validationSummary" class="text-danger font-weight-bold small" style="display:none;">
                        <i class="fas fa-exclamation-circle mr-1"></i> Please fix highlighted rows before saving.
                    </span>
                    <button type="submit" class="btn-brand-green ml-auto" id="savePricingBtn">
                        <i class="fas fa-save mr-1"></i> Save Team Pricing Rules
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
function recalculateRow(index) {
    const row = $('#row-' + index);
    const minReceivable = parseFloat(row.data('min')) || 0;
    const price = parseFloat(row.find('.price-input').val()) || 0;
    const comm = parseFloat(row.find('.comm-input').val()) || 0;

    const netPayable = Math.max(0, price - comm);
    const margin = Math.max(0, netPayable - minReceivable);

    $('#payable-' + index).text('₹' + netPayable.toFixed(2));
    $('#margin-' + index).text('+₹' + margin.toFixed(2));

    if (netPayable < minReceivable) {
        row.addClass('pricing-row-invalid');
        $('#error-' + index).show();
        return false;
    } else {
        row.removeClass('pricing-row-invalid');
        $('#error-' + index).hide();
        return true;
    }
}

function validateAllRows() {
    let allValid = true;
    $('.price-input').each(function() {
        const index = $(this).data('index');
        const isValid = recalculateRow(index);
        if (!isValid) allValid = false;
    });

    if (!allValid) {
        $('#savePricingBtn').prop('disabled', true);
        $('#validationSummary').show();
    } else {
        $('#savePricingBtn').prop('disabled', false);
        $('#validationSummary').hide();
    }
}

$(document).on('input', '.price-input, .comm-input', function() {
    validateAllRows();
});

$(document).ready(function() {
    validateAllRows();
});
</script>
@endsection
