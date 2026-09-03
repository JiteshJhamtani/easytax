@forelse($topAgents as $i => $agent)
<tr class="top-agent-row">
    <td class="text-center align-middle" style="width: 50px;">
        @if($i === 0)
            <span class="rank-pill rank-1 shadow-sm" title="Rank 1">
                <i class="fas fa-crown mr-1"></i>1
            </span>
        @elseif($i === 1)
            <span class="rank-pill rank-2 shadow-sm" title="Rank 2">
                <i class="fas fa-medal mr-1"></i>2
            </span>
        @elseif($i === 2)
            <span class="rank-pill rank-3 shadow-sm" title="Rank 3">
                <i class="fas fa-award mr-1"></i>3
            </span>
        @else
            <span class="rank-pill rank-other">
                {{ $i + 1 }}
            </span>
        @endif
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            <div class="agent-mini-avatar mr-2 shadow-sm">
                {{ strtoupper(substr($agent->name, 0, 1)) }}
            </div>
            <div>
                <a href="{{ route('admin.agents.show', $agent->id) }}" class="agent-profile-link font-weight-bold" title="View Agent Profile">
                    {{ $agent->name }}
                    <i class="fas fa-external-link-alt ml-1 text-xs opacity-50"></i>
                </a>
                <div>
                    <span class="code-tag mt-1" style="display:inline-block; font-size: 0.72rem; padding: 2px 6px;">{{ $agent->agent_code }}</span>
                </div>
            </div>
        </div>
    </td>
    <td class="text-center align-middle">
        <span class="custom-badge badge-info-soft font-weight-bold">{{ $agent->applications_count }}</span>
    </td>
    @if(strtoupper(auth()->user()->role) !== 'SUB-ADMIN')
    <td class="text-right align-middle font-weight-bold" style="color: var(--green); font-size: 0.95rem;">
        ₹{{ number_format($agent->total_revenue, 2) }}
    </td>
    <td class="text-right align-middle font-weight-bold" style="color: var(--slate); font-size: 0.95rem;">
        ₹{{ number_format($agent->commission_earned, 2) }}
    </td>
    @endif
</tr>
@empty
<tr>
    <td colspan="5" class="text-center text-muted py-4">
        <i class="fas fa-inbox fa-2x mb-2 d-block text-gray-300"></i>
        No agents found for this period
    </td>
</tr>
@endforelse
