@extends(auth()->user()->role === 'ADMIN' ? 'layouts.admin' : 'layouts.marketer')
@section('title', 'Leads CRM | EasyTax')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>
        .chq-hero { background-color: #f3e8ff; padding: 2.5rem 3rem 6rem; border-bottom: 1px solid #e9d5ff; }
        .chq-main { max-width: 1400px; margin: -3.5rem auto 3rem; padding: 0 1.5rem; position: relative; z-index: 10; }
        .data-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e8ecf0; overflow: hidden; }
        
        .compact-table { width: 100% !important; table-layout: auto !important; }
        .compact-table th, .compact-table td { padding: 8px 10px !important; font-size: 0.8rem !important; vertical-align: middle !important; border-bottom: 1px solid #f3f4f6 !important; word-break: break-word;}
        .compact-table thead th { border-bottom: 2px solid #e8ecf0 !important; color: #7a8799; text-transform: uppercase; font-size: 0.7rem; font-weight: 700; }
        
        .badge-info-soft { background-color: #e0f2fe; color: #0284c7; }
        .badge-warning-soft { background-color: #fef3c7; color: #d97706; }
        .badge-primary-soft { background-color: #e0e7ff; color: #4f46e5; }
        .badge-success-soft { background-color: #dcfce7; color: #166534; }
        .badge-danger-soft { background-color: #fee2e2; color: #991b1b; }
    </style>
@endsection

@section('content')
<div class="chq-wrapper">
    <header class="chq-hero">
        <div style="display:flex; justify-content:space-between; max-width: 1400px; margin: 0 auto;">
            <div>
                <h1 style="font-size:2rem; font-weight:800; color:#333; margin:0;">Leads CRM</h1>
                <p style="color:#666; margin:0;">Track and convert your potential clients.</p>
            </div>
            {{-- This button now correctly points to the new Create page --}}
            <a href="{{ route('crm.leads.create') }}" class="btn btn-primary font-weight-bold shadow-sm" style="height:fit-content; border-radius:8px; background: #8b5cf6; border-color: #8b5cf6;">
                <i class="fas fa-plus mr-1"></i> Add New Lead
            </a>
        </div>
    </header>

    <div class="chq-main">
        @if(session('success'))
            <div class="alert alert-success font-weight-bold rounded-lg shadow-sm border-0">{{ session('success') }}</div>
        @endif

        <div class="data-card">
            <div class="p-4">
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
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#leadsTable').DataTable({
            processing: true, serverSide: true,
            ajax: "{{ route('crm.leads.datatable') }}",
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
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right'}
            ],
            order: [[0, 'desc']]
        });
    });

    // Function to open the Update Status modal and set the form action dynamically
    
</script>
@endsection