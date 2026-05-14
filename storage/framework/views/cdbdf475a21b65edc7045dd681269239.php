<?php $__env->startSection('title', 'Process Task #' . $application->id . ' | EasyTax'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2 pt-2">
        <div>
            <a href="<?php echo e(route('team.dashboard')); ?>" class="text-muted text-sm font-weight-bold mb-2 d-inline-block transition-hover">
                <i class="fas fa-arrow-left mr-1"></i> Back to My Tasks
            </a>

            <?php
                $status = strtolower($application->status?->value ?? $application->status ?? 'unknown');
                $statusClass = match ($status) {
                    'completed'   => 'badge-success-soft',
                    'in_progress', 'processing' => 'badge-info-soft',
                    'pending', 'submitted'      => 'badge-warning-soft',
                    'rejected'    => 'badge-danger-soft',
                    'cancelled'   => 'badge-secondary-soft',
                    default       => 'badge-primary-soft',
                };
            ?>

            <div class="d-flex align-items-center mt-1">
                <h1 class="h3 font-weight-bold mb-0 text-dark">Process Task #<?php echo e($application->id); ?></h1>
                <span class="badge <?php echo e($statusClass); ?> ml-3 px-3 py-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <?php echo e(str_replace('_', ' ', $status)); ?>

                </span>
            </div>
            <p class="text-muted mt-2 mb-0 text-sm">
                <i class="far fa-calendar-alt mr-1"></i>
                Submitted on <span class="font-weight-bold"><?php echo e($application->submitted_at?->format('d M Y, h:i A') ?? $application->created_at->format('d M Y, h:i A')); ?></span>
            </p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">

        
        <div class="col-lg-8">

            
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-info-circle text-primary mr-2"></i> Application Overview
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 detail-table">
                        <tbody>
                            <tr>
                                <td class="text-muted text-uppercase text-xs font-weight-bold w-30 align-middle pl-4 border-top-0">Service Requested</td>
                                <td class="font-weight-bold text-dark border-top-0"><?php echo e($application->service->name ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted text-uppercase text-xs font-weight-bold w-30 align-middle pl-4">Application ID</td>
                                <td><span class="text-muted font-weight-bold">#<?php echo e($application->id); ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-clipboard-list text-primary mr-2"></i> Client Information
                    </h3>
                </div>

                <?php 
                    $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : ($application->form_data ?? []);
                    $formData = array_filter($formData, fn($key) => !in_array($key, ['admin_username', 'admin_password', 'moa', 'aoa']), ARRAY_FILTER_USE_KEY);

                    $regularData = [];
                    $repeaterGroups = [];
                    
                    foreach($formData as $key => $value) {
                        if (str_starts_with($key, 'director_') || str_starts_with($key, 'member_') || str_starts_with($key, 'partner_')) {
                            if (preg_match('/^([a-zA-Z]+)_(\d+)_(.+)$/', $key, $matches)) {
                                $prefix = $matches[1]; 
                                $index = (int)$matches[2] - 1; 
                                $subField = $matches[3]; 
                                $repeaterGroups[$prefix][$index][$subField] = $value;
                            } else {
                                $regularData[$key] = $value;
                            }
                        } else {
                            $regularData[$key] = $value;
                        }
                    }

                    foreach ($repeaterGroups as $prefix => $items) {
                        foreach ($items as $index => $itemData) {
                            $hasData = false;
                            foreach ($itemData as $val) {
                                if (!empty($val)) { $hasData = true; break; }
                            }
                            if (!$hasData) { unset($repeaterGroups[$prefix][$index]); }
                        }
                    }
                ?>

                <div class="card-body p-4 bg-light rounded-bottom">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($regularData) > 0): ?>
                        <div class="row">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $regularData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="col-md-6 mb-3">
                                    <div class="bg-white p-3 rounded-lg border shadow-sm h-100 data-box transition-hover">
                                        <span class="d-block text-muted text-uppercase text-xs font-weight-bold mb-1"><?php echo e(str_replace('_', ' ', $key)); ?></span>
                                        <span class="text-dark font-weight-normal" style="word-break: break-word;">
                                            <?php echo e(is_array($value) ? implode(', ', $value) : (empty($value) && $value !== '0' ? 'Not provided' : $value)); ?>

                                        </span>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php elseif(empty($repeaterGroups)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                            <h6 class="font-weight-bold">No Client Data</h6>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $repeaterGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($items) > 0): ?>
                            <h5 class="font-weight-bold text-dark mt-4 mb-3 border-bottom pb-2">
                                <i class="fas fa-users text-primary mr-2"></i> <?php echo e(ucfirst($groupName)); ?> Details
                            </h5>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $itemData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="row mb-2">
                                    <div class="col-12"><strong class="text-muted text-xs text-uppercase mb-2 d-block"><?php echo e(ucfirst($groupName)); ?> <?php echo e($index + 1); ?></strong></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $itemData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subKey => $subValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="bg-white p-3 rounded-lg border shadow-sm h-100 data-box transition-hover">
                                                <span class="d-block text-muted text-xs font-weight-bold text-uppercase mb-1"><?php echo e(str_replace('_', ' ', $subKey)); ?></span>
                                                <span class="text-dark font-weight-normal" style="word-break: break-word;">
                                                    <?php echo e(empty($subValue) && $subValue !== '0' ? 'Not provided' : $subValue); ?>

                                                </span>
                                            </div>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

        </div>

        
        <div class="col-lg-4">

            
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom text-center">
                    <h3 class="card-title font-weight-bold text-dark w-100 float-none mb-0">
                        <i class="fas fa-tasks text-primary mr-2"></i> Update Progress
                    </h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?php echo e(route('team.applications.status', $application->id)); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <button type="submit" name="status" value="IN_PROGRESS"
                            class="btn btn-warning btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                            <i class="fas fa-spinner mr-2"></i> Mark In Progress
                        </button>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->service->slug === 'itr-filing'): ?>
                            <button type="submit" name="status" value="E_FILING"
                                class="btn btn-info btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover text-white">
                                <i class="fas fa-laptop-code mr-2"></i> Mark E-Filing
                            </button>
                            <button type="submit" name="status" value="OTP_VERIFICATION"
                                class="btn btn-primary btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                                <i class="fas fa-mobile-alt mr-2"></i> Request OTP
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <button type="submit" name="status" value="COMPLETED"
                            class="btn btn-success btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                            <i class="fas fa-check-circle mr-2"></i> Mark Completed
                        </button>
                    </form>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->service->slug === 'itr-filing'): ?>
                        <hr class="border-light my-3">
                        <a href="<?php echo e(route('team.applications.balance-sheet', $application->id)); ?>" class="btn btn-outline-dark btn-block shadow-sm font-weight-bold py-2 mb-2">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> Create Balance Sheet
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php
                $companyServices = ['fpo-registration', 'section-8-company', 'llp-registration', 'opc-registration', 'private-limited-company-registration'];
                $isCompanySetup = in_array($application->service->slug ?? '', $companyServices);
            ?>

            <div class="card border-0 shadow-sm rounded-lg mb-4 elegant-border">
                <div class="card-header bg-white py-3 border-bottom text-center">
                    <h3 class="card-title font-weight-bold text-dark w-100 float-none mb-0">
                        <i class="fas fa-folder-open text-orange mr-2"></i> Documents
                    </h3>
                </div>

                <div class="card-body p-4 bg-light rounded-bottom">

                    
                    <form action="<?php echo e(route('team.applications.uploadDocument', $application->id)); ?>" method="POST"
                        enctype="multipart/form-data" class="mb-4">
                        <?php echo csrf_field(); ?>
                        <div class="position-relative"
                            style="border: 2px dashed #d1d5db; border-radius: 12px; padding: 2rem 1rem; text-align: center; background: #ffffff; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.borderColor='#1E9C5D'"
                            onmouseout="this.style.borderColor='#d1d5db'">
                            <div class="text-muted mb-2"><i class="fas fa-cloud-upload-alt fa-2x"></i></div>
                            <h6 class="font-weight-bold text-dark mb-1">Click to upload a generic document</h6>
                            <p class="text-xs text-muted mb-0 text-uppercase">PDF, PNG, JPG (Max 5MB)</p>
                            <input type="file" name="document" class="position-absolute"
                                style="top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;"
                                onchange="this.form.submit()" accept=".pdf,.png,.jpg,.jpeg">
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger text-xs font-weight-bold mt-1 d-block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </form>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->service->slug === 'itr-filing'): ?>
                        <div class="row px-2 mb-4">
                            <div class="col-12 mb-3">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                    <i class="fas fa-file-invoice text-primary mr-1"></i> ITR Ack
                                </label>
                                <?php $ackDoc = $application->getFirstMedia('itr_acknowledgement'); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ackDoc): ?>
                                    <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                        <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark" title="<?php echo e($ackDoc->name); ?>"><?php echo e($ackDoc->name); ?></div>
                                        <div class="d-flex gap-1">
                                            <a href="<?php echo e(route('team.documents.view', $ackDoc->id)); ?>" target="_blank" class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                            <form action="<?php echo e(route('team.applications.deleteDocument', $ackDoc->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?');">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form action="<?php echo e(route('team.applications.uploadDocument', $application->id)); ?>" method="POST" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <input type="file" name="ack_file" class="form-control border-light shadow-sm" style="height:auto;padding:0.35rem 0.5rem;font-size:0.8rem;border-radius:6px;" accept=".pdf" onchange="this.form.submit()">
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-12">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                    <i class="fas fa-calculator text-success mr-1"></i> Computation
                                </label>
                                <?php $compDoc = $application->getFirstMedia('computation_sheet'); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($compDoc): ?>
                                    <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                        <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark" title="<?php echo e($compDoc->name); ?>"><?php echo e($compDoc->name); ?></div>
                                        <div class="d-flex gap-1">
                                            <a href="<?php echo e(route('team.documents.view', $compDoc->id)); ?>" target="_blank" class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                            <form action="<?php echo e(route('team.applications.deleteDocument', $compDoc->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?');">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form action="<?php echo e(route('team.applications.uploadDocument', $application->id)); ?>" method="POST" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <input type="file" name="computation_file" class="form-control border-light shadow-sm" style="height:auto;padding:0.35rem 0.5rem;font-size:0.8rem;border-radius:6px;" accept=".pdf" onchange="this.form.submit()">
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCompanySetup): ?>
                        <div class="row px-2 mb-4">
                            <div class="col-12 mb-3">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                    <i class="fas fa-file-pdf text-danger mr-1"></i> Draft MOA
                                </label>
                                <?php $moaDoc = $application->getFirstMedia('moa_document'); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($moaDoc): ?>
                                    <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                        <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark" title="<?php echo e($moaDoc->name); ?>"><?php echo e($moaDoc->name); ?></div>
                                        <div class="d-flex gap-1">
                                            <a href="<?php echo e(route('team.documents.view', $moaDoc->id)); ?>" target="_blank" class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                            <form action="<?php echo e(route('team.applications.deleteDocument', $moaDoc->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?');">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form action="<?php echo e(route('team.applications.uploadDocument', $application->id)); ?>" method="POST" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <input type="file" name="moa_file" class="form-control border-light shadow-sm" style="height:auto;padding:0.35rem 0.5rem;font-size:0.8rem;border-radius:6px;" accept=".pdf,.doc,.docx" onchange="this.form.submit()">
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-12">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                    <i class="fas fa-file-pdf text-danger mr-1"></i> Draft AOA
                                </label>
                                <?php $aoaDoc = $application->getFirstMedia('aoa_document'); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aoaDoc): ?>
                                    <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                        <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark" title="<?php echo e($aoaDoc->name); ?>"><?php echo e($aoaDoc->name); ?></div>
                                        <div class="d-flex gap-1">
                                            <a href="<?php echo e(route('team.documents.view', $aoaDoc->id)); ?>" target="_blank" class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                            <form action="<?php echo e(route('team.applications.deleteDocument', $aoaDoc->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?');">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form action="<?php echo e(route('team.applications.uploadDocument', $application->id)); ?>" method="POST" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <input type="file" name="aoa_file" class="form-control border-light shadow-sm" style="height:auto;padding:0.35rem 0.5rem;font-size:0.8rem;border-radius:6px;" accept=".pdf,.doc,.docx" onchange="this.form.submit()">
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php
                        $adminCollections   = ['final_deliverables', 'admin_uploads', 'documents', 'default'];
                        $specialCollections = ['itr_acknowledgement', 'computation_sheet','moa_document', 'aoa_document'];
                        $adminDocs          = $application->media->whereIn('collection_name', $adminCollections);
                        $agentDocs          = $application->media->whereNotIn('collection_name', array_merge($adminCollections, $specialCollections));

                        $renderDoc = function($doc) {
                            $ext  = strtolower(pathinfo($doc->file_name ?? '', PATHINFO_EXTENSION));
                            $icon = match ($ext) {
                                'pdf'             => 'fa-file-pdf text-danger',
                                'jpg','jpeg','png' => 'fa-file-image text-primary',
                                'doc','docx'      => 'fa-file-word text-info',
                                default           => 'fa-file-alt text-secondary',
                            };
                            $bucketText = ($doc->collection_name !== 'documents' && $doc->collection_name !== 'default')
                                ? ' • <span class="text-primary">'.str_replace('_', ' ', $doc->collection_name).'</span>'
                                : '';
                            return '
                            <div class="document-item d-flex align-items-center p-3 mb-3 bg-white rounded-lg border shadow-sm transition-hover">
                                <div class="document-icon bg-light rounded d-flex align-items-center justify-content-center mr-3" style="width:45px;height:45px;flex-shrink:0;">
                                    <i class="fas '.$icon.' fa-lg"></i>
                                </div>
                                <div class="document-info flex-grow-1 overflow-hidden pr-2">
                                    <div class="text-dark font-weight-bold text-truncate text-sm mb-1" title="'.$doc->name.'">'.($doc->custom_properties['label'] ?? $doc->name).'</div>
                                    <div class="text-muted text-xs text-uppercase font-weight-bold">'.(strtoupper($ext) ?: 'FILE').' • '.number_format($doc->size / 1024, 1).' KB '.$bucketText.'</div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex gap-2">
                                        <a href="'.route('team.documents.view', $doc->id).'" target="_blank" class="btn btn-sm btn-light border text-primary action-btn shadow-sm"><i class="fas fa-eye"></i></a>
                                        <a href="'.route('team.documents.download', $doc->id).'" class="btn btn-sm btn-primary action-btn shadow-sm"><i class="fas fa-download"></i></a>
                                    </div>
                                    <form action="'.route('team.applications.deleteDocument', $doc->id).'" method="POST" onsubmit="return confirm(\'Delete this document?\');">'.csrf_field().method_field('DELETE').'
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 shadow-sm" style="height: 28px; padding: 0;"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>';
                        };
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->media->count()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($adminDocs->count() > 0): ?>
                            <hr class="border-light my-4">
                            <h6 class="font-weight-bold text-dark mb-3">
                                <i class="fas fa-user-shield text-primary mr-2"></i> System / Office Uploads
                            </h6>
                            <div class="document-list mb-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $adminDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?> <?php echo $renderDoc($doc); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($agentDocs->count() > 0): ?>
                            <hr class="border-light my-4">
                            <h6 class="font-weight-bold text-dark mb-3">
                                <i class="fas fa-user-tie text-secondary mr-2"></i> Client / Agent Uploads
                            </h6>
                            <div class="document-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $agentDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?> <?php echo $renderDoc($doc); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border" style="width:60px;height:60px;">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .w-30 { width: 30%; }
        .text-xs { font-size: 0.75rem; }
        .gap-2 { gap: 0.5rem !important; }
        .elegant-border { border: 1px solid rgba(0,0,0,0.05) !important; }
        .bg-primary-soft { background-color: #e8f0fe !important; }
        .bg-success-soft { background-color: #e6f4ea !important; }
        .bg-warning-soft { background-color: #fef7e0 !important; }
        .bg-danger-soft  { background-color: #fce8e6 !important; }
        .bg-info-soft    { background-color: #e0f2fe !important; color: #0284c7 !important; }
        .bg-secondary-soft { background-color: #f1f3f4 !important; }
        .text-primary-dark { color: #1e9c5d !important; }
        
        .badge-primary-soft   { background-color:#e8f0fe; color:#1a73e8; border:1px solid #d2e3fc; }
        .badge-success-soft   { background-color:#e6f4ea; color:#137333; border:1px solid #ceead6; }
        .badge-warning-soft   { background-color:#fef7e0; color:#b06000; border:1px solid #feefc3; }
        .badge-danger-soft    { background-color:#fce8e6; color:#c5221f; border:1px solid #fad2cf; }
        .badge-info-soft      { background-color:#e0f2fe; color:#0284c7; border:1px solid #bae6fd; }
        .badge-secondary-soft { background-color:#f1f3f4; color:#5f6368; border:1px solid #e8eaed; }
        
        .detail-table td { padding:1.2rem 1rem; vertical-align:middle; }
        .data-box { border-left: 3px solid #1e9c5d !important; }
        .transition-hover { transition: all 0.2s ease-in-out; }
        .data-box:hover, .document-item:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
        .btn { border-radius: 8px; letter-spacing: 0.3px; }
        .action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; padding:0; }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/team/applications/show.blade.php ENDPATH**/ ?>