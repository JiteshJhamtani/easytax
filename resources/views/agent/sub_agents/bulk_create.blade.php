@extends('layouts.agent')

@section('title', 'Bulk Onboard Team Members | EasyTax')

@section('css')
    <style>
        .chq-hero {
            background-color: var(--green-light);
            padding: 2.2rem 2.5rem 5rem;
            border-bottom: 1px solid #e2efe9;
        }
        .chq-hero-flex {
            max-width: 1200px;
            margin: 0 auto;
        }
        .chq-hero-title h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--slate);
            margin: 0 0 0.25rem;
        }
        .chq-hero-title p {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin: 0;
        }
        .chq-main {
            max-width: 1200px;
            margin: -3.5rem auto 3rem;
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }
        .card-box {
            background: #ffffff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .nav-tabs-custom {
            border-bottom: 1px solid var(--border);
            background: #f8fafc;
            padding: 0.5rem 1.5rem 0;
        }
        .nav-tabs-custom .nav-link {
            border: 1px solid transparent;
            color: var(--text-muted);
            font-weight: 700;
            padding: 0.75rem 1.25rem;
            border-radius: 8px 8px 0 0;
        }
        .nav-tabs-custom .nav-link.active {
            color: var(--green);
            background: #ffffff;
            border-color: var(--border) var(--border) #ffffff;
        }
        .btn-brand-green {
            background-color: var(--green);
            color: #ffffff !important;
            font-weight: 700;
            border-radius: 8px;
            padding: 0.6rem 1.3rem;
            border: none;
            transition: all 0.2s;
        }
        .btn-brand-green:hover {
            background-color: var(--green-dark);
            transform: translateY(-1px);
        }
        .csv-dropzone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
        }
        .csv-dropzone:hover {
            border-color: var(--green);
            background: #f1f9f5;
        }
    </style>
@endsection

