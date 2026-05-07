<?php $__env->startSection('title', 'Add Marketer | EasyTax'); ?>

<?php $__env->startSection('content'); ?>
<div class="chq-wrapper p-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?php echo e(route('crm.marketers.index')); ?>" class="text-muted font-weight-bold mr-3"><i class="fas fa-arrow-left"></i> Back</a>
        <h3 class="mb-0 font-weight-bold text-dark"><i class="fas fa-bullhorn text-danger mr-2"></i> Add New Marketer</h3>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger rounded-lg shadow-sm border-0" style="max-width: 600px;">
            <ul class="mb-0 pl-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?> 
                    <li><?php echo e($err); ?></li> 
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card border-0 shadow-sm rounded-lg" style="max-width: 600px;">
        <div class="card-body p-4">
            <form action="<?php echo e(route('crm.marketers.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group mb-3">
                    <label class="text-xs font-weight-bold text-muted text-uppercase">Full Name now what we want to do is when a markter  *</label>
                    <input type="text" name="name" class="form-control rounded-lg" value="<?php echo e(old('name')); ?>" required placeholder="e.g. John Doe">
                </div>
                <div class="form-group mb-3">
                    <label class="text-xs font-weight-bold text-muted text-uppercase">Email Address *</label>
                    <input type="email" name="email" class="form-control rounded-lg" value="<?php echo e(old('email')); ?>" required placeholder="marketer@easytax.com">
                </div>
                <div class="form-group mb-4">
                    <label class="text-xs font-weight-bold text-muted text-uppercase">Temporary Password *</label>
                    <input type="password" name="password" class="form-control rounded-lg" required minlength="6" placeholder="Enter at least 6 characters">
                </div>
                <button type="submit" class="btn btn-danger font-weight-bold shadow-sm px-4">Save Marketer</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/marketers/create.blade.php ENDPATH**/ ?>