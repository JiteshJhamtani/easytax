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

        /* ── WIZARD UI STYLES ── */
        .wizard-step { display: none; }
        .wizard-step.active { display: block; }

        .wizard-header {
            display: flex; 
            gap: 1rem; 
            margin-bottom: 2rem; 
            border-bottom: 2px solid var(--border-light); 
            padding-bottom: 1rem;
            overflow-x: auto; /* Allows horizontal scrolling if tabs exceed screen width */
            scrollbar-width: thin; /* Thin scrollbar for Firefox */
            scrollbar-color: var(--brand-green) transparent;
        }
        
        .wizard-header::-webkit-scrollbar { height: 6px; }
        .wizard-header::-webkit-scrollbar-track { background: transparent; }
        .wizard-header::-webkit-scrollbar-thumb { background: var(--border-light); border-radius: 10px; }
        .wizard-header::-webkit-scrollbar-thumb:hover { background: var(--brand-green); }

        .wizard-tab {
            flex: 1 0 auto; /* Prevent tabs from shrinking below their content width */
            text-align: center; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.05em; padding: 0.5rem; position: relative; transition: color 0.3s;
            white-space: nowrap; /* Keep text on one line */
        }
        
        .wizard-tab.active { color: var(--brand-green); }
        .wizard-tab.active::after {
            content: ''; position: absolute; bottom: -17px; left: 0; right: 0; height: 3px; background: var(--brand-green); border-radius: 3px 3px 0 0;
        }
        
        .wizard-actions {
            display: flex; justify-content: space-between; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-light);
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
                    @if(in_array
                    ($service->slug, ['gst-return-filing', 'itr-filing','gst-annual-package',
                    'gst-registration']
                    ))
                        <div class="card border-success mb-4 shadow-sm mt-4">
                            <div class="card-body bg-success-soft">
                                <table class="responsive-card-table table table-sm table-borderless mb-0 font-weight-bold">
                                    <tr>
                                        <td class="text-success text-uppercase text-xs">Total Fee</td>
                                        <td class="text-right text-dark" style="font-size: 1.2rem;">₹<span id="calc-total">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-success text-uppercase text-xs">Base Commission</td>
                                        <td class="text-right text-success">₹<span id="calc-comm">0.00</span></td>
                                    </tr>
                                    
                                    <tr id="promo-row" class="d-none">
                                        <td class="text-primary text-uppercase text-xs">Promo Bonus (<span id="applied-promo-name"></span>)</td>
                                        <td class="text-right text-primary">+ ₹<span id="promo-bonus-amount">0.00</span></td>
                                    </tr>

                                    <tr class="border-top border-success">
                                        <td class="text-dark text-uppercase text-sm pt-2">Total Payable Now</td>
                                        <td class="text-right text-danger pt-2" style="font-size: 1.3rem;">₹<span id="calc-deduct">0.00</span></td>
                                    </tr>
                                </table>
                                
                                <div class="input-group mt-3">
                                    <input type="text" id="promo-code-input" class="form-control text-uppercase" placeholder="Enter Promo Code" style="min-height: 38px;">
                                    <div class="input-group-append">
                                        <button class="btn btn-dark" type="button" id="apply-promo-btn" style="border-radius: 0 8px 8px 0;">Apply</button>
                                    </div>
                                </div>
                                <small id="promo-message" class="form-text text-muted mt-1"></small>

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
    {{-- REMOVED Duplicate jQuery and Popper to prevent conflicts with your Master Layout --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="{{ asset('assets/js/form.js') }}"></script>

    {{-- ── MAIN FORM BRAIN ── --}}
    <script>
        // ── 1. GLOBAL VARIABLES & PRICING RULES ──
        const rawRules = @json($service->pricingRules ?? []);
        const pricingRules = Array.isArray(rawRules) ? rawRules : Object.values(rawRules);
        let appliedPromoBonus = 0;
        let appliedPromoCode = '';

        function normalizeValue(val) {
            return val ? String(val).toLowerCase().trim() : '';
        }

       // ── 2. SMART TOOLTIP INJECTION ENGINE ──
        function injectTooltips() {
            const helpers = {
                // Filing Details
                'has_capital_gains': 'Did the client sell property, gold, shares, or mutual funds this year?',
                'has_business': 'Does the client own a shop, trade, or work as a freelancer?',
                'turnover': 'Total sales or earnings from the business before expenses.',
                'it_password': 'If the client doesn\'t remember, select No. We will reset it for them.',
                
                // Bank Details
                'ifsc_code': 'Found on the client\'s bank passbook or cheque leaf.',
                'bank_account_number': 'We need this so the Income Tax Dept knows where to send the refund.',
                
                // Documents
                'form_16': 'Ask the salaried client for the PDF their HR department gave them.',
                'broker_statement': 'Download from apps like Zerodha, Groww, or AngelOne.',
                'profit_loss_statement': 'A summary of business income and expenses.',
                'balance_sheet': 'Required for formal business accounts.',
                'pan': 'Upload a clear, readable photo of the PAN card front.',
                'aadhaar': 'Upload a clear photo of the Aadhaar card.'
            };

            Object.keys(helpers).forEach(function(fieldName) {
                let $fields = $('[name*="' + fieldName + '"]');
                
                $fields.each(function() {
                    let $label = $(this).closest('.form-group').find('label').first();
                    
                    if($label.length && $label.find('.tax-tooltip-icon').length === 0) {
                        
                        let safeContent = helpers[fieldName].replace(/"/g, '&quot;');
                        
                        $label.append(` 
                            <i class="fas fa-question-circle text-primary tax-tooltip-icon ml-1" 
                               tabindex="0" 
                               data-toggle="popover" 
                               data-trigger="hover focus" 
                               data-placement="top" 
                               data-content="${safeContent}" 
                               style="cursor: pointer; font-size: 0.85rem;">
                            </i>
                        `);
                    }
                });
            });

            try {
                $('[data-toggle="popover"]').popover();
            } catch (error) {
                console.warn("Bootstrap popover unavailable. Using standard tooltips.");
                $('.tax-tooltip-icon').each(function() {
                    $(this).attr('title', $(this).attr('data-content'));
                });
            }
        }
        // ── 3. SMART CONDITIONAL LOGIC ──
        function initSmartFormLogic() {
            const logicMap = {
                'has_salary': { inputs: ['salary_amount'], docs: ['form_16'] },
                'has_business': { inputs: ['business_amount', 'turnover'], docs: ['profit_loss_statement', 'balance_sheet'] },
                'has_capital_gains': { inputs: [], docs: ['broker_statement'] }
            };

            function toggleDependentFields(triggerName) {
                let val = $('select[name="' + triggerName + '"]').val() || $('input[name="' + triggerName + '"]:checked').val() || '';
                val = val.toLowerCase().trim();
                let isYes = (val === 'yes');
                let config = logicMap[triggerName];
                
                config.inputs.forEach(fieldName => {
                    let $input = $('[name="' + fieldName + '"]');
                    let $wrapper = $input.closest('.form-group');
                    if (isYes) { $wrapper.slideDown(250); } 
                    else { $wrapper.slideUp(250); $input.val('').trigger('change'); }
                });

                config.docs.forEach(docName => {
                    let $doc = $('input[type="file"][name="' + docName + '"]');
                    let $wrapper = $doc.closest('.form-group');
                    if (isYes) { 
                        $wrapper.slideDown(250); 
                        if ($doc.data('was-required')) $doc.prop('required', true);
                    } else { 
                        $wrapper.slideUp(250); 
                        $doc.data('was-required', $doc.prop('required')); 
                        $doc.prop('required', false).val(''); 
                    }
                });
            }

            Object.keys(logicMap).forEach(triggerName => {
                $('form').on('change', '[name="' + triggerName + '"]', function() {
                    toggleDependentFields(triggerName);
                });
                toggleDependentFields(triggerName);
            });
        }

      // ── 4. AUTO-MAGIC WIZARD GENERATOR (Now with Clickable Tabs) ──
        function convertToWizard() {
            let $sections = $('.form-section');
            if ($sections.length <= 1) return; 

            let headerHtml = '<div class="wizard-header">';
            $sections.each(function(index) {
                let title = $(this).find('h3').text().trim() || 'Step ' + (index + 1);
                // Added cursor:pointer to make it obvious they are clickable
                headerHtml += `<div class="wizard-tab ${index === 0 ? 'active' : ''}" data-step="${index}" style="cursor: pointer;">${title}</div>`;
                
                $(this).addClass('wizard-step').attr('data-step', index);
                if (index === 0) $(this).addClass('active');

                let buttonsHtml = '<div class="wizard-actions col-12">';
                if (index > 0) {
                    buttonsHtml += `<button type="button" class="btn btn-outline-secondary btn-prev">← Previous</button>`;
                } else {
                    buttonsHtml += `<div></div>`;
                }

                if (index < $sections.length - 1) {
                    buttonsHtml += `<button type="button" class="btn btn-dark btn-next" style="background: #111827; color: white;">Next Step →</button>`;
                } else {
                    let $submitBtn = $('.btn-submit').closest('.form-actions-sticky');
                    if($submitBtn.length) {
                        buttonsHtml += $submitBtn.html();
                        $submitBtn.hide(); 
                    }
                }
                buttonsHtml += '</div>';
                $(this).append(buttonsHtml);
            });
            headerHtml += '</div>';

            $('.form-body form').prepend(headerHtml);

            // Function to handle switching steps safely
            function goToStep($current, targetIndex) {
                $current.removeClass('active');
                $('.wizard-step[data-step="' + targetIndex + '"]').addClass('active');
                $('.wizard-tab').removeClass('active');
                $('.wizard-tab[data-step="' + targetIndex + '"]').addClass('active');
                window.scrollTo({ top: $('.sv-card').offset().top - 50, behavior: 'smooth' });
            }

            // Next Button Click
            $('.btn-next').on('click', function(e) {
                e.preventDefault();
                let $current = $(this).closest('.wizard-step');
                
                let isValid = true;
                $current.find(':input[required]:visible').each(function() {
                    if (!this.checkValidity()) {
                        this.reportValidity();
                        isValid = false;
                        return false; 
                    }
                });

                if (isValid) {
                    goToStep($current, $current.data('step') + 1);
                }
            });

            // Previous Button Click
            $('.btn-prev').on('click', function(e) {
                e.preventDefault();
                let $current = $(this).closest('.wizard-step');
                goToStep($current, $current.data('step') - 1);
            });

            // 🟢 NEW: Clickable Tabs Logic 🟢
            $('.wizard-tab').on('click', function() {
                let targetIndex = $(this).data('step');
                let $currentTab = $('.wizard-tab.active');
                let currentIndex = $currentTab.data('step');
                let $currentStep = $('.wizard-step[data-step="' + currentIndex + '"]');

                if (targetIndex === currentIndex) return; // Do nothing if clicking current tab

                // If jumping FORWARD, validate the current step first so they can't skip required fields
                if (targetIndex > currentIndex) {
                    let isValid = true;
                    $currentStep.find(':input[required]:visible').each(function() {
                        if (!this.checkValidity()) {
                            this.reportValidity();
                            isValid = false;
                            return false; 
                        }
                    });
                    if (!isValid) return; // Stop them from jumping
                }

                // If jumping backward, or if forward validation passed, switch steps
                goToStep($currentStep, targetIndex);
            });
        }

        // ── 5. BANK REPEATER & GST NESTED FIELDS ──
        function initBankRepeater() {
            let currentVisibleBank = 1;
            let maxBanks = 5; 

            for(let i = 2; i <= maxBanks; i++) {
                let accInput = $('input[name="bank_account_number_' + i + '"]');
                let ifscInput = $('input[name="ifsc_code_' + i + '"]');
                if (accInput.length === 0) break;

                if (accInput.val() !== '' || ifscInput.val() !== '') {
                    currentVisibleBank = i; 
                } else {
                    accInput.closest('.form-group').hide();
                    ifscInput.closest('.form-group').hide();
                }
            }

            let lastVisibleIfscName = currentVisibleBank === 1 ? 'ifsc_code' : 'ifsc_code_' + currentVisibleBank;
            let lastVisibleIfscWrapper = $('input[name="' + lastVisibleIfscName + '"]').closest('.form-group');

            if(lastVisibleIfscWrapper.length > 0 && $('input[name="bank_account_number_' + (currentVisibleBank + 1) + '"]').length > 0) {
                lastVisibleIfscWrapper.after(`
                    <div class="col-12 mb-3 mt-2 form-group" id="add-bank-wrapper">
                        <button type="button" id="add-bank-btn" class="btn btn-sm" style="background-color: #1E9C5D; color: white; border-radius: 5px; font-weight: bold; padding: 6px 16px; border: none;">
                            + Add Another Bank
                        </button>
                    </div>
                `);
            }

            $(document).off('click', '#add-bank-btn').on('click', '#add-bank-btn', function() {
                currentVisibleBank++;
                let nextAccWrapper = $('input[name="bank_account_number_' + currentVisibleBank + '"]').closest('.form-group');
                let nextIfscWrapper = $('input[name="ifsc_code_' + currentVisibleBank + '"]').closest('.form-group');

                nextAccWrapper.slideDown(250);
                nextIfscWrapper.slideDown(250);
                $('#add-bank-wrapper').insertAfter(nextIfscWrapper);

                if($('input[name="bank_account_number_' + (currentVisibleBank + 1) + '"]').length === 0) {
                    $('#add-bank-wrapper').hide();
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
            } else if (frequency === 'annual' || frequency === 'annual_gstr4' || frequency === 'annual_gstr9') {
                planWrapper.hide(); monthWrapper.hide(); quarterWrapper.hide();
                $('select[name="plan"]').val('');
                $('select[name="month"]').val('');
                $('select[name="quarter"]').val('');
            }
        }

        // ── 6. DYNAMIC PRICING CALCULATOR ──
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
            
            let totalComm = baseComm + appliedPromoBonus;
            let walletDeduct = finalTotal - totalComm;

            if(walletDeduct < 0) walletDeduct = 0;

            $('#calc-total').text(finalTotal.toFixed(2));
            $('#calc-comm').text(baseComm.toFixed(2));
            $('#calc-deduct').text(walletDeduct.toFixed(2));
        }

        // ── 7. OTHER DYNAMIC HIDE/SHOW ENGINE (Directors/Partners) ──
        function applyDynamicHideShow() {
            const REPEATER_CONFIGS = [
                { trigger: 'number_of_members', prefix: 'member' },
                { trigger: 'number_of_directors', prefix: 'director' },
                { trigger: 'number_of_partners', prefix: 'partner' }
            ];

            REPEATER_CONFIGS.forEach(cfg => {
                let $dropdown = $('select').filter(function() {
                    return ($(this).attr('name') || '').includes(cfg.trigger);
                });
                
                if ($dropdown.length === 0) return;

                $dropdown.on('change', function() {
                    let count = parseInt($(this).val()) || 0;
                    
                    for (let i = 1; i <= 8; i++) {
                        let $dataFields = $(':input').not('[type="file"]').filter(function() {
                            return ($(this).attr('name') || '').includes(cfg.prefix + '_' + i + '_');
                        });
                        
                        let $fileFields = $('input[type="file"]').filter(function() {
                            let name = $(this).attr('name') || '';
                            return name.endsWith('_' + i) || name.includes('_' + i + ']') || name.includes('_' + i + '[');
                        });
                        
                        let sectionTitleRegex = new RegExp(cfg.prefix + '\\s+' + i, 'i');
                        let $section = $('.form-section').filter(function() {
                            let text = $(this).find('h3').text();
                            return sectionTitleRegex.test(text);
                        });

                        let stepIndex = $section.data('step');
                        let $tab = (stepIndex !== undefined) ? $('.wizard-tab[data-step="' + stepIndex + '"]') : $();

                        let $containers = $dataFields.closest('.form-group').add($fileFields.closest('.form-group'));

                        if (i <= count) {
                            $containers.show();
                            $section.show();
                            $tab.show();
                            $dataFields.add($fileFields).each(function() {
                                if ($(this).data('was-required')) { $(this).prop('required', true); }
                            });
                        } else {
                            $containers.hide();
                            $section.hide();
                            $tab.hide();
                            $dataFields.add($fileFields).each(function() {
                                if ($(this).prop('required')) {
                                    $(this).data('was-required', true);
                                    $(this).prop('required', false);
                                }
                                if ($(this).is(':checkbox, :radio')) { $(this).prop('checked', false); } 
                                else { $(this).val(''); }
                            });
                        }
                    }
                });

                $dropdown.trigger('change');
            });
        }


        // ── 🟢 THE MASTER INITIALIZER (RUNS ON PAGE LOAD) 🟢 ──
        $(document).ready(function() {
            // FIX THE MODAL FREEZE BUG: Move the modal safely out of the nested wrapper
            $('#cheatSheetModal').appendTo('body');

            // 1. Build the UI
            injectTooltips();
            convertToWizard();
            initSmartFormLogic();
            initBankRepeater();
            applyDynamicHideShow();

            // 2. Setup IT Password Dropdown Behavior
            let $itPasswordInput = $('input[name="it_password"]');
            if ($itPasswordInput.length > 0) {
                let $itPasswordContainer = $itPasswordInput.closest('.form-group');
                let dropdownHtml = `
                    <div class="form-group mb-3" id="it_password_toggle_container">
                        <label class="form-label fw-bold" style="font-size: 0.8rem; color: #374151;">Do you have an Income Tax Portal Password? <span class="text-danger">*</span></label>
                        <div class="mt-2 d-flex flex-wrap" style="gap: 1.5rem;">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="has_it_password_yes" name="has_it_password" value="yes" class="custom-control-input" required>
                                <label class="custom-control-label" for="has_it_password_yes" style="cursor: pointer; font-weight: 500; font-size: 0.9rem; color: #374151; padding-top: 2px;">Yes, I have my password</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="has_it_password_no" name="has_it_password" value="no" class="custom-control-input" required>
                                <label class="custom-control-label" for="has_it_password_no" style="cursor: pointer; font-weight: 500; font-size: 0.9rem; color: #374151; padding-top: 2px;">No, create a new application</label>
                            </div>
                        </div>
                    </div>`;
                $itPasswordContainer.before(dropdownHtml);
                $itPasswordContainer.hide();
                $itPasswordInput.prop('required', false);
                
                $('input[name="has_it_password"]').on('change', function() {
                    let val = $(this).val();
                    if (val === 'yes') {
                        $itPasswordContainer.slideDown(200);
                        $itPasswordInput.prop('required', true).prop('type', 'password').val('');
                    } else if (val === 'no') {
                        $itPasswordContainer.slideUp(200);
                        $itPasswordInput.prop('required', false).prop('type', 'text').val('NEW_APPLICATION');
                    }
                });
            }

            // 3. Setup Pricing & Nested Field Listeners
            $('form').on('change', 'select, input[type="radio"]', function() {
                handleNestedFields();
                calculateDynamicPrice();
            });
            setTimeout(() => { handleNestedFields(); calculateDynamicPrice(); }, 500);

            // 4. Setup Coupon Logic
            $('#apply-promo-btn').on('click', function() {
                let codeInput = $('#promo-code-input').val().toUpperCase().trim();
                let $btn = $(this);
                let $msg = $('#promo-message');

                if (!codeInput) return;

                $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                $msg.removeClass('text-danger text-success').addClass('text-muted').text("Verifying code...");

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
                        appliedPromoBonus = parseFloat(data.bonus);
                        appliedPromoCode = data.code;
                        $('#promo-row').removeClass('d-none');
                        $('#applied-promo-name').text(data.code);
                        $('#promo-bonus-amount').text(appliedPromoBonus.toFixed(2));
                        calculateDynamicPrice();

                        if($('#hidden-coupon-input').length === 0) {
                            $('form').append('<input type="hidden" name="applied_coupon" id="hidden-coupon-input" value="'+data.code+'">');
                        } else {
                            $('#hidden-coupon-input').val(data.code);
                        }

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

        }); // END MASTER INITIALIZER

        // ── 8. THE OMNISCIENT MUTATION OBSERVER ENGINE ──
        (function() {
            var observer = new MutationObserver(function(mutations, obs) {
                var myDocs = $('input[type="file"]').filter(function() {
                    return $(this).attr('name') && $(this).attr('name').includes('bill_doc_');
                });

                if (myDocs.length > 0 && $('#add-more-bills-btn').length === 0) {
                    myDocs.each(function(index) {
                        if (index > 0) { $(this).closest('.form-group').hide(); }
                    });

                    let firstWrapper = myDocs.first().closest('.form-group');
                    firstWrapper.after(`
                        <div class="col-12 mb-3 mt-2" id="add-more-wrapper">
                            <button type="button" id="add-more-bills-btn" class="btn btn-primary btn-sm" style="background-color: #0d6efd; color: white; border-radius: 5px; padding: 6px 16px; border: none; font-weight: bold; cursor: pointer;">
                                + Add Another Bill
                            </button>
                        </div>
                    `);

                    let visibleCount = 1;
                    $(document).off('click', '#add-more-bills-btn').on('click', '#add-more-bills-btn', function() {
                        if (visibleCount < myDocs.length) {
                            let nextWrapper = $(myDocs[visibleCount]).closest('.form-group');
                            nextWrapper.slideDown(250);
                            $('#add-more-wrapper').insertAfter(nextWrapper);
                            visibleCount++;
                        }
                        if (visibleCount >= myDocs.length) { $('#add-more-wrapper').hide(); }
                    });
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        })();
    </script>
    
    {{-- ── 9. RAZORPAY GATEWAY INVOCATION ── --}}
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
                setTimeout(() => { rzp.open(); }, 300);
            });
        </script>
    @endif
@endsection