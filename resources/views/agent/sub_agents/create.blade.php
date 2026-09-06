@extends('layouts.agent')

@section('title', 'Add Team Member | EasyTax')

@section('css')
    <style>
        .chq-hero {
            background-color: var(--green-light);
            padding: 2.2rem 2.5rem 5rem;
            border-bottom: 1px solid #e2efe9;
        }
        .chq-hero-flex {
            max-width: 900px;
            margin: 0 auto;
        }
        .chq-hero-title h1 {
            font-size: 1.75rem;
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
            max-width: 900px;
            margin: -3.5rem auto 3rem;
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }
        .form-card {
            background: #ffffff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            padding: 2rem;
        }
        .btn-brand-green {
            background-color: var(--green);
            color: #ffffff !important;
            font-weight: 700;
            border-radius: 8px;
            padding: 0.65rem 1.4rem;
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
    <div class="chq-hero">
        <div class="chq-hero-flex">
            <div class="chq-hero-title">
                <h1>Add New Team Member (Sub-Agent)</h1>
                <p>Create an individual login for your staff or field executive under your agency account.</p>
            </div>
        </div>
    </div>

    <div class="chq-main">
        <div class="form-card">
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

            <form method="POST" action="{{ route('agent.sub-agents.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Rahul Sharma" required>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Email Address (Login Username) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="e.g. rahul.team@example.com" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Mobile Number</label>
                        <input type="text" name="mobile_number" class="form-control" value="{{ old('mobile_number') }}" placeholder="10-digit mobile number">
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">WhatsApp Number</label>
                        <input type="text" name="whatsapp_no" class="form-control" value="{{ old('whatsapp_no') }}" placeholder="10-digit WhatsApp number">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required minlength="6">
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required minlength="6">
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Address / Office Branch</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Optional physical location / branch details">{{ old('address') }}</textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
                    <a href="{{ route('agent.sub-agents.index') }}" class="btn btn-light border font-weight-bold">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Team
                    </a>
                    <button type="submit" class="btn-brand-green">
                        <i class="fas fa-save mr-1"></i> Create Team Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
