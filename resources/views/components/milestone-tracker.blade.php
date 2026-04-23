@props(['milestones'])

<style>
    /* Base Card Styling */
    .gm-card { position: relative; }
    .gm-card--monthly, .gm-card--quarterly, .gm-card--yearly { --gm-accent: #1E9C5D; --gm-track: #EDF7F4; }
    .gm-card__top { display: flex; justify-content: space-between; margin-bottom: 2rem; }
    .gm-card__label { font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #7a8799; margin-bottom: 0.4rem; }
    .gm-card__title { font-size: 1.1rem; font-weight: 800; color: #333; margin: 0; }
    
    .gm-card__count { text-align: right; }
    .gm-card__count-num { font-size: 2.2rem; font-weight: 800; line-height: 1; display: block; color: #333; }
    .gm-card__count-label { font-size: 0.75rem; font-weight: 600; color: #7a8799; }

    /* The Track */
    .gm-track-area { position: relative; margin: 1rem 0 4rem; z-index: 10; }
    .gm-track { height: 8px; border-radius: 99px; background: var(--gm-track); }
    .gm-track__fill { height: 100%; border-radius: 99px; width: 0; background: var(--gm-accent); transition: width 1.2s ease-out; }

    /* The Dot Anchors */
    .gm-dot-anchor { position: absolute; top: 50%; transform: translate(-50%, -50%); display: flex; flex-direction: column; align-items: center; cursor: pointer; z-index: 20; }
    .gm-dot-anchor:hover { z-index: 50; } /* Elevate hovered dot above others */

    .gm-dot-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--gm-track); border: 2px solid var(--gm-accent); transition: transform 0.2s; position: relative; z-index: 2; }
    .gm-dot-anchor:hover .gm-dot-icon { transform: scale(1.15); }
    .gm-dot-icon--unlocked-m, .gm-dot-icon--unlocked-q, .gm-dot-icon--unlocked-y { background: var(--gm-accent); border-color: #fff; box-shadow: 0 0 0 4px var(--gm-track); }

    .gm-dot-label { position: absolute; top: 45px; text-align: center; width: 80px; pointer-events: none; }
    .gm-dot-label__count { font-size: 0.75rem; font-weight: 800; display: block; color: var(--gm-accent); }

    /* ── THE PURE CSS TOOLTIP ENGINE ── */
    .gm-tooltip { 
        position: absolute; 
        bottom: 100%; 
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        margin-bottom: 18px; /* space for the arrow */
        background: #ffffff; 
        color: #111827; 
        border: 1px solid #e8ecf0; 
        border-radius: 12px; 
        padding: 1.25rem; 
        width: 220px; 
        
        /* Hidden by default */
        opacity: 0; 
        visibility: hidden;
        pointer-events: none; 
        
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); 
        z-index: 100; 
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.2) !important; 
    }
    
    /* Show on Hover */
    .gm-dot-anchor:hover .gm-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
        pointer-events: auto;
    }

    /* Tooltip Arrow */
    .gm-tooltip::after { 
        content: ''; 
        position: absolute; 
        top: 100%; 
        left: 50%;
        transform: translateX(-50%); 
        border: 8px solid transparent; 
        border-top-color: #ffffff; 
    }

    /* Edge Case: Prevent First Tooltip from falling off the left screen */
    .gm-tooltip--first { left: 0; transform: translateX(-18px) translateY(10px); }
    .gm-dot-anchor:hover .gm-tooltip--first { transform: translateX(-18px) translateY(0); }
    .gm-tooltip--first::after { left: 36px; }

    /* Edge Case: Prevent Last Tooltip from falling off the right screen */
    .gm-tooltip--last { left: auto; right: 0; transform: translateX(18px) translateY(10px); }
    .gm-dot-anchor:hover .gm-tooltip--last { transform: translateX(18px) translateY(0); }
    .gm-tooltip--last::after { left: auto; right: 36px; transform: none; }

    /* Inner Tooltip Content */
    .gm-tooltip__img { width: 100%; max-height: 100px; object-fit: cover; border-radius: 8px; margin-bottom: 0.6rem; display: block; }
    .gm-tooltip__name { font-size: 0.9rem; font-weight: 800; margin-bottom: 0.75rem; color: #111827; line-height: 1.3; }
    .gm-tooltip__row { font-size: 0.75rem; color: #6b7280; display: flex; justify-content: space-between; margin-top: 0.4rem; }
    .gm-tooltip__row span:last-child { color: #111827; font-weight: 700; }
    .gm-tooltip__unlocked { color: #1E9C5D; font-size: 0.8rem; font-weight: 800; margin-top: 0.8rem; }
    .gm-tooltip__locked { color: #ef4444; font-size: 0.8rem; font-weight: 800; margin-top: 0.8rem; }

    /* Footer Chips */
    .gm-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem; }
    .gm-chip { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: 20px; background: var(--gm-track); color: var(--gm-accent); }
    .gm-chip__dot { width: 6px; height: 6px; border-radius: 50%; background: var(--gm-accent); }
    .gm-hint { font-size: 0.85rem; color: #7a8799; display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem; }
    .gm-hint__pill { font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 20px; background: var(--gm-track); color: var(--gm-accent); }
</style>

@if (!empty($milestones))
    <div id="gm-root">
        @foreach ($milestones as $group)
            @php
                $count = $group['count'];
                $pct = $group['progress_pct'];
                $unlocked = collect($group['milestones'])->where('unlocked', true);
                $next = collect($group['milestones'])->firstWhere('unlocked', false);
                $milestoneCount = count($group['milestones']);
                $colorKey = substr($group['period_type'], 0, 1);
            @endphp

            <div class="sv-card gm-card gm-card--{{ $group['period_type'] }}" data-count="{{ $count }}" data-pct="{{ $pct }}" data-period="{{ $group['period_type'] }}">
                <div class="gm-card__top">
                    <div>
                        <div class="gm-card__label">{{ $group['period_label'] }} &bull; {{ $group['period_range'] }}</div>
                        <h3 class="gm-card__title">Gift Milestones</h3>
                    </div>
                    <div class="gm-card__count">
                        <span class="gm-card__count-num gm-counter" data-target="{{ $count }}">0</span>
                        <span class="gm-card__count-label">Submissions</span>
                    </div>
                </div>

                <div class="gm-track-area">
                    <div class="gm-track">
                        <div class="gm-track__fill" data-width="{{ $pct }}"></div>
                    </div>

                    @foreach ($group['milestones'] as $mi => $milestone)
                        <div class="gm-dot-anchor" data-index="{{ $mi }}" data-total="{{ $milestoneCount }}">
                            
                            {{-- THE TOOLTIP --}}
                            <div class="gm-tooltip @if($loop->first) gm-tooltip--first @elseif($loop->last) gm-tooltip--last @endif">
                                @if (!empty($milestone['banner_url']))
                                    <img class="gm-tooltip__img" src="{{ $milestone['banner_url'] }}" alt="{{ $milestone['name'] }}">
                                @endif
                                <div class="gm-tooltip__name">{{ $milestone['name'] }}</div>
                                <div class="gm-tooltip__row">
                                    <span>Threshold</span>
                                    <span>{{ number_format($milestone['min_count']) }}</span>
                                </div>
                                <div class="gm-tooltip__row">
                                    <span>Your count</span>
                                    <span>{{ number_format($count) }}</span>
                                </div>
                                @if ($milestone['unlocked'])
                                    <div class="gm-tooltip__unlocked">✓ Unlocked!</div>
                                @else
                                    <div class="gm-tooltip__row">
                                        <span>Still need</span>
                                        <span>{{ number_format($milestone['needed']) }}</span>
                                    </div>
                                    <div class="gm-tooltip__locked">Locked</div>
                                @endif
                            </div>

                            {{-- THE DOT ICON --}}
                            <div class="gm-dot-icon gm-dot-icon--{{ $milestone['unlocked'] ? 'unlocked' : 'locked' }}-{{ $colorKey }}">
                                <img src="{{ $milestone['banner_url'] ?? asset('img/default-gift.png') }}" alt="{{ $milestone['name'] }}" style="width:20px;height:20px;object-fit:cover;border-radius:50%">
                            </div>

                            {{-- THE LABEL BELOW DOT --}}
                            <div class="gm-dot-label">
                                <span class="gm-dot-label__count">{{ $milestone['min_count'] >= 1000 ? $milestone['min_count'] / 1000 . 'k' : $milestone['min_count'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($unlocked->isNotEmpty())
                    <div class="gm-chips">
                        @foreach ($unlocked as $m)
                            <span class="gm-chip"><span class="gm-chip__dot"></span>{{ $m['name'] }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="gm-hint">
                    @if ($next)
                        <span class="gm-hint__pill">{{ number_format($next['needed']) }} more</span>
                        to unlock <strong>{{ $next['name'] }}</strong>
                    @else
                        <span class="gm-hint__pill">All unlocked!</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.gm-card').forEach(card => {
            // 1. Animate Progress Bar safely
            const fill = card.querySelector('.gm-track__fill');
            if (fill) {
                setTimeout(() => { fill.style.width = fill.dataset.width + '%'; }, 150);
            }

            // 2. Animate Counter safely
            const counter = card.querySelector('.gm-counter');
            if (counter) {
                const target = parseInt(counter.dataset.target) || 0;
                let current = 0;
                const step = Math.max(1, Math.ceil(target / 60));
                const timer = setInterval(() => {
                    current = Math.min(current + step, target);
                    counter.textContent = current.toLocaleString();
                    if (current >= target) clearInterval(timer);
                }, 16);
            }

            // 3. Position the Dots safely
            const dots = card.querySelectorAll('.gm-dot-anchor');
            const total = dots.length;
            dots.forEach((dot, i) => {
                const pct = total > 1 ? (i / (total - 1)) * 100 : 50;
                dot.style.left = pct + '%';
            });
        });
    });
</script>