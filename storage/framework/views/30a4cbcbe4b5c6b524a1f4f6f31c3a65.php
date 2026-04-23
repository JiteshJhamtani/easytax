<?php $__env->startSection('title', 'Create Agent'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <h1 class="m-0 text-dark font-weight-bold">Create New Agent</h1>
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
                            <i class="fas fa-user-shield text-primary mr-2"></i> Agent Account Details
                        </h3>
                    </div>

                    <div class="card-body pt-0 px-4 pb-4">
                        <form method="POST" action="<?php echo e(route('admin.agents.store')); ?>">
                            <?php echo csrf_field(); ?>

                            
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
                                            value="<?php echo e(old('name')); ?>" placeholder="e.g. John Doe" required>
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
                                            value="<?php echo e(old('email')); ?>" placeholder="agent@example.com" required>
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

                            <hr class="my-4 border-light">

                            
                            <div class="row">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">Password <span class="text-danger">*</span></label>
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
                                            placeholder="Create a strong password" required>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
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
                                    <label class="form-label-custom">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-lock text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="password" name="password_confirmation"
                                            class="form-control custom-input border-left-0" placeholder="Repeat password"
                                            required>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="d-flex justify-content-end mt-2 pt-3 border-top">
                                <a href="<?php echo e(route('admin.agents.index')); ?>"
                                    class="btn btn-light font-weight-bold mr-2 text-muted">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4">
                                    <i class="fas fa-user-plus mr-1"></i> Create Agent
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
        .btn-primary {
            background-color: #0044b2;
            border-color: #0044b2;
            transition: all 0.2s;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #00368c;
            border-color: #00368c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 68, 178, 0.2) !important;
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/agents/create.blade.php ENDPATH**/ ?>