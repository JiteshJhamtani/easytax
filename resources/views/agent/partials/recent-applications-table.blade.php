{{-- resources/views/agent/partials/recent-applications-table.blade.php --}}
<div class="card dashboard-card mt-5 mb-5 shadow-sm border-0" style="border-radius: 16px;">
    <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="card-title font-weight-bold text-dark m-0" style="font-size: 1.25rem;">Recent Activity</h3>
            <p class="text-muted small m-0 mt-1">Your latest submitted applications</p>
        </div>
        <a href="{{ route('agent.applications.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-weight: 600;">View All</a>
    </div>
    
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover modern-table mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="border-top-0 border-bottom-0 pl-4">App ID</th>
                    <th class="border-top-0 border-bottom-0">Service Type</th>
                    <th class="border-top-0 border-bottom-0">Date Submitted</th>
                    <th class="border-top-0 border-bottom-0">Status</th>
                    <th class="border-top-0 border-bottom-0 text-right">Amount</th>
                    <th class="border-top-0 border-bottom-0 text-right pr-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentApplications as $app)
                    <tr>
                        <td class="pl-4 align-middle"><span class="text-muted font-weight-bold">#{{ $app->id }}</span></td>
                        <td class="align-middle"><span class="font-weight-bold text-dark">{{ $app->service->name ?? 'Unknown Service' }}</span></td>
                        <td class="align-middle">
                            <span class="d-block font-weight-bold text-dark">{{ $app->created_at->format('d M, Y') }}</span>
                            <span class="text-muted small">{{ $app->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="align-middle">
                            @php
                                $statusClass = match (strtolower($app->status->value ?? 'pending')) {
                                    'completed' => 'premium-badge-success',
                                    'pending'   => 'premium-badge-warning',
                                    'rejected'  => 'premium-badge-danger',
                                    default     => 'premium-badge-primary',
                                };
                            @endphp
                            <span class="premium-badge {{ $statusClass }}">{{ ucfirst($app->status->value ?? 'Pending') }}</span>
                        </td>
                        <td class="text-right align-middle font-weight-bold text-dark">₹{{ number_format($app->amount, 2) }}</td>
                        <td class="text-right align-middle pr-4">
                            <a href="{{ route('agent.applications.show', $app->id) }}" class="btn-premium-view">
                                View <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox mb-3 d-block" style="font-size: 2rem; opacity: 0.5;"></i>
                            No recent applications found. Start submitting to see them here!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>