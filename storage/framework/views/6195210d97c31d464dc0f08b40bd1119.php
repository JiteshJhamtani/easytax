<?php $__env->startSection('title', $service->name . ' Application | Agent Portal'); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/form.css')); ?>">
    
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="service-view-wrapper">
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger mx-auto mt-3" style="max-width: 1100px; border-radius: 12px;"><?php echo e(session('error')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <header class="sv-hero">
        <div class="sv-breadcrumbs">
            <a href="<?php echo e(route('services.index')); ?>">Service Catalog</a>
            <span>/</span>
            <span class="current"><?php echo e($service->name); ?></span>
        </div>
        
        <div class="sv-hero-content">
            <div class="sv-title-block">
                <h1><?php echo e($service->name); ?></h1>
                <p>New Application Initialization</p>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($service->price > 0): ?>
                <div class="sv-price-badge">
                    <span class="sv-price-label">Standard Processing Fee</span>
                    <span class="sv-price-value"><?php echo e(money($service->price)); ?></span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </header>
    
    <div class="sv-main-container">
        
        
        <?php if (isset($component)) { $__componentOriginal0cf4b3f5908f819f1ba6f0ded5f774d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0cf4b3f5908f819f1ba6f0ded5f774d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.milestone-tracker','data' => ['milestones' => $giftMilestones]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('milestone-tracker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['milestones' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($giftMilestones)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0cf4b3f5908f819f1ba6f0ded5f774d8)): ?>
<?php $attributes = $__attributesOriginal0cf4b3f5908f819f1ba6f0ded5f774d8; ?>
<?php unset($__attributesOriginal0cf4b3f5908f819f1ba6f0ded5f774d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0cf4b3f5908f819f1ba6f0ded5f774d8)): ?>
<?php $component = $__componentOriginal0cf4b3f5908f819f1ba6f0ded5f774d8; ?>
<?php unset($__componentOriginal0cf4b3f5908f819f1ba6f0ded5f774d8); ?>
<?php endif; ?>

        
        <div class="sv-card">
            <div class="form-section-layout">
                <div class="form-section-header">
                    <h2>Application Data</h2>
                    <p>Please enter the client's information. Verify all details carefully before submitting to avoid processing delays.</p>
                </div>
                
               
           <div class="form-section-body form-body">
                    <?php echo $form->render(); ?>

                </div>


</div>
<br>
        

    


                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($service->slug, ['gst-return-filing', 'itr-filing','gst-annual-package'])): ?>
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
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

 
</div>
</div>
 </div>

