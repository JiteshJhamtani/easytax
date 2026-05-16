<?php $__env->startSection('title', 'Marketer Dashboard | EasyTax'); ?>

<?php $__env->startSection('content'); ?>
<div class="chq-wrapper p-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="font-weight-bold text-dark mb-0">Here is your lead generation performance at a glance.</h2>
        </div>
        <div>
            <span class="badge px-3 py-2" style="background-color: #dbeafe; color: #1e40af; border-radius: 50px; font-size: 0.8rem; font-weight: 700;">
                <i class="fas fa-headset mr-1"></i> Support Helpline: +91 77259 81022
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100" style="background: #f8fafc; border-left: 5px solid #3b82f6 !important;">
                <div class="card-body p-4">
                    <h6 class="text-xs font-weight-bold text-muted text-uppercase mb-2">Total Leads Captured</h6>
                    <h2 class="font-weight-bold text-dark mb-0"><?php echo e($totalLeads); ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100" style="background: #f0fdf4; border-left: 5px solid #22c55e !important;">
                <div class="card-body p-4">
                    <h6 class="text-xs font-weight-bold text-muted text-uppercase mb-2">Converted to Clients</h6>
                    <h2 class="font-weight-bold text-success mb-0"><?php echo e($convertedLeads); ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100" style="background: #fef2f2; border-left: 5px solid #ef4444 !important;">
                <div class="card-body p-4">
                    <h6 class="text-xs font-weight-bold text-muted text-uppercase mb-2">Lost / Not Interested</h6>
                    <h2 class="font-weight-bold text-danger mb-0"><?php echo e($lostLeads); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold text-dark mb-0"><i class="fas fa-clock mr-2 text-muted"></i>Recent Leads</h4>
            <a href="<?php echo e(route('crm.leads.index')); ?>" class="btn btn-sm btn-outline-primary shadow-sm font-weight-bold" style="border-radius: 6px;">View All</a>
        </div>

        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f1f5f9;">
                        <tr>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3 pl-4">Date</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3">Client Name</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3">Service</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3">Status</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3 pr-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr>
                                <td class="align-middle text-sm text-muted pl-4"><?php echo e($lead->created_at->format('d M Y, h:i A')); ?></td>
                                <td class="align-middle font-weight-bold text-dark">
                                    <?php echo e($lead->name); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->source === 'VLE'): ?>
                                        <span class="badge badge-sm badge-success ml-1" style="font-size: 0.6rem;">VLE</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="align-middle text-sm text-muted"><?php echo e($lead->service_interested ?? 'N/A'); ?></td>
                                <td class="align-middle">
                                    <?php
                                        $colors = [
                                            'NEW' => 'badge-info',
                                            'CONTACTED' => 'badge-warning',
                                            'IN_DISCUSSION' => 'badge-primary',
                                            'CONVERTED' => 'badge-success',
                                            'LOST' => 'badge-danger'
                                        ];
                                        $badgeClass = $colors[$lead->status] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo e($badgeClass); ?> px-2 py-1"><?php echo e(str_replace('_', ' ', $lead->status)); ?></span>
                                </td>
                                <td class="align-middle text-right pr-4">
                                    <a href="<?php echo e(route('crm.leads.edit', $lead->id)); ?>" class="btn btn-sm btn-light border text-primary font-weight-bold shadow-sm" style="border-radius: 6px;">Update</a>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted font-italic">No leads captured yet. Start adding some!</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.marketer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/marketer/dashboard.blade.php ENDPATH**/ ?>