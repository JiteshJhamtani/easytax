@extends('layouts.admin')

@section('title', 'Edit Agent: ' . $agent->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Edit Agent</h1>
            <p class="text-muted mb-0 mt-1">Updating profile for {{ $agent->agent_code }}</p>
        </div>
        <a href="javascript:history.back()" class="btn-back-modern"><i class="fas fa-arrow-left"></i> Back to Agents</a>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <div class="card modern-card border-0 shadow-sm">
                    <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-user-edit text-primary mr-2"></i> Agent Account Details
                        </h3>
                    </div>

                    <div class="card-body pt-0 px-4 pb-4">
                        <form method="POST" action="{{ route('admin.agents.update', $agent) }}">
                            @csrf
                            @method('PUT')

                            {{-- Info Row --}}
                            <div class="row">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-user text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="name"
                                            class="form-control custom-input border-left-0 @error('name') is-invalid @enderror"
                                            value="{{ old('name', $agent->name) }}" required>
                                    </div>
                                    @error('name')
                                        <span class="text-danger small font-weight-bold mt-1 d-block"><i
                                                class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">Email Address <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-envelope text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="email" name="email"
                                            class="form-control custom-input border-left-0 @error('email') is-invalid @enderror"
                                            value="{{ old('email', $agent->email) }}" required>
                                    </div>
                                    @error('email')
                                        <span class="text-danger small font-weight-bold mt-1 d-block"><i
                                                class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- Contact Info Row --}}
                            <div class="row mt-3">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">Mobile Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-phone text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="mobile_number" class="form-control custom-input border-left-0" value="{{ old('mobile_number', $agent->mobile_number) }}" placeholder="e.g. 9876543210">
                                    </div>
                                </div>

                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">WhatsApp Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fab fa-whatsapp text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="whatsapp_no" class="form-control custom-input border-left-0" value="{{ old('whatsapp_no', $agent->whatsapp_no) }}" placeholder="e.g. 9876543210">
                                    </div>
                                </div>

                                <div class="col-md-12 form-group mb-4">
                                    <label class="form-label-custom">Full Address</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-map-marker-alt text-muted"></i>
                                            </span>
                                        </div>
                                        <textarea name="address" rows="2" class="form-control custom-input border-left-0" style="height: auto;" placeholder="Enter complete address">{{ old('address', $agent->address) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Informational Note --}}
                            {{-- Password Reset --}}
                            <div class="row mt-3">

                                <div class="col-md-6 form-group mb-4">
                                    <label class="form-label-custom">New Password</label>

                                    <div class="input-group">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-lock text-muted"></i>
                                            </span>
                                        </div>

                                        <input type="password" name="password"
                                            class="form-control custom-input border-left-0 @error('password') is-invalid @enderror"
                                            placeholder="Leave blank to keep current password">

                                    </div>

                                    @error('password')
                                        <span class="text-danger small font-weight-bold mt-1 d-block">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </span>
                                    @enderror

                                </div>


                                <div class="col-md-6 form-group mb-4">

                                    <label class="form-label-custom">Confirm Password</label>

                                    <div class="input-group">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                                <i class="fas fa-lock text-muted"></i>
                                            </span>
                                        </div>

                                        <input type="password" name="password_confirmation"
                                            class="form-control custom-input border-left-0"
                                            placeholder="Confirm new password">

                                    </div>

                                </div>

                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex justify-content-end mt-2 pt-3 border-top">
                                <a href="{{ route('admin.agents.index') }}"
                                    class="btn btn-light font-weight-bold mr-2 text-muted">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-success font-weight-bold shadow-sm px-4">
                                    <i class="fas fa-save mr-1"></i> Save Changes
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        /* Base Card */
        .modern-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }

        /* Labels */
        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        /* Input Addons (Icons) */
        .custom-icon-box {
            border-color: #cbd5e1;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
            transition: border-color 0.2s;
        }

        /* Input Fields */
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
            /* Matches your primary color theme */
            box-shadow: 0 0 0 3px rgba(0, 68, 178, 0.15);
            outline: none;
        }

        /* Fix border color on focus when using input-group */
        .custom-input:focus+.input-group-prepend .custom-icon-box,
        .input-group-prepend+.custom-input:focus {
            border-color: #0044b2;
        }

        /* Invalid State Styling */
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important;
        }

        /* Buttons */
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
@endsection
