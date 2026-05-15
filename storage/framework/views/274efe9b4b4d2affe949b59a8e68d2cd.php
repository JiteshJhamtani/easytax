<?php $__env->startSection('title', 'Operator Profile: ' . $operator->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?php echo e(route('admin.team.index')); ?>" class="text-muted text-decoration-none mb-2 d-inline-block"><i class="fas fa-arrow-left"></i> Back to Team</a>
            <h3 class="font-weight-bold text-dark mb-0">
                <?php echo e($operator->name); ?> 
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($operator->is_active): ?>
                    <span class="badge badge-success align-middle ml-2" style="font-size: 0.8rem;">Active</span>
                <?php else: ?>
                    <span class="badge badge-danger align-middle ml-2" style="font-size: 0.8rem;">Suspended</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h3> 
            <p class="text-muted mb-0"><i class="fas fa-envelope mr-1"></i> <?php echo e($operator->email); ?> | <i class="fas fa-phone mr-1"></i> <?php echo e($operator->mobile_number ?? 'No Phone'); ?></p>
        </div>
        <div>
            <a href="<?php echo e(route('admin.team.create-payout', $operator->id)); ?>" class="btn btn-success font-weight-bold shadow-sm">
                <i class="fas fa-rupee-sign mr-1"></i> Record Payout
            </a>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success shadow-sm rounded"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger shadow-sm rounded">
            <ul class="mb-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?> <li><?php echo e($error); ?></li> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-lg h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="letter-spacing: 1px;">Workload Summary</h6>
                    <div class="d-flex justify-content-between text-center">
                        <div>
                            <h3 class="font-weight-bold text-dark mb-0"><?php echo e($totalAssigned); ?></h3>
                            <span class="text-muted small">Assigned</span>
                        </div>
                        <div>
                            <h3 class="font-weight-bold text-success mb-0"><?php echo e($totalCompleted); ?></h3>
                            <span class="text-muted small">Completed</span>
                        </div>
                        <div>
                            <h3 class="font-weight-bold text-warning mb-0"><?php echo e($totalPending); ?></h3>
                            <span class="text-muted small">Pending</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-lg h-100" style="background: linear-gradient(135deg, #1e9c5d 0%, #15804c 100%); color: white;">
                <div class="card-body">
                    <h6 class="text-uppercase font-weight-bold mb-3" style="letter-spacing: 1px; color: #d1fae5;">Financial Summary</h6>
                    <div class="d-flex justify-content-between text-center">
                        <div>
                            <h3 class="font-weight-bold mb-0">₹<?php echo e(number_format($totalEarned)); ?></h3>
                            <span class="small" style="color: #d1fae5;">Total Earned</span>
                        </div>
                        <div>
                            <h3 class="font-weight-bold mb-0">₹<?php echo e(number_format($totalPaid)); ?></h3>
                            <span class="small" style="color: #d1fae5;">Total Paid</span>
                        </div>
                        <div>
                            <h3 class="font-weight-bold mb-0 <?php echo e($balanceDue > 0 ? 'text-warning' : ''); ?>">₹<?php echo e(number_format($balanceDue)); ?></h3>
                            <span class="small" style="color: #d1fae5;">Balance Due</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-4">
            <ul class="nav nav-tabs border-bottom-0" id="operatorTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold text-dark border-0 pb-3" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab" style="border-bottom: 3px solid #1e9c5d !important; background: transparent;">Assigned Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-muted border-0 pb-3" id="rates-tab" data-toggle="tab" href="#rates" role="tab" style="background: transparent;">Service Rates</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-muted border-0 pb-3" id="payouts-tab" data-toggle="tab" href="#payouts" role="tab" style="background: transparent;">Payout History</a>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content" id="operatorTabsContent">
                
                <div class="tab-pane fade show active p-4" id="tasks" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light text-muted text-uppercase small">
                                <tr>
                                    <th>App ID</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Pending Reason</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr>
                                    <td class="font-weight-bold">#<?php echo e($app->id); ?></td>
                                    <td><?php echo e($app->service->name ?? 'Unknown'); ?></td>
                                    <td><span class="badge badge-info"><?php echo e($app->status); ?></span></td>
                                    <td class="text-muted"><?php echo e($app->updated_at->format('d M Y')); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($app->pending_reason): ?>
                                            <span class="text-danger small font-weight-bold"><i class="fas fa-exclamation-circle mr-1"></i><?php echo e($app->pending_reason); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td><a href="<?php echo e(route('admin.applications.show', $app->id)); ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No applications assigned yet.</td></tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                        <div class="mt-3"><?php echo e($applications->links()); ?></div>
                    </div>
                </div>

                <div class="tab-pane fade p-4" id="rates" role="tabpanel">
                    <form action="<?php echo e(route('admin.team.save-rates', $operator->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold text-dark small"><?php echo e($service->name); ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                    <input type="number" step="0.01" name="rates[<?php echo e($service->id); ?>]" class="form-control" value="<?php echo e($currentRates[$service->id] ?? 0); ?>" placeholder="0.00">
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <hr>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">Save Rate Card</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade p-4" id="payouts" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-calendar-alt mr-1"></i> Month-wise Earnings</h6>
                            <table class="table table-sm table-hover">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-center">Apps Completed</th>
                                        <th class="text-right">Earned</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $monthlyEarnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <tr>
                                        <td class="font-weight-bold text-dark"><?php echo e($month->month_name); ?></td>
                                        <td class="text-center"><span class="badge badge-info"><?php echo e($month->total_apps); ?></span></td>
                                        <td class="text-right font-weight-bold text-success">₹<?php echo e(number_format($month->monthly_total, 2)); ?></td>
                                    </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">No completed apps yet.</td></tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-history mr-1"></i> Payout History</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th>Date Paid</th>
                                        <th class="text-right">Amount</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $payouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <tr>
                                        <td><?php echo e(\Carbon\Carbon::parse($payout->paid_at)->format('d M Y')); ?></td>
                                        <td class="text-right font-weight-bold text-dark">₹<?php echo e(number_format($payout->amount, 2)); ?></td>
                                        <td class="text-muted small"><?php echo e($payout->payment_note ?? '-'); ?></td>
                                    </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">No payouts recorded yet.</td></tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    // Simple script to handle Tab highlighting
    $('#operatorTabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
        $('#operatorTabs a').css('border-bottom', 'none').removeClass('text-dark').addClass('text-muted');
        $(this).css('border-bottom', '3px solid #1e9c5d').removeClass('text-muted').addClass('text-dark');
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/team/show.blade.php ENDPATH**/ ?>