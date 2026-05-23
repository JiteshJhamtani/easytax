@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.marketer')
@section('title', 'VLE Customers | EasyTax')

@section('content')
<div class="chq-wrapper p-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-4 mb-4 rounded-lg shadow-sm text-center text-md-left" style="background-color: #f3e8ff;">
        <div class="mb-3 mb-md-0">
            <h2 class="font-weight-bold text-dark mb-1">VLE Customers</h2>
            <p class="text-muted mb-0" style="color: #6b7280 !important;">Track and manage your high-value Village Level Entrepreneurs.</p>
        </div>
        <div>
            <a href="{{ route('crm.leads.vle.create') }}" class="btn font-weight-bold text-white shadow-sm px-4 py-2" style="background-color: #8b5cf6; border-radius: 8px;">
                <i class="fas fa-plus mr-2"></i> Add VLE Customer
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="responsive-card-table table table-hover w-100" id="vle-table">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3">Date</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3">Lead Name</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3">Contact Info</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3">Service Interest</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3">Total Leads</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3">Status</th>
                            <th class="border-0 text-muted text-xs font-weight-bold text-uppercase pb-3 pt-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#vle-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('crm.leads.vle.data') }}",
        columns: [
            { data: 'date', name: 'created_at' },
            { data: 'name', name: 'name', class: 'font-weight-bold text-dark align-middle' },
            { data: 'contact_info', name: 'phone', class: 'align-middle' },
            { data: 'service_interested', name: 'service_interested', class: 'align-middle text-muted' },
            { data: 'amount', name: 'amount', class: 'align-middle font-weight-bold', render: function(data) { return data + ' Leads'; } },
            { data: 'status_badge', name: 'status', class: 'align-middle' },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'align-middle text-right' }
        ],
        order: [[0, 'desc']],
        language: {
            search: "Search VLEs:",
            lengthMenu: "Show _MENU_ entries"
        }
    });
});
</script>
@endsection