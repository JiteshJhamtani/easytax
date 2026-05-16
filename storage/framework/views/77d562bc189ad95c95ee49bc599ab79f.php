<?php $__env->startSection('title', 'Marketers | EasyTax'); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    
    <style>
        /* ── DATATABLES OVERRIDES ── */
        .dataTables_wrapper .row { align-items: center; }
        
        .dataTables_filter label { font-weight: 600; color: #64748b; font-size: 0.85rem; }
        .dataTables_filter input { border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.4rem 0.75rem; margin-left: 0.5rem; outline: none; }
        
        .dataTables_length label { font-weight: 600; color: #64748b; font-size: 0.85rem; }
        .dataTables_length select { border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.3rem 0.5rem; margin: 0 0.5rem; outline: none; }

        .dataTables_info { color: #64748b; font-size: 0.85rem; font-weight: 500; }
        .page-item.active .page-link { background-color: #0f172a; border-color: #0f172a; color: white; border-radius: 6px; }
        .page-link { color: #475569; border: 1px solid #e2e8f0; border-radius: 6px; margin: 0 3px; font-size: 0.85rem; font-weight: 600; }

        /* ── DESKTOP TABLE STYLES ── */
        .table-card { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; overflow: hidden; }
        
        .marketers-table {
            border-collapse: collapse !important;
            width: 100% !important; /* Forces table to fit screen */
            margin-bottom: 0 !important;
        }
        
        .marketers-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            font-weight: 700;
            white-space: nowrap;
        }
        
        .marketers-table tbody td {
            white-space: normal !important; /* Allows text to wrap */
            word-break: break-word !important; 
            padding: 1rem 0.75rem !important;
            vertical-align: middle;
            font-size: 0.9rem !important;
            border-bottom: 1px solid #f1f5f9;
        }
        
        /* Protect the Action Column on Desktop */
        .marketers-table th:last-child,
        .marketers-table td:last-child {
            min-width: 100px;
            white-space: nowrap !important;
        }

        /* ==========================================================================
           🔥 MORPH TABLE INTO CARDS ON MOBILE & TABLET (Max 1024px) 🔥
           ========================================================================== */
        @media screen and (max-width: 1024px) {
            
            .table-responsive { overflow-x: visible !important; -webkit-overflow-scrolling: auto; }
            .table-card { overflow: visible !important; border: none !important; background: transparent !important; box-shadow: none !important; }
            
            .marketers-table, 
            .marketers-table tbody, 
            .marketers-table tr, 
            .marketers-table td {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important; 
                white-space: normal !important; 
            }

            .marketers-table thead {
                display: none !important;
            }

            .marketers-table tbody tr {
                margin-bottom: 1.25rem !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px !important;
                padding: 1rem !important;
                background: #ffffff !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
            }

            .marketers-table tbody td {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 0.6rem 0 !important;
                border-bottom: 1px dashed #e2e8f0 !important;
                text-align: right !important; 
                border-top: none !important;
            }
            
            /* Action Buttons Row */
            .marketers-table tbody td:last-child {
                border-bottom: none !important;
                padding-bottom: 0 !important;
                margin-top: 0.5rem;
                justify-content: flex-end !important;
            }

            .marketers-table tbody td::before {
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                text-align: left;
                margin-right: 1rem;
            }

            /* Fix Bootstrap Hover Dark Shadow inside mobile cards */
            .marketers-table tbody tr:nth-of-type(odd) td {
                box-shadow: none !important;
                background-color: transparent !important;
            }

            /* --- COLUMN MAP FOR MARKETERS TABLE --- */
            .marketers-table tbody td:nth-child(1)::before { content: "ID"; }
            .marketers-table tbody td:nth-child(2)::before { content: "Name & Contact"; }
            .marketers-table tbody td:nth-child(3)::before { content: "Leads Generated"; }
            .marketers-table tbody td:nth-child(4)::before { content: "Status"; }
            .marketers-table tbody td:nth-child(5)::before { content: "Actions"; display: none; }
        }
    </style>
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

    <div class="table-card">
        <div class="card-body p-0">
            <div class="table-responsive p-4">
                
                <table id="marketersTable" class="table table-hover mb-0 w-100 marketers-table">
                    <thead>
                        <tr>
                            <th class="pl-3">ID</th>
                            <th>Name & Contact</th>
                            <th>Leads Generated</th>
                            <th>Status</th>
                            <th class="text-right pr-3">Actions</th>
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
                {data: 'id', name: 'id', className: 'pl-3 font-weight-bold text-dark'},
                {data: 'name_email', name: 'name', orderable: false}, 
                {data: 'leads_count', name: 'leads_count', searchable: false},
                {data: 'is_active', name: 'is_active', searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right pr-3'}
            ],
            order: [[0, 'desc']] // Sorts by newest ID by default
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/marketers/index.blade.php ENDPATH**/ ?>