@extends('layouts.marketer')
@section('title', 'Marketer Dashboard | EasyTax')

@section('content')
<div class="chq-wrapper p-4">
    <div class="mb-4">
        <h2 class="font-weight-bold text-dark mb-0">Welcome back, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-muted">Here is your lead generation performance at a glance.</p>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100" style="background: #f8fafc; border-left: 5px solid #3b82f6 !important;">
                <div class="card-body p-4">
                    <h6 class="text-xs font-weight-bold text-muted text-uppercase mb-2">Total Leads Captured</h6>
                    <h2 class="font-weight-bold text-dark mb-0">{{ $totalLeads }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100" style="background: #f0fdf4; border-left: 5px solid #22c55e !important;">
                <div class="card-body p-4">
                    <h6 class="text-xs font-weight-bold text-muted text-uppercase mb-2">Converted to Clients</h6>
                    <h2 class="font-weight-bold text-success mb-0">{{ $convertedLeads }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100" style="background: #fef2f2; border-left: 5px solid #ef4444 !important;">
                <div class="card-body p-4">
                    <h6 class="text-xs font-weight-bold text-muted text-uppercase mb-2">Lost / Not Interested</h6>
                    <h2 class="font-weight-bold text-danger mb-0">{{ $lostLeads }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('crm.leads.create') }}" class="btn btn-primary font-weight-bold shadow-sm px-4 py-2" style="border-radius: 8px; background: #8b5cf6; border-color: #8b5cf6;">
            <i class="fas fa-plus mr-2"></i> Capture New Lead
        </a>
    </div>
</div>
@endsection