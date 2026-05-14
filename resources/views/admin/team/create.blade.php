@extends('layouts.admin')

@section('title', 'Add New Operator')

@section('css')
<style>
    /* Styling to match your beautiful screenshot */
    .form-label { font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; color: #475569; text-transform: uppercase; margin-bottom: 0.5rem; }
    .required-asterisk { color: #ef4444; margin-left: 2px; }
    
    .custom-input-group { display: flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background: #fff; transition: all 0.2s; }
    .custom-input-group:focus-within { border-color: #1E9C5D; box-shadow: 0 0 0 3px rgba(30,156,93,0.1); }
    
    .custom-input-icon { width: 45px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-right: 1px solid #cbd5e1; color: #64748b; font-size: 0.9rem; flex-shrink: 0; align-self: stretch; }
    
    .custom-input { border: none; padding: 0.75rem 1rem; width: 100%; color: #334155; font-weight: 500; font-size: 0.9rem; }
    .custom-input:focus { outline: none; }
    .custom-input::placeholder { color: #94a3b8; font-weight: 400; }
    
    .form-card { border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .form-header { padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem; }
    .form-body { padding: 2rem; }
    .form-footer { padding: 1.25rem 2rem; background: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 12px 12px; display: flex; justify-content: flex-end; gap: 1rem; }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    
    <div class="mb-4">
        <a href="{{ route('admin.team.index') }}" class="text-muted text-decoration-none font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Back to Operators
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 8px;">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('admin.team.store') }}" method="POST">
                @csrf
                <div class="form-card">
                    
                    <div class="form-header">
                        <i class="fas fa-user-shield text-primary" style="font-size: 1.25rem;"></i>
                        <h5 class="mb-0 font-weight-bold text-dark">Operator Account Details</h5>
                    </div>

                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Full Name <span class="required-asterisk">*</span></label>
                                <div class="custom-input-group">
                                    <div class="custom-input-icon"><i class="fas fa-user"></i></div>
                                    <input type="text" name="name" class="custom-input" placeholder="e.g. John Doe" value="{{ old('name') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Email Address <span class="required-asterisk">*</span></label>
                                <div class="custom-input-group">
                                    <div class="custom-input-icon"><i class="fas fa-envelope"></i></div>
                                    <input type="email" name="email" class="custom-input" placeholder="operator@easytax.live" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Mobile Number</label>
                                <div class="custom-input-group">
                                    <div class="custom-input-icon"><i class="fas fa-phone"></i></div>
                                    <input type="text" name="phone" class="custom-input" placeholder="e.g. 9876543210" value="{{ old('phone') }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Password <span class="required-asterisk">*</span></label>
                                <div class="custom-input-group">
                                    <div class="custom-input-icon"><i class="fas fa-lock"></i></div>
                                    <input type="password" name="password" class="custom-input" placeholder="Create a strong password" required>
                                </div>
                                <small class="text-muted mt-1 d-block">Must be at least 8 characters.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="{{ route('admin.team.index') }}" class="btn btn-light" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600; color: #475569;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm" style="border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 700;">
                            <i class="fas fa-user-plus mr-1"></i> Create Operator
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection