@extends('layouts.admin')

@section('title', 'Agent Profile: ' . $agent->name . ' (' . $agent->agent_code . ')')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2 flex-wrap" style="gap: 12px;">
        <div>
            <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.5rem; letter-spacing: -0.02em;">
                Agent 360° Profile
            </h1>
            <span class="text-muted text-sm">Comprehensive performance, work history, and status monitor</span>
        </div>
        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
            <x-session-switcher :sessions="$sessions" :current-session-label="$currentSessionLabel" />
            <a href="{{ route('admin.agents.index') }}" class="btn-back-modern">
                <i class="fas fa-arrow-left mr-1"></i> Back to Agents
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">

        {{-- =================================== --}}
        {{-- 1. AGENT IDENTITY & CONTACT HEADER  --}}
        {{-- =================================== --}}
        <div class="card modern-card border-0 mb-4 profile-header-card shadow-sm">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-center">
                    {{-- Avatar & Basic Identity --}}
                    <div class="col-lg-7 col-md-12 d-flex align-items-start align-items-sm-center flex-column flex-sm-row mb-3 mb-lg-0" style="gap: 18px;">
                        <div class="profile-avatar shadow-sm position-relative">
                            {{ strtoupper(substr($agent->name, 0, 1)) }}
                            <span class="status-indicator-dot {{ $agent->is_active ? 'bg-success' : 'bg-danger' }}" 
                                  title="{{ $agent->is_active ? 'Account Active' : 'Account Suspended' }}"></span>
                        </div>

                        <div class="profile-info flex-grow-1" style="min-width: 0;">
                            <div class="d-flex flex-wrap align-items-center mb-1" style="gap: 8px;">
                                <h2 class="font-weight-bold text-dark m-0" style="font-size: 1.45rem; letter-spacing: -0.02em;">
                                    {{ $agent->name }}
                                </h2>

                                @if($agent->is_active)
                                    <span class="custom-badge badge-success-soft">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                @else
                                    <span class="custom-badge badge-danger-soft">
                                        <i class="fas fa-ban mr-1"></i> Suspended
                                    </span>
                                @endif

                                <span class="agent-code-tag">
                                    <i class="fas fa-id-badge text-primary mr-1"></i> {{ $agent->agent_code }}
                                </span>
                            </div>

                            {{-- Contact Chips --}}
                            <div class="d-flex flex-wrap align-items-center mt-2" style="gap: 10px;">
                                @if($agent->mobile_number)
                                    <a href="tel:{{ $agent->mobile_number }}" class="contact-chip" title="Click to Call">
                                        <i class="fas fa-phone-alt text-primary mr-1"></i> {{ $agent->mobile_number }}
                                    </a>
                                @endif

                                @php
                                    $rawWa = $agent->whatsapp_no ?? $agent->mobile_number;
                                    $waDigits = preg_replace('/[^0-9]/', '', (string)$rawWa);
                                    if ($waDigits && strlen($waDigits) === 10) {
                                        $waDigits = '91' . $waDigits;
                                    }
                                @endphp

                                @if($waDigits)
                                    <a href="https://wa.me/{{ $waDigits }}" target="_blank" class="contact-chip chip-whatsapp" title="Open WhatsApp Chat">
                                        <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                                    </a>
                                @endif

                                @if($agent->email)
                                    <a href="mailto:{{ $agent->email }}" class="contact-chip" title="Send Email">
                                        <i class="fas fa-envelope text-info mr-1"></i> {{ $agent->email }}
                                    </a>
                                @endif

                                @if($agent->address)
                                    <span class="contact-chip" title="{{ $agent->address }}">
                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ Str::limit($agent->address, 30) }}
                                    </span>
                                @endif

                                @if($agent->marketer)
                                    <span class="contact-chip chip-marketer" title="Assigned Marketer">
                                        <i class="fas fa-user-tie text-purple mr-1"></i> Marketer: {{ $agent->marketer->name }}
                                    </span>
                                @endif

                                <span class="contact-chip text-muted">
                                    <i class="fas fa-calendar-check text-secondary mr-1"></i> Joined {{ $agent->created_at?->format('d M Y') ?? 'Recently' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="col-lg-5 col-md-12 text-lg-right mt-3 mt-lg-0">
                        <div class="d-flex flex-wrap justify-content-lg-end align-items-center" style="gap: 8px;">
                            <form action="{{ route('admin.agents.toggle-status', $agent) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ $agent->is_active ? 'Are you sure you want to suspend this agent?' : 'Are you sure you want to activate this agent?' }}');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $agent->is_active ? 'btn-outline-danger' : 'btn-success' }} font-weight-bold shadow-sm" style="border-radius: 8px; padding: 7px 14px;">
                                    <i class="fas {{ $agent->is_active ? 'fa-user-slash' : 'fa-user-check' }} mr-1"></i>
                                    {{ $agent->is_active ? 'Suspend Agent' : 'Activate Agent' }}
                                </button>
                            </form>

                            <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-sm btn-primary font-weight-bold shadow-sm" style="border-radius: 8px; padding: 7px 14px;">
                                <i class="fas fa-edit mr-1"></i> Edit Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================================== --}}
        {{-- 2. WORK VELOCITY & STATUS KPIS      --}}
        {{-- =================================== --}}
        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
            <h5 class="font-weight-bold text-dark m-0">
                <i class="fas fa-tachometer-alt text-primary mr-2"></i> Work & Performance Overview
            </h5>
            <span class="badge badge-light border px-2 py-1 text-muted text-xs font-weight-bold">
                Session: {{ $currentSessionLabel }}
            </span>
        </div>

        {{-- Row 1: Applications Status Pipeline --}}
        <div class="row mb-4">
            {{-- Total Applications --}}
            <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="metric-label">Total Applications</span>
                        <div class="metric-icon bg-blue-soft text-primary">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                    <div class="metric-value text-dark">{{ number_format($stats['applications']) }}</div>
                    <div class="text-xs text-muted mt-1">
                        <i class="fas fa-check text-success mr-1"></i> All submitted cases
                    </div>
                </div>
            </div>

            {{-- Completed Applications --}}
            <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="metric-label">Completed / Filed</span>
                        <div class="metric-icon bg-green-soft text-success">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                    <div class="metric-value text-success">{{ number_format($stats['completed']) }}</div>
                    <div class="text-xs text-muted mt-1">
                        <span class="badge badge-success font-weight-bold px-1.5 py-0.5 mr-1">{{ $stats['completion_rate'] }}%</span> completion rate
                    </div>
                </div>
            </div>

            {{-- Pending Applications --}}
            <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="metric-label">In Pipeline / Review</span>
                        <div class="metric-icon bg-orange-soft text-orange">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                    <div class="metric-value text-orange">{{ number_format($stats['pending']) }}</div>
                    <div class="text-xs text-muted mt-1">
                        <i class="fas fa-clock text-warning mr-1"></i> Awaiting team processing
                    </div>
                </div>
            </div>

            {{-- Incomplete / Action Required --}}
            <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="metric-label">Draft / Needs Action</span>
                        <div class="metric-icon bg-red-soft text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="metric-value text-danger">{{ number_format($stats['incomplete']) }}</div>
                    <div class="text-xs text-muted mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Drafts or payment failed
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Financials & Earnings --}}
        <div class="row mb-4">
            {{-- Gross Volume --}}
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="metric-label">Gross Client Volume</span>
                        <div class="metric-icon bg-blue-soft text-primary">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="metric-value text-dark">₹{{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="text-xs text-muted mt-1">Total application fees generated from clients</div>
                </div>
            </div>

            {{-- Total Commission Earned --}}
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="metric-card bg-white shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="metric-label">Total Commission Earned</span>
                        <div class="metric-icon bg-purple-soft text-purple">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="metric-value text-dark">₹{{ number_format($stats['commission_total'], 2) }}</div>
                    <div class="text-xs text-success mt-1 font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Credited automatically upon filing each application
                    </div>
                </div>
            </div>
        </div>

        {{-- =================================== --}}
        {{-- 3. SERVICE PORTFOLIO BREAKDOWN      --}}
        {{-- =================================== --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card modern-card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-dark" style="font-size: 1.05rem;">
                            <i class="fas fa-briefcase text-primary mr-2"></i> Service Portfolio Breakdown
                        </h5>
                        <span class="text-xs text-muted font-weight-bold">{{ $serviceStats->count() }} Services Active</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="bg-light text-muted text-xs text-uppercase">
                                    <tr>
                                        <th class="border-top-0 pl-3">Service Name</th>
                                        <th class="border-top-0 text-center">Applications</th>
                                        <th class="border-top-0 text-right">Gross Volume</th>
                                        <th class="border-top-0 text-right pr-3">Commission Earned</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($serviceStats as $stat)
                                        @php
                                            $svcPercentage = $stats['applications'] > 0 ? round(($stat->count / $stats['applications']) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td class="pl-3 font-weight-bold text-dark">
                                                {{ $stat->service->name ?? 'Unknown Service' }}
                                                <div class="progress mt-1" style="height: 4px; width: 140px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $svcPercentage }}%"></div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="custom-badge badge-info-soft font-weight-bold">
                                                    {{ $stat->count }} <span class="text-xs text-muted font-weight-normal">({{ $svcPercentage }}%)</span>
                                                </span>
                                            </td>
                                            <td class="text-right font-weight-bold" style="color: var(--green);">
                                                ₹{{ number_format($stat->total_amount, 2) }}
                                            </td>
                                            <td class="text-right font-weight-bold pr-3 text-dark">
                                                ₹{{ number_format($stat->total_commission, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                No service submissions recorded yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================================== --}}
        {{-- 4. AGENT APPLICATIONS WORK HISTORY  --}}
        {{-- =================================== --}}
        <div class="card modern-card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
                    <div>
                        <h5 class="m-0 font-weight-bold text-dark" style="font-size: 1.15rem;">
                            <i class="fas fa-tasks text-primary mr-2"></i> Applications Submitted by {{ $agent->name }}
                        </h5>
                        <span class="text-xs text-muted">Click View to open complete filing data & documents</span>
                    </div>

                    {{-- Search Form --}}
                    <form action="{{ route('admin.agents.show', $agent) }}" method="GET" class="d-flex align-items-center" style="gap: 8px;">
                        <input type="hidden" name="session" value="{{ request('session') }}">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search App ID or PAN..." style="border-radius: 6px 0 0 6px;">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('admin.agents.show', [$agent, 'session' => request('session'), 'status' => $statusFilter]) }}" class="btn btn-sm btn-outline-secondary" title="Clear Search">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Filter Tabs --}}
                <div class="d-flex flex-wrap align-items-center mt-3" style="gap: 8px;">
                    <a href="{{ route('admin.agents.show', [$agent, 'session' => request('session'), 'status' => 'all', 'search' => request('search')]) }}"
                       class="app-filter-tab {{ $statusFilter === 'all' ? 'active' : '' }}">
                        All ({{ $stats['applications'] }})
                    </a>
                    <a href="{{ route('admin.agents.show', [$agent, 'session' => request('session'), 'status' => 'completed', 'search' => request('search')]) }}"
                       class="app-filter-tab {{ $statusFilter === 'completed' ? 'active text-success' : '' }}">
                        <i class="fas fa-check-circle mr-1"></i> Completed ({{ $stats['completed'] }})
                    </a>
                    <a href="{{ route('admin.agents.show', [$agent, 'session' => request('session'), 'status' => 'pending', 'search' => request('search')]) }}"
                       class="app-filter-tab {{ $statusFilter === 'pending' ? 'active text-warning' : '' }}">
                        <i class="fas fa-clock mr-1"></i> Pending Review ({{ $stats['pending'] }})
                    </a>
                    <a href="{{ route('admin.agents.show', [$agent, 'session' => request('session'), 'status' => 'incomplete', 'search' => request('search')]) }}"
                       class="app-filter-tab {{ $statusFilter === 'incomplete' ? 'active text-danger' : '' }}">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Action Needed ({{ $stats['incomplete'] }})
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="pl-3" style="width: 75px;">App ID</th>
                                <th>Service Type</th>
                                <th>Primary Client Data</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Commission</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Payment</th>
                                <th>Submitted Date</th>
                                <th class="text-right pr-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                @php
                                    $formData = is_string($app->form_data) ? json_decode($app->form_data, true) : ($app->form_data ?? []);
                                    $primaryField = $app->service->primary_data_field ?? 'pan_number';
                                    $primaryVal = $formData[$primaryField] ?? ($formData['applicant_name'] ?? ($formData['pan_number'] ?? null));
                                    if (is_array($primaryVal)) {
                                        $primaryVal = implode(', ', $primaryVal);
                                    }
                                @endphp
                                <tr>
                                    <td class="pl-3 font-weight-bold text-dark">
                                        #{{ $app->id }}
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $app->service->name ?? 'Service #' . $app->service_id }}</div>
                                    </td>
                                    <td>
                                        @if($primaryVal)
                                            <span class="font-weight-bold text-dark font-mono" style="font-size: 0.9rem;">{{ $primaryVal }}</span>
                                        @else
                                            <span class="text-muted text-xs font-italic">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-weight-bold" style="color: var(--green);">
                                        ₹{{ number_format($app->amount, 2) }}
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        ₹{{ number_format($app->commission_amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $st = strtoupper($app->status->value ?? $app->status);
                                        @endphp
                                        @if($st === 'COMPLETED')
                                            <span class="custom-badge badge-success-soft font-weight-bold">COMPLETED</span>
                                        @elseif(in_array($st, ['SUBMITTED', 'UNDER_REVIEW', 'IN_PROGRESS', 'E_FILING', 'OTP_VERIFICATION']))
                                            <span class="custom-badge badge-info-soft font-weight-bold">{{ str_replace('_', ' ', $st) }}</span>
                                        @elseif(in_array($st, ['DRAFT', 'CANCELLED', 'REJECTED']))
                                            <span class="custom-badge badge-danger-soft font-weight-bold">{{ $st }}</span>
                                        @else
                                            <span class="custom-badge badge-info-soft font-weight-bold">{{ $st }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $pay = strtoupper($app->payment_status->value ?? $app->payment_status);
                                        @endphp
                                        @if($pay === 'PAID')
                                            <span class="badge badge-success px-2 py-1 font-weight-bold">PAID</span>
                                        @elseif($pay === 'FAILED')
                                            <span class="badge badge-danger px-2 py-1 font-weight-bold">FAILED</span>
                                        @else
                                            <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold">{{ $pay }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted text-sm">
                                        {{ $app->submitted_at ? $app->submitted_at->format('d M Y') : $app->created_at->format('d M Y') }}
                                    </td>
                                    <td class="text-right pr-3">
                                        <a href="{{ route('admin.applications.show', $app->id) }}" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 6px; padding: 4px 12px;" title="View Application Details">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block text-gray-300"></i>
                                        No applications found under this status filter
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($applications->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap">
                    <span class="text-xs text-muted mb-2 mb-sm-0">
                        Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of {{ $applications->total() }} applications
                    </span>
                    <div>
                        {{ $applications->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>

    </div>
@endsection

@section('css')
    <style>
        /* =========================================
           BASE CARD & PROFILE HEADER
        ========================================= */
        .modern-card {
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
            overflow: hidden;
            border: 1px solid #e2e8f0 !important;
        }

        .profile-header-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        }

        /* Dynamic Avatar Circle */
        .profile-avatar {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0044b2, #1e9c5d);
            color: #ffffff;
            font-size: 2.1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 12px rgba(0, 68, 178, 0.18);
        }

        .status-indicator-dot {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }

        /* Agent Code Tag */
        .agent-code-tag {
            background: #f1f5f9;
            color: #334155;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.95rem;
            font-weight: 700;
            border: 1px solid #e2e8f0;
        }

        /* Contact Chips */
        .contact-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            text-decoration: none !important;
            transition: all 0.15s ease;
        }

        .contact-chip:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
            transform: translateY(-1px);
        }

        .chip-whatsapp {
            background: #dcfce7;
            border-color: #86efac;
            color: #15803d;
        }
        .chip-whatsapp:hover {
            background: #bbf7d0;
            color: #14532d;
        }

        .chip-marketer {
            background: #f5f3ff;
            border-color: #ddd6fe;
            color: #6b21a8;
        }

        /* Soft Badges */
        .custom-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .badge-success-soft {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-info-soft {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .badge-danger-soft {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* =========================================
           METRIC CARDS
        ========================================= */
        .metric-card {
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e2e8f0 !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
        }

        .metric-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        /* Icon Colors */
        .bg-blue-soft {
            background: rgba(0, 68, 178, 0.1);
        }
        .text-primary {
            color: #0044b2 !important;
        }
        .bg-green-soft {
            background: rgba(30, 156, 93, 0.1);
        }
        .text-success {
            color: #1e9c5d !important;
        }
        .bg-orange-soft {
            background: rgba(245, 158, 11, 0.12);
        }
        .text-orange {
            color: #d97706 !important;
        }
        .bg-purple-soft {
            background: #f3e8ff;
        }
        .text-purple {
            color: #7e22ce !important;
        }
        .bg-red-soft {
            background: #fee2e2;
        }

        .metric-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-value {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        /* App Filter Tabs */
        .app-filter-tab {
            display: inline-flex;
            align-items: center;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            text-decoration: none !important;
            transition: all 0.15s ease;
        }

        .app-filter-tab:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .app-filter-tab.active {
            background: #0044b2;
            color: #ffffff !important;
            border-color: #0044b2;
        }
    </style>
@endsection
