@extends('layouts.admin')
@section('title', 'Add Marketer | EasyTax')

@section('content')
<div class="chq-wrapper p-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('crm.marketers.index') }}" class="text-muted font-weight-bold mr-3"><i class="fas fa-arrow-left"></i> Back</a>
        <h3 class="mb-0 font-weight-bold text-dark"><i class="fas fa-bullhorn text-danger mr-2"></i> Add New Marketer</h3>
    </div>

    {{-- ADDED: This will now show you exactly WHY the form fails! --}}
    @if($errors->any())
        <div class="alert alert-danger rounded-lg shadow-sm border-0" style="max-width: 600px;">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $err) 
                    <li>{{ $err }}</li> 
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-lg" style="max-width: 600px;">
        <div class="card-body p-4">
            <form action="{{ route('crm.marketers.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="text-xs font-weight-bold text-muted text-uppercase">Full Name now what we want to do is when a markter  *</label>
                    <input type="text" name="name" class="form-control rounded-lg" value="{{ old('name') }}" required placeholder="e.g. John Doe">
                </div>
                <div class="form-group mb-3">
                    <label class="text-xs font-weight-bold text-muted text-uppercase">Email Address *</label>
                    <input type="email" name="email" class="form-control rounded-lg" value="{{ old('email') }}" required placeholder="marketer@easytax.com">
                </div>
                <div class="form-group mb-4">
                    <label class="text-xs font-weight-bold text-muted text-uppercase">Temporary Password *</label>
                    <input type="password" name="password" class="form-control rounded-lg" required minlength="6" placeholder="Enter at least 6 characters">
                </div>
                <button type="submit" class="btn btn-danger font-weight-bold shadow-sm px-4">Save Marketer</button>
            </form>
        </div>
    </div>
</div>
@endsection