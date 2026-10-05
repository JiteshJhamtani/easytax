
@extends('layouts.admin')

@section('title', 'Application #' . $application->id . ' | EasyTax')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2 pt-2">
        <div>
            <a href="javascript:history.back()" class="btn-back-modern"><i class="fas fa-arrow-left"></i> Back to Applications</a>

            @php
                $status = strtolower($application->status?->value ?? 'unknown');
                $statusClass = match ($status) {
                    'completed'   => 'badge-success-soft',
                    'in_progress' => 'badge-info-soft',
                    'pending'     => 'badge-warning-soft',
                    'rejected'    => 'badge-danger-soft',
                    'cancelled'   => 'badge-secondary-soft',
                    default       => 'badge-primary-soft',
                };
            @endphp

            <div class="d-flex align-items-center mt-1">
                <h1 class="h3 font-weight-bold mb-0 text-dark">Application #{{ $application->id }}</h1>
                <span class="badge {{ $statusClass }} ml-3 px-3 py-2 text-uppercase"
                    style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    {{ $application->status?->value ?? 'UNKNOWN' }}
                </span>
            </div>

            <p class="text-muted mt-2 mb-0 text-sm">
                <i class="far fa-calendar-alt mr-1"></i>
                Submitted on <span class="font-weight-bold">{{ $application->submitted_at?->format('d M Y, h:i A') ?? 'N/A' }}</span>
            </p>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show font-weight-bold shadow-sm mb-4" role="alert" style="border-radius: 8px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show font-weight-bold shadow-sm mb-4" role="alert" style="border-radius: 8px;">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show font-weight-bold shadow-sm mb-4" role="alert" style="border-radius: 8px;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ $errors->first() }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">

        {{-- ── LEFT COLUMN ── --}}
        <div class="col-lg-8">

            {{-- QUICK STATS --}}
            <div class="row mb-4">
                @if(strtoupper(auth()->user()->role) !== 'SUB-ADMIN')
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 summary-card rounded-lg elegant-border">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="icon-box bg-success-soft text-success mr-3">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase text-xs font-weight-bold mb-1">Total Amount</h6>
                                <h4 class="mb-0 font-weight-bold text-dark">₹{{ number_format($application->amount ?? 0, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 summary-card rounded-lg elegant-border">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="icon-box bg-primary-soft text-primary mr-3">
                                <i class="fas {{ $application->isWebsiteDirect() ? 'fa-globe' : 'fa-user-tie' }} fa-lg"></i>
                            </div>
                            <div class="overflow-hidden">
                                @if($application->isWebsiteDirect())
                                    <h6 class="text-muted text-uppercase text-xs font-weight-bold mb-1">Website Customer</h6>
                                    <h5 class="mb-0 font-weight-bold text-dark text-truncate">{{ $application->customer_name ?? 'Retail Customer' }}</h5>
                                    @if($application->customer_phone)
                                        <div class="mt-1">
                                            <a href="{{ $application->customer_whatsapp_url }}" target="_blank" class="text-success font-weight-bold text-xs" style="text-decoration: none;">
                                                <i class="fab fa-whatsapp mr-1"></i>{{ $application->customer_phone }}
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <h6 class="text-muted text-uppercase text-xs font-weight-bold mb-1">Assigned Agent</h6>
                                    <h5 class="mb-0 font-weight-bold text-dark text-truncate">{{ $application->agent->name ?? 'Unassigned' }}</h5>
                                    @if($application->sub_agent_id && $application->subAgent)
                                        <div class="mt-1">
                                            <span class="badge badge-info text-dark" style="font-size: 0.75rem;"><i class="fas fa-users mr-1"></i>Team: {{ $application->subAgent->name }} ({{ $application->subAgent->agent_code }})</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    @php
                        $paymentStatus = strtolower($application->payment_status?->value ?? 'pending');
                        $payClass = match ($paymentStatus) {
                            'paid'     => 'bg-success-soft text-success',
                            'refunded' => 'bg-danger-soft text-danger',
                            default    => 'bg-warning-soft text-warning',
                        };
                    @endphp
                    <div class="card border-0 shadow-sm h-100 summary-card rounded-lg elegant-border">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="icon-box {{ $payClass }} mr-3">
                                <i class="fas {{ $paymentStatus === 'paid' ? 'fa-check-double' : ($paymentStatus === 'refunded' ? 'fa-undo-alt' : 'fa-hourglass-half') }} fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase text-xs font-weight-bold mb-1">Payment Status</h6>
                                <h5 class="mb-0 font-weight-bold text-dark text-capitalize">{{ $paymentStatus }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OVERVIEW --}}
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-info-circle text-primary mr-2"></i> Application Overview
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="responsive-card-table table table-hover mb-0 detail-table">
                        <tbody>
                            <tr>
                                <td class="text-muted text-uppercase text-xs font-weight-bold w-30 align-middle pl-4 border-top-0">Service Requested</td>
                                <td class="font-weight-bold text-dark border-top-0">
                                    {{ $application->service_name_fallback ?: ($application->service->name ?? 'N/A') }}
                                    @if($application->isWebsiteDirect())
                                        <span class="badge badge-light border text-primary ml-1" style="font-size: 0.75rem;"><i class="fas fa-globe mr-1"></i>Website Direct Order</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted text-uppercase text-xs font-weight-bold w-30 align-middle pl-4">Application ID</td>
                                <td><span class="text-muted font-weight-bold">#{{ $application->id }}</span></td>
                            </tr>
                            @if($application->sub_agent_id && $application->subAgent)
                            <tr>
                                <td class="text-muted text-uppercase text-xs font-weight-bold w-30 align-middle pl-4">Submitted By (Team)</td>
                                <td>
                                    <strong>{{ $application->subAgent->name }}</strong>
                                    <span class="badge badge-secondary ml-1">{{ $application->subAgent->agent_code }}</span>
                                    <span class="text-muted ml-2">({{ $application->subAgent->email }})</span>
                                </td>
                            </tr>
                            @if($application->parent_margin > 0)
                            <tr>
                                <td class="text-muted text-uppercase text-xs font-weight-bold w-30 align-middle pl-4">Parent Margin Refund</td>
                                <td>
                                    <span class="badge badge-success font-weight-bold" style="font-size: 0.85rem;">₹{{ number_format($application->parent_margin, 2) }}</span>
                                    <span class="text-muted ml-2">Status: <strong>{{ $application->parent_margin_status ?? 'PENDING' }}</strong></span>
                                    @if($application->parent_margin_refunded_at)
                                        <small class="text-muted d-block mt-1"><i class="fas fa-check-circle text-success mr-1"></i>Credited at {{ $application->parent_margin_refunded_at->format('d M Y, h:i A') }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CLIENT INFORMATION --}}
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-clipboard-list text-primary mr-2"></i> Client Information
                    </h3>
                    @if(strtoupper(auth()->user()->role) !== 'SUB-ADMIN' && !empty($application->form_data))
                        <a href="{{ route('admin.applications.exportSingle', $application->id) }}"
                            class="btn btn-sm btn-outline-success font-weight-bold shadow-sm transition-hover">
                            <i class="fas fa-file-excel mr-1"></i> Export to Excel
                        </a>
                    @endif
                </div>

@php 
                    // 1. Decode JSON
                    $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : ($application->form_data ?? []);
                    
                    // 2. Hide backend credentials from the loop
                    $formData = array_filter($formData, fn($key) => !in_array($key, ['admin_username', 'admin_password', 'moa', 'aoa']), ARRAY_FILTER_USE_KEY);

                    // 3. SMART REPEATER ENGINE
                    $regularData = [];
                    $repeaterGroups = [];
                    
                    foreach($formData as $key => $value) {
                        // Skip any field that is completely empty (except for '0')
                        if (empty($value) && $value !== '0') {
                            continue;
                        }

                        if (str_starts_with($key, 'director_') || str_starts_with($key, 'member_') || str_starts_with($key, 'partner_')) {
                            if (preg_match('/^([a-zA-Z]+)_(\d+)_(.+)$/', $key, $matches)) {
                                $prefix = $matches[1]; 
                                $index = (int)$matches[2] - 1; 
                                $subField = $matches[3]; 
                                $repeaterGroups[$prefix][$index][$subField] = $value;
                            } else {
                                $regularData[$key] = $value;
                            }
                        } else {
                            $regularData[$key] = $value;
                        }
                    }

                    //  4. CLEANUP FILTER: Remove any member that is completely empty (Members 3-8) 
                    foreach ($repeaterGroups as $prefix => $items) {
                        foreach ($items as $index => $itemData) {
                            $hasData = false;
                            foreach ($itemData as $val) {
                                if (!empty($val)) {
                                    $hasData = true;
                                    break;
                                }
                            }
                            // If all fields for this member are empty, delete the member from the view
                            if (!$hasData) {
                                unset($repeaterGroups[$prefix][$index]); 
                            }
                        }
                    }
                @endphp

                <div class="card-body p-4 bg-light rounded-bottom">
                    
                    {{-- REGULAR FORM FIELDS (Top Section) --}}
                    @if(count($regularData) > 0)
                        <div class="row">
                            @foreach($regularData as $key => $value)
                                <div class="col-md-6 mb-3">
                                    <div class="bg-white p-3 rounded-lg border shadow-sm h-100 data-box transition-hover" style="border-left: 3px solid #1e9c5d !important;">
                                        <span class="d-block text-muted text-uppercase text-xs font-weight-bold mb-1">{{ str_replace('_', ' ', $key) }}</span>
                                        <span class="text-dark font-weight-normal" style="word-break: break-word;">
                                            {{ is_array($value) ? implode(', ', $value) : (empty($value) && $value !== '0' ? 'Not provided' : $value) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif(empty($repeaterGroups))
                        <div class="text-center text-muted py-4">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border" style="width: 70px; height: 70px;">
                                <i class="fas fa-inbox fa-2x text-secondary opacity-50"></i>
                            </div>
                            <h6 class="font-weight-bold">No Client Data</h6>
                            <p class="text-sm mb-0">No form data was captured for this application.</p>
                        </div>
                    @endif

                    {{-- DYNAMIC REPEATER BOXES (Member/Director Details) --}}
                    @foreach($repeaterGroups as $groupName => $items)
                        @if(count($items) > 0)
                            <h5 class="font-weight-bold text-dark mt-4 mb-3 border-bottom pb-2">
                                <i class="fas fa-users text-primary mr-2"></i> {{ ucfirst($groupName) }} Details
                            </h5>
                            
                            @foreach($items as $index => $itemData)
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong class="text-muted text-xs text-uppercase mb-2 d-block">{{ ucfirst($groupName) }} {{ $index + 1 }}</strong>
                                    </div>
                                    @foreach($itemData as $subKey => $subValue)
                                        <div class="col-md-4 mb-3">
                                            <div class="bg-white p-3 rounded-lg border shadow-sm h-100 data-box transition-hover" style="border-left: 3px solid #1e9c5d !important;">
                                                <span class="d-block text-muted text-xs font-weight-bold text-uppercase mb-1">{{ str_replace('_', ' ', $subKey) }}</span>
                                                <span class="text-dark font-weight-normal" style="word-break: break-word;">
                                                    {{ empty($subValue) && $subValue !== '0' ? 'Not provided' : $subValue }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    @endforeach

                </div>
            </div>
{{-- IDENTIFY THE SERVICE TYPE --}}
            @php
                $companyServices = [
                    'fpo-registration', 
                    'section-8-company', 
                    'llp-registration', 
                    'opc-registration', 
                    'private-limited-company-registration'
                ];
                
                $isCompanySetup = in_array($application->service->slug ?? '', $companyServices);
                $isGstSetup = in_array($application->service->slug ?? '', ['gst-registration', 'gst-return-filing']);
            @endphp

           {{-- ADMIN INPUT FORM FOR COMPANY OR GST DELIVERABLES --}}
            @if($isCompanySetup || $isGstSetup || in_array($application->service->slug, ['gst-return-filing', 'gst-annual-package']))
            <div class="card border-0 shadow-sm mb-4 mt-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-key text-warning mr-2"></i> {{ $isCompanySetup ? 'Company' : 'GST' }} Credentials & Deliverables
                    </h3>
                </div>
                <div class="card-body p-4 bg-light rounded-bottom">
                    <form action="{{ route('admin.applications.storeCredentials', $application->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Portal Username / ID</label>
                                @php 
                                    // Bypasses the earlier array_filter so data stays visible after saving
                                    $rawFormData = is_string($application->form_data) ? json_decode($application->form_data, true) : ($application->form_data ?? []); 
                                @endphp
                                <input type="text" name="admin_username" class="form-control border-light shadow-sm" placeholder="Enter username" value="{{ $rawFormData['admin_username'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Portal Password</label>
                                @php
                                    $displayPassword = '';
                                    if (!empty($rawFormData['admin_password'])) {
                                        try {
                                            $displayPassword = \Illuminate\Support\Facades\Crypt::decryptString($rawFormData['admin_password']);
                                        } catch (\Exception $e) {
                                            $displayPassword = $rawFormData['admin_password'];
                                        }
                                    }
                                @endphp
                                <input type="text" name="admin_password" class="form-control border-light shadow-sm" placeholder="Enter password" value="{{ $displayPassword }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">{{ $isCompanySetup ? 'Incorporation Certificate' : 'GST Certificate / Final Document' }}</label>
                                <input type="file" name="final_document" class="form-control border-light shadow-sm" style="height:auto;padding:0.35rem 0.5rem;font-size:0.8rem;border-radius:6px;" accept=".pdf">
                                <small class="text-muted mt-1 d-block">Only PDF files up to 5MB are allowed.</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary font-weight-bold mt-2 shadow-sm">
                            <i class="fas fa-save mr-1"></i> Save Details
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- 12-MONTH GST COMPLIANCE TRACKER (GST ANNUAL PACKAGE ONLY) --}}
            @if($application->isGstAnnualPackage())
            <div class="card border-0 shadow-sm mb-4 mt-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title font-weight-bold text-dark mb-0">
                            <i class="fas fa-calendar-check text-success mr-2"></i> 12-Month GST Filing Compliance Tracker
                        </h3>
                        <p class="text-muted small mb-0 mt-1">
                            Track and update monthly return filings (GSTR-1 & GSTR-3B) for this annual package.
                        </p>
                    </div>
                    <div class="mt-2 mt-md-0 d-flex align-items-center gap-2">
                        <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 0.85rem;">
                            {{ $application->gst_annual_completed_months_count }} / 12 Months Done
                        </span>
                        @if($application->isGstAnnualExpired())
                            <span class="badge badge-danger px-3 py-2 font-weight-bold" style="font-size: 0.85rem;" title="Your annual-gst has ended please renew it">
                                <i class="fas fa-bell mr-1"></i> Annual Package Ended (Renewal Due)
                            </span>
                        @elseif($application->isGstAnnualExpiringSoon())
                            <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold" style="font-size: 0.85rem;">
                                <i class="fas fa-clock mr-1"></i> Expiring Soon ({{ $application->gst_annual_expiry_date?->format('d M Y') }})
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- Expiry Alert Callout --}}
                    @if($application->isGstAnnualExpired())
                        <div class="alert alert-danger rounded-lg border-0 shadow-sm d-flex align-items-center mb-4">
                            <i class="fas fa-exclamation-triangle fa-2x mr-3 text-danger"></i>
                            <div>
                                <h6 class="font-weight-bold mb-1">Annual Subscription Expired!</h6>
                                <p class="mb-0 text-sm">
                                    The 1-year service duration for this GST Annual Package expired on <strong>{{ $application->gst_annual_expiry_date?->format('d M Y') }}</strong>. Please notify the agent to renew the package.
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Progress Bar --}}
                    @php
                        $progressPercent = min(100, round(($application->gst_annual_completed_months_count / 12) * 100));
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between text-xs font-weight-bold text-muted text-uppercase mb-1">
                            <span>Annual Filing Progress</span>
                            <span>{{ $progressPercent }}% Completed</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%;" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    {{-- Months Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="bg-light text-muted text-uppercase text-xs">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Filing Month</th>
                                    <th>Status</th>
                                    <th>Filing Date</th>
                                    <th>ARN / Ack No.</th>
                                    <th>Receipt</th>
                                    <th class="text-center" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($application->gst_monthly_filings as $index => $filing)
                                    <tr>
                                        <td class="font-weight-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="font-weight-bold text-dark">{{ $filing['month_label'] }}</td>
                                        <td>
                                            @if($filing['status'] === 'FILED')
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Done</span>
                                            @elseif($filing['status'] === 'IN_PROGRESS')
                                                <span class="badge badge-info px-2 py-1"><i class="fas fa-spinner fa-spin mr-1"></i> In Progress</span>
                                            @elseif($filing['status'] === 'NOT_APPLICABLE')
                                                <span class="badge badge-secondary px-2 py-1">N/A</span>
                                            @else
                                                <span class="badge badge-warning text-dark px-2 py-1"><i class="far fa-clock mr-1"></i> Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ !empty($filing['filed_at']) ? \Carbon\Carbon::parse($filing['filed_at'])->format('d M Y') : '-' }}
                                        </td>
                                        <td>
                                            <span class="font-weight-bold font-monospace text-dark">{{ $filing['arn'] ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if(!empty($filing['media_id']))
                                                <a href="{{ route('admin.documents.download', $filing['media_id']) }}" class="btn btn-xs btn-outline-success font-weight-bold" target="_blank">
                                                    <i class="fas fa-download mr-1"></i> Receipt
                                                </a>
                                            @else
                                                <span class="text-muted text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 font-weight-bold"
                                                onclick="openGstMonthModal('{{ $filing['month_key'] }}', '{{ $filing['month_label'] }}', '{{ $filing['status'] }}', '{{ $filing['filed_at'] ?? '' }}', '{{ $filing['arn'] ?? '' }}', '{{ e($filing['notes'] ?? '') }}')">
                                                <i class="fas fa-edit mr-1"></i> Update
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Update Month Modal --}}
            <div class="modal fade" id="updateGstMonthModal" tabindex="-1" role="dialog" aria-labelledby="updateGstMonthModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-lg rounded-lg">
                        <form action="{{ route('admin.applications.gst-monthly-filing.update', $application->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="month_key" id="modalGstMonthKey">
                            <div class="modal-header bg-light py-3 border-bottom">
                                <h5 class="modal-title font-weight-bold text-dark" id="updateGstMonthModalTitle">
                                    <i class="fas fa-calendar-alt text-primary mr-2"></i> Update Monthly GST Filing (<span id="modalGstMonthLabel"></span>)
                                </h5>
                                <button type="button" class="close text-muted" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="form-group mb-3">
                                    <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Filing Status <span class="text-danger">*</span></label>
                                    <select name="status" id="modalGstStatus" class="form-control" required>
                                        <option value="PENDING">Pending</option>
                                        <option value="IN_PROGRESS">In Progress</option>
                                        <option value="FILED">Filed (Done)</option>
                                        <option value="NOT_APPLICABLE">Not Applicable (N/A)</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Filing Date</label>
                                    <input type="date" name="filed_at" id="modalGstFiledAt" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">ARN / Acknowledgment Number</label>
                                    <input type="text" name="arn" id="modalGstArn" class="form-control" placeholder="e.g. AA070326012345A">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Upload Filing Receipt / Challan (PDF / Image)</label>
                                    <input type="file" name="receipt" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted d-block mt-1">Allowed formats: PDF, JPG, PNG (Max: 5MB)</small>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Notes / Remarks</label>
                                    <textarea name="notes" id="modalGstNotes" rows="2" class="form-control" placeholder="Optional notes..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary font-weight-bold rounded-lg px-3" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success font-weight-bold rounded-lg px-4 shadow-sm">
                                    <i class="fas fa-save mr-1"></i> Save Month Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function openGstMonthModal(monthKey, monthLabel, status, filedAt, arn, notes) {
                    document.getElementById('modalGstMonthKey').value = monthKey;
                    document.getElementById('modalGstMonthLabel').textContent = monthLabel;
                    document.getElementById('modalGstStatus').value = status;
                    if (filedAt) {
                        document.getElementById('modalGstFiledAt').value = filedAt.split('T')[0];
                    }
                    document.getElementById('modalGstArn').value = arn;
                    document.getElementById('modalGstNotes').value = notes;

                    if (window.jQuery && typeof $('#updateGstMonthModal').modal === 'function') {
                        $('#updateGstMonthModal').modal('show');
                    } else {
                        var modal = document.getElementById('updateGstMonthModal');
                        if (modal) {
                            modal.classList.add('show');
                            modal.style.display = 'block';
                            document.body.classList.add('modal-open');
                        }
                    }
                }
            </script>
            @endif
        </div>

        {{-- ── RIGHT COLUMN ──   --}}
        <div class="col-lg-4">

            {{-- ADMIN ACTIONS --}}
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom text-center">
                    <h3 class="card-title font-weight-bold text-dark w-100 float-none mb-0">
                        <i class="fas fa-cogs text-primary mr-2"></i> Admin Actions
                    </h3> 
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.applications.updateStatus', $application->id) }}" class="no-loader">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" id="adminAppStatusField" value="">

                        <button type="submit" name="status" value="IN_PROGRESS"
                            onclick="document.getElementById('adminAppStatusField').value = 'IN_PROGRESS'"
                            class="btn btn-warning btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                            <i class="fas fa-spinner mr-2"></i> Mark In Progress
                        </button>

                        @if(($application->service?->slug ?? '') === 'itr-filing')
                            <button type="submit" name="status" value="E_FILING"
                                onclick="document.getElementById('adminAppStatusField').value = 'E_FILING'"
                                class="btn btn-info btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover text-white">
                                <i class="fas fa-laptop-code mr-2"></i> Mark E-Filing
                            </button>
                            <button type="submit" name="status" value="OTP_VERIFICATION"
                                onclick="document.getElementById('adminAppStatusField').value = 'OTP_VERIFICATION'"
                                class="btn btn-primary btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                                <i class="fas fa-mobile-alt mr-2"></i> Mark OTP Verification
                            </button>
                        @endif

                        <button type="submit" name="status" value="COMPLETED"
                            onclick="document.getElementById('adminAppStatusField').value = 'COMPLETED'"
                            class="btn btn-success btn-block mb-3 py-2 shadow-sm d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                            <i class="fas fa-check-circle mr-2"></i> Mark Completed
                        </button>
                    </form>
                </div>
            </div>

            {{-- OPERATOR PAYOUT OVERRIDE --}}
            <div class="card border-0 shadow-sm mb-4 rounded-lg elegant-border">
                <div class="card-header bg-white py-3 border-bottom text-center">
                    <h3 class="card-title font-weight-bold text-dark w-100 float-none mb-0">
                        <i class="fas fa-rupee-sign text-success mr-2"></i> Operator Payout Override
                    </h3> 
                </div>
                <div class="card-body p-4 bg-light">
                    <p class="text-xs text-muted mb-3 text-center">Set a custom payout for this specific application to override the operator's default rate.</p>
                    <form method="POST" action="{{ route('admin.applications.overridePayout', $application->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="input-group mb-3 shadow-sm">
                            <div class="input-group-prepend"><span class="input-group-text bg-white border-right-0">₹</span></div>
                            <input type="number" step="0.01" name="override_payout_amount" class="form-control border-left-0" value="{{ $application->override_payout_amount }}" placeholder="Default Rate">
                        </div>
                        <button type="submit" class="btn btn-dark btn-block font-weight-bold shadow-sm">Save Payout Rate</button>
                    </form>
                </div>
            </div>

                    @if ($application->status?->value === 'CANCELLED' && strtolower($application->payment_status?->value ?? '') === 'paid')
                        <div class="position-relative my-4">
                            <hr class="border-light m-0">
                            <span class="position-absolute top-50 left-50 translate-middle bg-white px-2 text-muted text-xs text-uppercase font-weight-bold"
                                style="transform: translate(-50%, -50%);">Financial</span>
                        </div>
                        <form id="refundApplicationForm" method="POST"
                            action="{{ route('admin.applications.updatePaymentStatus', $application->id) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="payment_status" value="REFUNDED">
                            <button type="button" onclick="openRefundModal()"
                                class="btn btn-outline-danger btn-block py-2 d-flex justify-content-center align-items-center font-weight-bold transition-hover">
                                <i class="fas fa-undo-alt mr-2"></i> Process Refund
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- DOCUMENTS --}}
            <div class="card border-0 shadow-sm rounded-lg mb-4 elegant-border">
                <div class="card-header bg-white py-3 border-bottom text-center">
                    <h3 class="card-title font-weight-bold text-dark w-100 float-none mb-0">
                        <i class="fas fa-folder-open text-orange mr-2"></i> Documents
                    </h3>
                </div>

                <div class="card-body p-4 bg-light rounded-bottom">

                    {{-- Generic Upload --}}
                    <form action="{{ route('admin.applications.uploadDocument', $application->id) }}" method="POST"
                        enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="position-relative"
                            style="border: 2px dashed #d1d5db; border-radius: 12px; padding: 2rem 1rem; text-align: center; background: #ffffff; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.borderColor='#1E9C5D'"
                            onmouseout="this.style.borderColor='#d1d5db'">
                            <div class="text-muted mb-2"><i class="fas fa-cloud-upload-alt fa-2x"></i></div>
                            <h6 class="font-weight-bold text-dark mb-1">Click to upload a generic document</h6>
                            <p class="text-xs text-muted mb-0 text-uppercase">PDF, PNG, JPG (Max 5MB)</p>
                            <input type="file" name="document" class="position-absolute"
                                style="top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;"
                                onchange="this.form.submit()" accept=".pdf,.png,.jpg,.jpeg">
                        </div>
                        @error('document')
                            <span class="text-danger text-xs font-weight-bold mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </form>

                    {{-- ITR specific uploads --}}
                    @if($application->service->slug === 'itr-filing')
                        <div class="row px-2 mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                    <i class="fas fa-file-invoice text-primary mr-1"></i> ITR Ack
                                </label>
                                @php $ackDoc = $application->getFirstMedia('itr_acknowledgement'); @endphp
                                @if($ackDoc)
                                    <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                        <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark"
                                            title="{{ $ackDoc->name }}">{{ $ackDoc->name }}</div>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.documents.view', $ackDoc->id) }}" target="_blank"
                                                class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                            <form action="{{ route('admin.applications.deleteDocument', $ackDoc->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('confirm-action', { detail: { form: this, title: 'Delete document?', message: 'Are you sure you want to delete this document?' } }));">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('admin.applications.uploadDocument', $application->id) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="ack_file" class="form-control border-light shadow-sm"
                                            style="height:auto;padding:0.35rem 0.5rem;font-size:0.8rem;border-radius:6px;"
                                            accept=".pdf" onchange="this.form.submit()">
                                    </form>
                                @endif
                                @error('ack_file')
                                    <span class="text-danger text-xs font-weight-bold mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                    <i class="fas fa-calculator text-success mr-1"></i> Computation
                                </label>
                                @php $compDoc = $application->getFirstMedia('computation_sheet'); @endphp
                                @if($compDoc)
                                    <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                        <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark"
                                            title="{{ $compDoc->name }}">{{ $compDoc->name }}</div>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.documents.view', $compDoc->id) }}" target="_blank"
                                                class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                            <form action="{{ route('admin.applications.deleteDocument', $compDoc->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('confirm-action', { detail: { form: this, title: 'Delete document?', message: 'Are you sure you want to delete this document?' } }));">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('admin.applications.uploadDocument', $application->id) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="computation_file"
                                            class="form-control border-light shadow-sm"
                                            style="height:auto;padding:0.35rem 0.5rem;font-size:0.8rem;border-radius:6px;"
                                            accept=".pdf" onchange="this.form.submit()">
                                    </form>
                                @endif
                                @error('computation_file')
                                    <span class="text-danger text-xs font-weight-bold mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- COMPANY specific uploads (MOA & AOA) --}}
                    @if($isCompanySetup)
                        <div class="row px-2 mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                    <i class="fas fa-file-pdf text-danger mr-1"></i> Draft MOA
                                </label>
                                @php $moaDoc = $application->getFirstMedia('moa_document'); @endphp
                                @if($moaDoc)
                                    <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                        <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark"
                                            title="{{ $moaDoc->name }}">{{ $moaDoc->name }}</div>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.documents.view', $moaDoc->id) }}" target="_blank"
                                                class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                            <form action="{{ route('admin.applications.deleteDocument', $moaDoc->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('confirm-action', { detail: { form: this, title: 'Delete document?', message: 'Are you sure you want to delete this document?' } }));">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('admin.applications.uploadDocument', $application->id) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="moa_file" class="form-control border-light shadow-sm"
                                            style="height:auto;padding:0.35rem 0.5rem;font-size:0.8rem;border-radius:6px;"
                                            accept=".pdf,.doc,.docx" onchange="this.form.submit()">
                                    </form>
                                @endif
                                @error('moa_file')
                                    <span class="text-danger text-xs font-weight-bold mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">
                                    <i class="fas fa-file-pdf text-danger mr-1"></i> Draft AOA
                                </label>
                                @php $aoaDoc = $application->getFirstMedia('aoa_document'); @endphp
                                @if($aoaDoc)
                                    <div class="d-flex align-items-center bg-white border rounded p-2 shadow-sm">
                                        <div class="text-truncate flex-grow-1 text-xs font-weight-bold mr-2 text-dark"
                                            title="{{ $aoaDoc->name }}">{{ $aoaDoc->name }}</div>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.documents.view', $aoaDoc->id) }}" target="_blank"
                                                class="btn btn-sm btn-light border text-primary px-2 py-1"><i class="fas fa-eye"></i></a>
                                            <form action="{{ route('admin.applications.deleteDocument', $aoaDoc->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('confirm-action', { detail: { form: this, title: 'Delete document?', message: 'Are you sure you want to delete this document?' } }));">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger px-2 py-1"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('admin.applications.uploadDocument', $application->id) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="aoa_file"
                                            class="form-control border-light shadow-sm"
                                            style="height:auto;padding:0.35rem 0.5rem;font-size:0.8rem;border-radius:6px;"
                                            accept=".pdf,.doc,.docx" onchange="this.form.submit()">
                                    </form>
                                @endif
                                @error('aoa_file')
                                    <span class="text-danger text-xs font-weight-bold mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Hidden delete forms --}}
                    @if(isset($ackDoc) && $ackDoc)
                        <form id="delete-ack-{{ $ackDoc->id }}"
                            action="{{ route('admin.applications.deleteDocument', $ackDoc->id) }}" method="POST"
                            class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    @endif
                    @if(isset($compDoc) && $compDoc)
                        <form id="delete-comp-{{ $compDoc->id }}"
                            action="{{ route('admin.applications.deleteDocument', $compDoc->id) }}" method="POST"
                            class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    @endif
                    @if(isset($moaDoc) && $moaDoc)
                        <form id="delete-moa-{{ $moaDoc->id }}" action="{{ route('admin.applications.deleteDocument', $moaDoc->id) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    @endif
                    @if(isset($aoaDoc) && $aoaDoc)
                        <form id="delete-aoa-{{ $aoaDoc->id }}" action="{{ route('admin.applications.deleteDocument', $aoaDoc->id) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    @endif

                    {{-- Document lists --}}
                    @php
                        $adminCollections   = ['final_deliverables', 'admin_uploads', 'documents', 'default'];
                        $specialCollections = ['itr_acknowledgement', 'computation_sheet','moa_document', 'aoa_document'];
                        $adminDocs          = $application->media->whereIn('collection_name', $adminCollections);
                        $agentDocs          = $application->media->whereNotIn('collection_name', array_merge($adminCollections, $specialCollections));

                        $renderDoc = function($doc) {
                            $ext  = strtolower(pathinfo($doc->file_name ?? '', PATHINFO_EXTENSION));
                            $icon = match ($ext) {
                                'pdf'             => 'fa-file-pdf text-danger',
                                'jpg','jpeg','png' => 'fa-file-image text-primary',
                                'doc','docx'      => 'fa-file-word text-info',
                                default           => 'fa-file-alt text-secondary',
                            };
                            $bucketText = ($doc->collection_name !== 'documents' && $doc->collection_name !== 'default')
                                ? ' • <span class="text-primary">'.str_replace('_', ' ', $doc->collection_name).'</span>'
                                : '';
                            return '
                            <div class="document-item d-flex align-items-center p-3 mb-3 bg-white rounded-lg border shadow-sm transition-hover">
                                <div class="document-icon bg-light rounded d-flex align-items-center justify-content-center mr-3" style="width:45px;height:45px;flex-shrink:0;">
                                    <i class="fas '.$icon.' fa-lg"></i>
                                </div>
                                <div class="document-info flex-grow-1 overflow-hidden pr-2">
                                    <div class="text-dark font-weight-bold text-truncate text-sm mb-1" title="'.$doc->name.'">'.($doc->custom_properties['label'] ?? $doc->name).'</div>
                                    <div class="text-muted text-xs text-uppercase font-weight-bold">'.(strtoupper($ext) ?: 'FILE').' • '.number_format($doc->size / 1024, 1).' KB '.$bucketText.'</div>
                                </div>
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <a href="'.route('admin.documents.view', $doc->id).'" target="_blank" class="btn btn-sm btn-light border text-primary action-btn shadow-sm"><i class="fas fa-eye"></i></a>
                                    <a href="'.route('admin.documents.download', $doc->id).'" class="btn btn-sm btn-primary action-btn shadow-sm"><i class="fas fa-download"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger action-btn shadow-sm" onclick="document.getElementById(\'delete-doc-'.$doc->id.'\').submit();"><i class="fas fa-trash"></i></button>
                                </div>
                                <form id="delete-doc-'.$doc->id.'" action="'.route('admin.applications.deleteDocument', $doc->id).'" method="POST" class="d-none">'.csrf_field().method_field('DELETE').'</form>
                            </div>';
                        };
                    @endphp

                    @if($application->media->count())
                        @if($adminDocs->count() > 0)
                            <hr class="border-light my-4">
                            <h6 class="font-weight-bold text-dark mb-3">
                                <i class="fas fa-user-shield text-primary mr-2"></i> Uploaded by Admin
                            </h6>
                            <div class="document-list mb-4">
                                @foreach ($adminDocs as $doc) {!! $renderDoc($doc) !!} @endforeach
                            </div>
                        @endif

                        @if($agentDocs->count() > 0)
                            <hr class="border-light my-4">
                            <h6 class="font-weight-bold text-dark mb-3">
                                <i class="fas fa-user-tie text-secondary mr-2"></i> Uploaded by Client / Agent
                            </h6>
                            <div class="document-list">
                                @foreach ($agentDocs as $doc) {!! $renderDoc($doc) !!} @endforeach
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4 text-muted">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border"
                                style="width:60px;height:60px;">
                                <i class="fas fa-file-excel fa-2x text-secondary opacity-50"></i>
                            </div>
                            <h6 class="font-weight-bold">No Documents</h6>
                            <p class="text-sm mb-0">No files uploaded.</p>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    {{-- REFUND MODAL --}}
    <div id="customRefundModal" class="custom-modal-backdrop" style="display:none;">
        <div class="custom-modal-dialog shadow-lg">
            <div class="custom-modal-content">
                <div class="custom-modal-header bg-danger-soft">
                    <h5 class="mb-0 text-danger font-weight-bold">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Confirm Refund
                    </h5>
                </div>
                <div class="custom-modal-body p-4 text-center">
                    <div class="mb-3"><i class="fas fa-undo-alt text-danger" style="font-size:3rem;opacity:0.8;"></i></div>
                    <p class="mb-0 font-weight-bold text-dark" style="font-size:1.1rem;">
                        Are you sure you want to process this refund?
                    </p>
                    <p class="text-muted text-sm mt-2">This action cannot be undone.</p>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-light border font-weight-bold"
                        onclick="closeRefundModal()">No, Go Back</button>
                    <button type="button" class="btn btn-danger font-weight-bold shadow-sm"
                        onclick="submitRefundForm()">Yes, Process Refund</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .w-30 { width: 30%; }
        .text-xs { font-size: 0.75rem; }
        .gap-2 { gap: 0.5rem !important; }
        .elegant-border { border: 1px solid rgba(0,0,0,0.05) !important; }
        .bg-primary-soft { background-color: #e8f0fe !important; }
        .bg-success-soft { background-color: #e6f4ea !important; }
        .bg-warning-soft { background-color: #fef7e0 !important; }
        .bg-danger-soft  { background-color: #fce8e6 !important; }
        .bg-info-soft    { background-color: #e0f2fe !important; color: #0284c7 !important; }
        .bg-secondary-soft { background-color: #f1f3f4 !important; }
        .text-primary-dark { color: #1e9c5d !important; }
        .badge-primary-soft   { background-color:#e8f0fe; color:#1a73e8; border:1px solid #d2e3fc; }
        .badge-success-soft   { background-color:#e6f4ea; color:#137333; border:1px solid #ceead6; }
        .badge-warning-soft   { background-color:#fef7e0; color:#b06000; border:1px solid #feefc3; }
        .badge-danger-soft    { background-color:#fce8e6; color:#c5221f; border:1px solid #fad2cf; }
        .badge-info-soft      { background-color:#e0f2fe; color:#0284c7; border:1px solid #bae6fd; }
        .badge-secondary-soft { background-color:#f1f3f4; color:#5f6368; border:1px solid #e8eaed; }
        .icon-box { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .detail-table td { padding:1.2rem 1rem; vertical-align:middle; }
        .data-box { border-left: 3px solid #1e9c5d !important; }
        .transition-hover { transition: all 0.2s ease-in-out; }
        .data-box:hover, .document-item:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
        .btn { border-radius: 8px; letter-spacing: 0.3px; }
        .action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; padding:0; }
        .custom-modal-backdrop { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1050; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(3px); }
        .custom-modal-dialog { background:#fff; border-radius:16px; width:100%; max-width:450px; overflow:hidden; animation:popIn 0.3s ease-out forwards; transform:scale(0.9); opacity:0; }
        .custom-modal-header { padding:1.25rem 1.5rem; border-bottom:1px solid #f1f3f4; }
        .custom-modal-footer { padding:1rem 1.5rem; background:#f8f9fa; display:flex; justify-content:center; gap:12px; }
        @keyframes popIn { to { transform:scale(1); opacity:1; } }
    </style>
@stop

@section('js')
    <script>
        function openRefundModal()  { document.getElementById('customRefundModal').style.display = 'flex'; }
        function closeRefundModal() { document.getElementById('customRefundModal').style.display = 'none'; }
        function submitRefundForm() {
            event.target.disabled = true;
            event.target.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';
            document.getElementById('refundApplicationForm').submit();
        }
        window.onclick = function(event) {
            var modal = document.getElementById('customRefundModal');
            if (event.target == modal) closeRefundModal();
        }
    </script>
@stop