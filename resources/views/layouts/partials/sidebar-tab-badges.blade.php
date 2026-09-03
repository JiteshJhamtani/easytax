@php
    $visibleBadges = array_filter($sidebarBadges[$tabKey] ?? [], fn($b) => ($b['count'] ?? 0) > 0);
@endphp
@if(!empty($visibleBadges))
    <span class="sb-badge-cluster" style="display: inline-flex; align-items: center; gap: 0.35rem; margin-left: auto; flex-shrink: 0;">
        @foreach($visibleBadges as $b)
            <span class="sb-badge {{ $b['color_class'] }}" 
                  title="{{ $b['tooltip'] }}" 
                  data-toggle="tooltip"
                  style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 5.5px; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; line-height: 1; color: {{ $b['text'] ?? '#ffffff' }} !important; background-color: {{ $b['bg'] ?? '#3b82f6' }} !important; box-shadow: 0 1px 3px rgba(0,0,0,0.35); user-select: none;">
                {{ $b['formatted_count'] }}
            </span>
        @endforeach
    </span>
@endif
