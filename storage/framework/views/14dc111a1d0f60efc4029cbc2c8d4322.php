<?php $__env->startSection('title', 'Pages Management'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="m-0 text-dark font-weight-bold">Pages Management</h1>
        <a href="<?php echo e(route('admin.pages.create')); ?>" class="btn btn-primary font-weight-bold shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Add New Page
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-0">

        <div class="card modern-card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-file-contract text-primary mr-2"></i> All Static Pages
                </h3>
            </div>

            <div class="card-body">
                
                <div class="table-responsive">
                    <table id="pagesTable" class="table table-bordered modern-table w-100">
                        <thead>
                            <tr>
                                <th class="pl-3">ID</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th class="text-center">Status</th>
                                <th>Created</th>
                                <th class="text-center pr-3">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>
        .modern-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }

        /* ── DESKTOP TABLE STYLES ── */
        table.dataTable.modern-table {
            border-collapse: collapse !important;
            margin-top: 0.5rem !important;
            margin-bottom: 1.5rem !important;
            width: 100% !important; /* Forces table to fit screen */
        }
        
        .modern-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            white-space: nowrap;
        }
        
        .modern-table tbody td {
            padding: 1rem 0.75rem !important;
            vertical-align: middle;
            color: #475569;
            font-size: 0.95rem !important;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important; /* Allows text to wrap */
            word-break: break-word !important; 
        }

        /* Protect the Action Column on Desktop */
        .modern-table th:last-child,
        .modern-table td:last-child {
            min-width: 80px;
            white-space: nowrap !important;
        }

        .modern-table tbody tr { transition: background 0.2s; }
        .modern-table tbody tr:hover { background: #f8fafc; }
        
        /* ── CUSTOM BADGES & PAGINATION ── */
        .custom-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .badge-success-soft {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge-danger-soft {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .dataTables_info { color: #64748b; font-size: 0.875rem; }
        .page-item.active .page-link {
            background-color: #0044b2;
            border-color: #0044b2;
            color: white;
        }
        .page-link {
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin: 0 2px;
        }

        /* ==========================================================================
           🔥 MORPH TABLE INTO CARDS ON MOBILE & TABLET (Max 1024px) 🔥
           ========================================================================== */
        @media screen and (max-width: 1024px) {
            
            .table-responsive { overflow-x: visible !important; -webkit-overflow-scrolling: auto; }
            .modern-card { overflow: visible !important; }
            
            table.dataTable.modern-table, 
            .modern-table tbody, 
            .modern-table tr, 
            .modern-table td {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important; 
                white-space: normal !important; 
            }

            .modern-table thead {
                display: none !important;
            }

            .modern-table tbody tr {
                margin-bottom: 1.25rem !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px !important;
                padding: 1rem !important;
                background: #ffffff !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
            }

            .modern-table tbody td {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 0.6rem 0 !important;
                border-bottom: 1px dashed #e2e8f0 !important;
                text-align: right !important; 
                border-top: none !important;
                border-left: none !important;
                border-right: none !important;
            }
            
            /* Action Buttons Row */
            .modern-table tbody td:last-child {
                border-bottom: none !important;
                padding-bottom: 0 !important;
                margin-top: 0.5rem;
                justify-content: flex-end !important;
            }

            .modern-table tbody td::before {
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                text-align: left;
                margin-right: 1rem;
            }

            /* --- COLUMN MAP FOR PAGES TABLE --- */
            .modern-table tbody td:nth-child(1)::before { content: "ID"; }
            .modern-table tbody td:nth-child(2)::before { content: "Title"; }
            .modern-table tbody td:nth-child(3)::before { content: "Slug"; }
            .modern-table tbody td:nth-child(4)::before { content: "Status"; }
            .modern-table tbody td:nth-child(5)::before { content: "Created"; }
            .modern-table tbody td:nth-child(6)::before { content: "Action"; display: none; }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $('#pagesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '<?php echo e(route('admin.pages.datatable')); ?>',
                columns: [
                    { data: 'id', className: 'pl-3 font-weight-bold text-dark' },
                    { data: 'title', className: 'font-weight-bold text-dark' },
                    { data: 'slug', className: 'text-muted', render: function(data) {
                        return `<code class="text-muted"><a href="/pages/${data}" target="_blank">${data}</a></code>`;
                    }},
                    { data: 'is_active', className: 'text-center', render: function(data) {
                        if (data) {
                            return '<span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i> Active</span>';
                        }
                        return '<span class="custom-badge badge-danger-soft"><i class="fas fa-ban mr-1"></i> Inactive</span>';
                    }},
                    { data: 'created_at', className: 'text-muted' },
                    { data: 'action', className: 'text-center pr-3', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']]
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/pages/index.blade.php ENDPATH**/ ?>