@extends('layouts.agent')

@section('title', $service->name . ' Application | Agent Portal')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    
    <style>
        /* ── PAGE RESET ── */
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

        /* ── PREMIUM FORM LAYOUT (Sticky Side-by-Side) ── */
        .form-body form {
            display: block; 
            margin-top: 1rem;
        }

        .form-section {
            display: flex;
            flex-wrap: wrap;
            padding-top: 2.5rem;
            padding-bottom: 2.5rem;
            border-bottom: 1px solid var(--border-light);
        }
        .form-section:last-of-type { border-bottom: none; }

        .form-section > h3 {
            width: 25%;
            padding-right: 2rem;
            font-size: 1.15rem;
            font-weight: 800;
            color: #111827;
            margin: 0;
        }

        .form-grid {
            width: 75%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem 1.5rem;
        }

        .form-group label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-group label .required { color: #ef4444; margin-left: 0.2rem; }

        .form-control {
            width: 100%; 
            padding: 0.75rem 1rem !important; 
            border-radius: 8px; 
            border: 1px solid #d1d5db; 
            font-family: inherit; 
            font-size: 0.95rem; 
            color: #111827; 
            background: #ffffff; 
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); 
            transition: all 0.2s;
            height: auto !important; 
            min-height: 46px;
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

        .form-group:has(textarea) { grid-column: span 2; }
        textarea.form-control { min-height: 100px; resize: vertical; }

        .form-group:has(input[type="file"]) { grid-column: span 2; }
        .file-input-wrapper { position: relative; }
        
        input[type="file"].form-control {
            border: 1.5px dashed #d1d5db;
            padding: 1.25rem 1rem !important;
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
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: #374151;
            font-weight: 600;
            font-size: 0.85rem;
            margin-right: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        input[type="file"]::file-selector-button:hover { background: #f3f4f6; }

        .file-help {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.5rem;
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

        @media (max-width: 991px) {
            .form-section { flex-direction: column; padding-top: 1.5rem; padding-bottom: 1.5rem; }
            .form-section > h3 { width: 100%; margin-bottom: 1.5rem; padding-right: 0; position: static; }
            .form-grid { width: 100%; }
        }
        @media (max-width: 768px) {
            .sv-hero { padding: 2rem 1.5rem 5rem; }
            .sv-main-container { margin-top: -3rem; }
            .sv-card { padding: 1.5rem; }
            .sv-title-block h1 { font-size: 1.6rem; }
            .form-grid { grid-template-columns: 1fr; gap: 1rem; }
            .form-group:has(textarea), .form-group:has(input[type="file"]) { grid-column: span 1; }
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
                <div class="form-section-header">
                    <h2>Application Data</h2>
                    <p>Please enter the client's information. Verify all details carefully before submitting to avoid processing delays.</p>
                </div>
                
               
           <div class="form-section-body form-body">
                    {!! $form->render() !!}
                </div>
</div>
<br>
        

    

{{-- LIVE PRICE PREVIEW BOX --}}
                    @if(in_array($service->slug, ['gst-return-filing', 'itr-filing']))
                        <div class="card border-success mb-4 shadow-sm mt-4">
                            <div class="card-body bg-success-soft">
                                <table class="table table-sm table-borderless mb-0 font-weight-bold">
                                    <tr>
                                        <td class="text-success text-uppercase text-xs">Total Fee</td>
                                        <td class="text-right text-dark" style="font-size: 1.2rem;">₹<span id="calc-total">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-success text-uppercase text-xs">Your Commission (Approx)</td>
                                        <td class="text-right text-success">₹<span id="calc-comm">0.00</span></td>
                                    </tr>
                                    <tr class="border-top border-success">
                                        <td class="text-dark text-uppercase text-sm pt-2">Wallet Deduct</td>
                                        <td class="text-right text-danger pt-2" style="font-size: 1.3rem;">₹<span id="calc-deduct">0.00</span></td>
                                    </tr>
                                </table>
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
    
   {{-- The Javascript Brain (Bulletproof Version) --}}
   {{-- The Javascript Brain (Bulletproof Version) --}}
   <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Safely load rules from the DB
            const rawRules = @json($service->pricingRules ?? []);
            const pricingRules = Array.isArray(rawRules) ? rawRules : Object.values(rawRules);

            function normalizeValue(val) {
                return val ? String(val).toLowerCase().trim() : '';
            }

            // --- NEW: NESTED FIELD VISIBILITY LOGIC ---
            function handleNestedFields() {
                // 1. Handle Business -> Turnover
                let businessVal = $('input[name="has_business"]:checked').val();
                let turnoverWrapper = $('input[name="turnover"]').closest('.form-group');
                
                if (businessVal === 'yes') {
                    turnoverWrapper.slideDown(200);
                } else {
                    turnoverWrapper.slideUp(200);
                    $('input[name="turnover"]').prop('checked', false); // Clear hidden selection
                }

               
            }

            // --- THE PRICING CALCULATOR ---
            function calculateDynamicPrice() {
                let selectedGst = normalizeValue($('select[name="gst_type"]').val() || $('input[name="gst_type"]:checked').val());
                let selectedFreq = normalizeValue($('select[name="frequency_of_return"]').val() || $('input[name="frequency_of_return"]:checked').val());
                
                let s_type = normalizeValue($('select[name="itr_type"]').val() || $('input[name="itr_type"]:checked').val());
                let s_bus  = normalizeValue($('select[name="has_business"]').val() || $('input[name="has_business"]:checked').val());
                let s_cg   = normalizeValue($('select[name="has_capital_gains"]').val() || $('input[name="has_capital_gains"]:checked').val());
                let s_sal  = normalizeValue($('select[name="has_salary"]').val() || $('input[name="has_salary"]:checked').val());
                
                let selectedTurnover = normalizeValue($('select[name="turnover"]').val() || $('input[name="turnover"]:checked').val() || $('select[name="annual_turnover_range"]').val());

                let match = pricingRules.find(rule => {
                    let r_gst  = normalizeValue(rule.gst_type);
                    let r_freq = normalizeValue(rule.frequency);
                    let r_turn = normalizeValue(rule.turnover);
                    let r_type = normalizeValue(rule.itr_type);
                    let r_bus  = normalizeValue(rule.itr_business);
                    let r_cg   = normalizeValue(rule.itr_capital_gains);
                    let r_sal  = normalizeValue(rule.itr_salary);

                    return (r_gst  === '' || r_gst  === selectedGst) && 
                           (r_freq === '' || r_freq === selectedFreq) &&
                           (r_turn === '' || r_turn === selectedTurnover) && 
                           (r_type === '' || r_type === s_type) &&
                           (r_bus  === '' || r_bus  === s_bus) && 
                           (r_cg   === '' || r_cg   === s_cg) && 
                           (r_sal  === '' || r_sal  === s_sal);
                });

                let finalTotal = match ? parseFloat(match.base_price) : {{ $service->price ?? 0 }};
                let finalComm = match ? parseFloat(match.commission_amount) : {{ $service->commission_value ?? 0 }};
                let walletDeduct = finalTotal - finalComm;

                // Update UI
                $('#calc-total').text(finalTotal.toFixed(2)); 
                $('#calc-comm').text(finalComm.toFixed(2)); 
                $('#calc-deduct').text(walletDeduct.toFixed(2));
            }

            // Listen for any clicks/changes on the form
            $('form').on('change', 'select, input[type="radio"]', function() {
                handleNestedFields();     // Check if we need to hide/show fields
                calculateDynamicPrice();  // Recalculate price
            });

            // Initial setup on page load
            setTimeout(() => {
                handleNestedFields();
                calculateDynamicPrice();
            }, 500); 
        });
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