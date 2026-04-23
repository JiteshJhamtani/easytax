<?php $__env->startSection('title', 'Services Management'); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <style>
        /* ── PAGE HEADER ── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--slate-dark);
            margin: 0;
            letter-spacing: -0.02em;
        }

        /* ── PREMIUM BUTTON ── */
        .btn-premium {
            background-color: var(--slate-dark);
            color: #ffffff;
            font-weight: 700;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .btn-premium:hover {
            background-color: #000000;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* ── MASTER TABLE CARD ── */
        .table-card {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            padding: 1.5rem;
            overflow: hidden;
        }

        /* ── DATATABLES OVERRIDES ── */
        .dataTables_wrapper .row { align-items: center; }
        
        .dataTables_filter label { font-weight: 600; color: var(--text-muted); font-size: 0.85rem; }
        .dataTables_filter input {
            border: 1px solid var(--border); border-radius: 8px;
            padding: 0.4rem 0.75rem; margin-left: 0.5rem; outline: none; transition: all 0.2s;
        }
        .dataTables_filter input:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(30,156,93,0.15); }

        .dataTables_length label { font-weight: 600; color: var(--text-muted); font-size: 0.85rem; }
        .dataTables_length select {
            border: 1px solid var(--border); border-radius: 6px;
            padding: 0.3rem 1.5rem 0.3rem 0.5rem; margin: 0 0.5rem; outline: none;
        }

        table.dataTable {
            border-collapse: collapse !important; margin-top: 1rem !important;
            margin-bottom: 1.5rem !important; width: 100% !important; border-bottom: 1px solid var(--border);
        }
        table.dataTable thead th {
            background: #f8fafc; color: var(--text-muted); font-size: 0.75rem;
            text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--border) !important;
            border-top: none !important; padding: 1rem 0.75rem; vertical-align: middle; font-weight: 700;
        }
        table.dataTable tbody td {
            padding: 1rem 0.75rem; vertical-align: middle; color: var(--text);
            font-size: 0.9rem; border-bottom: 1px solid var(--ink-100);
        }
        table.dataTable tbody tr { transition: background 0.2s; }
        table.dataTable tbody tr:hover { background: #f8fafc; }

        .dataTables_info { color: var(--text-muted); font-size: 0.85rem; font-weight: 500; }
        .page-item.active .page-link { background-color: var(--slate-dark); border-color: var(--slate-dark); color: white; border-radius: 6px; }
        .page-link {
            color: var(--slate); border: 1px solid var(--border); border-radius: 6px;
            margin: 0 3px; font-size: 0.85rem; font-weight: 600;
        }
        .page-link:hover { background: var(--ink-100); color: var(--slate-dark); }

        /* ── CUSTOM BADGES ── */
        .custom-badge {
            display: inline-flex; align-items: center; padding: 0.35rem 0.85rem;
            border-radius: 50px; font-size: 0.75rem; font-weight: 700;
            letter-spacing: 0.05em; text-transform: uppercase;
        }
        .badge-success-soft { background-color: var(--green-light); color: var(--green-dark); }
        .badge-danger-soft { background-color: #FEE2E2; color: #DC2626; }
        .badge-info-soft { background-color: #DBEAFE; color: #1E40AF; }
        
        .code-tag {
            background: var(--ink-100); color: var(--slate); padding: 0.3rem 0.6rem;
            border-radius: 6px; font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem; font-weight: 700; border: 1px solid var(--border);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <h1 class="page-title">Services Management</h1>
        <a href="<?php echo e(route('admin.services.create')); ?>" class="btn-premium">
            <i class="fas fa-plus-circle"></i> Add New Service
        </a>
    </div>

    <div class="table-card">
        <table id="servicesTable" class="table w-100">
            <thead>
                <tr>
                    <th class="pl-3">ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th class="text-right">Price</th>
                    <th class="text-center">Commission</th>
                    <th class="text-center">Apps</th>
                    <th class="text-center">Status</th>
                    <th class="text-center pr-3">Action</th>
                </tr>
            </thead>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

   <script>
    $(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#servicesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '<?php echo e(route('admin.services.datatable')); ?>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search services..."
            },
            columns: [
                { data: 'id', className: 'pl-3 font-weight-bold text-dark' },
                { data: 'name', className: 'font-weight-bold text-dark' },
                { 
                    data: 'slug', 
                    render: function(data) { return `<span class="code-tag">${data}</span>`; }
                },
                { 
                    data: 'price', 
                    className: 'text-right font-weight-bold',
                    render: function(data) { 
                        let amount = data ? parseFloat(data) : 0;
                        return `<span style="color: var(--green);">₹${amount.toFixed(2)}</span>`; 
                    }
                },
                { 
                    data: 'commission_display', 
                    className: 'text-center font-weight-bold', 
                    searchable: false, // <-- THE FIX: Stop frontend from searching this column
                    render: function(data){ return `<span style="color: var(--slate);">${data}</span>`; } 
                },
                { 
                    data: 'applications_count', 
                    className: 'text-center',
                    searchable: false, // <-- THE FIX: Stop frontend from searching this column
                    render: function(data) { 
                        return data ? `<span class="custom-badge badge-info-soft">${data}</span>` : '<span class="text-muted">-</span>'; 
                    }
                },
                { 
                    data: 'active', 
                    className: 'text-center',
                    searchable: false, // <-- Good practice to not text-search booleans either
                    render: function(data) { 
                        if (data) { return '<span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i> Active</span>'; }
                        return '<span class="custom-badge badge-danger-soft"><i class="fas fa-ban mr-1"></i> Inactive</span>'; 
                    }
                },
                { data: 'action', className: 'text-center pr-3', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/services/index.blade.php ENDPATH**/ ?>