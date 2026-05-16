<?php $__env->startSection('title', 'Add VLE Customer | EasyTax'); ?>

<?php $__env->startSection('content'); ?>
<div class="chq-wrapper p-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?php echo e(route('marketer.dashboard')); ?>" class="text-muted font-weight-bold mr-3"><i class="fas fa-arrow-left"></i> Back</a>
        <h3 class="mb-0 font-weight-bold" style="color: #10b981;"><i class="fas fa-handshake mr-2"></i> Add VLE Customer</h3>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger shadow-sm rounded-lg mb-4">
            <ul class="mb-0 pl-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <li><?php echo e($error); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card border-0 shadow-sm rounded-lg" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="<?php echo e(route('crm.leads.vle.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Full Name *</label>
                        <input type="text" name="name" class="form-control rounded-lg" required>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Phone Number *</label>
                        <input type="text" name="phone" class="form-control rounded-lg" required>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-lg">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Service Interest</label>
                        <select name="service_interested" class="form-control rounded-lg">
                            <option value="">Unknown / General</option>
                            <option value="ITR Filing">ITR Filing</option>
                            <option value="GST Registration">GST Registration</option>
                            <option value="GST Return">GST Return</option>
                            <option value="Company Formation">Company Formation</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Total Leads Provided (Min 10) *</label>
                        <select name="amount" class="form-control rounded-lg" required>
                            <option value="" disabled selected>Select number of leads...</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 10; $i <= 20; $i++): ?>
                                <option value="<?php echo e($i); ?>"><?php echo e($i); ?> Leads</option>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <option value="100+">More than 100 Leads</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 form-group mb-4">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">VLE Notes</label>
                        <textarea name="notes" class="form-control rounded-lg" rows="3" placeholder="Enter specific details regarding this VLE customer..."></textarea>
                    </div>
                </div>
                
                <hr class="border-light mt-0 mb-4">
                <button type="submit" class="btn text-white font-weight-bold shadow-sm px-4" style="background: #10b981;">Save VLE Customer</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make(auth()->user()->role === 'ADMIN' ? 'layouts.admin' : 'layouts.marketer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/marketer/vle/create.blade.php ENDPATH**/ ?>