@extends('layouts.admin')

@section('title', 'Edit Service: ' . $service->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Edit Service</h1>
            <p class="text-muted mb-0 mt-1">Editing <code>{{ $service->slug }}</code></p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary font-weight-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Services
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">
        <form method="POST" action="{{ route('admin.services.update', $service) }}">
            @csrf
            @method('PUT')

            {{-- Service Details Card --}}
            <div class="card modern-card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-concierge-bell text-primary mr-2"></i> Service Details
                    </h3>
                </div>

                <div class="card-body pt-0 px-4 pb-4">
                    {{-- Name & Slug --}}
                    <div class="row">
                        
                        <div class="col-md-6 form-group mb-4">
                            <label class="form-label-custom">Service Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                        <i class="fas fa-tag text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" name="name" id="serviceName"
                                    class="form-control custom-input border-left-0 @error('name') is-invalid @enderror"
                                    value="{{ old('name', $service->name) }}" required>
                            </div>
                            @error('name')
                                <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group mb-4">
                            <label class="form-label-custom">Slug <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                        <i class="fas fa-link text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" name="slug" id="serviceSlug"
                                    class="form-control custom-input border-left-0 @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $service->slug) }}" required
                                    style="font-family: monospace;">
                            </div>
                            @error('slug')
                                <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="form-label-custom">Primary Data Field (Optional)</label>
                            <input type="text" name="primary_data_field" class="form-control custom-input" value="{{ old('primary_data_field', $service->primary_data_field ?? '') }}" placeholder="e.g., gst_number">
                            <small class="text-muted">Type the exact input name to display it on the Applications List.</small>
                        </div>

                        <div class="col-md-6 form-group mt-3">
                            <label class="form-label-custom"><i class="fab fa-whatsapp text-success"></i> WhatsApp Number Field (Optional)</label>
                            <input type="text" name="whatsapp_number_field" class="form-control custom-input" value="{{ old('whatsapp_number_field', $service->whatsapp_number_field ?? '') }}" placeholder="e.g., contact_mobile">
                            <small class="text-muted">Field name for the client's phone number.</small>
                        </div>

                        <div class="col-md-6 form-group mt-3">
                            <label class="form-label-custom"><i class="fas fa-envelope text-primary"></i> Applicant Email Field (Optional)</label>
                            <input type="text" name="applicant_email_field" class="form-control custom-input" value="{{ old('applicant_email_field', $service->applicant_email_field ?? '') }}" placeholder="e.g., firm_email">
                            <small class="text-muted">Field name for the client's email address.</small>
                        </div>

                        <div class="col-md-6 form-group mt-3">
                            <label class="form-label-custom"><i class="fas fa-sort-numeric-down text-info"></i> Sort Order</label>
                            <input type="number" name="sort_order" class="form-control custom-input" value="{{ old('sort_order', $service->sort_order ?? 0) }}">
                            <small class="text-muted">Lower numbers appear first (e.g., 1, 2, 3).</small>
                        </div>
                    </div>


                    {{-- Description --}}
                    <div class="form-group mb-4 mt-3">
                        <label class="form-label-custom">Description</label>
                        <textarea name="description" rows="3"
                            class="form-control custom-input @error('description') is-invalid @enderror"
                            style="height: auto;">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                        @enderror
                    </div>

                    <hr class="my-4 border-light">

                    {{-- Pricing & Commission (DYNAMIC SWITCH) --}}
                    @if($service->slug !== 'gst-return-filing')
                        {{-- STANDARD FLAT PRICING (For all normal services) --}}
                        <div class="row">
                            <div class="col-md-4 form-group mb-4">
                                <label class="form-label-custom">Price (₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                            <i class="fas fa-rupee-sign text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="number" name="price" step="0.01" min="0"
                                        class="form-control custom-input border-left-0 @error('price') is-invalid @enderror"
                                        value="{{ old('price', $service->price) }}" required>
                                </div>
                                @error('price')
                                    <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group mb-4">
                                <label class="form-label-custom">Commission Type <span class="text-danger">*</span></label>
                                <select name="commission_type"
                                    class="form-control custom-input @error('commission_type') is-invalid @enderror" required>
                                    <option value="flat" {{ old('commission_type', $service->commission_type) === 'flat' ? 'selected' : '' }}>Flat (₹)</option>
                                    <option value="percentage" {{ old('commission_type', $service->commission_type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                </select>
                                @error('commission_type')
                                    <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group mb-4">
                                <label class="form-label-custom">Commission Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                            <i class="fas fa-coins text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="number" name="commission_value" step="0.01" min="0"
                                        class="form-control custom-input border-left-0 @error('commission_value') is-invalid @enderror"
                                        value="{{ old('commission_value', $service->commission_value) }}" required>
                                </div>
                                @error('commission_value')
                                    <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @else

                    <input type="hidden" name="price" value="0">
                        <input type="hidden" name="commission_type" value="flat">
                        <input type="hidden" name="commission_value" value="0">

                        
                        {{-- ENTERPRISE DYNAMIC PRICING MATRIX (Only for GST Return) --}}
                        <div class="card border border-primary rounded-lg mb-4 shadow-sm" style="overflow: hidden;">
                            <div class="card-header bg-primary text-white font-weight-bold py-3">
                                <i class="fas fa-table mr-2"></i> Dynamic Pricing Matrix
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-bordered mb-0" id="pricing-matrix-table">
                                    <thead class="bg-light text-xs text-muted text-uppercase">
                                        <tr>
                                            <th>GST Type</th>
                                            <th>Turnover Range</th>
                                            <th>Frequency</th>
                                            <th>Plan</th>
                                            <th>Base Price (₹)</th>
                                            <th>VLE Commission (₹)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($service->pricingRules) && $service->pricingRules->count() > 0)
                                            @foreach($service->pricingRules as $index => $rule)
                                            <tr>
                                                <td><input type="text" name="pricing_rules[{{$index}}][gst_type]" class="form-control custom-input" value="{{ $rule->gst_type }}"></td>
                                                <td><input type="text" name="pricing_rules[{{$index}}][turnover]" class="form-control custom-input" value="{{ $rule->turnover }}"></td>
                                                <td><input type="text" name="pricing_rules[{{$index}}][frequency]" class="form-control custom-input" value="{{ $rule->frequency }}"></td>
                                                <td><input type="text" name="pricing_rules[{{$index}}][plan]" class="form-control custom-input" value="{{ $rule->plan }}"></td>
                                                <td><input type="number" step="0.01" name="pricing_rules[{{$index}}][base_price]" class="form-control custom-input" value="{{ $rule->base_price }}" required></td>
                                                <td><input type="number" step="0.01" name="pricing_rules[{{$index}}][commission_amount]" class="form-control custom-input" value="{{ $rule->commission_amount }}"></td>
                                                <td class="text-center align-middle"><button type="button" class="btn btn-danger btn-sm rounded" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <div class="p-3 bg-light border-top">
                                    <button type="button" class="btn btn-primary btn-sm font-weight-bold shadow-sm" onclick="addPricingRow()">
                                        <i class="fas fa-plus mr-1"></i> Add Pricing Rule
                                    </button>
                                    <small class="text-muted ml-3">Type exactly what appears in the form builder dropdowns (e.g., "regular", "monthly"). Use empty boxes as wildcards.</small>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Form Builder --}}
            @include('admin.services.partials.form-builder')

            {{-- Submit --}}
            <div class="d-flex justify-content-end mt-4 mb-4">
                <a href="{{ route('admin.services.index') }}" class="btn btn-light font-weight-bold mr-2 text-muted">
                    Cancel
                </a>
                <button type="submit" class="btn btn-success font-weight-bold shadow-sm px-4">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection

@section('css')
    <style>
        .modern-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }
        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }
        .custom-icon-box {
            border-color: #cbd5e1;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .custom-input {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
            height: 42px;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.2s ease;
        }
        .custom-input:focus {
            border-color: #0044b2;
            box-shadow: 0 0 0 3px rgba(0, 68, 178, 0.15);
            outline: none;
        }
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important;
        }
        .btn-success {
            background-color: #00b259;
            border-color: #00b259;
            transition: all 0.2s;
            border-radius: 8px;
        }
        .btn-success:hover {
            background-color: #00964b;
            border-color: #00964b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 178, 89, 0.2) !important;
        }
        .btn-light {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            border-radius: 8px;
        }
        .btn-light:hover {
            background-color: #f1f5f9;
            color: #1e293b !important;
        }
    </style>
    @stack('css')
