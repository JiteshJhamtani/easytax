<?php $__env->startSection('title', 'Generate Balance Sheet'); ?>

<?php $__env->startSection('css'); ?>
<style>
    /* Clean layout styles  */ 
    .bs-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e8ecf0; margin-bottom: 1rem; overflow: hidden; }
    .bs-header { background: #f8f9fa; padding: 12px 20px; border-bottom: 1px solid #e8ecf0; font-weight: 700; font-size: 1.05rem; color: #2E3D4E; }
    .bs-col-header { background: #EDF7F4; color: #1E9C5D; padding: 10px 20px; font-weight: 700; display: flex; justify-content: space-between; font-size: 0.9rem; text-transform: uppercase; }
    
    /* Compact Row sizing */
    .bs-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
    .bs-row:last-child { border-bottom: none; }
    .bs-input { text-align: right; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px 10px; width: 130px; transition: border-color 0.2s; font-size: 0.95rem; }
    .bs-input:focus { border-color: #1E9C5D; outline: none; box-shadow: 0 0 0 2px rgba(30, 156, 93, 0.15); }
    .bs-input[readonly] { background: #f1f5f9; color: #475569; border-color: transparent; font-weight: 600; }
    
    /* Totals and New Inline Status Box */
    .bs-total-row { background: #EDF7F4; padding: 12px 20px; font-weight: 800; font-size: 1.1rem; color: #1E9C5D; display: flex; justify-content: space-between; }
    
    /* INLINE FOOTER BOX */
    .bs-status-box { background: #2e3d4e; border-radius: 8px; border: 1px solid #e8ecf0; padding: 20px 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .tally-status { font-size: 1.1rem; font-weight: 700; }
    .tally-match { color: #10b981; } .tally-error { color: #f43f5e; }

    /* Tab Styling */
    .nav-pills .nav-link { border-radius: 50px; padding: 8px 24px; font-weight: 600; color: #475569; background: #f1f5f9; margin-right: 10px; border: 1px solid transparent; cursor: pointer; }
    .nav-pills .nav-link.active { background: #1E9C5D; color: #fff; box-shadow: 0 4px 10px rgba(30, 156, 93, 0.3); }
    .nav-link.disabled-tab { pointer-events: none; opacity: 0.6; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold mb-1">Balance Sheet Generator</h2>
            <p class="text-muted mb-0">Application ID: #<?php echo e($application->id); ?> | Target Net Profit: <strong class="text-dark">₹<?php echo e(number_format($netProfit)); ?></strong></p>
        </div>
        <a href="<?php echo e(route('team.applications.show', $application->id)); ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left mr-2"></i> Back to Task
        </a>
    </div>

    <ul class="nav nav-pills mb-4" id="bsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pnl-tab" data-toggle="pill" href="#pnl" role="tab">1. Trading & P&L Account</a>
        </li>
        <li class="nav-item">
            <a class="nav-link disabled-tab" id="bs-tab" data-toggle="pill" href="#bs" role="tab">2. Balance Sheet</a>
        </li>
    </ul>

    <form id="pdfGenerateForm" action="<?php echo e(route('team.applications.balance-sheet.generate', $application->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" id="val-target-np" value="<?php echo e($netProfit); ?>">

        <div class="tab-content" id="bsTabsContent">
            
            <div class="tab-pane fade show active" id="pnl" role="tabpanel">
                <div class="bs-card">
                    <div class="row no-gutters">
                        <div class="col-md-6" style="border-right: 1px solid #e8ecf0;">
                            <div class="bs-col-header"><span>Expenses</span><span>Amount</span></div>
                            <div class="bs-row"><span>Opening Stock</span> <input type="number" class="bs-input calc-trigger" name="opening_stock" placeholder="0"></div>
                            <div class="bs-row"><span>Purchases</span> <input type="number" class="bs-input calc-trigger" name="purchases" placeholder="0"></div>
                            <div class="bs-row"><span>Direct Expenses</span> <input type="number" class="bs-input calc-trigger" name="direct_expenses" placeholder="0"></div>
                            <div class="bs-row"><span>Salaries Expenses</span> <input type="number" class="bs-input calc-trigger" name="salaries" placeholder="0"></div>
                            <div class="bs-row"><span>Electricity Expenses</span> <input type="number" class="bs-input calc-trigger" name="electricity" placeholder="0"></div>
                            <div class="bs-row"><span>Shop Rent</span> <input type="number" class="bs-input calc-trigger" name="shop_rent" placeholder="0"></div>
                            <div class="bs-row"><span>Telephone & Internet</span> <input type="number" class="bs-input calc-trigger" name="telephone_internet" placeholder="0"></div>
                            <div class="bs-row"><span>Printing & Stationery</span> <input type="number" class="bs-input calc-trigger" name="printing_stationery" placeholder="0"></div>
                            <div class="bs-row"><span>Repairs & Maintenance</span> <input type="number" class="bs-input calc-trigger" name="repairs_maintenance" placeholder="0"></div>
                            <div class="bs-row"><span>Interest on loan</span> <input type="number" class="bs-input calc-trigger" name="interest_on_loan" placeholder="0"></div>
                            <div class="bs-row"><span>Other Expenses</span> <input type="number" class="bs-input calc-trigger" name="other_expenses" placeholder="0"></div>
                            <div class="bs-row" style="background: #f8f9fa;"><span class="font-weight-bold">Live Net Profit</span> <span class="font-weight-bold" id="disp-live-np">₹0</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="bs-col-header"><span>Income</span><span>Amount</span></div>
                            <div class="bs-row"><span>Sales <small class="text-muted">(Auto)</small></span> <input type="number" class="bs-input calc-trigger" name="sales" value="<?php echo e($sales); ?>" readonly></div>
                            <div class="bs-row"><span>Closing Stock</span> <input type="number" class="bs-input calc-trigger" name="closing_stock" value="<?php echo e($extractedData['closing_stock'] ?? ''); ?>" placeholder="0"></div>
                            <div class="bs-row"><span>Interest Income</span> <input type="number" class="bs-input calc-trigger" name="interest_income" placeholder="0"></div>
                            <div class="bs-row"><span>Other Income <small class="text-muted">(Auto)</small></span> <input type="number" class="bs-input calc-trigger" name="other_income" value="<?php echo e($otherIncome); ?>" readonly></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="bs" role="tabpanel">
                <div class="bs-card">
                    <div class="row no-gutters">
                        <div class="col-md-6" style="border-right: 1px solid #e8ecf0;">
                            <div class="bs-col-header"><span>Liabilities</span><span>Amount</span></div>
                            <div class="p-3" style="background: #fbfbfc; border-bottom: 1px solid #f1f5f9;">
                                <h6 class="font-weight-bold mb-2 text-dark">Capital Account</h6>
                                <div class="bs-row px-0 py-1"><span>Opening Capital</span> <input type="number" class="bs-input calc-trigger" name="opening_capital" placeholder="0"></div>
                                <div class="bs-row px-0 py-1"><span>Less: Drawings</span> <input type="number" class="bs-input calc-trigger" name="drawings" placeholder="0"></div>
                                <div class="bs-row px-0 pt-2 pb-0"><span class="font-weight-bold text-primary">Closing Capital</span> <span class="font-weight-bold text-primary" id="disp-closing-cap">₹0</span></div>
                            </div>
                            <h6 class="font-weight-bold mt-3 mx-3 text-dark">Long Term Liabilities</h6>
                            <div class="bs-row py-1"><span>Loan taken from Bank</span> <input type="number" class="bs-input calc-trigger" name="bank_loan" placeholder="0"></div>
                            <div class="bs-row py-1"><span>Other Loans</span> <input type="number" class="bs-input calc-trigger" name="other_loans" placeholder="0"></div>
                            
                            <h6 class="font-weight-bold mt-3 mx-3 text-dark">Current Liabilities</h6>
                            <div class="bs-row py-1"><span>Sundry Creditors</span> <input type="number" class="bs-input calc-trigger" name="sundry_creditors" value="<?php echo e($extractedData['sundry_creditors'] ?? ''); ?>" placeholder="0"></div>
                            <div class="bs-row py-1"><span>Other Current Liabilities</span> <input type="number" class="bs-input calc-trigger" name="other_current_liabilities" placeholder="0"></div>
                            <div class="bs-total-row mt-2"><span>Total</span><span id="disp-tot-liab">₹0</span></div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="bs-col-header"><span>Assets</span><span>Amount</span></div>
                            <h6 class="font-weight-bold mt-3 mx-3 text-dark">Fixed Assets</h6>
                            <div class="bs-row py-1"><span>Furniture</span> <input type="number" class="bs-input calc-trigger" name="furniture" placeholder="0"></div>
                            <div class="bs-row py-1"><span>Vehicle</span> <input type="number" class="bs-input calc-trigger" name="vehicle" placeholder="0"></div>
                            <div class="bs-row py-1"><span>Computer</span> <input type="number" class="bs-input calc-trigger" name="computer" placeholder="0"></div>
                            <div class="bs-row py-1"><span>Other Fixed Assets</span> <input type="number" class="bs-input calc-trigger" name="other_fixed_assets" placeholder="0"></div>
                            
                            <h6 class="font-weight-bold mt-3 mx-3 text-dark">Investments</h6>
                            <div class="bs-row py-1"><span>Total Investments</span> <input type="number" class="bs-input calc-trigger" name="total_investments" placeholder="0"></div>
                            
                            <h6 class="font-weight-bold mt-3 mx-3 text-dark">Current Assets</h6>
                            <div class="bs-row py-1"><span>Sundry Debtors</span> <input type="number" class="bs-input calc-trigger" name="sundry_debtors" value="<?php echo e($extractedData['sundry_debtors'] ?? ''); ?>" placeholder="0"></div>
                            <div class="bs-row py-1"><span>Cash in hand</span> <input type="number" class="bs-input calc-trigger" name="cash_in_hand" value="<?php echo e($extractedData['cash_in_hand'] ?? ''); ?>" placeholder="0"></div>
                            <div class="bs-row py-1"><span>Bank Balance</span> <input type="number" class="bs-input calc-trigger" name="bank_balance" placeholder="0"></div>
                            <div class="bs-row py-1"><span>Closing Stock <small class="text-muted">(Synced)</small></span> <input type="number" class="bs-input" id="slave-closing-stock" readonly placeholder="0"></div>
                            <div class="bs-row py-1"><span>TDS</span> <input type="number" class="bs-input calc-trigger" name="tds" placeholder="0"></div>
                            <div class="bs-total-row mt-2"><span>Total</span><span id="disp-tot-assets">₹0</span></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <div class="bs-status-box">
        <div class="row align-items-center">
            <div class="col-md-4">
                <span class="d-block small text-uppercase text-muted font-weight-bold mb-1">P&L Status</span>
                <span class="tally-status tally-error" id="status-np"><i class="fas fa-times-circle mr-1"></i> Net Profit Mismatch</span>
            </div>
            <div class="col-md-4 text-center">
                <span class="d-block small text-uppercase text-muted font-weight-bold mb-1">Balance Sheet Difference</span>
                <h4 class="mb-0 font-weight-bold text-danger" id="disp-diff">₹0</h4>
            </div>
            <div class="col-md-4 text-right">
                
                <button type="button" class="btn btn-secondary btn-lg font-weight-bold px-4 shadow-sm" id="btn-continue" disabled>
                    Continue to Balance Sheet <i class="fas fa-arrow-right ml-2"></i>
                </button>

                <button type="submit" form="pdfGenerateForm" class="btn btn-dark btn-lg font-weight-bold px-4 shadow-sm d-none" id="btn-submit" disabled>
                    <i class="fas fa-file-pdf mr-2"></i> Save & Generate PDF
                </button>

            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
$(document).ready(function() {
    const val = (name) => parseFloat($(`input[name="${name}"]`).val()) || 0;

    // --- Tab Switching Logic ---
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href"); // Activated tab
        if (target === '#pnl') {
            $('#btn-continue').removeClass('d-none');
            $('#btn-submit').addClass('d-none');
        } else {
            $('#btn-continue').addClass('d-none');
            $('#btn-submit').removeClass('d-none');
        }
    });

    $('#btn-continue').click(function() {
        $('#bs-tab').tab('show'); // Switches to the second tab
    });

    function calculateAll() {
        // Sync Closing Stock
        const closingStock = val('closing_stock');
        $('#slave-closing-stock').val(closingStock > 0 ? closingStock : '');

        // P&L Math
        const sales = val('sales');
        const otherInc = val('other_income');
        const targetNP = parseFloat($('#val-target-np').val()) || 0;

        const gp = (sales + closingStock) - (val('opening_stock') + val('purchases') + val('direct_expenses'));
        const totalExp = val('salaries') + val('shop_rent') + val('electricity') + val('telephone_internet') + val('printing_stationery') + val('repairs_maintenance') + val('interest_on_loan') + val('other_expenses');
        const np = (gp + otherInc + val('interest_income')) - totalExp;

        $('#disp-live-np').text('₹' + np.toLocaleString('en-IN'));
        
        let isNpValid = false;
        if (np === targetNP && targetNP > 0) {
            isNpValid = true;
            $('#status-np').removeClass('tally-error').addClass('tally-match').html('<i class="fas fa-check-circle mr-1"></i> Net Profit Matched');
            $('#disp-live-np').removeClass('text-danger').addClass('text-success');
            
            // Unlock Tab 2 and Continue Button
            $('#bs-tab').removeClass('disabled-tab');
            $('#btn-continue').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        } else {
            $('#status-np').removeClass('tally-match').addClass('tally-error').html('<i class="fas fa-times-circle mr-1"></i> Net Profit Mismatch');
            $('#disp-live-np').removeClass('text-success').addClass('text-danger');
            
            // Lock Tab 2 and Continue Button
            $('#bs-tab').addClass('disabled-tab');
            $('#btn-continue').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
        }

        // Balance Sheet Math
        const closingCap = val('opening_capital') + np - val('drawings');
        $('#disp-closing-cap').text('₹' + closingCap.toLocaleString('en-IN'));

        const totalLiab = closingCap + val('bank_loan') + val('other_loans') + val('sundry_creditors') + val('other_current_liabilities');
        const totalAssets = closingStock + val('furniture') + val('vehicle') + val('computer') + val('other_fixed_assets') + val('total_investments') + val('sundry_debtors') + val('cash_in_hand') + val('bank_balance') + val('tds');

        $('#disp-tot-liab').text('₹' + totalLiab.toLocaleString('en-IN'));
        $('#disp-tot-assets').text('₹' + totalAssets.toLocaleString('en-IN'));

        const diff = Math.abs(totalLiab - totalAssets);
        $('#disp-diff').text('₹' + diff.toLocaleString('en-IN'));

        let isTallyValid = false;
        if (diff === 0 && totalAssets > 0) {
            isTallyValid = true;
            $('#disp-diff').removeClass('text-danger').addClass('text-success');
        } else {
            $('#disp-diff').removeClass('text-success').addClass('text-danger');
        }

        // Unlock Final Generate Button
        if (isNpValid && isTallyValid) {
            $('#btn-submit').prop('disabled', false).removeClass('btn-dark').addClass('btn-success');
        } else {
            $('#btn-submit').prop('disabled', true).removeClass('btn-success').addClass('btn-dark');
        }
    }

    $(document).on('input', '.calc-trigger', calculateAll);
    calculateAll();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/team/applications/balance_sheet.blade.php ENDPATH**/ ?>