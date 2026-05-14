<?php $__env->startSection('title', 'Marketers | EasyTax'); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold text-dark mb-0">📢 Marketing Team</h3>
            <p class="text-muted mb-0">Manage your marketers and track their lead generation.</p>
        </div>
        <a href="<?php echo e(route('crm.marketers.create')); ?>" class="btn btn-primary font-weight-bold shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-plus mr-1"></i> Add Marketer
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success shadow-sm" style="border-radius: 8px;"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive p-4">
                <table id="marketersTable" class="table table-hover mb-0 w-100">
                    <thead class="bg-light text-uppercase text-muted" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                        <tr>
                            <th class="border-0">ID</th>
                            <th class="border-0">Name & Contact</th>
                            <th class="border-0">Leads Generated</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-right">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
            processing: true, 
            serverSide: true,
            ajax: "<?php echo e(route('crm.marketers.datatable')); ?>",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name_email', name: 'name', orderable: false}, // New combined column
                {data: 'leads_count', name: 'leads_count', searchable: false},
                {data: 'is_active', name: 'is_active', searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right'}
            ]
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/marketers/index.blade.php ENDPATH**/ ?>