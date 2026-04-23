<?php $__env->startSection('title', 'Agent Profile: ' . $agent->name); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <h1 class="m-0 text-dark font-weight-bold">Agent Overview</h1>
        <a href="<?php echo e(route('admin.agents.index')); ?>" class="btn btn-outline-secondary font-weight-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Agents
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-0">

        
        
        
        <div class="card modern-card border-0 mb-4 profile-header-card">
            <div class="card-body p-4 d-flex align-items-center">

                
                <div class="profile-avatar mr-4 shadow-sm">
                    <?php echo e(strtoupper(substr($agent->name, 0, 1))); ?>

                </div>

                <div class="profile-info flex-grow-1">
                    <h2 class="font-weight-bold text-dark mb-1 d-flex align-items-center">
                        <?php echo e($agent->name); ?>

                        <span class="custom-badge badge-success-soft ml-3 text-sm">
                            <i class="fas fa-check-circle mr-1"></i> Active
                        </span>
                    </h2>
                    <div class="d-flex align-items-center mt-2 text-muted">
                        <span class="agent-code-tag mr-3">
                            <i class="fas fa-id-badge text-primary mr-1"></i> <?php echo e($agent->agent_code); ?>

                        </span>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($agent->email): ?>
                            <span class="mr-3"><i class="fas fa-envelope mr-1"></i> <?php echo e($agent->email); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span><i class="fas fa-calendar-alt mr-1"></i> Joined
                            <?php echo e($agent->created_at?->format('M Y') ?? 'Recently'); ?></span>
                    </div>
                </div>

                
                <div class="profile-actions pl-3 border-left">
                    <a href="<?php echo e(route('admin.agents.edit', $agent)); ?>" class="btn btn-primary font-weight-bold shadow-sm">
                        <i class="fas fa-edit mr-1"></i> Edit Profile
                    </a>
                </div>

            </div>
        </div>

        
        
        
        <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-chart-pie text-primary mr-2"></i> Performance Stats
        </h5>

        <div class="row">
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-4">
                    <div class="metric-icon bg-blue-soft text-primary mb-3">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Total Applications</span>
                        <span class="metric-value text-dark"><?php echo e($stats['applications']); ?></span>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-4">
                    <div class="metric-icon bg-green-soft text-success mb-3">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Submitted Apps</span>
                        <span class="metric-value text-dark"><?php echo e($stats['submitted']); ?></span>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-4">
                    <div class="metric-icon bg-purple-soft text-purple mb-3">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Lifetime Commission</span>
                        <span class="metric-value text-dark">₹<?php echo e(number_format($stats['commission_total'], 2)); ?></span>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-4">
                    <div class="metric-icon bg-orange-soft text-orange mb-3">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="metric-data">
                        <span class="metric-label">Unpaid Commission</span>
                        <span class="metric-value text-danger">₹<?php echo e(number_format($stats['commission_unpaid'], 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        /* =========================================
               BASE CARD & PROFILE HEADER
            ========================================= */
        .modern-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
            overflow: hidden;
        }

        .profile-header-card {
            background: linear-gradient(to right, #ffffff, #f8fafc);
            border: 1px solid #e2e8f0 !important;
        }

        /* Dynamic Avatar Circle */
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0044b2, #0066ff);
            color: #ffffff;
            font-size: 2.2rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0, 68, 178, 0.15);
        }

        /* Agent Code Tag */
        .agent-code-tag {
            background: #f1f5f9;
            color: #334155;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.95rem;
            font-weight: 700;
            border: 1px solid #e2e8f0;
        }

        /* Soft Badges */
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

        /* =========================================
               METRIC CARDS (Vertical Layout)
            ========================================= */
        .metric-card {
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e2e8f0 !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06) !important;
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        /* Icon Color Themes */
        .bg-blue-soft {
            background: rgba(0, 68, 178, 0.1);
        }

        .text-primary {
            color: #0044b2 !important;
        }

        .bg-green-soft {
            background: rgba(0, 178, 89, 0.1);
        }

        .text-success {
            color: #00b259 !important;
        }

        .bg-orange-soft {
            background: rgba(255, 107, 0, 0.1);
        }

        .text-orange {
            color: #ff6b00 !important;
        }

        .bg-purple-soft {
            background: #f3e8ff;
        }

        .text-purple {
            color: #7e22ce !important;
        }

        /* Typography */
        .metric-data {
            display: flex;
            flex-direction: column;
        }

        .metric-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .metric-value {
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/agents/show.blade.php ENDPATH**/ ?>