<?php if (isset($component)) { $__componentOriginal224571a5377083f2e754900a8b27c9dc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal224571a5377083f2e754900a8b27c9dc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.payment-modal','data' => ['service' => $service,'commission' => $commissionAmount,'toPay' => $amountToPay]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('payment-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['service' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($service),'commission' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($commissionAmount),'toPay' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($amountToPay)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal224571a5377083f2e754900a8b27c9dc)): ?>
<?php $attributes = $__attributesOriginal224571a5377083f2e754900a8b27c9dc; ?>
<?php unset($__attributesOriginal224571a5377083f2e754900a8b27c9dc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal224571a5377083f2e754900a8b27c9dc)): ?>
<?php $component = $__componentOriginal224571a5377083f2e754900a8b27c9dc; ?>
<?php unset($__componentOriginal224571a5377083f2e754900a8b27c9dc); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="<?php echo e(asset('assets/js/form.js')); ?>"></script>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── PRICING RULES ────────────────────────────────
            const rawRules = <?php echo json_encode($service->pricingRules ?? [], 15, 512) ?>;
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

            // ── NESTED FIELD VISIBILITY ──────────────────────
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
                } else if (frequency === 'annual' || frequency === 'annual_gstr4') {
                    planWrapper.hide(); monthWrapper.hide(); quarterWrapper.hide();
                    $('select[name="plan"]').val('');
                    $('select[name="month"]').val('');
                    $('select[name="quarter"]').val('');
                }
            }

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
                    return (normalizeValue(rule.gst_type)          === '' || normalizeValue(rule.gst_type)          === selectedGst) &&
                           (normalizeValue(rule.frequency)         === '' || normalizeValue(rule.frequency)         === selectedFreq) &&
                           (normalizeValue(rule.plan)              === '' || normalizeValue(rule.plan)              === selectedPlan) &&
                           (normalizeValue(rule.turnover)          === '' || normalizeValue(rule.turnover)          === selectedTurnover) &&
                           (normalizeValue(rule.itr_type)          === '' || normalizeValue(rule.itr_type)          === s_type) &&
                           (normalizeValue(rule.itr_business)      === '' || normalizeValue(rule.itr_business)      === s_bus) &&
                           (normalizeValue(rule.itr_capital_gains) === '' || normalizeValue(rule.itr_capital_gains) === s_cg) &&
                           (normalizeValue(rule.itr_salary)        === '' || normalizeValue(rule.itr_salary)        === s_sal);
                           
                });

                let finalTotal   = match ? parseFloat(match.base_price)        : <?php echo e($service->price ?? 0); ?>;
                let finalComm    = match ? parseFloat(match.commission_amount)  : <?php echo e($service->commission_value ?? 0); ?>;
                let walletDeduct = finalTotal - finalComm;
                $('#calc-total').text(finalTotal.toFixed(2));
                $('#calc-comm').text(finalComm.toFixed(2));
                $('#calc-deduct').text(walletDeduct.toFixed(2));
            }

            $('form').on('change', 'select, input[type="radio"]', function() {
                handleNestedFields();
                calculateDynamicPrice();
            });
            setTimeout(() => { handleNestedFields(); calculateDynamicPrice(); }, 500);

          const REPEATERS = [
                {
                    // 1. For Section 8, OPC, and Private Limited Company
                    triggerName : 'number_of_directors',
                    dinTrigger  : 'director_din_available',
                    prefix      : 'director',
                    sectionTitle: 'Director Details',
                    min: 1, max: 8, // Set to 1 to accommodate OPCs
                    fields: [
                        { name: 'name',    label: 'Full Name',           type: 'text',     required: true  },
                       
                        { name: 'phone',   label: 'Mobile Number',       type: 'text',     required: true  },
                        { name: 'email',   label: 'Email Address',       type: 'email',    required: true  },
                       
                    ]
                },
                {
                    // 2. For FPO Registration
                    triggerName : 'number_of_members',
                    prefix      : 'member',
                    sectionTitle: 'Member Details',
                    min: 2, max: 15,
                    fields: [
                        { name: 'name',         label: 'Full Name',            type: 'text', required: true  },
                        { name: 'phone',        label: 'Mobile Number',        type: 'text', required: true  },
                        { name: 'email',        label: 'Email Address',        type: 'email',    required: true  },

                    ]
                },
                {
                    // 3. For LLP Registration
                    triggerName : 'number_of_partners',
                    dinTrigger  : 'partner_dpin_available', // LLPs use DPIN instead of DIN
                    prefix      : 'partner',
                    sectionTitle: 'Partner Details',
                    min: 2, max: 8,
                    fields: [
                        { name: 'name',    label: 'Full Name',           type: 'text',     required: true  },
                        { name: 'phone',   label: 'Mobile Number',       type: 'text',     required: true  },
                        { name: 'email',   label: 'Email Address',       type: 'email',    required: true  },
                       
                    ]
                }
            ];



            function buildField(prefix, index, fieldCfg, showDin) {
                const fieldName = `${prefix}_${index}_${fieldCfg.name}`;
                if (fieldCfg.dinConditional && !showDin) return '';
                const required = fieldCfg.required ? 'required' : '';
                const star     = fieldCfg.required ? '<span class="required">*</span>' : '';
                const existing = document.querySelector(`[name="${fieldName}"]`);
                const savedVal = existing ? existing.value : '';
                let input = fieldCfg.type === 'textarea'
                    ? `<textarea name="${fieldName}" class="form-control" rows="2" ${required}>${savedVal}</textarea>`
                    : `<input type="${fieldCfg.type}" name="${fieldName}" class="form-control" value="${savedVal}" ${required}>`;
                const spanStyle = fieldCfg.type === 'textarea' ? 'style="grid-column:span 2;"' : '';
                return `<div class="form-group" ${spanStyle}><label>${fieldCfg.label}${star}</label>${input}</div>`;
            }

            function buildCard(prefix, index, fields, showDin) {
                const fieldsHtml = fields.map(f => buildField(prefix, index, f, showDin)).join('');
                return `
                    <div class="repeater-card" data-index="${index}" style="
                        grid-column:span 2; background:#f8fffe;
                        border:1.5px solid #c8eadb; border-radius:12px;
                        padding:1.25rem 1.5rem 0.5rem; margin-bottom:0.5rem;">
                        <div style="margin-bottom:1rem;">
                            <span style="font-weight:700;font-size:0.9rem;color:#1E9C5D;text-transform:uppercase;letter-spacing:0.05em;">
                                ${prefix.charAt(0).toUpperCase() + prefix.slice(1)} ${index}
                            </span>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.5rem;">
                            ${fieldsHtml}
                        </div>
                    </div>`;
            }

          function getRepeaterContainer(triggerName) {
                const id = `repeater-container-${triggerName}`;
                let container = document.getElementById(id);
                
                if (!container) {
                    container = document.createElement('div');
                    container.id = id;
                    container.style.cssText = 'grid-column:span 2; margin-top:0.5rem;';
                    
                    // 🚀 NEW: Search for the "Contact Details" section
                    const allSections = document.querySelectorAll('.form-section');
                    let contactSection = null;
                    
                    allSections.forEach(section => {
                        const heading = section.querySelector('h3');
                        if (heading && heading.textContent.toLowerCase().includes('contact')) {
                            contactSection = section;
                        }
                    });

                    // Inject into Contact section, or fallback to the current section
                    const targetSection = contactSection || (() => {
                        const trigger = document.querySelector(`[name="${triggerName}"]`);
                        return trigger ? trigger.closest('.form-section') : null;
                    })();

                    if (!targetSection) return null;
                    
                    const grid = targetSection.querySelector('.form-grid');
                    if (grid) grid.insertBefore(container, grid.firstChild); // Puts it at the top of the section
                }
                return container;
            }
            function renderRepeater(cfg) {
                const trigger = document.querySelector(`[name="${cfg.triggerName}"]`);
                if (!trigger) return;
                const container = getRepeaterContainer(cfg.triggerName);
                if (!container) return;
                let count = Math.min(Math.max(parseInt(trigger.value) || 0, 0), cfg.max);
                const showDin = cfg.dinTrigger
                    ? (document.querySelector(`[name="${cfg.dinTrigger}"]`)?.value === 'yes')
                    : false;
                trigger.setCustomValidity(count > 0 && count < cfg.min ? `Minimum ${cfg.min} required` : '');
                if (count < cfg.min) { container.innerHTML = ''; return; }
                let html = `<div style="grid-column:span 2;display:grid;grid-template-columns:1fr;gap:0.75rem;margin-top:0.75rem;">`;
                for (let i = 1; i <= count; i++) html += buildCard(cfg.prefix, i, cfg.fields, showDin);
                html += '</div>';
                container.innerHTML = html;
            }

            REPEATERS.forEach(cfg => {
                const trigger = document.querySelector(`[name="${cfg.triggerName}"]`);
                if (!trigger) return;
                trigger.setAttribute('min', cfg.min);
                trigger.setAttribute('max', cfg.max);
                trigger.addEventListener('input',  () => renderRepeater(cfg));
                trigger.addEventListener('change', () => renderRepeater(cfg));
                if (cfg.dinTrigger) {
                    const dinSelect = document.querySelector(`[name="${cfg.dinTrigger}"]`);
                    if (dinSelect) dinSelect.addEventListener('change', () => renderRepeater(cfg));
                }
                if (trigger.value) renderRepeater(cfg);
            });

            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    REPEATERS.forEach(cfg => {
                        const trigger = document.querySelector(`[name="${cfg.triggerName}"]`);
                        if (!trigger || !trigger.value) return;
                        const count = parseInt(trigger.value);
                        if (count < cfg.min || count > cfg.max) {
                            e.preventDefault();
                            alert(`${cfg.sectionTitle}: number must be between ${cfg.min} and ${cfg.max}.`);
                            trigger.focus();
                        }
                    });
                });
            }

        }); 
    </script>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('razorpay_order')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ORDER = <?php echo json_encode(session('razorpay_order'), 15, 512) ?>;

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
                        form.action = '<?php echo e(route('payment.success')); ?>';

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '<?php echo e(csrf_token()); ?>';
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
                            form.action = '<?php echo e(route('payment.failure')); ?>';

                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '<?php echo e(csrf_token()); ?>';
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.agent', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/front/pages/services/show.blade.php ENDPATH**/ ?>