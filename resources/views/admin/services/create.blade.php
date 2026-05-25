@extends('layouts.admin')

@section('title', 'Create Service')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <h1 class="m-0 text-dark font-weight-bold">Create New Service</h1>
        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary font-weight-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Services
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">
        <form method="POST" action="{{ route('admin.services.store') }}">
            @csrf

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
                                    value="{{ old('name') }}" placeholder="e.g. GST Registration" required>
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
                                    value="{{ old('slug') }}" placeholder="gst-registration" required
                                    style="font-family: monospace;">
                            </div>
                            @error('slug')
                                <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="form-group mb-4">
                        <label class="form-label-custom">Description</label>
                        <textarea name="description" rows="3"
                            class="form-control custom-input @error('description') is-invalid @enderror"
                            placeholder="Brief description of this service"
                            style="height: auto;">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                        @enderror
                    </div>

                    <hr class="my-4 border-light">

                    {{-- Pricing & Commission --}}
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
                                    value="{{ old('price') }}" placeholder="0.00" required>
                            </div>
                            @error('price')
                                <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-4">
                            <label class="form-label-custom">Commission Type <span class="text-danger">*</span></label>
                            <select name="commission_type"
                                class="form-control custom-input @error('commission_type') is-invalid @enderror" required>
                                <option value="flat" {{ old('commission_type') === 'flat' ? 'selected' : '' }}>Flat (₹)</option>
                                <option value="percentage" {{ old('commission_type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
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
                                    value="{{ old('commission_value') }}" placeholder="0.00" required>
                            </div>
                            @error('commission_value')
                                <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Builder --}}
            @include('admin.services.partials.form-builder')

            {{-- Submit --}}
            <div class="d-flex justify-content-end mt-4 mb-4">
                <a href="{{ route('admin.services.index') }}" class="btn btn-light font-weight-bold mr-2 text-muted">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4">
                    <i class="fas fa-plus-circle mr-1"></i> Create Service
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
        .btn-primary {
            background-color: #0044b2;
            border-color: #0044b2;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #00368c;
            border-color: #00368c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 68, 178, 0.2) !important;
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
    <script>
        // Auto-generate slug from name
        document.getElementById('serviceName').addEventListener('input', function() {
            let slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            document.getElementById('serviceSlug').value = slug;
        });
    </script>
    @stack('js')
@endsection
