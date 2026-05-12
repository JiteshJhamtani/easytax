<?php $__env->startSection('css'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Makes Select2 look good inside bootstrap */
        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title', 'Manage Promo Campaigns'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold text-dark mb-0">🎟️ Promo Codes & Campaigns</h3>
            <p class="text-muted mb-0">Create and manage bonus commissions for your agents.</p>
        </div>
        <button class="btn btn-primary font-weight-bold shadow-sm" data-toggle="modal" data-target="#createPromoModal" style="border-radius: 8px;">
            <i class="fas fa-plus mr-1"></i> Create New Coupon
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success shadow-sm" style="border-radius: 8px;"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger shadow-sm" style="border-radius: 8px;">
            <ul class="mb-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <li><?php echo e($error); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-uppercase text-muted" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                        <tr>
                            <th class="border-0 pl-4">Promo Code</th>
                            <th class="border-0">Bonus (₹)</th>
                            <th class="border-0">Target Agent</th>
                            <th class="border-0">Usage Limit</th> <th class="border-0">Status</th>
                            <th class="border-0 text-right pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr>
                            <td class="pl-4 font-weight-bold text-primary"><?php echo e($coupon->code); ?></td>
                            <td class="font-weight-bold text-success">+ ₹<?php echo e(number_format($coupon->bonus_amount, 2)); ?></td>
                            <td>
                                <?php
                                    $targets = $coupon->target_agents ? json_decode($coupon->target_agents, true) : [];
                                ?>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($targets) && count($targets) > 0): ?>
                                    <span class="badge badge-dark mb-1" style="border-radius: 6px;">
                                        <?php echo e(count($targets)); ?> Agent(s) Targeted
                                    </span>
                                    <div class="text-xs text-muted" style="max-width: 150px; word-wrap: break-word;">
                                        IDs: <?php echo e(implode(', ', $targets)); ?>

                                    </div>
                                <?php else: ?>
                                    <span class="badge badge-light border text-muted">All Agents</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <div class="small">
                                    <strong><?php echo e($coupon->total_used); ?></strong> used <br>
                                    <span class="text-muted">of <?php echo e($coupon->global_max_uses ?? 'Unlimited'); ?> total</span>
                                </div>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coupon->is_active): ?>
                                    <span style="background-color: #e6f4ea; color: #1e8e3e; padding: 5px 12px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Active
                                    </span>
                                <?php else: ?>
                                    <span style="background-color: #fce8e6; color: #d93025; padding: 5px 12px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Disabled
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-right pr-4">
                                <form action="<?php echo e(route('admin.coupons.toggle', $coupon->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm <?php echo e($coupon->is_active ? 'btn-outline-danger' : 'btn-outline-success'); ?>" title="Toggle Status">
                                        <i class="fas <?php echo e($coupon->is_active ? 'fa-ban' : 'fa-check'); ?>"></i>
                                    </button>
                                </form>
                                <form action="<?php echo e(route('admin.coupons.destroy', $coupon->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this promo permanently?');">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-ticket-alt fa-3x mb-3 text-light"></i>
                                <h5>No Promos Found</h5>
                                <p>Click the button above to create your first campaign.</p>
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createPromoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header bg-light" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold text-dark">Create New Promo Campaign</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('admin.coupons.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Promo Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="e.g. ITR50-XJ9" required style="border-radius: 8px;">
                        <small class="text-muted">Agents will type this exactly at checkout.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Bonus Commission (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="bonus_amount" class="form-control" placeholder="50.00" required style="border-radius: 8px;">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Target Specific Agents (Optional)</label>
                        <select name="target_agents[]" class="form-control select2-agents" multiple="multiple" style="width: 100%;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->name); ?> (ID: <?php echo e($agent->id); ?>)</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <small class="text-muted">Leave blank so ALL agents can use it.</small>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Total Campaign Uses</label>
                            <input type="number" name="global_max_uses" class="form-control" placeholder="e.g. 50" style="border-radius: 8px;">
                            <small class="text-muted">Auto-expires after X uses.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Max Uses Per Agent</label>
                            <input type="number" name="max_uses_per_agent" class="form-control" value="1" required style="border-radius: 8px;">
                            <small class="text-muted">Usually 1 time per agent.</small>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm" style="border-radius: 8px;">Create Coupon</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix the modal trap
        $('#createPromoModal').appendTo('body');
        
        // Initialize the multi-select dropdown
        $('.select2-agents').select2({
            placeholder: "Search and select agents...",
            allowClear: true,
            dropdownParent: $('#createPromoModal') // <--- CRITICAL FOR MODALS
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/coupons/index.blade.php ENDPATH**/ ?>