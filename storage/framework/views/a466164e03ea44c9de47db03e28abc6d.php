<?php $__env->startSection('title', 'Application Manager | EasyTax'); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>

        /* Typeform Balance Sheet Modal Styles */
.tf-step {
    display: none;
    animation: tfSlideUp 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) forwards;
    padding: 2rem;
}
.tf-step.active { display: block; }
@keyframes tfSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.tf-input {
    border: none; border-bottom: 2px solid #e2e8f0; border-radius: 0;
    background: transparent; font-size: 1.5rem; font-weight: 700; color: #1e293b;
    padding: 8px 0; box-shadow: none !important; transition: border-color 0.2s;
}
.tf-input:focus { border-bottom-color: var(--green); outline: none; }
.tf-input::placeholder { color: #cbd5e1; font-weight: 400; font-size: 1.2rem; }
.tally-bar {
    background: #1e293b; color: white; padding: 15px 20px; border-radius: 12px;
    display: flex; justify-content: space-between; align-items: center; margin-top: 20px;
}
.tally-match { background: var(--green); }
        /* ── PAGE HEADER ── */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: var(--slate-dark); margin: 0 0 0.25rem 0; letter-spacing: -0.02em; }
        .page-subtitle { font-size: 0.9rem; color: var(--text-muted); margin: 0; }

        /* ── EXPORT DROPDOWN ── */
        .export-btn {
            background-color: var(--surface); color: var(--slate-dark); font-weight: 700; padding: 0.5rem 1.25rem;
            border-radius: 8px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 0.5rem;
            transition: all 0.2s; font-size: 0.85rem; cursor: pointer;
        }
        .export-btn:hover, .export-btn[aria-expanded="true"] { background-color: var(--ink-100); color: var(--slate-dark); }
        .dropdown-menu { border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 0.5rem; min-width: 240px; }
        .dropdown-item { font-size: 0.85rem; font-weight: 600; color: var(--slate); border-radius: 6px; padding: 0.6rem 1rem; display: flex; align-items: center; gap: 0.75rem; transition: all 0.15s; }
        .dropdown-item:hover { background-color: var(--ink-100); color: var(--slate-dark); }
        .dropdown-item i { width: 16px; text-align: center; }

        /* ── KPI CARDS ── */
        .kpi-card {
            display: flex; align-items: center; padding: 1.25rem 1.5rem; border-radius: 16px;
            background: var(--surface); border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s; height: 100%;
        }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; margin-right: 1rem; }
        .kpi-body { flex: 1; }
        .kpi-value { font-size: 1.3rem; font-weight: 800; color: var(--slate-dark); line-height: 1.2; }
        .kpi-label { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; }

        .kpi-blue   .kpi-icon { background: #dbeafe; color: #2563eb; }
        .kpi-orange .kpi-icon { background: #fff7ed; color: #ea580c; }
        .kpi-green  .kpi-icon { background: #dcfce7; color: #16a34a; }
        .kpi-red    .kpi-icon { background: #FEE2E2; color: #DC2626; }

        /* ── FILTER BAR ── */
        .filter-bar {
            background: var(--surface); border-radius: 12px; border: 1px solid var(--border);
            padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; align-items: flex-end; gap: 1rem;
        }
        .filter-group { display: flex; flex-direction: column; gap: 0.4rem; flex: 1; min-width: 150px; }
        .filter-label { font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: var(--text-muted); }
        .filter-control {
            font-size: 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-weight: 600; color: var(--slate);
            padding: 0.45rem 0.8rem; background: #fafbfc; transition: all 0.2s; height: 40px; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .filter-control:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(30,156,93,0.15); background: #fff; }
        
        .btn-reset {
            background: #fff; border: 1px solid var(--border); color: var(--text-muted); height: 40px; width: 40px;
            border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;
        }
        .btn-reset:hover { background: #FEE2E2; color: #DC2626; border-color: #fca5a5; }

        /* ── DATA TABLE CARD ── */
        .table-card { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        .dataTables_wrapper { padding: 1.5rem; }
        table.dataTable { border-collapse: collapse !important; width: 100% !important; margin-bottom: 1rem !important; border-bottom: 1px solid var(--border); }
        table.dataTable thead th { background: #f8fafc; color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--border) !important; border-top: none !important; padding: 1rem; white-space: nowrap; }
        table.dataTable tbody td { padding: 1rem; vertical-align: middle; color: var(--text); font-size: 0.9rem; border-bottom: 1px solid var(--ink-100); }
        table.dataTable tbody tr:hover { background: #f8fafc; }
        
        .dataTables_info { color: var(--text-muted); font-size: 0.85rem; font-weight: 500; }
        .page-item.active .page-link { background: var(--slate-dark); border-color: var(--slate-dark); color: #fff; border-radius: 6px; }
        .page-link { color: var(--slate); border: 1px solid var(--border); border-radius: 6px; margin: 0 2px; font-size: 0.85rem; font-weight: 600; }

        /* ── ULTRA-COMPACT TABLE (NO SCROLLING) ── */
        .table-card { overflow: visible !important; }
        
        .compact-table {
            width: 100% !important;
            table-layout: auto !important;
        }
        
        /* Shrink text and padding to the absolute minimum */
        .compact-table th, 
        .compact-table td {
            padding: 6px 4px !important; 
            font-size: 0.75rem !important; 
            white-space: normal !important; /* Allows text to wrap to a new line instead of stretching horizontally */
            vertical-align: middle !important;
            word-break: break-word;
        }

        /* Shrink the Generate, Download, and View buttons */
        .compact-table .btn {
            padding: 3px 6px !important;
            font-size: 0.7rem !important;
            line-height: 1.2;
        }

        /* Prevent the action column from getting crushed */
        .compact-table th:last-child,
        .compact-table td:last-child {
            min-width: 60px;
            text-align: right;
        }
    </style>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/applications.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <div>
            <h1><?php echo e($pageTitle); ?></h1>
            <p class="page-subtitle">Monitor, filter, and manage all agent submissions.</p>
        </div>
        <div>
            
            <div class="dropdown d-inline-block">
                <button class="export-btn dropdown-toggle" type="button" id="exportMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-export text-primary"></i> Export Data
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportMenu">
                    
                    <a class="dropdown-item" href="<?php echo e(route('admin.applications.export')); ?>">
                        <i class="fas fa-table text-secondary"></i> Standard Excel Export
                    </a>
                    <div class="dropdown-divider"></div>
                    
                    <a class="dropdown-item" href="<?php echo e(route('admin.applications.export', ['filter' => 'completed_forms'])); ?>">
                        <i class="fas fa-check-circle text-success"></i> Export Completed (Form Data)
                    </a>
                    
                    <a class="dropdown-item" href="<?php echo e(route('admin.applications.export', ['filter' => 'pending_only'])); ?>">
                        <i class="fas fa-hourglass-half text-warning"></i> Export Pending Applications
                    </a>
                </div>
            </div>
        </div>
    </div>
  
    
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">  
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value"><?php echo e($stats->total ?? 0); ?></div>
                    <div class="kpi-label">Total Volume</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-orange">
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value"><?php echo e($stats->pending ?? 0); ?></div>
                    <div class="kpi-label">Pending Review</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value"><?php echo e($stats->completed ?? 0); ?></div>
                    <div class="kpi-label">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="kpi-card kpi-red">
                <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value"><?php echo e($stats->failed ?? 0); ?></div>
                    <div class="kpi-label">Failed Payments</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="filter-bar">
        <div class="filter-group">
            <label class="filter-label" for="filterAgent">Assigned Agent</label>
            <select id="filterAgent" class="filter-control">
                <option value="">All Agents</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterService">Service Type</label>
            <select id="filterService" class="filter-control">
                <option value="">All Services</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <option value="<?php echo e($service->id); ?>"><?php echo e($service->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterStatus">App Status</label>
            <select id="filterStatus" class="filter-control">
                <option value="">Any Status</option>
                <option value="DRAFT">Draft</option>
                <option value="SUBMITTED">Submitted</option>
                <option value="IN_PROGRESS">In Progress</option>
                <option value="COMPLETED">Completed</option>
                <option value="CANCELLED">Cancelled</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterPayment">Payment</label>
            <select id="filterPayment" class="filter-control">
                <option value="">Any Payment</option>
                <option value="SUCCESS">Success</option>
                <option value="FAILED">Failed</option>
                <option value="PENDING">Pending</option>
                <option value="REFUNDED">Refunded</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterDateFrom">Date From</label>
            <input type="date" id="filterDateFrom" class="filter-control">
        </div>

        <div class="filter-group">
            <label class="filter-label" for="filterDateTo">Date To</label>
            <input type="date" id="filterDateTo" class="filter-control">
        </div>

        <div>
            <button id="resetFilters" class="btn-reset" title="Clear Filters">
                <i class="fas fa-undo-alt"> </i>
            </button>
        </div>
    </div>


    <div class="table-card">
        <table id="applicationsTable" class="table w-100 compact-table">
           <thead>
                <tr>
                    <th>App ID</th>
                    <th>Agent</th>
                    <th>Service Type</th>
                    <th>Primary Data</th> 
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Amount</th>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'itr-filing'): ?>
                        <th>ACK NO</th>
                        <th>COMPUTATION</th>
                        <th>BALANCE SHEET</th>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <th>Date Submitted</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<div class="modal fade" id="balanceSheetModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; background: #f8f9fa;">
            
            <div class="progress" style="height: 6px; border-radius: 16px 16px 0 0;">
                <div class="progress-bar bg-success" id="tf-progress" style="width: 16%; transition: width 0.4s ease;"></div>
            </div>

            <div class="modal-body position-relative p-0" style="min-height: 550px;">
                <form id="balanceSheetForm">
                    <input type="hidden" id="tf-app-id">
                    <input type="hidden" id="tf-val-sales" value="0">
                    <input type="hidden" id="tf-val-target-np" value="0">
                    <input type="hidden" id="tf-val-other-inc" value="0">

                    <div class="tf-step active" id="step-1">
                        <div class="text-center mt-4">
                            <span class="text-muted font-weight-bold mb-2 d-block">STEP 1 OF 6</span>
                            <h3 class="mb-4 font-weight-bold">Let's verify the P&L basics.</h3>
                            
                            <div class="row mb-4 justify-content-center">
                                <div class="col-md-4">
                                    <label class="text-muted small text-uppercase">Business Turnover</label>
                                    <h4 class="font-weight-bold" id="tf-disp-sales">₹0</h4>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small text-uppercase">Target Net Profit</label>
                                    <h4 class="font-weight-bold text-success" id="tf-disp-target-np">₹0</h4>
                                </div>
                            </div>
                            <p class="text-muted">We pulled these numbers from the ITR form.</p>
                            
                            <button type="button" class="btn btn-success btn-lg px-5 mt-4 rounded-pill tf-next">
                                Let's Start <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="tf-step" id="step-2">
                        <span class="text-muted font-weight-bold mb-2 d-block text-center">STEP 2 OF 6: TRADING A/C</span>
                        <h4 class="mb-4 font-weight-bold text-center">Enter your Stock & Direct Costs</h4>
                        
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Opening Stock</label><input type="number" class="form-control tf-input calc-trigger" name="opening_stock" placeholder="0"></div>
                            <div class="col-md-6 form-group"><label>Closing Stock (Required)</label><input type="number" class="form-control tf-input calc-trigger" name="closing_stock" placeholder="0"></div>
                            <div class="col-md-6 form-group"><label>Purchases</label><input type="number" class="form-control tf-input calc-trigger" name="purchases" placeholder="0"></div>
                            <div class="col-md-6 form-group"><label>Direct Expenses</label><input type="number" class="form-control tf-input calc-trigger" name="direct_expenses" placeholder="0"></div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <h5 class="text-primary mb-3">Live Gross Profit: ₹<span id="disp-gp">0</span></h5>
                            <button type="button" class="btn btn-secondary rounded-pill tf-prev mr-2">Back</button>
                            <button type="button" class="btn btn-success rounded-pill px-5 tf-next">Next <i class="fas fa-arrow-right ml-2"></i></button>
                        </div>
                    </div>

                    <div class="tf-step" id="step-3">
                        <span class="text-muted font-weight-bold mb-2 d-block text-center">STEP 3 OF 6: EXPENSES</span>
                        <h4 class="mb-4 font-weight-bold text-center">Major Business Expenses</h4>
                        <p class="text-center text-muted small mb-4">Goal: Hit Net Profit of ₹<span id="disp-goal-np" class="font-weight-bold text-dark"></span></p>

                        <div class="row" style="max-height: 300px; overflow-y: auto;">
                            <div class="col-md-6 form-group"><label>Salaries</label><input type="number" class="form-control tf-input calc-trigger" name="salaries" placeholder="0"></div>
                            <div class="col-md-6 form-group"><label>Shop Rent</label><input type="number" class="form-control tf-input calc-trigger" name="shop_rent" placeholder="0"></div>
                            <div class="col-md-6 form-group"><label>Electricity</label><input type="number" class="form-control tf-input calc-trigger" name="electricity" placeholder="0"></div>
                            <div class="col-md-6 form-group"><label>Other Expenses (Adjuster)</label><input type="number" class="form-control tf-input calc-trigger" name="other_expenses" placeholder="0"></div>
                        </div>

                        <div class="text-center mt-4">
                            <h5 id="np-status" class="mb-3">Live Net Profit: ₹<span id="disp-np">0</span></h5>
                            <button type="button" class="btn btn-secondary rounded-pill tf-prev mr-2">Back</button>
                            <button type="button" class="btn btn-success rounded-pill px-5 tf-next">Next <i class="fas fa-arrow-right ml-2"></i></button>
                        </div>
                    </div>

                    <div class="tf-step" id="step-4">
                        <span class="text-muted font-weight-bold mb-2 d-block text-center">STEP 4 OF 6: CAPITAL A/C</span>
                        <h4 class="mb-4 font-weight-bold text-center">Owner's Equity & Withdrawals</h4>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8 form-group"><label>Opening Capital (Balance b/d)</label><input type="number" class="form-control tf-input calc-trigger" name="opening_capital" placeholder="0"></div>
                            <div class="col-md-8 form-group"><label>Drawings (Personal use)</label><input type="number" class="form-control tf-input calc-trigger" name="drawings" placeholder="0"></div>
                            <div class="col-md-8 form-group"><label>Interest Income</label><input type="number" class="form-control tf-input calc-trigger" name="interest_income" placeholder="0"></div>
                        </div>

                        <div class="text-center mt-4">
                            <h5 class="text-primary mb-3">Closing Capital: ₹<span id="disp-cap">0</span></h5>
                            <button type="button" class="btn btn-secondary rounded-pill tf-prev mr-2">Back</button>
                            <button type="button" class="btn btn-success rounded-pill px-5 tf-next">Next <i class="fas fa-arrow-right ml-2"></i></button>
                        </div>
                    </div>

                    <div class="tf-step" id="step-5">
                        <span class="text-muted font-weight-bold mb-2 d-block text-center">STEP 5 OF 6: LIABILITIES</span>
                        <h4 class="mb-4 font-weight-bold text-center">What the business owes</h4>
                        
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Bank Loan</label><input type="number" class="form-control tf-input calc-trigger" name="bank_loan" placeholder="0"></div>
                            <div class="col-md-6 form-group"><label>Other Loans</label><input type="number" class="form-control tf-input calc-trigger" name="other_loans" placeholder="0"></div>
                            <div class="col-md-6 form-group"><label>Sundry Creditors</label><input type="number" class="form-control tf-input calc-trigger" name="sundry_creditors" placeholder="0"></div>
                            <div class="col-md-6 form-group"><label>Other Current Liab.</label><input type="number" class="form-control tf-input calc-trigger" name="other_current_liabilities" placeholder="0"></div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-secondary rounded-pill tf-prev mr-2">Back</button>
                            <button type="button" class="btn btn-success rounded-pill px-5 tf-next">Final Step <i class="fas fa-arrow-right ml-2"></i></button>
                        </div>
                    </div>

                    <div class="tf-step" id="step-6">
                        <span class="text-muted font-weight-bold mb-2 d-block text-center">STEP 6 OF 6: ASSETS & TALLY</span>
                        
                        <div class="row" style="max-height: 250px; overflow-y: auto;">
                            <div class="col-md-4 form-group"><label>Cash in Hand</label><input type="number" class="form-control tf-input calc-trigger" name="cash_in_hand" placeholder="0"></div>
                            <div class="col-md-4 form-group"><label>Bank Balance</label><input type="number" class="form-control tf-input calc-trigger" name="bank_balance" placeholder="0"></div>
                            <div class="col-md-4 form-group"><label>Sundry Debtors</label><input type="number" class="form-control tf-input calc-trigger" name="sundry_debtors" placeholder="0"></div>
                            <div class="col-md-4 form-group"><label>Fixed Assets</label><input type="number" class="form-control tf-input calc-trigger" name="furniture" placeholder="0"></div>
                            <div class="col-md-4 form-group"><label>TDS</label><input type="number" class="form-control tf-input calc-trigger" name="tds" placeholder="0"></div>
                        </div>

                        <div class="tally-bar" id="tally-bar">
                            <div>
                                <span class="small text-uppercase d-block" style="opacity: 0.7;">Total Liabilities</span>
                                <h4 class="m-0 font-weight-bold">₹<span id="disp-tot-liab">0</span></h4>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-equals mb-1" id="tally-icon"></i><br>
                                <span class="small" id="tally-text">Difference: ₹<span id="disp-diff">0</span></span>
                            </div>
                            <div class="text-right">
                                <span class="small text-uppercase d-block" style="opacity: 0.7;">Total Assets</span>
                                <h4 class="m-0 font-weight-bold">₹<span id="disp-tot-assets">0</span></h4>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-secondary rounded-pill tf-prev mr-2">Back</button>
                            <button type="button" class="btn btn-dark rounded-pill px-5" id="btn-generate-pdf" disabled>Generate PDF <i class="fas fa-file-pdf ml-2"></i></button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('js'); ?>
 
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo e(asset('assets/js/admin-applications.js')); ?>?v=<?php echo e(time()); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/applications/index.blade.php ENDPATH**/ ?>