@endsection

@section('js')
    @stack('js')
    @if($service->slug === 'gst-return-filing')
    <script>
        let rowIndex = {{ isset($service->pricingRules) ? $service->pricingRules->count() : 0 }};
        function addPricingRow() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="pricing_rules[${rowIndex}][gst_type]" class="form-control custom-input" placeholder="e.g. regular"></td>
                <td><input type="text" name="pricing_rules[${rowIndex}][turnover]" class="form-control custom-input" placeholder="e.g. upto_1_5"></td>
                <td><input type="text" name="pricing_rules[${rowIndex}][frequency]" class="form-control custom-input" placeholder="e.g. monthly"></td>
                <td><input type="text" name="pricing_rules[${rowIndex}][plan]" class="form-control custom-input" placeholder="e.g. yearly"></td>
                <td><input type="number" step="0.01" name="pricing_rules[${rowIndex}][base_price]" class="form-control custom-input" placeholder="0.00" required></td>
                <td><input type="number" step="0.01" name="pricing_rules[${rowIndex}][commission_amount]" class="form-control custom-input" placeholder="0.00"></td>
                <td class="text-center align-middle"><button type="button" class="btn btn-danger btn-sm rounded" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
            `;
            document.querySelector('#pricing-matrix-table tbody').appendChild(tr);
            rowIndex++;
        }
    </script>
    @endif
@endsection