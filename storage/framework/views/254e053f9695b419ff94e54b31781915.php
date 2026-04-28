<?php $__env->startSection('title', 'Edit Service: ' . $service->name); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Edit Service</h1>
            <p class="text-muted mb-0 mt-1">Editing <code><?php echo e($service->slug); ?></code></p>
        </div>
        <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-outline-secondary font-weight-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Services
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-0">
        <form method="POST" action="<?php echo e(route('admin.services.update', $service)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            
            <div class="card modern-card border-0 shadow-sm mb-4">
                <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-concierge-bell text-primary mr-2"></i> Service Details
                    </h3>
                </div>

                <div class="card-body pt-0 px-4 pb-4">
                    <div class="row">
                        <div class="col-md-6 form-group mb-4">
                            <label class="form-label-custom">Service Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0 custom-icon-box"><i class="fas fa-tag text-muted"></i></span>
                                </div>
                                <input type="text" name="name" class="form-control custom-input border-left-0 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name', $service->name)); ?>" required>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="col-md-6 form-group mb-4">
                            <label class="form-label-custom">Slug <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0 custom-icon-box"><i class="fas fa-link text-muted"></i></span>
                                </div>
                                <input type="text" name="slug" class="form-control custom-input border-left-0 <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('slug', $service->slug)); ?>" required style="font-family: monospace;">
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="form-label-custom">Primary Data Field (Optional)</label>
                            <input type="text" name="primary_data_field" class="form-control custom-input" value="<?php echo e(old('primary_data_field', $service->primary_data_field ?? '')); ?>" placeholder="e.g., gst_number">
                            <small class="text-muted">Type the exact input name to display it on the Applications List.</small>
                        </div>

                        <div class="col-md-4 form-group mt-3">
                            <label class="form-label-custom"><i class="fab fa-whatsapp text-success"></i> WhatsApp Number Field</label>
                            <input type="text" name="whatsapp_number_field" class="form-control custom-input" value="<?php echo e(old('whatsapp_number_field', $service->whatsapp_number_field ?? '')); ?>">
                        </div>

                        <div class="col-md-4 form-group mt-3">
                            <label class="form-label-custom"><i class="fas fa-envelope text-primary"></i> Email Field</label>
                            <input type="text" name="applicant_email_field" class="form-control custom-input" value="<?php echo e(old('applicant_email_field', $service->applicant_email_field ?? '')); ?>">
                        </div>

                        <div class="col-md-4 form-group mt-3">
                            <label class="form-label-custom"><i class="fas fa-sort-numeric-down text-info"></i> Sort Order</label>
                            <input type="number" name="sort_order" class="form-control custom-input" value="<?php echo e(old('sort_order', $service->sort_order ?? 0)); ?>">
                        </div>

                        <div class="col-md-12 form-group mb-4 mt-3">
                            <label class="form-label-custom">Description</label>
                            <textarea name="description" rows="3" class="form-control custom-input <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="height: auto;"><?php echo e(old('description', $service->description)); ?></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-4 border-light">

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($service->slug, ['gst-return-filing', 'itr-filing'])): ?>
                        <input type="hidden" name="price" value="<?php echo e($service->price ?? 0); ?>">
                        <input type="hidden" name="commission_type" value="<?php echo e($service->commission_type ?? 'flat'); ?>">
                        <input type="hidden" name="commission_value" value="<?php echo e($service->commission_value ?? 0); ?>">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($service->slug === 'gst-return-filing'): ?>
                        
                        <div class="card border border-primary rounded-lg mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white font-weight-bold py-3">
                                <i class="fas fa-table mr-2"></i> GST Pricing Matrix
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-bordered mb-0" id="pricing-matrix-table-gst">
                                    <thead class="bg-light text-xs text-muted text-uppercase">
                                        <tr>
                                            <th>GST Type</th><th>Turnover Range</th><th>Frequency</th><th>Plan</th>
                                            <th>Price (₹)</th><th>VLE Comm (₹)</th><th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($service->pricingRules) && $service->pricingRules->count() > 0): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $service->pricingRules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <tr>
                                                    <td><input type="text" name="pricing_rules[<?php echo e($index); ?>][gst_type]" class="form-control" value="<?php echo e($rule->gst_type); ?>"></td>
                                                    <td><input type="text" name="pricing_rules[<?php echo e($index); ?>][turnover]" class="form-control" value="<?php echo e($rule->turnover); ?>"></td>
                                                    <td><input type="text" name="pricing_rules[<?php echo e($index); ?>][frequency]" class="form-control" value="<?php echo e($rule->frequency); ?>"></td>
                                                    <td><input type="text" name="pricing_rules[<?php echo e($index); ?>][plan]" class="form-control" value="<?php echo e($rule->plan); ?>"></td>
                                                    <td><input type="number" step="0.01" name="pricing_rules[<?php echo e($index); ?>][base_price]" class="form-control" value="<?php echo e($rule->base_price); ?>" required></td>
                                                    <td><input type="number" step="0.01" name="pricing_rules[<?php echo e($index); ?>][commission_amount]" class="form-control" value="<?php echo e($rule->commission_amount); ?>"></td>
                                                    <td class="text-center align-middle"><button type="button" class="btn btn-danger btn-sm rounded" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                                <div class="p-3 bg-light border-top">
                                    <button type="button" class="btn btn-primary btn-sm font-weight-bold shadow-sm" onclick="addGstRow()"><i class="fas fa-plus mr-1"></i> Add Rule</button>
                                </div>
                            </div>
                        </div>
                    <?php elseif($service->slug === 'itr-filing'): ?>
                        
                        <div class="card border border-success rounded-lg mb-4 shadow-sm">
                            <div class="card-header bg-success text-white font-weight-bold py-3">
                                <i class="fas fa-file-invoice-dollar mr-2"></i> ITR Dynamic Pricing Rules
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-bordered mb-0" id="pricing-matrix-table-itr">
                                    <thead class="bg-light text-xs text-muted text-uppercase text-center">
                                        <tr>
                                            <th>FY Type</th><th>User Type</th><th>Salary?</th><th>Business?</th><th>Turnover</th><th>Cap Gains?</th>
                                            <th>Price (₹)</th><th>VLE Comm (₹)</th><th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($service->pricingRules) && $service->pricingRules->count() > 0): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $service->pricingRules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <tr>
                                                  <td>
                                                        <select name="pricing_rules[<?php echo e($index); ?>][itr_type]" class="form-control custom-input">
                                                            <option value="" <?php echo e(empty($rule->itr_type) ? 'selected' : ''); ?>>Any</option>
                                                            <option value="2026-27" <?php echo e($rule->itr_type === '2026-27' ? 'selected' : ''); ?>>F.Y. 2026-27 (Current)</option>
                                                            <option value="2025-26" <?php echo e($rule->itr_type === '2025-26' ? 'selected' : ''); ?>>F.Y. 2025-26 (ITR-U)</option>
                                                            <option value="2024-25" <?php echo e($rule->itr_type === '2024-25' ? 'selected' : ''); ?>>F.Y. 2024-25 (ITR-U)</option>
                                                            <option value="2023-24" <?php echo e($rule->itr_type === '2023-24' ? 'selected' : ''); ?>>F.Y. 2023-24 (ITR-U)</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="pricing_rules[<?php echo e($index); ?>][user_type]" class="form-control custom-input">
                                                            <option value="" <?php echo e(empty($rule->user_type) ? 'selected' : ''); ?>>Any</option>
                                                            <option value="vle" <?php echo e($rule->user_type === 'vle' ? 'selected' : ''); ?>>VLE</option>
                                                            <option value="user" <?php echo e($rule->user_type === 'user' ? 'selected' : ''); ?>>User/Citizen</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="pricing_rules[<?php echo e($index); ?>][itr_salary]" class="form-control custom-input">
                                                            <option value="" <?php echo e(empty($rule->itr_salary) ? 'selected' : ''); ?>>Any</option>
                                                            <option value="yes" <?php echo e($rule->itr_salary === 'yes' ? 'selected' : ''); ?>>Yes</option>
                                                            <option value="no" <?php echo e($rule->itr_salary === 'no' ? 'selected' : ''); ?>>No</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="pricing_rules[<?php echo e($index); ?>][itr_business]" class="form-control custom-input">
                                                            <option value="" <?php echo e(empty($rule->itr_business) ? 'selected' : ''); ?>>Any</option>
                                                            <option value="yes" <?php echo e($rule->itr_business === 'yes' ? 'selected' : ''); ?>>Yes</option>
                                                            <option value="no" <?php echo e($rule->itr_business === 'no' ? 'selected' : ''); ?>>No</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="pricing_rules[<?php echo e($index); ?>][turnover]" class="form-control custom-input">
                                                            <option value="" <?php echo e(empty($rule->turnover) ? 'selected' : ''); ?>>Any</option>
                                                            <option value="less_than_20l" <?php echo e($rule->turnover === 'less_than_20l' ? 'selected' : ''); ?>>< 20 Lakh</option>
                                                            <option value="more_than_20l" <?php echo e($rule->turnover === 'more_than_20l' ? 'selected' : ''); ?>>> 20 Lakh</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="pricing_rules[<?php echo e($index); ?>][itr_capital_gains]" class="form-control custom-input">
                                                            <option value="" <?php echo e(empty($rule->itr_capital_gains) ? 'selected' : ''); ?>>Any</option>
                                                            <option value="yes" <?php echo e($rule->itr_capital_gains === 'yes' ? 'selected' : ''); ?>>Yes</option>
                                                            <option value="no" <?php echo e($rule->itr_capital_gains === 'no' ? 'selected' : ''); ?>>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" step="0.01" name="pricing_rules[<?php echo e($index); ?>][base_price]" class="form-control custom-input" value="<?php echo e($rule->base_price); ?>" required></td>
                                                    <td><input type="number" step="0.01" name="pricing_rules[<?php echo e($index); ?>][commission_amount]" class="form-control custom-input" value="<?php echo e($rule->commission_amount); ?>"></td>
                                                    <td class="text-center align-middle"><button type="button" class="btn btn-danger btn-sm rounded" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                                <div class="p-3 bg-light border-top">
                                    <button type="button" class="btn btn-success btn-sm font-weight-bold shadow-sm" onclick="addItrRow()"><i class="fas fa-plus mr-1"></i> Add ITR Rule</button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        
                        <div class="row">
                            <div class="col-md-4 form-group mb-4">
                                <label class="form-label-custom">Price (₹) *</label>
                                <input type="number" name="price" step="0.01" class="form-control custom-input" value="<?php echo e(old('price', $service->price)); ?>" required>
                            </div>
                            <div class="col-md-4 form-group mb-4">
                                <label class="form-label-custom">Commission Type *</label>
                                <select name="commission_type" class="form-control custom-input">
                                    <option value="flat" <?php echo e($service->commission_type === 'flat' ? 'selected' : ''); ?>>Flat (₹)</option>
                                    <option value="percentage" <?php echo e($service->commission_type === 'percentage' ? 'selected' : ''); ?>>Percentage (%)</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-4">
                                <label class="form-label-custom">Commission Value *</label>
                                <input type="number" name="commission_value" step="0.01" class="form-control custom-input" value="<?php echo e(old('commission_value', $service->commission_value)); ?>" required>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php echo $__env->make('admin.services.partials.form-builder', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="d-flex justify-content-end mt-4 mb-4">
                <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-light font-weight-bold mr-2 text-muted">Cancel</a>
                <button type="submit" class="btn btn-success font-weight-bold shadow-sm px-4">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .modern-card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important; }
        .form-label-custom { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem; letter-spacing: 0.5px; }
        .custom-input { border-radius: 8px; border: 1px solid #cbd5e1; height: 42px; }
        .custom-input:focus { border-color: #0044b2; box-shadow: 0 0 0 3px rgba(0, 68, 178, 0.15); }
        .is-invalid { border-color: #dc3545 !important; box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important; }
        .btn-success { background-color: #00b259; border-color: #00b259; border-radius: 8px; }
        .btn-light { background-color: #f8fafc; border-color: #e2e8f0; border-radius: 8px; }
    </style>
    <?php echo $__env->yieldPushContent('css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <?php echo $__env->yieldPushContent('js'); ?>
    <script>
        var rowIndex = <?php echo e(isset($service->pricingRules) ? $service->pricingRules->count() : 0); ?>;

        function addGstRow() {
            var tableBody = document.querySelector('#pricing-matrix-table-gst tbody');
            if(!tableBody) return;
            var tr = document.createElement('tr');
            var html = '<td><input type="text" name="pricing_rules['+rowIndex+'][gst_type]" class="form-control" placeholder="e.g. regular"></td>' +
                       '<td><input type="text" name="pricing_rules['+rowIndex+'][turnover]" class="form-control" placeholder="e.g. upto_1_5"></td>' +
                       '<td><input type="text" name="pricing_rules['+rowIndex+'][frequency]" class="form-control" placeholder="e.g. monthly"></td>' +
                       '<td><input type="text" name="pricing_rules['+rowIndex+'][plan]" class="form-control" placeholder="e.g. yearly"></td>' +
                       '<td><input type="number" step="0.01" name="pricing_rules['+rowIndex+'][base_price]" class="form-control" placeholder="0.00" required></td>' +
                       '<td><input type="number" step="0.01" name="pricing_rules['+rowIndex+'][commission_amount]" class="form-control" placeholder="0.00"></td>' +
                       '<td class="text-center align-middle"><button type="button" class="btn btn-danger btn-sm rounded" onclick="this.closest(\'tr\').remove()"><i class="fas fa-trash"></i></button></td>';
            tr.innerHTML = html;
            tableBody.appendChild(tr);
            rowIndex++;
        }

      function addItrRow() {
            var tableBody = document.querySelector('#pricing-matrix-table-itr tbody');
            if(!tableBody) return;
            var tr = document.createElement('tr');
            
            var html = '<td><select name="pricing_rules['+rowIndex+'][itr_type]" class="form-control custom-input"><option value="">Any</option><option value="2026-27">F.Y. 2026-27 (Current)</option><option value="2025-26">F.Y. 2025-26 (ITR-U)</option><option value="2024-25">F.Y. 2024-25 (ITR-U)</option><option value="2023-24">F.Y. 2023-24 (ITR-U)</option></select></td>' +
                       '<td><select name="pricing_rules['+rowIndex+'][user_type]" class="form-control custom-input"><option value="">Any</option><option value="vle">VLE</option><option value="user">User/Citizen</option></select></td>' +
                       '<td><select name="pricing_rules['+rowIndex+'][itr_salary]" class="form-control custom-input"><option value="">Any</option><option value="yes">Yes</option><option value="no">No</option></select></td>' +
                       '<td><select name="pricing_rules['+rowIndex+'][itr_business]" class="form-control custom-input"><option value="">Any</option><option value="yes">Yes</option><option value="no">No</option></select></td>' +
                       '<td><select name="pricing_rules['+rowIndex+'][turnover]" class="form-control custom-input"><option value="">Any</option><option value="less_than_20l">< 20 Lakh</option><option value="more_than_20l">> 20 Lakh</option></select></td>' +
                       '<td><select name="pricing_rules['+rowIndex+'][itr_capital_gains]" class="form-control custom-input"><option value="">Any</option><option value="yes">Yes</option><option value="no">No</option></select></td>' +
                       '<td><input type="number" step="0.01" name="pricing_rules['+rowIndex+'][base_price]" class="form-control custom-input" placeholder="0.00" required></td>' +
                       '<td><input type="number" step="0.01" name="pricing_rules['+rowIndex+'][commission_amount]" class="form-control custom-input" placeholder="0.00"></td>' +
                       '<td class="text-center align-middle"><button type="button" class="btn btn-danger btn-sm rounded" onclick="this.closest(\'tr\').remove()"><i class="fas fa-trash"></i></button></td>';
            
            tr.innerHTML = html;
            tableBody.appendChild(tr);
            rowIndex++;
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/services/edit.blade.php ENDPATH**/ ?>