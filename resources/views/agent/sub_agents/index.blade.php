@extends('layouts.agent')

@section('title', 'My Team & Sub-Agents | EasyTax')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>
        .chq-hero {
            background-color: var(--green-light);
            padding: 2.2rem 2.5rem 5.5rem;
            border-bottom: 1px solid #e2efe9;
        }
        .chq-hero-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.25rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        .chq-hero-title h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--slate);
            margin: 0 0 0.3rem;
            letter-spacing: -0.02em;
        }
        .chq-hero-title p {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin: 0;
        }
        .chq-main {
            max-width: 1400px;
            margin: -3.5rem auto 3rem;
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }
        .kpi-card {
            background: #ffffff;
            border-radius: var(--radius);
            padding: 1.3rem 1.4rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 1.1rem;
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .kpi-val {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--slate);
            line-height: 1.1;
        }
        .kpi-lbl {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }
        .card-box {
            background: #ffffff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .card-header-bar {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            background: #ffffff;
        }
        .bulk-toolbar {
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.5rem;
            display: none;
            align-items: center;
            gap: 0.75rem;
        }
        .btn-brand-green {
            background-color: var(--green);
            color: #ffffff !important;
            font-weight: 700;
            border-radius: 8px;
            padding: 0.55rem 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            transition: all 0.2s;
        }
        .btn-brand-green:hover {
            background-color: var(--green-dark);
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('content')
<div class="chq-wrapper">
    <!-- Hero Header -->
    <div class="chq-hero">
        <div class="chq-hero-flex">
            <div class="chq-hero-title">
                <h1>My Agency Team (Sub-Agents)</h1>
                <p>Onboard and manage your team members, set their custom service pricing, and track your extra margin earnings.</p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <a href="{{ route('agent.team-pricing.index') }}" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 8px;">
                    <i class="fas fa-tags mr-1"></i> Team Pricing
                </a>
                <a href="{{ route('agent.margin-ledger.index') }}" class="btn btn-outline-success font-weight-bold" style="border-radius: 8px;">
                    <i class="fas fa-coins mr-1"></i> Margin Earnings
                </a>
                <a href="{{ route('agent.sub-agents.bulk-create') }}" class="btn btn-light border font-weight-bold" style="border-radius: 8px;">
                    <i class="fas fa-file-csv mr-1"></i> Bulk Onboard
                </a>
                <a href="{{ route('agent.sub-agents.create') }}" class="btn-brand-green">
                    <i class="fas fa-user-plus"></i> Add Team Member
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="chq-main">
        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {!! session('success') !!}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i> {!! session('error') !!}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- KPI Grid -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="kpi-val">{{ number_format($kpis['total_members']) }}</div>
                    <div class="kpi-lbl">Total Team Members</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <div class="kpi-val">{{ number_format($kpis['active_members']) }}</div>
                    <div class="kpi-lbl">Active Members</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fef3c7; color:#d97706;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <div class="kpi-val">{{ number_format($kpis['team_applications']) }}</div>
                    <div class="kpi-lbl">Team Applications</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#ede9fe; color:#7c3aed;">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <div class="kpi-val">₹{{ number_format($kpis['total_margin_earned'], 2) }}</div>
                    <div class="kpi-lbl">Total Margin Earned</div>
                </div>
            </div>
        </div>

        <!-- DataTable Container -->
        <div class="card-box">
            <div class="card-header-bar">
                <h5 class="mb-0 font-weight-bold" style="color:var(--slate);">All Sub-Agents</h5>
                <span class="text-muted small">Session: <strong>{{ $currentSessionLabel }}</strong></span>
            </div>

            <!-- Bulk Toolbar -->
            <form id="bulkActionForm" method="POST" action="{{ route('agent.sub-agents.bulk-action') }}">
                @csrf
                <input type="hidden" name="action" id="bulkActionType" value="">
                <input type="hidden" name="bulk_password" id="bulkActionPassword" value="">

                <div class="bulk-toolbar" id="bulkToolbar">
                    <span class="text-muted small mr-2"><strong id="selectedCount">0</strong> selected</span>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="submitBulkAction('activate')">
                        <i class="fas fa-user-check mr-1"></i> Activate
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="submitBulkAction('deactivate')">
                        <i class="fas fa-user-slash mr-1"></i> Suspend
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openBulkPasswordModal()">
                        <i class="fas fa-key mr-1"></i> Reset Passwords
                    </button>
                </div>

                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle w-100" id="subAgentsTable">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="selectAll"></th>
                                <th>Agent Code</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Applications</th>
                                <th>Margin Earned</th>
                                <th>Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Single Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" action="" id="resetPasswordForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Reset Password: <span id="resetMemberName"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required minlength="6">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password" required minlength="6">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Update Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Password Modal -->
<div class="modal fade" id="bulkPasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Bulk Password Reset</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Set a new temporary password for all selected team members.</p>
                <div class="form-group">
                    <label class="font-weight-bold">New Common Password</label>
                    <input type="password" id="bulkNewPassword" class="form-control" placeholder="Minimum 6 characters" minlength="6">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary font-weight-bold" onclick="confirmBulkPassword()">Apply to Selected</button>
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
    const table = $('#subAgentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("agent.sub-agents.data") }}',
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'agent_code', name: 'agent_code', render: data => '<code>' + data + '</code>' },
            { data: 'name', name: 'name', render: (data, type, row) => '<strong>' + data + '</strong>' },
            { data: 'email', name: 'email' },
            { data: 'mobile_number', name: 'mobile_number', defaultContent: '-' },
            { data: 'applications', name: 'applications', searchable: false },
            { data: 'margin_earned', name: 'margin_earned', searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search team members...",
        }
    });

    // Handle select all checkbox
    $('#selectAll').on('change', function() {
        const checked = $(this).is(':checked');
        $('.subagent-select').prop('checked', checked);
        updateBulkToolbar();
    });

    $(document).on('change', '.subagent-select', function() {
        updateBulkToolbar();
    });

    function updateBulkToolbar() {
        const count = $('.subagent-select:checked').length;
        $('#selectedCount').text(count);
        if (count > 0) {
            $('#bulkToolbar').css('display', 'flex');
        } else {
            $('#bulkToolbar').hide();
            $('#selectAll').prop('checked', false);
        }
    }

    window.submitBulkAction = function(actionType) {
        if ($('.subagent-select:checked').length === 0) {
            alert('Please select at least one team member.');
            return;
        }
        if (!confirm('Are you sure you want to ' + actionType + ' selected members?')) {
            return;
        }
        $('#bulkActionType').val(actionType);
        // Append selected IDs to form
        $('.subagent-select:checked').each(function() {
            $('#bulkActionForm').append('<input type="hidden" name="sub_agent_ids[]" value="' + $(this).val() + '">');
        });
        $('#bulkActionForm').submit();
    };

    window.openBulkPasswordModal = function() {
        if ($('.subagent-select:checked').length === 0) {
            alert('Please select at least one team member.');
            return;
        }
        $('#bulkPasswordModal').modal('show');
    };

    window.confirmBulkPassword = function() {
        const pass = $('#bulkNewPassword').val();
        if (!pass || pass.length < 6) {
            alert('Password must be at least 6 characters.');
            return;
        }
        $('#bulkActionType').val('reset_password');
        $('#bulkActionPassword').val(pass);
        $('.subagent-select:checked').each(function() {
            $('#bulkActionForm').append('<input type="hidden" name="sub_agent_ids[]" value="' + $(this).val() + '">');
        });
        $('#bulkActionForm').submit();
    };

    window.openResetPasswordModal = function(id, name) {
        $('#resetMemberName').text(name);
        $('#resetPasswordForm').attr('action', '/agent/team/' + id + '/reset-password');
        $('#resetPasswordModal').modal('show');
    };
});
</script>
@endsection
