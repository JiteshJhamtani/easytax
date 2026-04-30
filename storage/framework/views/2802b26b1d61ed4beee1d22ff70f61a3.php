
<div class="sv-card gm-card gm-card--monthly mb-4" style="--gm-accent: #1E9C5D; --gm-track: #EDF7F4;">
    <div class="gm-card__top">
        <div>
            <div class="gm-card__label"><?php echo e($group['period_label']); ?> &bull; <?php echo e($group['period_range']); ?></div>
            <h4 class="gm-card__title" style="font-size: 1.15rem;"><?php echo e($group['service_name']); ?></h4>
        </div>
        <div class="gm-card__count">
            <span class="gm-card__count-num"><?php echo e($group['agent_count']); ?></span>
            <span class="gm-card__count-label">Submissions</span>
        </div>
    </div>

    <div class="gm-track-area">
        <div class="gm-track">
            <div class="gm-track__fill" style="width: <?php echo e($group['progress_pct']); ?>%;"></div>
        </div>

        <?php
            $milestones = $group['milestones'];
            $milestoneCount = count($milestones);
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $milestones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mi => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php
                $leftPos = $milestoneCount > 1 ? ($mi / ($milestoneCount - 1)) * 100 : 50;
            ?>
            <div class="gm-dot-anchor tooltip-anchor" style="left: <?php echo e($leftPos); ?>%;">
                
                <div class="gm-dot-icon <?php echo e($m['unlocked'] ? 'is-unlocked' : ''); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($m['image_url']) || !empty($m['banner_url'])): ?>
                        <img src="<?php echo e($m['image_url'] ?? $m['banner_url']); ?>" alt="<?php echo e($m['name']); ?>" style="width:28px;height:28px;object-fit:cover;border-radius:50%">
                    <?php else: ?>
                        <span style="font-size: 14px;">🎁</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="gm-dot-label">
                    <span class="gm-dot-label__count"><?php echo e($m['min_count'] >= 1000 ? ($m['min_count']/1000).'k' : $m['min_count']); ?></span>
                   
                </div>

                
                <div class="gm-tooltip">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($m['image_url']) || !empty($m['banner_url'])): ?>
                        <img class="gm-tooltip__img" src="<?php echo e($m['image_url'] ?? $m['banner_url']); ?>" alt="<?php echo e($m['name']); ?>">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="gm-tooltip__name"><?php echo e($m['name']); ?></div>
                    <div class="gm-tooltip__row">
                        <span>Target</span>
                        <span><?php echo e(number_format($m['min_count'])); ?></span>
                    </div>
                    <div class="gm-tooltip__row">
                        <span>Your count</span>
                        <span><?php echo e(number_format($group['agent_count'])); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m['unlocked']): ?>
                        <div class="gm-tooltip__unlocked"><i class="fas fa-check-circle mr-1"></i> Unlocked!</div>
                    <?php else: ?>
                        <div class="gm-tooltip__row mt-2 pt-2 border-top">
                            <span>Still need</span>
                            <span class="text-danger"><?php echo e(number_format($m['needed'])); ?></span>
                        </div>
                        <div class="gm-tooltip__locked"><i class="fas fa-lock mr-1"></i> Locked</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <div class="gm-hint mt-4 pt-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['next_milestone']): ?>
            <span class="gm-hint__pill"><?php echo e($group['next_milestone']['needed']); ?> more</span>
            <span class="ml-1 text-muted">to unlock <strong class="text-dark"><?php echo e($group['next_milestone']['name']); ?></strong></span>
        <?php else: ?>
            <span class="gm-hint__pill" style="background:#1E9C5D; color:white;">Completed</span>
            <span class="ml-2 text-muted font-weight-bold">You have unlocked all rewards for this period!</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div><?php /**PATH /var/www/uat.easytax.live/resources/views/agent/partials/gift-timeline-single.blade.php ENDPATH**/ ?>