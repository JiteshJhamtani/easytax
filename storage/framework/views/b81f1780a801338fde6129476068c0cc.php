<?php $__env->startSection('title', 'Record Payout - ' . $operator->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="mb-4">
                <a href="<?php echo e(route('admin.team.show', $operator->id)); ?>" class="text-muted text-decoration-none"><i class="fas fa-arrow-left"></i> Back to Profile</a>
                <h3 class="font-weight-bold text-dark mt-2 mb-0">Record Payout</h3>
                <p class="text-muted">Issue a payment to <strong><?php echo e($operator->name); ?></strong></p>
            </div>

            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-body p-4">
                    
                    <div class="alert alert-info mb-4" style="border-radius: 8px;">
                        <h6 class="font-weight-bold text-info mb-1">Current Balance Due</h6>
                        <h3 class="mb-0 text-dark">₹<?php echo e(number_format($balanceDue, 2)); ?></h3>
                    </div>

                    <form action="<?php echo e(route('admin.team.add-payout', $operator->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Auto-Calculate from Months <span class="text-muted font-weight-normal">(Optional)</span></label>
                            
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-left d-flex justify-content-between align-items-center bg-light" type="button" id="monthDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-color: #cbd5e1; height: 45px;">
                                    <span id="dropdown-text"><i class="fas fa-calendar-alt mr-2 text-muted"></i> Select Months to Pay...</span>
                                </button>
                                
                                <div class="dropdown-menu w-100 p-3 shadow-sm border-0" aria-labelledby="monthDropdown" onclick="event.stopPropagation()" style="max-height: 300px; overflow-y: auto;"> 
                                    
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" id="check-all" value="ALL">
                                        <label class="custom-control-label font-weight-bold text-primary" for="check-all">Pay Total Balance Due (₹<?php echo e(number_format($balanceDue, 2)); ?>)</label>
                                    </div>
                                    
                                    <div class="dropdown-divider my-2"></div>
                                    
                                    <div class="text-muted small font-weight-bold text-uppercase mb-2">Individual Months</div>
                                   <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $monthlyEarnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input month-selector" id="month-<?php echo e($index); ?>" value="<?php echo e($month->month_name); ?>" data-amount="<?php echo e($month->unpaid_balance); ?>">
                                            <label class="custom-control-label d-flex justify-content-between w-100 pr-3" for="month-<?php echo e($index); ?>" style="cursor: pointer;">
                                                <span><?php echo e($month->month_name); ?></span>
                                                <span class="text-success font-weight-bold">₹<?php echo e(number_format($month->unpaid_balance, 2)); ?></span>
                                            </label>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <span class="text-muted small font-weight-bold text-success"><i class="fas fa-check-double mr-1"></i> All previous months have been fully paid!</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Payment Amount <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <div class="input-group-prepend"><span class="input-group-text bg-light border-right-0 text-muted">₹</span></div>
                                <input type="number" step="0.01" name="amount" id="payment-amount" class="form-control border-left-0 pl-0 font-weight-bold" required autofocus>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Payment Note / Reference</label>
                            <input type="text" name="payment_note" id="payment-note" class="form-control" placeholder="e.g. UPI Transaction ID #123456789">
                        </div>

                        <hr class="mt-5 mb-4">
                        
                        <div class="d-flex justify-content-end">
                            <a href="<?php echo e(route('admin.team.show', $operator->id)); ?>" class="btn btn-light font-weight-bold mr-2 px-4">Cancel</a>
                            <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm">Save Payment</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    // 
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('payment-amount');
        const noteInput = document.getElementById('payment-note');
        const monthCheckboxes = document.querySelectorAll('.month-selector');
        const checkAll = document.getElementById('check-all');
        const dropdownText = document.getElementById('dropdown-text');
        
        // Ensure balance due is treated correctly in JavaScript
        const balanceDue = parseFloat('<?php echo e($balanceDue > 0 ? $balanceDue : 0); ?>');

        function updateForm() {
            let total = 0;
            let selectedMonths = [];
            
            // Loop through checked months and sum them up
            monthCheckboxes.forEach(cb => {
                if (cb.checked) {
                    total += parseFloat(cb.dataset.amount);
                    selectedMonths.push(cb.value);
                }
            });

            if (checkAll.checked) {
                // If they checked "Pay Total Balance"
                amountInput.value = balanceDue.toFixed(2);
                noteInput.value = "Full Balance Payment";
                dropdownText.innerHTML = '<i class="fas fa-check-circle mr-2 text-success"></i> Paying Total Balance';
            } else if (selectedMonths.length > 0) {
                // If they checked specific months
                amountInput.value = total.toFixed(2);
                noteInput.value = "Payment for: " + selectedMonths.join(', ');
                dropdownText.innerHTML = '<i class="fas fa-calendar-check mr-2 text-primary"></i> ' + selectedMonths.length + ' Month(s) Selected';
            } else {
                // If nothing is checked, clear it
                amountInput.value = '';
                noteInput.value = '';
                dropdownText.innerHTML = '<i class="fas fa-calendar-alt mr-2 text-muted"></i> Select Months to Pay...';
            }
        }

        // Add Event Listeners to specific month checkboxes
        monthCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                checkAll.checked = false; // Uncheck 'All' if a specific month is clicked 
                updateForm();
            });
        });

        // Add Event Listener to the 'Pay All' checkbox
        checkAll.addEventListener('change', function() {
            if (this.checked) {
                monthCheckboxes.forEach(cb => cb.checked = false); // Clear individual months
            }
            updateForm();
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/team/create-payout.blade.php ENDPATH**/ ?>