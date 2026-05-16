<?php $__env->startSection('title', 'Leads CRM | EasyTax'); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>
        .chq-hero { background-color: #f3e8ff; padding: 2.5rem 3rem 6rem; border-bottom: 1px solid #e9d5ff; }
        .chq-main { max-width: 1400px; margin: -3.5rem auto 3rem; padding: 0 1.5rem; position: relative; z-index: 10; }
        .data-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e8ecf0; overflow: hidden; }
        
        /* ── DATATABLES OVERRIDES ── */
        .dataTables_wrapper .row { align-items: center; }
        .dataTables_filter label { font-weight: 600; color: #64748b; font-size: 0.85rem; }
        .dataTables_filter input { border: 1px solid #e8ecf0; border-radius: 8px; padding: 0.4rem 0.75rem; margin-left: 0.5rem; outline: none; }
        .dataTables_length label { font-weight: 600; color: #64748b; font-size: 0.85rem; }
        .dataTables_length select { border: 1px solid #e8ecf0; border-radius: 6px; padding: 0.3rem 0.5rem; margin: 0 0.5rem; outline: none; }
        .dataTables_info { color: #64748b; font-size: 0.85rem; font-weight: 500; }
        .page-item.active .page-link { background-color: #8b5cf6; border-color: #8b5cf6; color: white; border-radius: 6px; }
        .page-link { color: #475569; border: 1px solid #e8ecf0; border-radius: 6px; margin: 0 3px; font-size: 0.85rem; font-weight: 600; }

        /* ── DESKTOP TABLE STYLES ── */
        .compact-table { 
            width: 100% !important; 
            border-collapse: collapse !important;
            margin-bottom: 0 !important;
        }
        .compact-table thead th { 
            border-bottom: 2px solid #e8ecf0 !important; 
            border-top: none !important;
            color: #7a8799; 
            text-transform: uppercase; 
            font-size: 0.75rem; 
            font-weight: 700; 
            letter-spacing: 0.05em;
            padding: 1rem 0.75rem !important;
            white-space: nowrap;
        }
        .compact-table tbody td { 
            padding: 1rem 0.75rem !important; 
            font-size: 0.9rem !important; 
            vertical-align: middle !important; 
            border-bottom: 1px solid #f3f4f6 !important; 
            white-space: normal !important; /* Allow text wrapping */
            word-break: break-word !important;
        }
        
        /* Protect Action Column on Desktop */
        .compact-table th:last-child,
        .compact-table td:last-child {
            min-width: 100px;
            white-space: nowrap !important;
        }
        
        /* ── BADGES ── */
        .badge-info-soft { background-color: #e0f2fe; color: #0284c7; }
        .badge-warning-soft { background-color: #fef3c7; color: #d97706; }
        .badge-primary-soft { background-color: #e0e7ff; color: #4f46e5; }
        .badge-success-soft { background-color: #dcfce7; color: #166534; }
        .badge-danger-soft { background-color: #fee2e2; color: #991b1b; }

        /* ==========================================================================
           🔥 MORPH TABLE INTO CARDS ON MOBILE & TABLET (Max 1024px) 🔥
           ========================================================================== */
        @media screen and (max-width: 1024px) {
            
            .table-responsive { overflow-x: visible !important; -webkit-overflow-scrolling: auto; }
            .data-card { overflow: visible !important; border: none !important; background: transparent !important; box-shadow: none !important; }
            
            .compact-table, 
            .compact-table tbody, 
            .compact-table tr, 
            .compact-table td {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important; 
                white-space: normal !important; 
            }

            .compact-table thead {
                display: none !important;
            }

            .compact-table tbody tr {
                margin-bottom: 1.25rem !important;
                border: 1px solid #e8ecf0 !important;
                border-radius: 12px !important;
                padding: 1rem !important;
                background: #ffffff !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
            }

            .compact-table tbody td {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 0.6rem 0 !important;
                border-bottom: 1px dashed #e8ecf0 !important;
                text-align: right !important; 
                border-top: none !important;
            }
            
            /* Action Buttons Row */
            .compact-table tbody td:last-child {
                border-bottom: none !important;
                padding-bottom: 0 !important;
                margin-top: 0.5rem;
                justify-content: flex-end !important;
            }

            .compact-table tbody td::before {
                font-weight: 700;
                color: #7a8799;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                text-align: left;
                margin-right: 1rem;
            }

            /* Fix Hover Dark Shadow inside mobile cards */
            .compact-table tbody tr:nth-of-type(odd) td {
                box-shadow: none !important;
                background-color: transparent !important;
            }

            /* --- COLUMN MAP FOR LEADS CRM TABLE --- */
            .compact-table tbody td:nth-child(1)::before { content: "Date"; }
            .compact-table tbody td:nth-child(2)::before { content: "Lead Name"; }
            .compact-table tbody td:nth-child(3)::before { content: "Contact Info"; }
            .compact-table tbody td:nth-child(4)::before { content: "Service Interest"; }
            .compact-table tbody td:nth-child(5)::before { content: "Source"; }
            .compact-table tbody td:nth-child(6)::before { content: "Marketer"; }
            .compact-table tbody td:nth-child(7)::before { content: "Status"; }
            .compact-table tbody td:nth-child(8)::before { content: "Actions"; display: none; }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="chq-wrapper">
    <header class="chq-hero">
        <div style="display:flex; justify-content:space-between; max-width: 1400px; margin: 0 auto; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size:2rem; font-weight:800; color:#333; margin:0;">Leads CRM</h1>
                <p style="color:#666; margin:0;">Track and convert your potential clients.</p>
            </div>
            
            <a href="<?php echo e(route('crm.leads.create')); ?>" class="btn btn-primary font-weight-bold shadow-sm" style="height:fit-content; border-radius:8px; background: #8b5cf6; border-color: #8b5cf6;">
                <i class="fas fa-plus mr-1"></i> Add New Lead
            </a>
        </div>
    </header>

    <div class="chq-main">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success font-weight-bold rounded-lg shadow-sm border-0"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="data-card">
            <div class="p-4">
                
                <div class="table-responsive">
                    <table id="leadsTable" class="table w-100 compact-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Lead Name</th>
                                <th>Contact Info</th>
                                <th>Service Interest</th>
                                <th>Source</th>
                                <th>Marketer</th>
                                <th>Status</th>
                                <th class="text-right pr-3">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
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
        $('#leadsTable').DataTable({
            processing: true, 
            serverSide: true,
            ajax: "<?php echo e(route('crm.leads.datatable')); ?>",
            columns: [
                {data: 'date', name: 'created_at'},
                {data: 'name', name: 'name', className: 'font-weight-bold text-dark'},
                {data: 'phone', name: 'phone', render: function(data, type, row) {
                    return data + (row.email ? '<br><small class="text-muted">' + row.email + '</small>' : '');
                }},
                {data: 'service_interested', name: 'service_interested', defaultContent: '-'},
                {data: 'source', name: 'source', defaultContent: '-'},
                {data: 'marketer_name', name: 'marketer.name'},
                {data: 'status_badge', name: 'status', searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right pr-3'}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make(auth()->user()->role === 'ADMIN' ? 'layouts.admin' : 'layouts.marketer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/admin/leads/index.blade.php ENDPATH**/ ?>