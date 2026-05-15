@extends('layouts.admin')

@section('title', 'Operator Profile: ' . $operator->name)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.team.index') }}" class="text-muted text-decoration-none mb-2 d-inline-block"><i class="fas fa-arrow-left"></i> Back to Team</a>
            <h3 class="font-weight-bold text-dark mb-0">
                {{ $operator->name }} 
                @if($operator->is_active)
                    <span class="badge badge-success align-middle ml-2" style="font-size: 0.8rem;">Active</span>
                @else
                    <span class="badge badge-danger align-middle ml-2" style="font-size: 0.8rem;">Suspended</span>
                @endif
            </h3> 
            <p class="text-muted mb-0"><i class="fas fa-envelope mr-1"></i> {{ $operator->email }} | <i class="fas fa-phone mr-1"></i> {{ $operator->mobile_number ?? 'No Phone' }}</p>
        </div>
        <div>
            <a href="{{ route('admin.team.create-payout', $operator->id) }}" class="btn btn-success font-weight-bold shadow-sm">
                <i class="fas fa-rupee-sign mr-1"></i> Record Payout
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger shadow-sm rounded">
            <ul class="mb-0">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-lg h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="letter-spacing: 1px;">Workload Summary</h6>
                    <div class="d-flex justify-content-between text-center">
                        <div>
                            <h3 class="font-weight-bold text-dark mb-0">{{ $totalAssigned }}</h3>
                            <span class="text-muted small">Assigned</span>
                        </div>
                        <div>
                            <h3 class="font-weight-bold text-success mb-0">{{ $totalCompleted }}</h3>
                            <span class="text-muted small">Completed</span>
                        </div>
                        <div>
                            <h3 class="font-weight-bold text-warning mb-0">{{ $totalPending }}</h3>
                            <span class="text-muted small">Pending</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-lg h-100" style="background: linear-gradient(135deg, #1e9c5d 0%, #15804c 100%); color: white;">
                <div class="card-body">
                    <h6 class="text-uppercase font-weight-bold mb-3" style="letter-spacing: 1px; color: #d1fae5;">Financial Summary</h6>
                    <div class="d-flex justify-content-between text-center">
                        <div>
                            <h3 class="font-weight-bold mb-0">₹{{ number_format($totalEarned) }}</h3>
                            <span class="small" style="color: #d1fae5;">Total Earned</span>
                        </div>
                        <div>
                            <h3 class="font-weight-bold mb-0">₹{{ number_format($totalPaid) }}</h3>
                            <span class="small" style="color: #d1fae5;">Total Paid</span>
                        </div>
                        <div>
                            <h3 class="font-weight-bold mb-0 {{ $balanceDue > 0 ? 'text-warning' : '' }}">₹{{ number_format($balanceDue) }}</h3>
                            <span class="small" style="color: #d1fae5;">Balance Due</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-4">
            <ul class="nav nav-tabs border-bottom-0" id="operatorTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold text-dark border-0 pb-3" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab" style="border-bottom: 3px solid #1e9c5d !important; background: transparent;">Assigned Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-muted border-0 pb-3" id="rates-tab" data-toggle="tab" href="#rates" role="tab" style="background: transparent;">Service Rates</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-muted border-0 pb-3" id="payouts-tab" data-toggle="tab" href="#payouts" role="tab" style="background: transparent;">Payout History</a>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content" id="operatorTabsContent">
                
                <div class="tab-pane fade show active p-4" id="tasks" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light text-muted text-uppercase small">
                                <tr>
                                    <th>App ID</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Pending Reason</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $app)
                                <tr>
                                    <td class="font-weight-bold">#{{ $app->id }}</td>
                                    <td>{{ $app->service->name ?? 'Unknown' }}</td>
                                    <td><span class="badge badge-info">{{ $app->status }}</span></td>
                                    <td class="text-muted">{{ $app->updated_at->format('d M Y') }}</td>
                                    <td>
                                        @if($app->pending_reason)
                                            <span class="text-danger small font-weight-bold"><i class="fas fa-exclamation-circle mr-1"></i>{{ $app->pending_reason }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('admin.applications.show', $app->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No applications assigned yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $applications->links() }}</div>
                    </div>
                </div>

                <div class="tab-pane fade p-4" id="rates" role="tabpanel">
                    <form action="{{ route('admin.team.save-rates', $operator->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            @foreach($services as $service)
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold text-dark small">{{ $service->name }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                    <input type="number" step="0.01" name="rates[{{ $service->id }}]" class="form-control" value="{{ $currentRates[$service->id] ?? 0 }}" placeholder="0.00">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <hr>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">Save Rate Card</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade p-4" id="payouts" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-calendar-alt mr-1"></i> Month-wise Earnings</h6>
                            <table class="table table-sm table-hover">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-center">Apps Completed</th>
                                        <th class="text-right">Earned</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($monthlyEarnings as $month)
                                    <tr>
                                        <td class="font-weight-bold text-dark">{{ $month->month_name }}</td>
                                        <td class="text-center"><span class="badge badge-info">{{ $month->total_apps }}</span></td>
                                        <td class="text-right font-weight-bold text-success">₹{{ number_format($month->monthly_total, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No completed apps yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-history mr-1"></i> Payout History</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th>Date Paid</th>
                                        <th class="text-right">Amount</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payouts as $payout)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payout->paid_at)->format('d M Y') }}</td>
                                        <td class="text-right font-weight-bold text-dark">₹{{ number_format($payout->amount, 2) }}</td>
                                        <td class="text-muted small">{{ $payout->payment_note ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No payouts recorded yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Simple script to handle Tab highlighting
    $('#operatorTabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
        $('#operatorTabs a').css('border-bottom', 'none').removeClass('text-dark').addClass('text-muted');
        $(this).css('border-bottom', '3px solid #1e9c5d').removeClass('text-muted').addClass('text-dark');
    });
</script>
@endsection