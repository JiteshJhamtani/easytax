<?php $__env->startSection('title', 'Agent Workspace | EasyTax'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="workspace-header">
        <div>
            <h1 class="workspace-title">Welcome back, <?php echo e(optional(Auth::user())->name ?? 'Agent'); ?></h1>
            <p class="workspace-subtitle">Here is what's happening with your applications today.</p>
        </div>
        <div class="workspace-actions">
            <a href="<?php echo e(route('services.index')); ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-2"></i> New Application
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="dashboard-container">

        
        <div class="kpi-section-wrapper mb-4">
            <h4 class="kpi-section-title">Application Funnels</h4>
            <div class="row kpi-row">
                <?php if (isset($component)) { $__componentOriginald51543babe8c21cab5293c0cc3f52b78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald51543babe8c21cab5293c0cc3f52b78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.agent.kpi-card','data' => ['title' => 'Total Applications','value' => $stats->total_applications ?? 0,'icon' => 'svg.kpi-total']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('agent.kpi-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Applications','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats->total_applications ?? 0),'icon' => 'svg.kpi-total']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald51543babe8c21cab5293c0cc3f52b78)): ?>
<?php $attributes = $__attributesOriginald51543babe8c21cab5293c0cc3f52b78; ?>
<?php unset($__attributesOriginald51543babe8c21cab5293c0cc3f52b78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald51543babe8c21cab5293c0cc3f52b78)): ?>
<?php $component = $__componentOriginald51543babe8c21cab5293c0cc3f52b78; ?>
<?php unset($__componentOriginald51543babe8c21cab5293c0cc3f52b78); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald51543babe8c21cab5293c0cc3f52b78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald51543babe8c21cab5293c0cc3f52b78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.agent.kpi-card','data' => ['title' => 'Completed','value' => $stats->completed_applications ?? 0,'icon' => 'svg.kpi-completed']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('agent.kpi-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Completed','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats->completed_applications ?? 0),'icon' => 'svg.kpi-completed']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald51543babe8c21cab5293c0cc3f52b78)): ?>
<?php $attributes = $__attributesOriginald51543babe8c21cab5293c0cc3f52b78; ?>
<?php unset($__attributesOriginald51543babe8c21cab5293c0cc3f52b78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald51543babe8c21cab5293c0cc3f52b78)): ?>
<?php $component = $__componentOriginald51543babe8c21cab5293c0cc3f52b78; ?>
<?php unset($__componentOriginald51543babe8c21cab5293c0cc3f52b78); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald51543babe8c21cab5293c0cc3f52b78 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald51543babe8c21cab5293c0cc3f52b78 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.agent.kpi-card','data' => ['title' => 'In Progress','value' => $stats->pending_applications ?? 0,'icon' => 'svg.kpi-progress']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('agent.kpi-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'In Progress','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats->pending_applications ?? 0),'icon' => 'svg.kpi-progress']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald51543babe8c21cab5293c0cc3f52b78)): ?>
<?php $attributes = $__attributesOriginald51543babe8c21cab5293c0cc3f52b78; ?>
<?php unset($__attributesOriginald51543babe8c21cab5293c0cc3f52b78); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald51543babe8c21cab5293c0cc3f52b78)): ?>
<?php $component = $__componentOriginald51543babe8c21cab5293c0cc3f52b78; ?>
<?php unset($__componentOriginald51543babe8c21cab5293c0cc3f52b78); ?>
<?php endif; ?>
            </div>
        </div>

        
        <div class="row mt-4 mb-4">
            <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                <div class="card dashboard-card h-100 shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h3 class="card-title font-weight-bold text-dark" style="font-size: 1.1rem;">Application Velocity</h3>
                        <p class="text-muted small">Applications submitted over the last 6 months</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="chart-container" style="position:relative; height:280px; width:100%">
                            <canvas id="velocityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card dashboard-card h-100 shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h3 class="card-title font-weight-bold text-dark" style="font-size: 1.1rem;">Overview</h3>
                        <p class="text-muted small">Current status breakdown</p>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center px-4 pb-4">
                        <div class="chart-container" style="position:relative; height:220px; width:100%">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($giftGroups)): ?>
            <div class="mt-5 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="font-size: 2rem;">🎁</div>
                        <div>
                            <h3 class="font-weight-bold text-dark m-0" style="font-size: 1.25rem;">Your gift milestones</h3>
                            <p class="text-muted small m-0 mt-1">Keep submitting to unlock more rewards</p>
                        </div>
                    </div>
                    <a href="<?php echo e(route('agent.gifts')); ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-weight: 600;">View All</a>
                </div>

                <div class="gift-groups-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $giftGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupIdx => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['type'] === 'single'): ?>
                            <?php echo $__env->make('agent.partials.gift-timeline-single', ['group' => $group], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php else: ?>
                            <?php echo $__env->make('agent.partials.gift-timeline-multi', ['group' => $group], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php echo $__env->make('agent.partials.recent-applications-table', ['recentApplications' => $recentApplications], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        <?php echo $__env->make('agent.partials.dashboard-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        <?php echo $__env->make('agent.partials.dashboard-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.agent', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/agent/dashboard.blade.php ENDPATH**/ ?>