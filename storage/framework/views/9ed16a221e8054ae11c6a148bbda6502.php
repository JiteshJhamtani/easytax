<div class="d-flex justify-content-center gap-2">
    <a href="<?php echo e(route('admin.payouts.show', $payout)); ?>" class="btn btn-sm btn-outline-primary shadow-sm"
        title="View Details">
        <i class="fas fa-eye"></i> View
    </a>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$payout->paid_at): ?>
        <button class="btn btn-sm btn-success shadow-sm markPaid" data-id="<?php echo e($payout->id); ?>" title="Mark as Paid">
            <i class="fas fa-check-circle mr-1"></i> Mark Paid
        </button>
    <?php else: ?>
        <span class="badge badge-success d-flex align-items-center px-2 py-1 shadow-sm">
            <i class="fas fa-check mr-1"></i> Settled
        </span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/uat.easytax.live/resources/views/admin/payouts/partials/actions.blade.php ENDPATH**/ ?>