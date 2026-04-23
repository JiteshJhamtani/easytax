
<?php $gift = $group['milestones'][0]; ?>
<div class="multi-card mb-4">
    <div class="mc-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="tc-period text-uppercase font-weight-bold mb-1" style="color: #7a8799;">
                <?php echo e($group['period_label']); ?> &bull; <?php echo e($group['period_range']); ?> &bull; Multi-service
            </div>
            <h4 class="tc-title font-weight-bold mb-0 text-dark"><?php echo e($gift['icon'] ?? '🎁'); ?> <?php echo e($gift['name']); ?></h4>
        </div>
        <div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gift['unlocked']): ?>
                <span class="mc-badge mc-badge-success"><i class="fas fa-check-circle mr-1"></i> Eligible</span>
            <?php else: ?>
                <span class="mc-badge mc-badge-warning"><i class="fas fa-lock mr-1"></i> Locked</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="mc-circles-row d-flex flex-wrap gap-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $gift['conditions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ci => $cond): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="mc-circle-wrap text-center mr-4 mb-3">
                <div class="mc-svg-container position-relative mx-auto mb-2" style="width: 80px; height: 80px;">
                    <svg viewBox="0 0 80 80" class="w-100 h-100">
                        <circle cx="40" cy="40" r="32" fill="none" stroke="#e8ecf0" stroke-width="6" />
                        <circle cx="40" cy="40" r="32" fill="none"
                            stroke="<?php echo e($cond['unlocked'] ? '#1E9C5D' : '#e5e7eb'); ?>"
                            stroke-width="6" stroke-linecap="round"
                            stroke-dasharray="201"
                            stroke-dashoffset="<?php echo e(201 - (201 * ($cond['pct'] / 100))); ?>"
                            transform="rotate(-90 40 40)"
                            style="transition: stroke-dashoffset 1s ease-out;" />
                    </svg>
                    <div class="position-absolute d-flex flex-column align-items-center justify-content-center" style="inset:0;">
                        <span class="font-weight-bold text-dark" style="font-size: 1rem;"><?php echo e($cond['pct']); ?>%</span>
                    </div>
                </div>
                <div class="mc-label text-dark font-weight-bold" style="font-size: 0.85rem;"><?php echo e($cond['service_name']); ?></div>
                <div class="mc-progress small text-muted"><?php echo e(number_format($cond['agent_count'])); ?> / <?php echo e(number_format($cond['min_count'])); ?></div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    
    <div class="mt-2 text-muted small">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$gift['unlocked']): ?>
            <i class="fas fa-info-circle mr-1"></i> Complete all conditions above to unlock this gift.
        <?php else: ?>
            <span class="text-success font-weight-bold">🎉 You qualify for this gift!</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div><?php /**PATH /var/www/uat.easytax.live/resources/views/agent/partials/gift-timeline-multi.blade.php ENDPATH**/ ?>