<?php $__env->startSection('title', 'Service: ' . $service->name); ?>

<?php $__env->startSection('css'); ?>
    <style>
        /* ── PAGE HEADER ── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-title { font-size: 1.5rem; font-weight: 800; color: var(--slate-dark); margin: 0 0 0.25rem 0; letter-spacing: -0.02em; }
        .page-subtitle { font-size: 0.9rem; color: var(--text-muted); margin: 0; }
        
        .header-actions { display: flex; gap: 0.75rem; }
        
        .btn-action-outline {
            font-size: 0.85rem; font-weight: 700; padding: 0.5rem 1rem; border-radius: 8px;
            border: 1px solid var(--border); color: var(--slate-dark); background: var(--surface);
            cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none;
        }
        .btn-action-outline:hover { background: var(--ink-100); color: var(--slate-dark); text-decoration: none; }
        
        .btn-premium {
            background-color: var(--slate-dark); color: #ffffff; font-weight: 700; padding: 0.5rem 1.25rem;
            border-radius: 8px; border: none; display: inline-flex; align-items: center; gap: 0.5rem;
            transition: all 0.2s; text-decoration: none; font-size: 0.85rem;
        }
        .btn-premium:hover { background-color: #000000; color: #ffffff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-decoration: none; }

        /* ── KPI CARDS ── */
        .kpi-card {
            display: flex; align-items: center; padding: 1.25rem 1.5rem; border-radius: 16px;
            background: var(--surface); border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s; height: 100%;
        }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .kpi-icon {
            width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0; margin-right: 1rem;
        }
        .kpi-body { flex: 1; }
        .kpi-value { font-size: 1.3rem; font-weight: 800; color: var(--slate-dark); line-height: 1.2; }
        .kpi-label { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; }

        .kpi-blue   .kpi-icon { background: #dbeafe; color: #2563eb; }
        .kpi-green  .kpi-icon { background: #dcfce7; color: #16a34a; }
        .kpi-purple .kpi-icon { background: #f3e8ff; color: #9333ea; }
        .kpi-orange .kpi-icon { background: #fff7ed; color: #ea580c; }

        /* ── DETAILS PANELS ── */
        .dash-panel {
            background: var(--surface); border-radius: 16px; border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02); height: 100%; display: flex; flex-direction: column; overflow: hidden;
        }
        .dash-panel-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--ink-100); display: flex; align-items: center; }
        .dash-panel-title { font-size: 1.05rem; font-weight: 800; color: var(--slate-dark); margin: 0; display: flex; align-items: center; gap: 0.5rem; }
        .dash-panel-body { padding: 1.5rem; flex: 1; }

        /* ── TABLES & LISTS ── */
        .info-table { width: 100%; }
        .info-table td { padding: 0.75rem 0; border-bottom: 1px dashed var(--ink-100); font-size: 0.9rem; }
        .info-table tr:last-child td { border-bottom: none; }
        .info-label { color: var(--text-muted); font-weight: 700; width: 35%; }
        .info-value { color: var(--slate-dark); font-weight: 600; }

        .schema-section { margin-bottom: 1.5rem; }
        .schema-title { font-size: 0.85rem; font-weight: 800; color: var(--slate-dark); border-bottom: 2px solid var(--ink-100); padding-bottom: 0.5rem; margin-bottom: 1rem; }
        
        .schema-table { width: 100%; border-collapse: collapse; }
        .schema-table th { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; padding: 0.5rem; border-bottom: 1px solid var(--border); }
        .schema-table td { font-size: 0.85rem; color: var(--slate-dark); padding: 0.75rem 0.5rem; border-bottom: 1px solid var(--ink-100); }
        .schema-table tr:last-child td { border-bottom: none; }

        .doc-list { list-style: none; padding: 0; margin: 0; }
        .doc-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--slate-dark); font-weight: 600; padding: 0.5rem 0; border-bottom: 1px dashed var(--ink-100); }
        .doc-item:last-child { border-bottom: none; }

        /* ── BADGES ── */
        .custom-badge {
            display: inline-flex; align-items: center; padding: 0.25rem 0.65rem;
            border-radius: 6px; font-size: 0.7rem; font-weight: 700;
            letter-spacing: 0.05em; text-transform: uppercase;
        }
        .badge-success-soft { background: var(--green-light); color: var(--green-dark); }
        .badge-danger-soft  { background: #FEE2E2; color: #DC2626; }
        
        .code-tag {
            background: var(--ink-100); color: var(--slate); padding: 0.2rem 0.5rem;
            border-radius: 4px; font-family: 'Courier New', Courier, monospace;
            font-size: 0.75rem; font-weight: 700; border: 1px solid var(--border);
        }
        .type-tag {
            color: #d97706; font-family: monospace; font-size: 0.8rem; font-weight: 700;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <div>
            <h1 class="page-title"><?php echo e($service->name); ?></h1>
            <p class="page-subtitle"><span class="code-tag"><?php echo e($service->slug); ?></span></p>
        </div>
        <div class="header-actions">
            <a href="<?php echo e(route('admin.services.index')); ?>" class="btn-action-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="<?php echo e(route('admin.services.edit', $service)); ?>" class="btn-premium">
                <i class="fas fa-edit"></i> Edit Service
            </a>
        </div>
    </div>

    
    <div class="row mb-4">
        <div class="row mb-4">
       <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fas fa-tag"></i></div>
                <div class="kpi-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($service->slug === 'gst-return-filing'): ?>
                        <div class="kpi-value text-primary" style="font-size: 1.1rem;">Dynamic</div>
                        <div class="kpi-label">Matrix Pricing</div>
                    <?php else: ?>
                        <div class="kpi-value">₹<?php echo e(number_format($service->price, 2)); ?></div>
                        <div class="kpi-label">Service Price</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fas fa-coins"></i></div>
                <div class="kpi-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($service->slug === 'gst-return-filing'): ?>
                        <div class="kpi-value text-success" style="font-size: 1.1rem;">Dynamic</div>
                        <div class="kpi-label">Matrix Commission</div>
                    <?php else: ?>
                        <div class="kpi-value"><?php echo e($service->commission_type === 'percentage' ? $service->commission_value . '%' : '₹' . number_format($service->commission_value, 2)); ?></div>
                        <div class="kpi-label">Commission (<?php echo e(ucfirst($service->commission_type)); ?>)</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

        </div>
        
        <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
            <div class="kpi-card kpi-purple">
                <div class="kpi-icon"><i class="fas fa-file-alt"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value"><?php echo e(number_format($stats['total_applications'])); ?></div>
                    <div class="kpi-label">Total Applications</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="kpi-card kpi-orange">
                <div class="kpi-icon"><i class="fas fa-rupee-sign"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">₹<?php echo e(number_format($stats['total_revenue'], 2)); ?></div>
                    <div class="kpi-label">Total Revenue</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        
        
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="dash-panel">
                <div class="dash-panel-header">
                    <h3 class="dash-panel-title"><i class="fas fa-info-circle text-primary"></i> Service Details</h3>
                </div>
                <div class="dash-panel-body">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Status</td>
                            <td class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($service->active): ?>
                                    <span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i> Active</span>
                                <?php else: ?>
                                    <span class="custom-badge badge-danger-soft"><i class="fas fa-ban mr-1"></i> Inactive</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">Description</td>
                            <td class="info-value"><?php echo e($service->description ?: '—'); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Created</td>
                            <td class="info-value"><?php echo e($service->created_at->format('d M Y, h:i A')); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Last Updated</td>
                            <td class="info-value"><?php echo e($service->updated_at->format('d M Y, h:i A')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-lg-7">
            <div class="dash-panel">
                <div class="dash-panel-header">
                    <h3 class="dash-panel-title"><i class="fas fa-wpforms" style="color: var(--green);"></i> Application Form Schema</h3>
                </div>
                <div class="dash-panel-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formConfig && isset($formConfig['sections'])): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $formConfig['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionKey => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="schema-section">
                                <h6 class="schema-title"><i class="fas fa-layer-group text-muted mr-1"></i> <?php echo e($section['label']); ?></h6>
                                <table class="schema-table">
                                    <thead>
                                        <tr>
                                            <th>Field Label</th>
                                            <th>Input Type</th>
                                            <th class="text-center">Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $section['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <tr>
                                                <td class="font-weight-bold"><?php echo e($field['label']); ?></td>
                                                <td><span class="type-tag"><?php echo e($field['type']); ?></span></td>
                                                <td class="text-center">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field['required'] ?? false): ?>
                                                        <i class="fas fa-check text-success"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-minus" style="color: #cbd5e1;"></i>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($formConfig['documents']) && count($formConfig['documents'])): ?>
                            <div class="schema-section mb-0">
                                <h6 class="schema-title"><i class="fas fa-paperclip text-muted mr-1"></i> Required Documents</h6>
                                <ul class="doc-list">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $formConfig['documents']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <li class="doc-item">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($doc['required'] ?? false): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php else: ?>
                                                <i class="far fa-circle text-muted"></i>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php echo e($doc['label']); ?>

                                            <small class="text-muted font-weight-normal ml-1">(<?php echo e(implode(', ', $doc['mimes'] ?? [])); ?>)</small>
                                        </li>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-code" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
                            <p class="text-muted font-weight-bold mb-0">No custom form fields configured.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?> 


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/services/show.blade.php ENDPATH**/ ?>