<?php $__env->startSection('title', 'Marketers | EasyTax'); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>
        .chq-hero { background-color: #fff1f2; padding: 2.5rem 3rem 6rem; border-bottom: 1px solid #ffe4e6; }
        .chq-main { max-width: 1400px; margin: -3.5rem auto 3rem; padding: 0 1.5rem; position: relative; z-index: 10; }
        .data-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e8ecf0; overflow: hidden; }
        .data-card-header { padding: 1.5rem 2rem; border-bottom: 1px solid #e8ecf0; display: flex; justify-content: space-between; align-items: center; }
        
        /* Compact Table Styles */
        .compact-table { width: 100% !important; table-layout: auto !important; }
        .compact-table th, .compact-table td { padding: 8px 10px !important; font-size: 0.8rem !important; vertical-align: middle !important; border-bottom: 1px solid #f3f4f6 !important; }
        .compact-table thead th { border-bottom: 2px solid #e8ecf0 !important; color: #7a8799; text-transform: uppercase; font-size: 0.7rem; font-weight: 700; }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="chq-wrapper">
    <header class="chq-hero">
        <div style="display:flex; justify-content:space-between; max-width: 1400px; margin: 0 auto;">
            <div>
                <h1 style="font-size:2rem; font-weight:800; color:#333; margin:0;">Marketing Team</h1>
                <p style="color:#666; margin:0;">Manage your marketers and track their lead generation.</p>
            </div>
           <a href="<?php echo e(route('crm.marketers.create')); ?>" class="btn btn-danger font-weight-bold shadow-sm" style="height:fit-content; border-radius:8px;">
    <i class="fas fa-plus mr-1"></i> Add Marketer
</a>
        </div>
    </header>

    <div class="chq-main">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success font-weight-bold rounded-lg shadow-sm border-0"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-danger rounded-lg shadow-sm border-0">
                <ul class="mb-0 pl-3"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?> <li><?php echo e($err); ?></li> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="data-card">
            <div class="data-card-header">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-users text-danger mr-2"></i> Active Marketers</h5>
            </div>
            <div class="p-4">
                <table id="marketersTable" class="table w-100 compact-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Leads Generated</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addMarketerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-light border-0" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-bullhorn text-danger mr-2"></i> Add New Marketer</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="<?php echo e(route('crm.marketers.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Full Name</label>
                        <input type="text" name="name" class="form-control rounded-lg" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-lg" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Temporary Password</label>
                        <input type="text" name="password" class="form-control rounded-lg" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-white border font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger font-weight-bold shadow-sm">Create Marketer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#marketersTable').DataTable({
            processing: true, serverSide: true,
            ajax: "<?php echo e(route('crm.marketers.datatable')); ?>",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name', className: 'font-weight-bold text-dark'},
                {data: 'email', name: 'email'},
                {data: 'leads_count', name: 'leads_count', searchable: false},
                {data: 'is_active', name: 'is_active', render: function(data) {
                    return data ? '<span class="badge badge-success-soft">Active</span>' : '<span class="badge badge-danger-soft">Suspended</span>';
                }},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right'}
            ]
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/marketers/index.blade.php ENDPATH**/ ?>