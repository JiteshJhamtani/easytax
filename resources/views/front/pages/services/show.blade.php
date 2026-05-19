@extends('layouts.agent')

@section('title', $service->name . ' Application | Agent Portal')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    
    <style>
        /* ── PAGE RESET ──   */  
        .content-body { padding: 0 !important; background-color: #F8F9FA; }
        
        .service-view-wrapper {
            --brand-green: #1E9C5D;
            --brand-mint: #EDF7F4;
            --brand-slate: #2E3D4E;
            --text-main: #333333;
            --text-muted: #7a8799;
            --border-light: #e8ecf0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        /* ── CUSTOM HERO SECTION (The Mint Area) ── h */
        .sv-hero {
            background-color: var(--brand-mint);
            padding: 3rem 3rem 6.5rem; 
            border-bottom: 1px solid #e2efe9;
        }

        .sv-breadcrumbs {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.05em; margin-bottom: 1.5rem;
        }
        .sv-breadcrumbs a { color: var(--brand-green); text-decoration: none; }
        .sv-breadcrumbs span { color: var(--text-muted); margin: 0 0.5rem; }
        .sv-breadcrumbs .current { color: var(--text-muted); margin: 0; }

        .sv-hero-content {
            display: flex; justify-content: space-between; align-items: flex-start;
            flex-wrap: wrap; gap: 1.5rem; max-width: 1100px; margin: 0 auto;
        }

        .sv-title-block h1 {
            font-size: 2.2rem; font-weight: 800; color: var(--text-main);
            margin: 0 0 0.5rem; letter-spacing: -0.02em;
        }
        .sv-title-block p { font-size: 1rem; color: var(--text-muted); margin: 0; }

        .sv-price-badge {
            background: #ffffff; padding: 1.2rem 1.8rem; border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.02);
            text-align: right;
        }
        .sv-price-label {
            font-size: 0.7rem; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.3rem;
        }
        .sv-price-value { font-size: 1.8rem; font-weight: 800; color: var(--brand-green); line-height: 1; display: block; }

        /* ── MAIN CONTENT AREA (The White Cards) ── */
        .sv-main-container {
            max-width: 1100px;
            margin: -4rem auto 3rem; /* Pulls cards up over the mint background */
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }

        .sv-card {
            background: #ffffff; border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.04);
            margin-bottom: 2rem; padding: 2.5rem;
        }

        /* ── PREMIUM FORM LAYOUT (Top-Stacked & Compact) ── */
        .form-body form {
            display: block; 
            margin-top: 1rem;
        }

        .form-section {
            display: block; /* Removed flex side-by-side */
            padding-top: 2rem;
            padding-bottom: 2.5rem;
            border-bottom: 1px solid var(--border-light);
        }
        .form-section:last-of-type { border-bottom: none; padding-bottom: 0; }

        /* Moved Section Title to Top */
        .form-section > h3 {
            width: 100%;
            font-size: 1.2rem;
            font-weight: 800;
            color: #111827;
            margin: 0 0 1.25rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px dashed var(--brand-mint); /* Added subtle separator line */
        }

        /* 6-Column Grid Engine */
        .form-grid {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(6, 1fr); /* 6 total columns */
            gap: 1.1rem 1.25rem; /* Tighter gaps for less scrolling */
        }

        .form-group {
            grid-column: span 2; /* Default fields take 2 cols = 3 fields per row */
            margin-bottom: 0; /* Overrides Bootstrap's default margin */
        }

        .form-group label {
            font-weight: 600;
            font-size: 0.8rem; /* Slightly smaller for compactness */
            color: #374151;
            margin-bottom: 0.4rem;
            display: block;
        }
        .form-group label .required { color: #ef4444; margin-left: 0.2rem; }

        .form-control {
            width: 100%; 
            padding: 0.6rem 0.8rem !important; /* Tighter padding */
            border-radius: 8px; 
            border: 1px solid #d1d5db; 
            font-family: inherit; 
            font-size: 0.9rem; 
            color: #111827; 
            background: #ffffff; 
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); 
            transition: all 0.2s;
            height: auto !important; 
            min-height: 42px; /* Tighter height */
            appearance: none; 
        }
        
        .form-control:focus {
            border-color: var(--brand-green); 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(30,156,93,0.15);
        }

        select.form-control {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px 12px;
            padding-right: 2.5rem !important;
        }

        .form-group:has(textarea) { grid-column: span 6; } /* Textareas span all 6 = 1 per row */
        textarea.form-control { min-height: 80px; resize: vertical; } /* Less tall */

        .form-group:has(input[type="file"]) { grid-column: span 3; } /* Files span 3 = 2 per row */
        .file-input-wrapper { position: relative; }
        
        input[type="file"].form-control {
            border: 1.5px dashed #d1d5db;
            padding: 0.8rem 1rem !important; /* Tighter padding */
            background: #f9fafb;
            border-radius: 10px;
            width: 100%;
            cursor: pointer;
            color: #6b7280;
        }
        input[type="file"].form-control:hover {
            border-color: var(--brand-green);
            background: #EDF7F4;
        }
        
        input[type="file"]::file-selector-button {
            background: #ffffff;
            border: 1px solid #d1d5db;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            color: #374151;
            font-weight: 600;
            font-size: 0.8rem;
            margin-right: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        input[type="file"]::file-selector-button:hover { background: #f3f4f6; }

        .file-help {
            font-size: 0.7rem;
            color: #9ca3af;
            margin-top: 0.4rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-actions-sticky {
            margin-top: 1rem;
            padding-top: 1.5rem;
        }
        .actions-inner {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }
        
        .btn-outline-secondary {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            color: #374151;
            font-weight: 700;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-outline-secondary:hover { background-color: #f9fafb; color: #111827; }

        .btn-submit {
            background-color: #111827; 
            color: #ffffff;
            font-weight: 700;
            padding: 0.75rem 2.5rem;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-submit:hover { background-color: #000000; transform: translateY(-1px); }
        .btn-submit svg { width: 16px; height: 16px; }

        /* ── RESPONSIVE TWEAKS ── */
        @media (max-width: 991px) {
            /* Tablet: 2 standard fields per row, 2 files per row, 1 textarea per row */
            .form-grid { grid-template-columns: repeat(2, 1fr); }
            .form-group { grid-column: span 1; }
            .form-group:has(input[type="file"]) { grid-column: span 1; }
            .form-group:has(textarea) { grid-column: span 2; }
        }
        
        @media (max-width: 768px) {
            .sv-hero { padding: 2rem 1.5rem 5rem; }
            .sv-main-container { margin-top: -3rem; }
            .sv-card { padding: 1.5rem; }
            .sv-title-block h1 { font-size: 1.6rem; }
            
            /* Mobile: Everything 1 field per row */
            .form-grid { grid-template-columns: 1fr; gap: 1rem; }
            .form-group, .form-group:has(input[type="file"]), .form-group:has(textarea) { grid-column: span 1; }
            
            .actions-inner { flex-direction: column-reverse; }
            .btn-outline-secondary, .btn-submit { width: 100%; text-align: center; justify-content: center; }
        }
    </style>
@endsection

@section('content')
<div class="service-view-wrapper">
    
    @if (session('error'))
        <div class="alert alert-danger mx-auto mt-3" style="max-width: 1100px; border-radius: 12px;">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mx-auto mt-3" style="max-width: 1100px; border-radius: 12px; background-color: #fef2f2; border: 1px solid #f87171; color: #991b1b;">
            <h6 style="font-weight: bold; margin-bottom: 0.5rem;">Please fix the following errors:</h6>
            <ul style="margin-bottom: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── HERO SECTION ── --}}
    <header class="sv-hero">
        <div class="sv-breadcrumbs">
            <a href="{{ route('services.index') }}">Service Catalog</a>
            <span>/</span>
            <span class="current">{{ $service->name }}</span>
        </div>
        
        <div class="sv-hero-content">
            <div class="sv-title-block">
                <h1>{{ $service->name }}</h1>
                <p>New Application Initialization</p>
            </div>
            
            @if ($service->price > 0)
                <div class="sv-price-badge">
                    <span class="sv-price-label">Standard Processing Fee</span>
                    <span class="sv-price-value">{{ money($service->price) }}</span>
                </div>
            @endif
        </div>
    </header>
    
    {{-- ── MAIN CONTENT ── --}}
    <div class="sv-main-container">
        
        {{-- INJECTING THE MILESTONE COMPONENT --}}
        <x-milestone-tracker :milestones="$giftMilestones" />

        {{-- ── Application Form ── --}}
        <div class="sv-card">
            <div class="form-section-layout">
                <div class="form-section-header" style="margin-bottom: 1.5rem;">
                    <h2>Application Data</h2>
                    <p style="color: var(--text-muted);">Please enter the client's information. Verify all details carefully before submitting to avoid processing delays.</p>
                </div>
                
                <div class="form-section-body form-body">
                    {!! $form->render() !!}
                </div>
            </div>
            <br>
        

    {{-- LIVE PRICE PREVIEW BOX WITH COUPON SYSTEM --}}
                    @if(in_array($service->slug, ['gst-return-filing', 'itr-filing','gst-annual-package']))
                        <div class="card border-success mb-4 shadow-sm mt-4">
                            <div class="card-body bg-success-soft">
                                <table class="table table-sm table-borderless mb-0 font-weight-bold">
                                    <tr>
                                        <td class="text-success text-uppercase text-xs">Total Fee</td>
                                        <td class="text-right text-dark" style="font-size: 1.2rem;">₹<span id="calc-total">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-success text-uppercase text-xs">Base Commission</td>
                                        <td class="text-right text-success">₹<span id="calc-comm">0.00</span></td>
                                    </tr>
                                    
                                    <tr id="promo-row" style="display: none;">
                                        <td class="text-primary text-uppercase text-xs">Promo Bonus (<span id="applied-promo-name"></span>)</td>
                                        <td class="text-right text-primary">+ ₹<span id="promo-bonus-amount">0.00</span></td>
                                    </tr>

                                    <tr class="border-top border-success">
                                        <td class="text-dark text-uppercase text-sm pt-2">Total Payable Now</td>
                                        <td class="text-right text-danger pt-2" style="font-size: 1.3rem;">₹<span id="calc-deduct">0.00</span></td>
                                    </tr>
                                </table>
                                
                                @if($service->slug === 'itr-filing')
                                <div class="input-group mt-3">
                                    <input type="text" id="promo-code-input" class="form-control text-uppercase" placeholder="Enter Promo Code (e.g. ITR50)" style="min-height: 38px;">
                                    <div class="input-group-append">
                                        <button class="btn btn-dark" type="button" id="apply-promo-btn" style="border-radius: 0 8px 8px 0;">Apply</button>
                                    </div>
                                </div>
                                <small id="promo-message" class="form-text text-muted mt-1"></small>
                                @endif

                            </div>
                        </div>
                    @endif
        </div>
    </div>
 </div>
{{-- INJECTING THE PAYMENT MODAL COMPONENT --}}
<x-payment-modal 
    :service="$service" 
    :commission="$commissionAmount" 
    :toPay="$amountToPay" 
/>

@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="{{ asset('assets/js/form.js') }}"></script>

    {{-- ── MAIN FORM BRAIN ── --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── PRICING RULES ──────────────────────────────── 
            const rawRules = @json($service->pricingRules ?? []);
            const pricingRules = Array.isArray(rawRules) ? rawRules : Object.values(rawRules);

            function normalizeValue(val) {
                return val ? String(val).toLowerCase().trim() : '';
            }

            // ── INCOME TAX PASSWORD LOGIC ────────────────────
            let $itPasswordInput = $('input[name="it_password"]');
            if ($itPasswordInput.length > 0) {
                let $itPasswordContainer = $itPasswordInput.closest('.form-group');
                let dropdownHtml = `
                    <div class="form-group mb-3" id="it_password_toggle_container">
                        <label class="form-label fw-bold">Do you have an Income Tax Portal Password? <span class="text-danger">*</span></label>
                        <select id="has_it_password" class="form-control form-select" required>
                            <option value="">-- Select an Option --</option>
                            <option value="yes">Yes, I have my password</option>
                            <option value="no">No, create a new application</option>
                        </select>
                    </div>`;
                $itPasswordContainer.before(dropdownHtml);
                $itPasswordContainer.hide();
                $itPasswordInput.prop('required', false);
                $('#has_it_password').on('change', function() {
                    let val = $(this).val();
                    if (val === 'yes') {
                        $itPasswordContainer.slideDown(200);
                        $itPasswordInput.prop('required', true).prop('type', 'password').val('');
                    } else if (val === 'no') {
                        $itPasswordContainer.slideUp(200);
                        $itPasswordInput.prop('required', false).prop('type', 'text').val('NEW_APPLICATION');
                    } else {
                        $itPasswordContainer.slideUp(200);
                        $itPasswordInput.prop('required', false).val('');
                    }
                });
            }

           function handleNestedFields() {
                let businessVal = $('input[name="has_business"]:checked').val();
                let itrTurnoverWrapper = $('input[name="turnover"]').closest('.form-group');
                if (businessVal === 'yes') {
                    itrTurnoverWrapper.slideDown(200);
                } else if (businessVal !== undefined) {
                    itrTurnoverWrapper.slideUp(200);
                    $('input[name="turnover"]').prop('checked', false);
                }

                let gstType = $('select[name="gst_type"]').val();
                let frequencyDropdown = $('select[name="frequency_of_return"]');
                if (gstType === 'composition') {
                    $('select[name="annual_turnover_range"] option[value="nil_turnover"]').show();
                    $('select[name="annual_turnover_range"] option[value="with_turnover"]').show();
                    $('select[name="annual_turnover_range"] option[value="nil"]').hide();
                    $('select[name="annual_turnover_range"] option[value="upto_35"]').hide();
                    $('select[name="annual_turnover_range"] option[value="above_35"]').hide();
                    let ct = $('select[name="annual_turnover_range"]').val();
                    if (['nil','upto_35','above_35'].includes(ct)) $('select[name="annual_turnover_range"]').val('');
                    frequencyDropdown.find('option[value="monthly"]').hide();
                    frequencyDropdown.find('option[value="annual"]').hide();
                    frequencyDropdown.find('option[value="quarterly"]').show();
                    frequencyDropdown.find('option[value="annual_gstr4"]').show();
                    let cf = frequencyDropdown.val();
                    if (['monthly','annual'].includes(cf)) frequencyDropdown.val('').trigger('change');
                } else if (gstType === 'regular') {
                    $('select[name="annual_turnover_range"] option[value="nil"]').show();
                    $('select[name="annual_turnover_range"] option[value="upto_35"]').show();
                    $('select[name="annual_turnover_range"] option[value="above_35"]').show();
                    $('select[name="annual_turnover_range"] option[value="nil_turnover"]').hide();
                    $('select[name="annual_turnover_range"] option[value="with_turnover"]').hide();
                    let ct = $('select[name="annual_turnover_range"]').val();
                    if (['nil_turnover','with_turnover'].includes(ct)) $('select[name="annual_turnover_range"]').val('');
                    frequencyDropdown.find('option[value="monthly"]').show();
                    frequencyDropdown.find('option[value="quarterly"]').show();
                    frequencyDropdown.find('option[value="annual"]').show();
                    frequencyDropdown.find('option[value="annual_gstr4"]').hide();
                    let cf = frequencyDropdown.val();
                    if (['annual_gstr4'].includes(cf)) frequencyDropdown.val('').trigger('change');
                }

                let frequency = frequencyDropdown.val();
                let planWrapper    = $('select[name="plan"]').closest('.form-group');
                let monthWrapper   = $('select[name="month"]').closest('.form-group');
                let quarterWrapper = $('select[name="quarter"]').closest('.form-group');
                
                if (frequency === 'monthly') {
                    planWrapper.show(); monthWrapper.show(); quarterWrapper.hide();
                    $('select[name="plan"] option[value="yearly_12"]').show();
                    $('select[name="plan"] option[value="yearly_4"]').hide();
                } else if (frequency === 'quarterly') {
                    planWrapper.show(); monthWrapper.hide(); quarterWrapper.show();
                    $('select[name="plan"] option[value="yearly_4"]').show();
                    $('select[name="plan"] option[value="yearly_12"]').hide();
                } 
                else if (frequency === 'annual' || frequency === 'annual_gstr4' || frequency === 'annual_gstr9') {
                    planWrapper.hide(); monthWrapper.hide(); quarterWrapper.hide();
                    $('select[name="plan"]').val('');
                    $('select[name="month"]').val('');
                    $('select[name="quarter"]').val('');
                }
            }

           // ── GLOBAL PROMO VARIABLES ──
            let appliedPromoBonus = 0;
            let appliedPromoCode = '';

            // ── PRICING CALCULATOR ───────────────────────────
            function calculateDynamicPrice() {
                let selectedGst  = normalizeValue($('select[name="gst_type"]').val() || $('input[name="gst_type"]:checked').val());
                let selectedFreq = normalizeValue($('select[name="frequency_of_return"]').val() || $('input[name="frequency_of_return"]:checked').val());
                let selectedPlan = normalizeValue($('select[name="plan"]').val() || $('input[name="plan"]:checked').val());
                let s_type = normalizeValue($('select[name="itr_type"]').val() || $('input[name="itr_type"]:checked').val());
                let s_bus  = normalizeValue($('select[name="has_business"]').val() || $('input[name="has_business"]:checked').val());
                let s_cg   = normalizeValue($('select[name="has_capital_gains"]').val() || $('input[name="has_capital_gains"]:checked').val());
                let s_sal  = normalizeValue($('select[name="has_salary"]').val() || $('input[name="has_salary"]:checked').val());
                let selectedTurnover = normalizeValue($('select[name="turnover"]').val() || $('input[name="turnover"]:checked').val() || $('select[name="annual_turnover_range"]').val() || $('select[name="total_bills"]').val());
                
                let match = pricingRules.find(rule => {
                    return (normalizeValue(rule.gst_type)          === '' || normalizeValue(rule.gst_type)          === 'any' || normalizeValue(rule.gst_type)          === selectedGst) &&
                           (normalizeValue(rule.frequency)         === '' || normalizeValue(rule.frequency)         === 'any' || normalizeValue(rule.frequency)         === selectedFreq) &&
                           (normalizeValue(rule.plan)              === '' || normalizeValue(rule.plan)              === 'any' || normalizeValue(rule.plan)              === selectedPlan) &&
                           (normalizeValue(rule.turnover)          === '' || normalizeValue(rule.turnover)          === 'any' || normalizeValue(rule.turnover)          === selectedTurnover) &&
                           (normalizeValue(rule.itr_type)          === '' || normalizeValue(rule.itr_type)          === 'any' || normalizeValue(rule.itr_type)          === s_type) &&
                           (normalizeValue(rule.itr_business)      === '' || normalizeValue(rule.itr_business)      === 'any' || normalizeValue(rule.itr_business)      === s_bus) &&
                           (normalizeValue(rule.itr_capital_gains) === '' || normalizeValue(rule.itr_capital_gains) === 'any' || normalizeValue(rule.itr_capital_gains) === s_cg) &&
                           (normalizeValue(rule.itr_salary)        === '' || normalizeValue(rule.itr_salary)        === 'any' || normalizeValue(rule.itr_salary)        === s_sal);
                });

                let finalTotal   = match ? parseFloat(match.base_price)        : {{ $service->price ?? 0 }};
                let baseComm     = match ? parseFloat(match.commission_amount) : {{ $service->commission_value ?? 0 }};
                
                // ADD THE COUPON BONUS TO THE COMMISSION
                let totalComm = baseComm + appliedPromoBonus;
                let walletDeduct = finalTotal - totalComm;

                // Ensure wallet deduct never goes below 0
                if(walletDeduct < 0) walletDeduct = 0;

                $('#calc-total').text(finalTotal.toFixed(2));
                $('#calc-comm').text(baseComm.toFixed(2));
                $('#calc-deduct').text(walletDeduct.toFixed(2));
            }

            $('form').on('change', 'select, input[type="radio"]', function() {
                handleNestedFields();
                calculateDynamicPrice();
            });
            setTimeout(() => { handleNestedFields(); calculateDynamicPrice(); }, 500);

            function initIncomeFields() {
                // Safely get values whether they are Dropdowns or Radio buttons
                let hasSalary = $('select[name="has_salary"]').val() || $('input[name="has_salary"]:checked').val() || '';
                let hasBusiness = $('select[name="has_business"]').val() || $('input[name="has_business"]:checked').val() || '';
                
                // Convert to lowercase so 'Yes', 'YES', and 'yes' all work perfectly
                hasSalary = hasSalary.toLowerCase().trim();
                hasBusiness = hasBusiness.toLowerCase().trim();
                
                if (hasSalary !== 'yes') $('input[name="salary_amount"]').closest('.form-group').hide();
                if (hasBusiness !== 'yes') $('input[name="business_amount"]').closest('.form-group').hide();
            }

            $('form').on('change', '[name="has_salary"], [name="has_business"]', function() {
                let name = $(this).attr('name');
                let val = $(this).val() ? $(this).val().toLowerCase().trim() : '';
                
                let amountInput = (name === 'has_salary') ? $('input[name="salary_amount"]') : $('input[name="business_amount"]');
                let wrapper = amountInput.closest('.form-group');

                if (val === 'yes') {
                    wrapper.slideDown(250);
                } else {
                    wrapper.slideUp(250);
                    amountInput.val(''); 
                }
            });
            
            function initBankRepeater() {
                let currentVisibleBank = 1;
                let maxBanks = 5; // Adjust this if you made more than 5 in admin

                // 1. Check which banks actually have data (crucial for form validation reloads)
                for(let i = 2; i <= maxBanks; i++) {
                    let accInput = $('input[name="bank_account_number_' + i + '"]');
                    let ifscInput = $('input[name="ifsc_code_' + i + '"]');

                    // Stop checking if these fields don't exist in the HTML at all
                    if (accInput.length === 0) break;

                    // If Laravel repopulated data after an error, KEEP IT VISIBLE
                    if (accInput.val() !== '' || ifscInput.val() !== '') {
                        currentVisibleBank = i; 
                    } else {
                        // Safe to hide
                        accInput.closest('.form-group').hide();
                        ifscInput.closest('.form-group').hide();
                    }
                }

                // 2. Inject Button safely immediately after the LAST currently visible bank
                let lastVisibleIfscName = currentVisibleBank === 1 ? 'ifsc_code' : 'ifsc_code_' + currentVisibleBank;
                let lastVisibleIfscWrapper = $('input[name="' + lastVisibleIfscName + '"]').closest('.form-group');

                // Only inject if there is actually room for another bank
                if(lastVisibleIfscWrapper.length > 0 && $('input[name="bank_account_number_' + (currentVisibleBank + 1) + '"]').length > 0) {
                    lastVisibleIfscWrapper.after(`
                        <div class="col-12 mb-3 mt-2 form-group" id="add-bank-wrapper">
                            <button type="button" id="add-bank-btn" class="btn btn-sm" style="background-color: #1E9C5D; color: white; border-radius: 5px; font-weight: bold; padding: 6px 16px; border: none;">
                                + Add Another Bank
                            </button>
                        </div>
                    `);
                }

                // 3. Button Click Event
                $(document).off('click', '#add-bank-btn').on('click', '#add-bank-btn', function() {
                    currentVisibleBank++;
                    
                    let nextAccWrapper = $('input[name="bank_account_number_' + currentVisibleBank + '"]').closest('.form-group');
                    let nextIfscWrapper = $('input[name="ifsc_code_' + currentVisibleBank + '"]').closest('.form-group');

                    nextAccWrapper.slideDown(250);
                    nextIfscWrapper.slideDown(250);
                    
                    $('#add-bank-wrapper').insertAfter(nextIfscWrapper);

                    // If we reached the last bank available in the HTML, hide the button
                    if($('input[name="bank_account_number_' + (currentVisibleBank + 1) + '"]').length === 0) {
                        $('#add-bank-wrapper').hide();
                    }
                });
            }

            // Run initializers immediately 
            $(document).ready(function() {
                initIncomeFields();
                initBankRepeater();
            });
// ── SMART FORM HIDE/SHOW ENGINE ─────────────────────────── 
            const REPEATER_CONFIGS = [
                { trigger: 'number_of_members', prefix: 'member' },
                { trigger: 'number_of_directors', prefix: 'director' },
                { trigger: 'number_of_partners', prefix: 'partner' }
            ];

            function applyDynamicHideShow() {
                REPEATER_CONFIGS.forEach(cfg => {
                    // Find the dropdown (handles normal names or array names)
                    let $dropdown = $('select').filter(function() {
                        return ($(this).attr('name') || '').includes(cfg.trigger);
                    });
                    
                    if ($dropdown.length === 0) return; // Skip if this service doesn't have it

                    $dropdown.on('change', function() {
                        let count = parseInt($(this).val()) || 0;
                        
                        // Loop 1 through 8 (Max potential fields)
                        for (let i = 1; i <= 8; i++) {
                            
                            // 1. Find Data Fields (e.g., member_1_name)
                            let $dataFields = $(':input').not('[type="file"]').filter(function() {
                                return ($(this).attr('name') || '').includes(cfg.prefix + '_' + i + '_');
                            });
                            
                            // 2. Find Document Uploads (BULLETPROOF FILE FINDER)
                            let $fileFields = $('input[type="file"]').filter(function() {
                                let name = $(this).attr('name') || '';
                                // This matches bank_statement_1, documents[bank_statement_1], bank_statement_1[], etc.
                                return name.endsWith('_' + i) || name.includes('_' + i + ']') || name.includes('_' + i + '[');
                            });
                            
                            // 3. Find the Entire Section Wrapper (e.g., "Member 1 Details")
                            let sectionTitleRegex = new RegExp(cfg.prefix + '\\s+' + i, 'i');
                            let $section = $('.form-section').filter(function() {
                                let text = $(this).find('h3').text();
                                return sectionTitleRegex.test(text);
                            });

                            // Group all wrappers to hide/show cleanly
                            let $containers = $dataFields.closest('.form-group').add($fileFields.closest('.form-group'));

                            if (i <= count) {
                                // SHOW: The user needs this index
                                $containers.show();
                                $section.show();
                                
                                // Re-apply 'required' if the field originally needed it
                                $dataFields.add($fileFields).each(function() {
                                    if ($(this).data('was-required')) {
                                        $(this).prop('required', true);
                                    }
                                });
                            } else {
                                // HIDE: The user doesn't need this index
                                $containers.hide();
                                $section.hide();
                                
                                // Remove 'required' so the form can submit without error, and clear values
                                $dataFields.add($fileFields).each(function() {
                                    if ($(this).prop('required')) {
                                        $(this).data('was-required', true); // Remember it was required
                                        $(this).prop('required', false);
                                    }
                                    if ($(this).is(':checkbox, :radio')) {
                                        $(this).prop('checked', false);
                                    } else {
                                        $(this).val('');
                                    }
                                });
                            }
                        }
                    });

                    // Trigger immediately on page load to hide extras
                    $dropdown.trigger('change');
                });
            }

            // Start the engine
            applyDynamicHideShow();


            // ── COUPON SYSTEM LOGIC ───────────────────────────
            $('#apply-promo-btn').on('click', function() {
                let codeInput = $('#promo-code-input').val().toUpperCase().trim();
                let $btn = $(this);
                let $msg = $('#promo-message');

                if (!codeInput) return;

                $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                $msg.removeClass('text-danger text-success').addClass('text-muted').text("Verifying code...");

                // Call the backend API
                fetch('/agent/validate-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        code: codeInput,
                        service_slug: '{{ $service->slug }}' 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    $btn.html('Apply');

                    if (data.valid) {
                        // Update global variables
                        appliedPromoBonus = parseFloat(data.bonus);
                        appliedPromoCode = data.code;

                        // Show the UI Row
                        $('#promo-row').show();
                        $('#applied-promo-name').text(data.code);
                        $('#promo-bonus-amount').text(appliedPromoBonus.toFixed(2));

                        // Recalculate totals
                        calculateDynamicPrice();

                        // Add a hidden input directly inside the form so Laravel gets it on submit
                        if($('#hidden-coupon-input').length === 0) {
                            $('form').append('<input type="hidden" name="applied_coupon" id="hidden-coupon-input" value="'+data.code+'">');
                        } else {
                            $('#hidden-coupon-input').val(data.code);
                        }

                        // Lock the UI now 
                        $('#promo-code-input').prop('disabled', true);
                        $msg.removeClass('text-muted text-danger').addClass('text-success font-weight-bold').html('<i class="fas fa-check-circle"></i> Promo applied successfully!');
                    } else {
                        $btn.prop('disabled', false);
                        $msg.removeClass('text-muted text-success').addClass('text-danger').text(data.message || "Invalid or expired promo code.");
                    }
                })
                .catch(error => {
                    $btn.html('Apply').prop('disabled', false);
                    $msg.removeClass('text-muted text-success').addClass('text-danger').text("Connection error. Try again.");
                });
            });

        }); // end DOMContentLoaded

        // ── THE OMNISCIENT MUTATION OBSERVER ENGINE ──────────────────
            console.log("Add-More Script is officially loaded and watching...");

            (function() {
                // Create an observer that watches the entire page for HTML changes
                var observer = new MutationObserver(function(mutations, obs) {
                    
                    // Look for our 30 documents
                    var myDocs = $('input[type="file"]').filter(function() {
                        return $(this).attr('name') && $(this).attr('name').includes('bill_doc_');
                    });

                    // If it found them AND we haven't added the button yet...
                    if (myDocs.length > 0 && $('#add-more-bills-btn').length === 0) {
                        
                        console.log("Form detected! Hiding 29 documents now.");

                        // 1. Hide documents 2 through 30
                        myDocs.each(function(index) {
                            if (index > 0) { 
                                $(this).closest('.form-group').hide();
                            }
                        });

                        // 2. Inject the Button right below the first document
                        let firstWrapper = myDocs.first().closest('.form-group');
                        firstWrapper.after(`
                            <div class="col-12 mb-3 mt-2" id="add-more-wrapper">
                                <button type="button" id="add-more-bills-btn" class="btn btn-primary btn-sm" style="background-color: #0d6efd; color: white; border-radius: 5px; padding: 6px 16px; border: none; font-weight: bold; cursor: pointer;">
                                    + Add Another Bill
                                </button>
                            </div>
                        `);

                        // 3. Button Click Logic
                        let visibleCount = 1;
                        $(document).off('click', '#add-more-bills-btn').on('click', '#add-more-bills-btn', function() {
                            if (visibleCount < myDocs.length) {
                                let nextWrapper = $(myDocs[visibleCount]).closest('.form-group');
                                nextWrapper.slideDown(250);
                                
                                // Move the button down below the newly opened box
                                $('#add-more-wrapper').insertAfter(nextWrapper);
                                
                                visibleCount++;
                            }

                            // Hide the button permanently if they reach 30
                            if (visibleCount >= myDocs.length) {
                                $('#add-more-wrapper').hide();
                            }
                        });
                    }
                });

                // Start watching the body for any dynamic HTML injections
                observer.observe(document.body, { childList: true, subtree: true });
            })();
    </script>
    
   
    {{-- ── RAZORPAY GATEWAY INVOCATION ── --}}
    @if (session('razorpay_order'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ORDER = @json(session('razorpay_order'));

                const options = {
                    key: ORDER.key_id,
                    amount: ORDER.amount,
                    currency: ORDER.currency,
                    order_id: ORDER.order_id,
                    name: 'EasyTax',
                    description: 'Application Processing Fee',
                    handler: function(response) {
                        // User paid successfully! Send them to the payment.success route
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('payment.success') }}';

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);

                        for (const [key, value] of Object.entries(response)) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = value;
                            form.appendChild(input);
                        }

                        document.body.appendChild(form);
                        form.submit();
                    },


                    

                    modal: {
                        ondismiss: function() {
                            // User closed the Razorpay window without paying
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route('payment.failure') }}';

                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            form.appendChild(csrf);

                            const orderInput = document.createElement('input');
                            orderInput.type = 'hidden';
                            orderInput.name = 'razorpay_order_id';
                            orderInput.value = ORDER.order_id;
                            form.appendChild(orderInput);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    },
                    theme: { color: '#1E9C5D' }
                };

                const rzp = new Razorpay(options);
                
                // Add a tiny delay so the page can finish rendering before the popup takes over
                setTimeout(() => { rzp.open(); }, 300);
            });

           
</script>
    @endif
@endsection