@section('content')
<div class="chq-wrapper">
    <div class="chq-hero">
        <div class="chq-hero-flex d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="chq-hero-title">
                <h1>Bulk Onboard Team Members</h1>
                <p>Quickly add multiple sub-agents using either our interactive multi-row table or by uploading a CSV spreadsheet.</p>
            </div>
            <a href="{{ route('agent.sub-agents.index') }}" class="btn btn-light border font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Back to Team
            </a>
        </div>
    </div>

    <div class="chq-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4">
                <i class="fas fa-check-circle mr-2"></i> {!! session('success') !!}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i> {!! session('error') !!}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="card-box">
            <!-- Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom" id="bulkTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="table-tab" data-toggle="tab" href="#tablePane" role="tab">
                        <i class="fas fa-table mr-1"></i> Interactive Quick Add (Multi-Row)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="csv-tab" data-toggle="tab" href="#csvPane" role="tab">
                        <i class="fas fa-file-csv mr-1"></i> CSV Spreadsheet Upload
                    </a>
                </li>
            </ul>

            <div class="tab-content p-4" id="bulkTabContent">
                <!-- TAB 1: Interactive Multi-Row Table -->
                <div class="tab-pane fade show active" id="tablePane" role="tabpanel">
                    <form method="POST" action="{{ route('agent.sub-agents.bulk-store') }}">
                        @csrf
                        <p class="text-muted small mb-3">Add as many members as you need. Hierarchical agent codes will be generated automatically for each row.</p>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="multiRowTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 24%;">Full Name <span class="text-danger">*</span></th>
                                        <th style="width: 26%;">Email (Username) <span class="text-danger">*</span></th>
                                        <th style="width: 18%;">Password <span class="text-danger">*</span></th>
                                        <th style="width: 16%;">Mobile No.</th>
                                        <th style="width: 16%;">WhatsApp No.</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="multiRowBody">
                                    <tr>
                                        <td><input type="text" name="members[0][name]" class="form-control form-control-sm" placeholder="e.g. Rahul Sharma" required></td>
                                        <td><input type="email" name="members[0][email]" class="form-control form-control-sm" placeholder="rahul@example.com" required></td>
                                        <td><input type="password" name="members[0][password]" class="form-control form-control-sm" placeholder="Min 6 chars" required minlength="6"></td>
                                        <td><input type="text" name="members[0][mobile_number]" class="form-control form-control-sm" placeholder="10 digits"></td>
                                        <td><input type="text" name="members[0][whatsapp_no]" class="form-control form-control-sm" placeholder="10 digits"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" disabled><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="members[1][name]" class="form-control form-control-sm" placeholder="e.g. Priya Patel" required></td>
                                        <td><input type="email" name="members[1][email]" class="form-control form-control-sm" placeholder="priya@example.com" required></td>
                                        <td><input type="password" name="members[1][password]" class="form-control form-control-sm" placeholder="Min 6 chars" required minlength="6"></td>
                                        <td><input type="text" name="members[1][mobile_number]" class="form-control form-control-sm" placeholder="10 digits"></td>
                                        <td><input type="text" name="members[1][whatsapp_no]" class="form-control form-control-sm" placeholder="10 digits"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold" id="addRowBtn">
                                <i class="fas fa-plus mr-1"></i> Add Another Row
                            </button>
                            <button type="submit" class="btn-brand-green">
                                <i class="fas fa-save mr-1"></i> Save All Team Members
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: CSV Upload -->
                <div class="tab-pane fade" id="csvPane" role="tabpanel">
                    <div class="row">
                        <div class="col-md-7">
                            <form method="POST" action="{{ route('agent.sub-agents.import-csv') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="csv-dropzone mb-3" onclick="$('#csvFileInput').click();">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                    <h6 class="font-weight-bold mb-1">Click to select or drop CSV file here</h6>
                                    <p class="text-muted small mb-2">Supported format: .csv (Max 5MB)</p>
                                    <input type="file" name="csv_file" id="csvFileInput" class="d-none" accept=".csv,text/csv" required onchange="displaySelectedFile(this)">
                                    <div id="fileSelectedDisplay" class="font-weight-bold text-success mt-2"></div>
                                </div>
                                <button type="submit" class="btn-brand-green w-100">
                                    <i class="fas fa-file-import mr-1"></i> Upload & Onboard Members
                                </button>
                            </form>
                        </div>

                        <div class="col-md-5">
                            <div class="card bg-light border-0 p-3 h-100">
                                <h6 class="font-weight-bold text-dark"><i class="fas fa-info-circle text-primary mr-1"></i> CSV Instructions</h6>
                                <p class="small text-muted mb-2">Ensure your CSV contains these columns in header:</p>
                                <code class="d-block p-2 bg-white rounded border mb-3 small">name, email, password, mobile_number, whatsapp_no</code>
                                <ul class="small text-muted pl-3 mb-3">
                                    <li>Email must be unique across the platform.</li>
                                    <li>Password must be at least 6 characters.</li>
                                    <li>Hierarchical codes (e.g. <code>AGT-000005-01</code>) are assigned automatically.</li>
                                </ul>
                                <a href="{{ route('agent.sub-agents.download-template') }}" class="btn btn-sm btn-outline-success font-weight-bold mt-auto">
                                    <i class="fas fa-download mr-1"></i> Download Sample CSV Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
let rowCount = 2;

$('#addRowBtn').on('click', function() {
    const row = `
        <tr>
            <td><input type="text" name="members[${rowCount}][name]" class="form-control form-control-sm" placeholder="Full Name" required></td>
            <td><input type="email" name="members[${rowCount}][email]" class="form-control form-control-sm" placeholder="email@example.com" required></td>
            <td><input type="password" name="members[${rowCount}][password]" class="form-control form-control-sm" placeholder="Min 6 chars" required minlength="6"></td>
            <td><input type="text" name="members[${rowCount}][mobile_number]" class="form-control form-control-sm" placeholder="Mobile"></td>
            <td><input type="text" name="members[${rowCount}][whatsapp_no]" class="form-control form-control-sm" placeholder="WhatsApp"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
    $('#multiRowBody').append(row);
    rowCount++;
    updateRemoveButtons();
});

$(document).on('click', '.remove-row-btn', function() {
    if ($('#multiRowBody tr').length > 1) {
        $(this).closest('tr').remove();
        updateRemoveButtons();
    }
});

function updateRemoveButtons() {
    const totalRows = $('#multiRowBody tr').length;
    $('.remove-row-btn').prop('disabled', totalRows <= 1);
}

function displaySelectedFile(input) {
    if (input.files && input.files[0]) {
        $('#fileSelectedDisplay').html('<i class="fas fa-check-circle mr-1"></i> Selected: ' + input.files[0].name);
    }
}
</script>
@endsection
