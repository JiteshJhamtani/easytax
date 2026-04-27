<?php $__env->startSection('title', 'Marketer Dashboard | EasyTax'); ?>

<?php $__env->startSection('content'); ?>
<div class="chq-wrapper p-4">
    <div class="mb-4">
        <h2 class="font-weight-bold text-dark mb-0">Welcome back, <?php echo e(auth()->user()->name); ?>! 👋</h2>
        <p class="text-muted">Here is your lead generation performance at a glance.</p>
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

    <div class="mt-4">
        <a href="<?php echo e(route('crm.leads.create')); ?>" class="btn btn-primary font-weight-bold shadow-sm px-4 py-2" style="border-radius: 8px; background: #8b5cf6; border-color: #8b5cf6;">
            <i class="fas fa-plus mr-2"></i> Capture New Lead
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.marketer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/marketer/dashboard.blade.php ENDPATH**/ ?>