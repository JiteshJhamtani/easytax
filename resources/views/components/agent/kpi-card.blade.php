{{-- resources/views/components/agent/kpi-card.blade.php --}}
@props(['title', 'value', 'icon'])

<div class="col-lg-4 col-md-6 col-sm-12 mb-3">
    <div class="kpi-card h-100">
        <div class="kpi-icon kpi-icon-green-soft">
            @include($icon)
        </div>
        <div class="kpi-info">
            <div class="kpi-label">{{ $title }}</div>
            <div class="kpi-value text-dark">{{ $value }}</div>
        </div>
    </div>
</div>