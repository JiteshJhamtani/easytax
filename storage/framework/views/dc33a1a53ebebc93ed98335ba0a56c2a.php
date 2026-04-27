<?php $__env->startSection('title', 'Application #' . $application->id . ' | EasyTax'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2 pt-2">
        <div>
            <a href="<?php echo e(route('admin.applications.index')); ?>"
                class="text-muted text-sm font-weight-bold mb-2 d-inline-block transition-hover">
                <i class="fas fa-arrow-left mr-1"></i> Back to Applications
            </a>

            <?php
                $status = strtolower($application->status->value ?? 'unknown');

                $statusClass = match ($status) {
                    'completed' => 'badge-success-soft',
                    'in_progress' => 'badge-info-soft',
                    'pending' => 'badge-warning-soft',
                    'rejected' => 'badge-danger-soft',
                    'cancelled' => 'badge-secondary-soft',
                    default => 'badge-primary-soft',
                };
            ?>

            <div class="d-flex align-items-center mt-1">
                <h1 class="h3 font-weight-bold mb-0 text-dark">
                    Application #<?php echo e($application->id); ?>

                </h1>
                <span class="badge <?php echo e($statusClass); ?> ml-3 px-3 py-2 text-uppercase"
                    style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <?php echo e($application->status->value ?? 'UNKNOWN'); ?>

                </span>
            </div>

            <p class="text-muted mt-2 mb-0 text-sm">
                <i class="far fa-calendar-alt mr-1"></i>
                Submitted on <span
                    class="font-weight-bold"><?php echo e($application->submitted_at?->format('d M Y, h:i A') ?? 'N/A'); ?></span>
            </p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">

        
        <div class="col-lg-8">

            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 summary-card rounded-lg elegant-border">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="icon-box bg-success-soft text-success mr-3">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase text-xs font-weight-bold mb-1">Total Amount</h6>
                                <h4 class="mb-0 font-weight-bold text-dark">
                                    ₹<?php echo e(number_format($application->amount ?? 0, 2)); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 summary-card rounded-lg elegant-border">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="icon-box bg-primary-soft text-primary mr-3">
                                <i class="fas fa-user-tie fa-lg"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="text-muted text-uppercase text-xs font-weight-bold mb-1">Assigned Agent</h6>
                                <h5 class="mb-0 font-weight-bold text-dark text-truncate">
                                    <?php echo e($application->agent->name ?? 'Unassigned'); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <?php
                        $paymentStatus = strtolower($application->payment_status->value ?? 'pending');
                        $payClass = match ($paymentStatus) {
                            'paid' => 'bg-success-soft text-success',
                            'refunded' => 'bg-danger-soft text-danger',
                            default => 'bg-warning-soft text-warning',
                        };
                    ?>
                    <div class="card border-0 shadow-sm h-100 summary-card rounded-lg elegant-border">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="icon-box <?php echo e($payClass); ?> mr-3">
                                <i
                                    class="fas <?php echo e($paymentStatus === 'paid' ? 'fa-check-double' : ($paymentStatus === 'refunded' ? 'fa-undo-alt' : 'fa-hourglass-half')); ?> fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase text-xs font-weight-bold mb-1">Payment Status</h6>
                                <h5 class="mb-0 font-weight-bold text-dark text-capitalize"><?php echo e($paymentStatus); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        Application Overview
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 detail-table">
                        <tbody>
                            <tr>
                                <td
                                    class="text-muted text-uppercase text-xs font-weight-bold w-30 align-middle pl-4 border-top-0">
                                    Service Requested</td>
                                <td class="font-weight-bold text-dark border-top-0">
                                    <?php echo e($application->service->name ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted text-uppercase text-xs font-weight-bold w-30 align-middle pl-4">
                                    Application ID</td>
                                <td><span class="text-muted font-weight-bold">#<?php echo e($application->id); ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            
            
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-clipboard-list text-primary mr-2"></i>
                        Client Information
                    </h3>
                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($application->form_data)): ?>
                        <a href="<?php echo e(route('admin.applications.exportSingle', $application->id)); ?>" class="btn btn-sm btn-outline-success font-weight-bold shadow-sm transition-hover">
                            <i class="fas fa-file-excel mr-1"></i> Export to Excel
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

               <?php 
              $formData = array_filter($application->form_data ?? [], fn($key) => !in_array($key, ['admin_username', 'admin_password']), ARRAY_FILTER_USE_KEY); 
            ?>

                <div class="card-body p-4 bg-light rounded-bottom">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($formData)): ?>
                        <div class="row">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $formData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="col-md-6 mb-3">
                                    <div class="bg-white p-3 rounded-lg border shadow-sm h-100 data-box transition-hover">
                                        <span class="d-block text-muted text-uppercase text-xs font-weight-bold mb-1">
                                            <?php echo e(Str::title(str_replace('_', ' ', $field))); ?>

                                        </span>
                                        <span class="text-dark font-weight-normal" style="word-break: break-word;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($value)): ?>
                                                <?php echo e(implode(', ', $value)); ?>

                                            <?php elseif(is_bool($value)): ?>
                                                <span
                                                    class="badge <?php echo e($value ? 'badge-success-soft' : 'badge-secondary-soft'); ?> px-2 py-1">
                                                    <?php echo e($value ? 'Yes' : 'No'); ?>

                                                </span>
                                            <?php elseif(empty($value)): ?>
                                                <span class="text-muted font-italic">Not provided</span>
                                            <?php else: ?>
                                                <?php echo e($value); ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border"
                                style="width: 70px; height: 70px;">
                                <i class="fas fa-inbox fa-2x text-secondary opacity-50"></i>
                            </div>
                            <h6 class="font-weight-bold">No Client Data</h6>
                            <p class="text-sm mb-0">No form data was captured for this application.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

           
           <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->service->slug !== 'itr-filing'): ?>
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-key text-primary mr-2"></i> Deliverables & Credentials
                    </h3>
                </div>
                <div class="card-body p-4 bg-primary-soft rounded-bottom">
                    <form action="<?php echo e(route('admin.applications.storeCredentials', $application->id)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark text-sm">GST Username</label>
                                <input type="text" name="admin_username" class="form-control rounded-lg" value="<?php echo e($application->form_data['admin_username'] ?? ''); ?>" placeholder="Enter Username for Agent">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark text-sm">GST Password</label>
                                <input type="text" name="admin_password" class="form-control rounded-lg" value="<?php echo e($application->form_data['admin_password'] ?? ''); ?>" placeholder="Enter Password for Agent">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="font-weight-bold text-dark text-sm">Upload GST Certificate (PDF/Image)</label>
                                <input type="file" name="final_document" class="form-control-file" accept=".pdf,.png,.jpg,.jpeg">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary font-weight-bold shadow-sm">Save & Share with Agent</button>
                    </form>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->getMedia('final_deliverables')->count()): ?>
                        <hr class="my-4 border-light">
                        <h6 class="font-weight-bold text-dark mb-3">Uploaded GST Certificates</h6>
                        <div class="document-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $application->getMedia('final_deliverables'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="document-item d-flex align-items-center p-3 mb-2 bg-white rounded-lg border shadow-sm">
                                    <div class="document-icon bg-light rounded d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                    </div>
                                    <div class="document-info flex-grow-1">
                                        <div class="text-dark font-weight-bold text-sm"><?php echo e($doc->name); ?></div>
                                        <div class="text-muted text-xs font-weight-bold"><?php echo e(number_format($doc->size / 1024, 1)); ?> KB</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo e(route('admin.documents.view', $doc->id)); ?>" target="_blank" class="btn btn-sm btn-light border text-primary"><i class="fas fa-eye"></i></a>
                                        <form action="<?php echo e(route('admin.applications.deleteDocument', $doc->id)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>

        
        <div class="col-lg-4">

            
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom text-center">
                    <h3 class="card-title font-weight-bold text-dark w-100 float-none mb-0">
                        <i class="fas fa-cogs text-primary mr-2"></i>
                        Admin Actions
                    </h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?php echo e(route('admin.applications.updateStatus', $application->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>

                        
                        <button type="submit" name="status" value="IN_PROGRESS"
                            class="btn btn-warning btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                            <i class="fas fa-spinner mr-2"></i> Mark In Progress
                        </button>

                        <button type="submit" name="status" value="COMPLETED"
                            class="btn btn-success btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                            <i class="fas fa-check-circle mr-2"></i> Mark Completed
                        </button>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->service->slug === 'itr-filing'): ?>
                            <button type="submit" name="status" value="E_FILING"
                                class="btn btn-info btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover text-white">
                                <i class="fas fa-laptop-code mr-2"></i> Mark E-Filing
                            </button>

                            <button type="submit" name="status" value="OTP_VERIFICATION"
                                class="btn btn-primary btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                                <i class="fas fa-mobile-alt mr-2"></i> Mark OTP Verification
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </form>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->status->value === 'CANCELLED' && strtolower($application->payment_status->value) === 'paid'): ?>
                        <div class="position-relative my-4">
                            <hr class="border-light m-0">
                            <span
                                class="position-absolute top-50 left-50 translate-middle bg-white px-2 text-muted text-xs text-uppercase font-weight-bold"
                                style="transform: translate(-50%, -50%);">Financial</span>
                        </div>

                        
                        <form id="refundApplicationForm" method="POST" action="<?php echo e(route('admin.applications.updatePaymentStatus', $application->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <input type="hidden" name="payment_status" value="REFUNDED">
                            <button type="button" onclick="openRefundModal()"
                                class="btn btn-outline-danger btn-block py-2 d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                                <i class="fas fa-undo-alt mr-2"></i> Process Refund
                            </button>
                        </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm rounded-lg mb-4 elegant-border">
                <div class="card-header bg-white py-3 border-bottom text-center">
                    <h3 class="card-title font-weight-bold text-dark w-100 float-none mb-0">
                        <i class="fas fa-folder-open text-orange mr-2"></i>
                        Documents
                    </h3>
                </div>

                <div class="card-body p-4 bg-light rounded-bottom">

              
                <form action="<?php echo e(route('admin.applications.uploadDocument', $application->id)); ?>" method="POST" enctype="multipart/form-data" class="mb-4">
                    <?php echo csrf_field(); ?>
                    <div class="position-relative" style="border: 2px dashed #d1d5db; border-radius: 12px; padding: 2rem 1rem; text-align: center; background: #ffffff; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#1E9C5D'" onmouseout="this.style.borderColor='#d1d5db'">
                        <div class="text-muted mb-2">
                            <i class="fas fa-cloud-upload-alt fa-2x"></i>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Click to upload a generic document</h6>
                        <p class="text-xs text-muted mb-0 text-uppercase">PDF, PNG, JPG (Max 5MB)</p>
                        
                        <input type="file" name="document" class="position-absolute" style="top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" onchange="this.form.submit()" accept=".pdf,.png,.jpg,.jpeg">
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-xs font-weight-bold mt-1 d-block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </form>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->service->slug === 'itr-filing'): ?>
                    <div class="row px-2 mb-4">
                        
                        
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                <i class="fas fa-file-invoice text-primary mr-1"></i> ITR Ack
                            </label>
                            <?php $ackDoc = $application->getFirstMedia('itr_acknowledgement'); ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ackDoc): ?>
                                <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                    <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark" title="<?php echo e($ackDoc->name); ?>"><?php echo e($ackDoc->name); ?></div>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo e(route('admin.documents.view', $ackDoc->id)); ?>" target="_blank" class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                        
                                        
                                        <form action="<?php echo e(route('admin.applications.deleteDocument', $ackDoc->id)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            <?php else: ?>
                                
                                <form action="<?php echo e(route('admin.applications.uploadDocument', $application->id)); ?>" method="POST" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="file" name="ack_file" class="form-control border-light shadow-sm" style="height: auto; padding: 0.35rem 0.5rem; font-size: 0.8rem; border-radius: 6px;" accept=".pdf" onchange="this.form.submit()">
                                </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ack_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-xs font-weight-bold mt-1 d-block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                <i class="fas fa-calculator text-success mr-1"></i> Computation
                            </label>
                            <?php $compDoc = $application->getFirstMedia('computation_sheet'); ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($compDoc): ?>
                                <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                    <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark" title="<?php echo e($compDoc->name); ?>"><?php echo e($compDoc->name); ?></div>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo e(route('admin.documents.view', $compDoc->id)); ?>" target="_blank" class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                        
                                        
                                        <form action="<?php echo e(route('admin.applications.deleteDocument', $compDoc->id)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            <?php else: ?>
                                
                                <form action="<?php echo e(route('admin.applications.uploadDocument', $application->id)); ?>" method="POST" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="file" name="computation_file" class="form-control border-light shadow-sm" style="height: auto; padding: 0.35rem 0.5rem; font-size: 0.8rem; border-radius: 6px;" accept=".pdf" onchange="this.form.submit()">
                                </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['computation_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-xs font-weight-bold mt-1 d-block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-xs font-weight-bold mt-1 d-block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ack_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-xs font-weight-bold mt-1 d-block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['computation_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-xs font-weight-bold mt-1 d-block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </form>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($ackDoc) && $ackDoc): ?>
                    <form id="delete-ack-<?php echo e($ackDoc->id); ?>" action="<?php echo e(route('admin.applications.deleteDocument', $ackDoc->id)); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    </form>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($compDoc) && $compDoc): ?>
                    <form id="delete-comp-<?php echo e($compDoc->id); ?>" action="<?php echo e(route('admin.applications.deleteDocument', $compDoc->id)); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    </form>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                   
                    <?php
                        // We add 'documents' and 'default' here because that's where the generic Admin upload box saves files!
                        $adminCollections = ['final_deliverables', 'admin_uploads', 'documents', 'default'];
                        $specialCollections = ['itr_acknowledgement', 'computation_sheet'];
                        
                        $adminDocs = $application->media->whereIn('collection_name', $adminCollections);
                        
                        // Agent docs are all the specific form buckets (Form 16, Bank Statement, etc.)
                        $agentDocs = $application->media->whereNotIn('collection_name', array_merge($adminCollections, $specialCollections));
                    ?>

                    
                    
                    <?php
                        $renderDoc = function($doc) {
                            $ext = strtolower(pathinfo($doc->file_name ?? '', PATHINFO_EXTENSION));
                            $icon = match ($ext) {
                                'pdf' => 'fa-file-pdf text-danger',
                                'jpg', 'jpeg', 'png' => 'fa-file-image text-primary',
                                'doc', 'docx' => 'fa-file-word text-info',
                                default => 'fa-file-alt text-secondary',
                            };
                            
                            $bucketText = '';
                            if($doc->collection_name !== 'documents' && $doc->collection_name !== 'default') {
                                $bucketText = ' • <span class="text-primary">'.str_replace('_', ' ', $doc->collection_name).'</span>';
                            }
                            
                            return '
                            <div class="document-item d-flex align-items-center p-3 mb-3 bg-white rounded-lg border shadow-sm transition-hover">
                                <div class="document-icon bg-light rounded d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                                    <i class="fas '.$icon.' fa-lg"></i>
                                </div>
                                <div class="document-info flex-grow-1 overflow-hidden pr-2">
                                    <div class="text-dark font-weight-bold text-truncate text-sm mb-1" title="'.$doc->name.'">'.($doc->custom_properties['label'] ?? $doc->name).'</div>
                                    <div class="text-muted text-xs text-uppercase font-weight-bold">'.(strtoupper($ext) ?: 'FILE').' • '.number_format($doc->size / 1024, 1).' KB '.$bucketText.'</div>
                                </div>
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <a href="'.route('admin.documents.view', $doc->id).'" target="_blank" class="btn btn-sm btn-light border text-primary action-btn shadow-sm" title="View Document"><i class="fas fa-eye"></i></a>
                                    <a href="'.route('admin.documents.download', $doc->id).'" class="btn btn-sm btn-primary action-btn shadow-sm" title="Download Document"><i class="fas fa-download"></i></a>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-danger action-btn shadow-sm" title="Delete Document" onclick="document.getElementById(\'delete-doc-'.$doc->id.'\').submit();"><i class="fas fa-trash"></i></button>
                                </div>
                                <form id="delete-doc-'.$doc->id.'" action="'.route('admin.applications.deleteDocument', $doc->id).'" method="POST" class="d-none">'.csrf_field().method_field('DELETE').'</form>
                            </div>';
                        };
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->media->count()): ?>
                        
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($adminDocs->count() > 0): ?>
                            <hr class="border-light my-4">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-user-shield text-primary mr-2"></i> Uploaded by Admin</h6>
                            <div class="document-list mb-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $adminDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php echo $renderDoc($doc); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($agentDocs->count() > 0): ?>
                            <hr class="border-light my-4">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-user-tie text-secondary mr-2"></i> Uploaded by Client / Agent</h6>
                            <div class="document-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $agentDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php echo $renderDoc($doc); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-file-excel fa-2x text-secondary opacity-50"></i>
                            </div>
                            <h6 class="font-weight-bold">No Documents</h6>
                            <p class="text-sm mb-0">No files uploaded.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                </div>
            </div>

        </div>
    </div>

    
    <div id="customRefundModal" class="custom-modal-backdrop" style="display: none;">
        <div class="custom-modal-dialog shadow-lg">
            <div class="custom-modal-content">
                <div class="custom-modal-header bg-danger-soft">
                    <h5 class="mb-0 text-danger font-weight-bold">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Confirm Refund
                    </h5>
                </div>
                <div class="custom-modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-undo-alt text-danger" style="font-size: 3rem; opacity: 0.8;"></i>
                    </div>
                    <p class="mb-0 font-weight-bold text-dark" style="font-size: 1.1rem;">
                        Are you sure you want to process this refund?
                    </p>
                    <p class="text-muted text-sm mt-2">This application will be marked as refunded. This action cannot be undone.</p>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-light border font-weight-bold" onclick="closeRefundModal()">No, Go Back</button>
                    <button type="button" class="btn btn-danger font-weight-bold shadow-sm" onclick="submitRefundForm()">Yes, Process Refund</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        /* Typography & Utilities */
        .w-30 { width: 30%; }
        .text-xs { font-size: 0.75rem; }
        .gap-2 { gap: 0.5rem !important; }

        /* Containers */
        .elegant-border { border: 1px solid rgba(0, 0, 0, 0.05) !important; }

        /* Soft Background Colors */
        .bg-primary-soft { background-color: #e8f0fe !important; }
        .text-primary-dark { color: #1a73e8 !important; }
        .bg-success-soft { background-color: #e6f4ea !important; }
        .bg-warning-soft { background-color: #fef7e0 !important; }
        .bg-danger-soft { background-color: #fce8e6 !important; }
        .bg-info-soft { background-color: #e0f2fe !important; color: #0284c7 !important; }
        .bg-secondary-soft { background-color: #f1f3f4 !important; }

        /* Badges */
        .badge-primary-soft { background-color: #e8f0fe; color: #1a73e8; border: 1px solid #d2e3fc; }
        .badge-success-soft { background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6; }
        .badge-warning-soft { background-color: #fef7e0; color: #b06000; border: 1px solid #feefc3; }
        .badge-danger-soft { background-color: #fce8e6; color: #c5221f; border: 1px solid #fad2cf; }
        .badge-info-soft { background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .badge-secondary-soft { background-color: #f1f3f4; color: #5f6368; border: 1px solid #e8eaed; }

        /* Icons */
        .icon-box {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        /* Detail Tables */
        .detail-table td { padding: 1.2rem 1rem; vertical-align: middle; }

        /* Data Boxes (Dynamic Forms) */
        .data-box { border-left: 3px solid #1a73e8 !important; }

        /* Hover Transitions */
        .transition-hover { transition: all 0.2s ease-in-out; }
        .data-box:hover, .document-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
            border-color: #d1d5db !important;
        }
        .transition-color { transition: color 0.2s ease-in-out; }

        /* Button Tweaks */
        .btn { border-radius: 8px; letter-spacing: 0.3px; }
        .action-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; padding: 0;
        }

        /* ── CUSTOM MODAL ── */
        .custom-modal-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); z-index: 1050;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(3px); 
        }
        .custom-modal-dialog {
            background: #fff; border-radius: 16px; width: 100%; max-width: 450px;
            overflow: hidden; animation: popIn 0.3s ease-out forwards;
            transform: scale(0.9); opacity: 0;
        }
        .custom-modal-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f3f4; }
        .custom-modal-footer { padding: 1rem 1.5rem; background: #f8f9fa; display: flex; justify-content: center; gap: 12px; }
        
        @keyframes popIn {
            to { transform: scale(1); opacity: 1; }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        // --- Custom Refund Modal Logic ---
        function openRefundModal() {
            document.getElementById('customRefundModal').style.display = 'flex';
        }

        function closeRefundModal() {
            document.getElementById('customRefundModal').style.display = 'none';
        }

        function submitRefundForm() {
            event.target.disabled = true;
            event.target.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';
            document.getElementById('refundApplicationForm').submit();
        }

        // Close modal if they click outside the white box (on the dark background)
        window.onclick = function(event) {
            var modal = document.getElementById('customRefundModal');
            if (event.target == modal) {
                closeRefundModal();
            }
        }
        
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/applications/show.blade.php ENDPATH**/ ?>