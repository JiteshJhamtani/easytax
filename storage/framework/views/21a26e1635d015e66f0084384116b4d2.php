<?php $__env->startSection('title', 'Edit Marketer | EasyTax'); ?>

<?php $__env->startSection('css'); ?>
<style>
    .form-label { font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; color: #475569; text-transform: uppercase; margin-bottom: 0.5rem; }
    .required-asterisk { color: #ef4444; margin-left: 2px; }
    .custom-input-group { display: flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background: #fff; transition: all 0.2s; }
    .custom-input-group:focus-within { border-color: #1E9C5D; box-shadow: 0 0 0 3px rgba(30,156,93,0.1); }
    .custom-input-icon { width: 45px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-right: 1px solid #cbd5e1; color: #64748b; font-size: 0.9rem; flex-shrink: 0; align-self: stretch; }
    .custom-input { border: none; padding: 0.75rem 1rem; width: 100%; color: #334155; font-weight: 500; font-size: 0.9rem; }
    .custom-input:focus { outline: none; }
    .form-card { border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .form-header { padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem; }
    .form-body { padding: 2rem; }
    .form-footer { padding: 1.25rem 2rem; background: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 12px 12px; display: flex; justify-content: flex-end; gap: 1rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="<?php echo e(route('crm.marketers.index')); ?>" class="text-muted text-decoration-none font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Back to Marketers
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger shadow-sm" style="border-radius: 8px;">
            <ul class="mb-0 pl-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?> <li><?php echo e($err); ?></li> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="<?php echo e(route('crm.marketers.update', $marketer->id)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="form-card">
                    <div class="form-header">
                        <i class="fas fa-user-edit text-primary" style="font-size: 1.25rem;"></i>
                        <h5 class="mb-0 font-weight-bold text-dark">Edit Marketer: <?php echo e($marketer->name); ?></h5>
                    </div>

                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Full Name <span class="required-asterisk">*</span></label>
                                <div class="custom-input-group">
                                    <div class="custom-input-icon"><i class="fas fa-user"></i></div>
                                    <input type="text" name="name" class="custom-input" value="<?php echo e(old('name', $marketer->name)); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Email Address <span class="required-asterisk">*</span></label>
                                <div class="custom-input-group">
                                    <div class="custom-input-icon"><i class="fas fa-envelope"></i></div>
                                    <input type="email" name="email" class="custom-input" value="<?php echo e(old('email', $marketer->email)); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Change Password</label>
                                <div class="custom-input-group">
                                    <div class="custom-input-icon"><i class="fas fa-key"></i></div>
                                    <input type="password" name="password" class="custom-input" placeholder="Leave blank to keep existing">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="<?php echo e(route('crm.marketers.index')); ?>" class="btn btn-light" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600;">Cancel</a>
                        <button type="submit" class="btn btn-success shadow-sm" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 700;">
                            <i class="fas fa-save mr-1"></i> Update Marketer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/marketers/edit.blade.php ENDPATH**/ ?>