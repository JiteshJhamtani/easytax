<?php $__env->startSection('title', 'Payout Details #' . $payout->id); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Payout #<?php echo e($payout->id); ?></h1>
            <p class="text-muted mb-0 mt-1">Generated on <?php echo e($payout->created_at?->format('d M Y, h:i A') ?? 'N/A'); ?></p>
        </div>
        <a href="<?php echo e(route('admin.payouts.index')); ?>" class="btn btn-outline-secondary font-weight-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Payouts
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-0">
        <div class="row">

            
            
            
            <div class="col-lg-4 mb-4">
                <div class="card detail-card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-receipt text-primary mr-2"></i> Payout Summary
                        </h3>
                    </div>

                    <div class="card-body pt-0">

                        
                        <div class="amount-showcase mb-4">
                            <span class="amount-label">Total Commission</span>
                            <h2 class="amount-value text-success">₹<?php echo e(number_format($payout->amount, 2)); ?></h2>

                            <div class="mt-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payout->paid_at): ?>
                                    <span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i>
                                        Paid</span>
                                <?php else: ?>
                                    <span class="custom-badge badge-warning-soft"><i class="fas fa-clock mr-1"></i>
                                        Pending</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <ul class="summary-list">
                            <li>
                                <div class="sl-icon"><i class="fas fa-user-tie"></i></div>
                                <div class="sl-content">
                                    <span class="sl-label">Agent Name</span>
                                    <span class="sl-value font-weight-bold"><?php echo e($payout->agent->name); ?></span>
                                </div>
                            </li>

                            <li>
                                <div class="sl-icon"><i class="fas fa-calendar-alt"></i></div>
                                <div class="sl-content">
                                    <span class="sl-label">Period</span>
                                    <span
                                        class="sl-value"><?php echo e(\Carbon\Carbon::parse($payout->period_start)->format('d M Y')); ?>

                                        &rarr; <?php echo e(\Carbon\Carbon::parse($payout->period_end)->format('d M Y')); ?></span>
                                </div>
                            </li>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payout->paid_at): ?>
                                <li>
                                    <div class="sl-icon text-success"><i class="fas fa-calendar-check"></i></div>
                                    <div class="sl-content">
                                        <span class="sl-label">Settled At</span>
                                        <span
                                            class="sl-value"><?php echo e(\Carbon\Carbon::parse($payout->paid_at)->format('d M Y, h:i A')); ?></span>
                                    </div>
                                </li>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payout->notes): ?>
                                <li class="align-items-start border-bottom-0">
                                    <div class="sl-icon mt-1"><i class="fas fa-sticky-note"></i></div>
                                    <div class="sl-content">
                                        <span class="sl-label">Notes</span>
                                        <span class="sl-value text-muted font-italic"><?php echo e($payout->notes); ?></span>
                                    </div>
                                </li>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>

                    </div>
                </div>
            </div>

            
            
            
            <div class="col-lg-8 mb-4">
                <div class="card detail-card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-layer-group text-primary mr-2"></i> Included Applications
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table simple-line-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="pl-4">App ID</th>
                                        <th>Service</th>
                                        <th>Submitted Date</th>
                                        <th class="text-right pr-4">Commission</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $payout->applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <tr>
                                            <td class="pl-4 font-weight-bold text-dark">#<?php echo e($app->id); ?></td>
                                            <td>
                                                <span
                                                    class="service-tag"><?php echo e($app->service->name ?? 'Unknown Service'); ?></span>
                                            </td>
                                            <td class="text-muted">
                                                <?php echo e(\Carbon\Carbon::parse($app->submitted_at)->format('d M Y')); ?></td>
                                            <td class="text-right pr-4 text-success font-weight-bold">
                                                ₹<?php echo e(number_format($app->commission_amount, 2)); ?>

                                            </td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-2x mb-3 opacity-50 d-block"></i>
                                                No applications attached to this payout.
                                            </td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payout->applications->count() > 0): ?>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="3" class="text-right font-weight-bold text-dark py-3">Total:
                                            </td>
                                            <td class="text-right pr-4 font-weight-bold text-dark py-3">
                                                ₹<?php echo e(number_format($payout->amount, 2)); ?></td>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        /* ---------------------------------------------------
           BASE CARD STYLES
        --------------------------------------------------- */
        .detail-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
            overflow: hidden;
        }

        /* ---------------------------------------------------
           AMOUNT SHOWCASE (Left Column Top)
        --------------------------------------------------- */
        .amount-showcase {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }

        .amount-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .amount-value {
            font-size: 2.25rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }

        /* ---------------------------------------------------
           SUMMARY LIST (Left Column Bottom)
        --------------------------------------------------- */
        .summary-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .summary-list li {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-list li:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .sl-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .sl-content {
            display: flex;
            flex-direction: column;
        }

        .sl-label {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 0.1rem;
        }

        .sl-value {
            font-size: 0.95rem;
            color: #1e293b;
        }

        /* ---------------------------------------------------
           CUSTOM BADGES
        --------------------------------------------------- */
        .custom-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .badge-success-soft {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-warning-soft {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
        }

        /* ---------------------------------------------------
           LINE ITEMS TABLE (Right Column)
        --------------------------------------------------- */
        .simple-line-table {
            width: 100%;
            border-collapse: collapse;
        }

        .simple-line-table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            border-top: none;
        }

        .simple-line-table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
            font-size: 0.95rem;
        }

        .simple-line-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .simple-line-table tfoot td {
            border-top: 2px solid #e2e8f0;
            border-bottom: none;
        }

        .service-tag {
            background: #f1f5f9;
            color: #334155;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/payouts/show.blade.php ENDPATH**/ ?>