<?php $__env->startSection('title', 'My Commissions'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="m-0 text-dark font-weight-bold">My Commissions</h1>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-0">

        
        
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="metric-card bg-white shadow-sm border-0"
                    style="border-radius: 12px; padding: 1.5rem; display: flex; align-items: center;">
                    <div class="metric-icon"
                        style="width: 54px; height: 54px; border-radius: 12px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-right: 1.25rem;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <span
                            style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Pending
                            Balance</span>
                        
                        <h3 class="m-0 font-weight-bold text-dark" style="font-size: 1.8rem;">
                            ₹<?php echo e(number_format($pendingTotal ?? 0, 2)); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        
        
        
        <div class="card modern-card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-list text-primary mr-2"></i> Unpaid Applications
                </h3>
            </div>

            <div class="card-body">
                <table id="commissionTable" class="table table-bordered table-striped modern-table w-100">
                    <thead>
                        <tr>
                            <th class="pl-3">App ID</th>
                            <th>Service</th>
                            <th>Submitted Date</th>
                            <th class="text-right pr-4">Commission</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <style>
        /* Base Card Styling */
        .modern-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }

        /* Modern Table Styling */
        table.dataTable.modern-table {
            border-collapse: collapse !important;
            margin-top: 0.5rem !important;
            margin-bottom: 1.5rem !important;
            width: 100% !important;
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
        }

        .modern-table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            color: #475569;
            font-size: 0.95rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .modern-table tbody tr {
            transition: background 0.2s;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        /* Bottom Controls (Info & Pagination) */
        .dataTables_wrapper .row:last-child {
            align-items: center;
            padding-top: 0.5rem;
        }

        .dataTables_info {
            color: #64748b;
            font-size: 0.875rem;
        }

        .page-item.active .page-link {
            background-color: #0044b2;
            border-color: #0044b2;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 68, 178, 0.2);
        }

        .page-link {
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin: 0 2px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {

            // Ensure CSRF token is sent with AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#commissionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "<?php echo e(route('agent.commissions.table')); ?>",
                columns: [{
                        data: 'id',
                        name: 'id',
                        className: 'pl-3 font-weight-bold text-dark',
                        render: function(data) {
                            return '#' + data; // Adds a nice # to the ID
                        }
                    },
                    {
                        data: 'service',
                        name: 'service',
                        render: function(data) {
                            return `<span style="background: #f1f5f9; color: #334155; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500;">${data}</span>`;
                        }
                    },
                    {
                        data: 'date',
                        name: 'date',
                        className: 'text-muted'
                    },
                    {
                        data: 'commission',
                        name: 'commission',
                        className: 'text-right pr-4 text-success font-weight-bold', // Right-aligned for financial data
                        render: function(data) {
                            // Ensures the money displays beautifully even if the backend just sends a raw number
                            return '₹' + parseFloat(data).toFixed(2);
                        }
                    }
                ],
                order: [
                    [2, 'desc']
                ] // Order by Date descending by default
            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/agent/commissions/index.blade.php ENDPATH**/ ?>