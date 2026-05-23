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
        <button class="btn btn-primary font-weight-bold shadow-sm" onclick="window.dispatchEvent(new CustomEvent('open-coupon-modal'))" style="border-radius: 8px;">
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
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('confirm-action', { detail: { form: this, title: 'Delete Promo?', message: 'Are you sure you want to permanently delete this promo?' } }));">
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

<!-- NEW ALPINE COUPON MODAL -->
<div x-data="{ open: false }" 
     id="createPromoModal"
     @open-coupon-modal.window="open = true"
     @keydown.escape.window="open = false"
     class="relative z-[9999]"
     x-cloak
     x-show="open">
    
    <!-- Backdrop Overlay -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
         @click="open = false"></div>

    <!-- Modal Panel Container -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-lg border border-gray-100">
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 m-0">Create New Promo Campaign</h3>
                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 transition-colors focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.coupons.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-5">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Promo Code <span class="text-red-500">*</span></label>
                            <input type="text" name="code" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 uppercase px-3 py-2 border" placeholder="e.g. ITR50-XJ9" required>
                            <p class="mt-1 text-xs text-gray-500">Agents will type this exactly at checkout.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Bonus Commission (₹) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="bonus_amount" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border" placeholder="50.00" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Target Specific Agents (Optional)</label>
                            <div wire:ignore>
                                <select name="target_agents[]" class="form-control select2-agents w-full" multiple="multiple">
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name }} (ID: {{ $agent->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Leave blank so ALL agents can use it.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Applicable Services (Optional)</label>
                            <div wire:ignore>
                                <select name="target_services[]" class="form-control select2-services w-full" multiple="multiple">
                                    @if(isset($services))
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Leave blank so it applies to ALL services.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Total Uses</label>
                                <input type="number" name="global_max_uses" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border" placeholder="e.g. 50">
                                <p class="mt-1 text-xs text-gray-500">Auto-expires after X uses.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Max Uses Per Agent</label>
                                <input type="number" name="max_uses_per_agent" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border" value="1" required>
                                <p class="mt-1 text-xs text-gray-500">Usually 1 time per agent.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-xl">
                        <button type="button" @click="open = false" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Create Coupon</button>
                    </div>
                </form>
            </div>
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