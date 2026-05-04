<?php $__env->startSection('title', 'Edit Agent: ' . $agent->name); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Edit Agent</h1>
            <p class="text-muted mb-0 mt-1">Updating profile for <?php echo e($agent->agent_code); ?></p>
        </div>
        <a href="<?php echo e(route('admin.agents.index')); ?>" class="btn btn-outline-secondary font-weight-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Agents
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <div class="card modern-card border-0 shadow-sm">
                    <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-user-edit text-primary mr-2"></i> Agent Account Details
                        </h3>
                    </div>

                    <div class="card-body pt-0 px-4 pb-4">
                        <form method="POST" action="<?php echo e(route('admin.agents.update', $agent)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            
                            <div class="row">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-user text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="name"
                                            class="form-control custom-input border-left-0 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            value="<?php echo e(old('name', $agent->name)); ?>" required>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger small font-weight-bold mt-1 d-block"><i
                                                class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">Email Address <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-envelope text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="email" name="email"
                                            class="form-control custom-input border-left-0 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            value="<?php echo e(old('email', $agent->email)); ?>" required>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger small font-weight-bold mt-1 d-block"><i
                                                class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">Mobile Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-phone text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="mobile_number" class="form-control custom-input border-left-0" value="<?php echo e(old('mobile_number', $agent->mobile_number)); ?>" placeholder="e.g. 9876543210">
                                    </div>
                                </div>

                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">WhatsApp Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fab fa-whatsapp text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="whatsapp_no" class="form-control custom-input border-left-0" value="<?php echo e(old('whatsapp_no', $agent->whatsapp_no)); ?>" placeholder="e.g. 9876543210">
                                    </div>
                                </div>

                                <div class="col-md-12 form-group mb-4">
                                    <label class="form-label-custom">Full Address</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-map-marker-alt text-muted"></i>
                                            </span>
                                        </div>
                                        <textarea name="address" rows="2" class="form-control custom-input border-left-0" style="height: auto;" placeholder="Enter complete address"><?php echo e(old('address', $agent->address)); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            
                            
                            <div class="row mt-3">

                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">New Password</label>

                                    <div class="input-group">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-lock text-muted"></i>
                                            </span>
                                        </div>

                                        <input type="password" name="password"
                                            class="form-control custom-input border-left-0 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            placeholder="Leave blank to keep current password">

                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger small font-weight-bold mt-1 d-block">
                                            <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                                        </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                </div>


                                <div class="col-md-6 form-group mb-4">

                                    <label class="form-label-custom">Confirm Password</label>

                                    <div class="input-group">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-lock text-muted"></i>
                                            </span>
                                        </div>

                                        <input type="password" name="password_confirmation"
                                            class="form-control custom-input border-left-0"
                                            placeholder="Confirm new password">

                                    </div>

                                </div>

                            </div>

                            
                            <div class="d-flex justify-content-end mt-2 pt-3 border-top">
                                <a href="<?php echo e(route('admin.agents.index')); ?>"
                                    class="btn btn-light font-weight-bold mr-2 text-muted">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-success font-weight-bold shadow-sm px-4">
                                    <i class="fas fa-save mr-1"></i> Save Changes
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        /* Base Card */
        .modern-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }

        /* Labels */
        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        /* Input Addons (Icons) */
        .custom-icon-box {
            border-color: #cbd5e1;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
            transition: border-color 0.2s;
        }

        /* Input Fields */
        .custom-input {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
            height: 42px;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .custom-input:focus {
            border-color: #0044b2;
            /* Matches your primary color theme */
            box-shadow: 0 0 0 3px rgba(0, 68, 178, 0.15);
            outline: none;
        }

        /* Fix border color on focus when using input-group */
        .custom-input:focus+.input-group-prepend .custom-icon-box,
        .input-group-prepend+.custom-input:focus {
            border-color: #0044b2;
        }

        /* Invalid State Styling */
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important;
        }

        /* Buttons */
        .btn-success {
            background-color: #00b259;
            border-color: #00b259;
            transition: all 0.2s;
            border-radius: 8px;
        }

        .btn-success:hover {
            background-color: #00964b;
            border-color: #00964b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 178, 89, 0.2) !important;
        }

        .btn-light {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            border-radius: 8px;
        }

        .btn-light:hover {
            background-color: #f1f5f9;
            color: #1e293b !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/agents/edit.blade.php ENDPATH**/ ?>