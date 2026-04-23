@extends('layouts.agent')

@section('title', 'Agent Workspace | EasyTax')

@section('content_header')
    <div class="workspace-header">
        <div>
            <h1 class="workspace-title">Welcome back, {{ optional(Auth::user())->name ?? 'Agent' }}</h1>
            <p class="workspace-subtitle">Here is what's happening with your applications today.</p>
        </div>
        <div class="workspace-actions">
            <a href="{{ route('services.index') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-2"></i> New Application
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="dashboard-container">

        {{-- 1. KPI ROW (Using Blade Components) --}}
        <div class="kpi-section-wrapper mb-4">
            <h4 class="kpi-section-title">Application Funnels</h4>
            <div class="row kpi-row">
                <x-agent.kpi-card title="Total Applications" :value="$stats->total_applications ?? 0" icon="svg.kpi-total" />
                <x-agent.kpi-card title="Completed" :value="$stats->completed_applications ?? 0" icon="svg.kpi-completed" />
                <x-agent.kpi-card title="In Progress" :value="$stats->pending_applications ?? 0" icon="svg.kpi-progress" />
            </div>
        </div>

        {{-- 2. CHARTS SECTION --}}
        <div class="row mt-4 mb-4">
            <div class="col-lg-8 col-md-12 mb-4 mb-lg-0">
                <div class="card dashboard-card h-100 shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h3 class="card-title font-weight-bold text-dark" style="font-size: 1.1rem;">Application Velocity</h3>
                        <p class="text-muted small">Applications submitted over the last 6 months</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="chart-container" style="position:relative; height:280px; width:100%">
                            <canvas id="velocityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card dashboard-card h-100 shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h3 class="card-title font-weight-bold text-dark" style="font-size: 1.1rem;">Overview</h3>
                        <p class="text-muted small">Current status breakdown</p>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center px-4 pb-4">
                        <div class="chart-container" style="position:relative; height:220px; width:100%">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- 3. GIFT MILESTONES --}}
        @if (!empty($giftGroups))
            <div class="mt-5 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="font-size: 2rem;">🎁</div>
                        <div>
                            <h3 class="font-weight-bold text-dark m-0" style="font-size: 1.25rem;">Your gift milestones</h3>
                            <p class="text-muted small m-0 mt-1">Keep submitting to unlock more rewards</p>
                        </div>
                    </div>
                    <a href="{{ route('agent.gifts') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-weight: 600;">View All</a>
                </div>

                <div class="gift-groups-grid">
                    @foreach ($giftGroups as $groupIdx => $group)
                        @if ($group['type'] === 'single')
                            @include('agent.partials.gift-timeline-single', ['group' => $group])
                        @else
                            @include('agent.partials.gift-timeline-multi', ['group' => $group])
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 4. RECENT APPLICATIONS TABLE --}}
        @include('agent.partials.recent-applications-table', ['recentApplications' => $recentApplications])

    </div>
@endsection

@section('css')
    <style>
        @include('agent.partials.dashboard-css')
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @include('agent.partials.dashboard-js')
    </script>
@stop