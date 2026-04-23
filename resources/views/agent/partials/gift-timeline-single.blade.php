{{-- resources/views/agent/partials/gift-timeline-single.blade.php --}}
<div class="sv-card gm-card gm-card--monthly mb-4" style="--gm-accent: #1E9C5D; --gm-track: #EDF7F4;">
    <div class="gm-card__top">
        <div>
            <div class="gm-card__label">{{ $group['period_label'] }} &bull; {{ $group['period_range'] }}</div>
            <h4 class="gm-card__title" style="font-size: 1.15rem;">{{ $group['service_name'] }}</h4>
        </div>
        <div class="gm-card__count">
            <span class="gm-card__count-num">{{ $group['agent_count'] }}</span>
            <span class="gm-card__count-label">Submissions</span>
        </div>
    </div>

    <div class="gm-track-area">
        <div class="gm-track">
            <div class="gm-track__fill" style="width: {{ $group['progress_pct'] }}%;"></div>
        </div>

        @php
            $milestones = $group['milestones'];
            $milestoneCount = count($milestones);
        @endphp

        @foreach ($milestones as $mi => $m)
            @php
                $leftPos = $milestoneCount > 1 ? ($mi / ($milestoneCount - 1)) * 100 : 50;
            @endphp
            <div class="gm-dot-anchor tooltip-anchor" style="left: {{ $leftPos }}%;">
                
                <div class="gm-dot-icon {{ $m['unlocked'] ? 'is-unlocked' : '' }}">
                    @if (!empty($m['image_url']) || !empty($m['banner_url']))
                        <img src="{{ $m['image_url'] ?? $m['banner_url'] }}" alt="{{ $m['name'] }}" style="width:28px;height:28px;object-fit:cover;border-radius:50%">
                    @else
                        <span style="font-size: 14px;">🎁</span>
                    @endif
                </div>

                <div class="gm-dot-label">
                    <span class="gm-dot-label__count">{{ $m['min_count'] >= 1000 ? ($m['min_count']/1000).'k' : $m['min_count'] }}</span>
                   
                </div>

                {{-- HIDDEN HOVER TOOLTIP --}}
                <div class="gm-tooltip">
                    @if (!empty($m['image_url']) || !empty($m['banner_url']))
                        <img class="gm-tooltip__img" src="{{ $m['image_url'] ?? $m['banner_url'] }}" alt="{{ $m['name'] }}">
                    @endif
                    <div class="gm-tooltip__name">{{ $m['name'] }}</div>
                    <div class="gm-tooltip__row">
                        <span>Target</span>
                        <span>{{ number_format($m['min_count']) }}</span>
                    </div>
                    <div class="gm-tooltip__row">
                        <span>Your count</span>
                        <span>{{ number_format($group['agent_count']) }}</span>
                    </div>
                    @if ($m['unlocked'])
                        <div class="gm-tooltip__unlocked"><i class="fas fa-check-circle mr-1"></i> Unlocked!</div>
                    @else
                        <div class="gm-tooltip__row mt-2 pt-2 border-top">
                            <span>Still need</span>
                            <span class="text-danger">{{ number_format($m['needed']) }}</span>
                        </div>
                        <div class="gm-tooltip__locked"><i class="fas fa-lock mr-1"></i> Locked</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="gm-hint mt-4 pt-2">
        @if($group['next_milestone'])
            <span class="gm-hint__pill">{{ $group['next_milestone']['needed'] }} more</span>
            <span class="ml-1 text-muted">to unlock <strong class="text-dark">{{ $group['next_milestone']['name'] }}</strong></span>
        @else
            <span class="gm-hint__pill" style="background:#1E9C5D; color:white;">Completed</span>
            <span class="ml-2 text-muted font-weight-bold">You have unlocked all rewards for this period!</span>
        @endif
    </div>
</div>