{{-- resources/views/agent/partials/gift-timeline-multi.blade.php --}}
@php $gift = $group['milestones'][0]; @endphp
<div class="multi-card mb-4">
    <div class="mc-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="tc-period text-uppercase font-weight-bold mb-1" style="color: #7a8799;">
                {{ $group['period_label'] }} &bull; {{ $group['period_range'] }} &bull; Multi-service
            </div>
            <h4 class="tc-title font-weight-bold mb-0 text-dark">{{ $gift['icon'] ?? '🎁' }} {{ $gift['name'] }}</h4>
        </div>
        <div>
            @if ($gift['unlocked'])
                <span class="mc-badge mc-badge-success"><i class="fas fa-check-circle mr-1"></i> Eligible</span>
            @else
                <span class="mc-badge mc-badge-warning"><i class="fas fa-lock mr-1"></i> Locked</span>
            @endif
        </div>
    </div>

    <div class="mc-circles-row d-flex flex-wrap gap-4">
        @foreach ($gift['conditions'] as $ci => $cond)
            <div class="mc-circle-wrap text-center mr-4 mb-3">
                <div class="mc-svg-container position-relative mx-auto mb-2" style="width: 80px; height: 80px;">
                    <svg viewBox="0 0 80 80" class="w-100 h-100">
                        <circle cx="40" cy="40" r="32" fill="none" stroke="#e8ecf0" stroke-width="6" />
                        <circle cx="40" cy="40" r="32" fill="none"
                            stroke="{{ $cond['unlocked'] ? '#1E9C5D' : '#e5e7eb' }}"
                            stroke-width="6" stroke-linecap="round"
                            stroke-dasharray="201"
                            stroke-dashoffset="{{ 201 - (201 * ($cond['pct'] / 100)) }}"
                            transform="rotate(-90 40 40)"
                            style="transition: stroke-dashoffset 1s ease-out;" />
                    </svg>
                    <div class="position-absolute d-flex flex-column align-items-center justify-content-center" style="inset:0;">
                        <span class="font-weight-bold text-dark" style="font-size: 1rem;">{{ $cond['pct'] }}%</span>
                    </div>
                </div>
                <div class="mc-label text-dark font-weight-bold" style="font-size: 0.85rem;">{{ $cond['service_name'] }}</div>
                <div class="mc-progress small text-muted">{{ number_format($cond['agent_count']) }} / {{ number_format($cond['min_count']) }}</div>
            </div>
        @endforeach
    </div>
    
    <div class="mt-2 text-muted small">
        @if (!$gift['unlocked'])
            <i class="fas fa-info-circle mr-1"></i> Complete all conditions above to unlock this gift.
        @else
            <span class="text-success font-weight-bold">🎉 You qualify for this gift!</span>
        @endif
    </div>
</div>