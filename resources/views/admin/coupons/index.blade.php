@extends('layouts.admin')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Makes Select2 look good inside bootstrap */ 
        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        /* ── DESKTOP TABLE STYLES ── */
        .table-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        
        .promo-table {
            border-collapse: collapse !important;
            width: 100% !important; /* Forces table to fit screen */
            margin-bottom: 0 !important;
        }
        
        .promo-table thead th {
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
            
        }
        
        .promo-table tbody td {
            white-space: normal !important; 
            word-break: break-word !important; 
            padding: 1rem 0.75rem !important;
            vertical-align: middle;
            font-size: 0.9rem !important;
            border-bottom: 1px solid #f1f5f9;
        }
        
        /* Protect the Action Column on Desktop */
        .promo-table th:last-child,
        .promo-table td:last-child {
            min-width: 100px;
            
        }

        
    </style>
@endsection

@section('title', 'Manage Promo Campaigns')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold text-dark mb-0">🎟️ Promo Codes & Campaigns</h3>
            <p class="text-muted mb-0">Create and manage bonus commissions for your agents.</p>
        </div>
        <button class="btn btn-primary font-weight-bold shadow-sm" data-toggle="modal" data-target="#createPromoModal" style="border-radius: 8px;">
            <i class="fas fa-plus mr-1"></i> Create New Coupon
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 8px;">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 8px;">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="responsive-card-table table table-hover mb-0 promo-table">
                    <thead>
                        <tr>
                            <th class="pl-4">Promo Code</th>
                            <th>Bonus (₹)</th>
                            <th>Target Agent</th>
                            <th>Usage Limit</th> 
                            <th>Status</th>
                            <th class="text-right pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                        <tr>
                            <td class="pl-4 font-weight-bold text-primary">{{ $coupon->code }}</td>
                            <td class="font-weight-bold text-success">+ ₹{{ number_format($coupon->bonus_amount, 2) }}</td>
                            <td>
                                <div>
                                    @php
                                        $targets = $coupon->target_agents ? json_decode($coupon->target_agents, true) : [];
                                    @endphp
                                    
                                    @if(is_array($targets) && count($targets) > 0)
                                        <span class="badge badge-dark mb-1" style="border-radius: 6px;">
                                            {{ count($targets) }} Agent(s) Targeted
                                        </span>
                                        <div class="text-xs text-muted" style="max-width: 150px; word-wrap: break-word;">
                                            IDs: {{ implode(', ', $targets) }}
                                        </div>
                                    @else
                                        <span class="badge badge-light border text-muted">All Agents</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <strong>{{ $coupon->total_used }}</strong> used <br>
                                    <span class="text-muted">of {{ $coupon->global_max_uses ?? 'Unlimited' }} total</span>
                                </div>
                            </td>
                            <td>
                                @if($coupon->is_active)
                                    <span style="background-color: #e6f4ea; color: #1e8e3e; padding: 5px 12px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Active
                                    </span>
                                @else
                                    <span style="background-color: #fce8e6; color: #d93025; padding: 5px 12px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Disabled
                                    </span>
                                @endif
                            </td>
                            <td class="pr-4">
                                <div class="d-flex justify-content-end align-items-center">
                                    <form action="{{ route('admin.coupons.toggle', $coupon->id) }}" method="POST" class="d-inline mr-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $coupon->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="Toggle Status">
                                            <i class="fas {{ $coupon->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this promo permanently?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-ticket-alt fa-3x mb-3 text-light"></i>
                                <h5>No Promos Found</h5>
                                <p>Click the button above to create your first campaign.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createPromoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header bg-light" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold text-dark">Create New Promo Campaign</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Promo Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="e.g. ITR50-XJ9" required style="border-radius: 8px;">
                        <small class="text-muted">Agents will type this exactly at checkout.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Bonus Commission (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="bonus_amount" class="form-control" placeholder="50.00" required style="border-radius: 8px;">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Target Specific Agents (Optional)</label>
                        <select name="target_agents[]" class="form-control select2-agents" multiple="multiple" style="width: 100%;">
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }} (ID: {{ $agent->id }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Leave blank so ALL agents can use it.</small>
                    </div>

                    {{-- NEW: Target Specific Services --}}
                    <div class="form-group mt-3">
                        <label class="font-weight-bold">Applicable Services (Optional)</label>
                        <select name="target_services[]" class="form-control select2-services" multiple="multiple" style="width: 100%;">
                            {{-- Assuming you pass $services from your controller --}}
                            @if(isset($services))
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted">Leave blank so it applies to ALL services.</small>
                    </div>
                    {{-- END NEW --}}

                    <div class="row mt-3">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Total Campaign Uses</label>
                            <input type="number" name="global_max_uses" class="form-control" placeholder="e.g. 50" style="border-radius: 8px;">
                            <small class="text-muted">Auto-expires after X uses.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Max Uses Per Agent</label>
                            <input type="number" name="max_uses_per_agent" class="form-control" value="1" required style="border-radius: 8px;">
                            <small class="text-muted">Usually 1 time per agent.</small>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm" style="border-radius: 8px;">Create Coupon</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix the modal trap
        $('#createPromoModal').appendTo('body');
        
        // Initialize the agents multi-select
        $('.select2-agents').select2({
            placeholder: "Search and select agents...",
            allowClear: true,
            dropdownParent: $('#createPromoModal')
        });

        // Initialize the services multi-select
        $('.select2-services').select2({
            placeholder: "Search and select services...",
            allowClear: true,
            dropdownParent: $('#createPromoModal')
        });
    });
</script>
@endsection