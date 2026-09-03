@extends('layouts.admin')

@section('title', 'Sidebar Tab Badges | EasyTax Admin')

@section('css')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .page-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--slate-dark);
        margin: 0;
    }
    .page-subtitle {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }
    .badge-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .badge-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .preview-sidebar {
        background: var(--sb-bg, #313d4f);
        border-radius: 14px;
        padding: 1rem 0.75rem;
        color: #fff;
        max-width: 250px;
    }
    .preview-section-title {
        color: #8d9bb0;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 0.5rem 0.75rem 0.4rem;
    }
    .preview-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.65rem 0.85rem;
        margin-bottom: 0.25rem;
        border-radius: 10px;
        color: #8d9bb0;
        font-size: 0.83rem;
        font-weight: 600;
        text-decoration: none;
    }
    .preview-item.active {
        background: #283343;
        color: #1E9C5D;
    }
    .preview-item-label {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .preview-badge-cluster {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-left: auto;
    }
    .color-swatch-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid transparent;
        cursor: pointer;
        transition: transform 0.15s, border-color 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .color-swatch-btn:hover {
        transform: scale(1.15);
    }
    .color-swatch-btn.selected {
        border-color: #0f172a;
        box-shadow: 0 0 0 2px #fff inset;
    }
</style>
@stop

@section('content')
<div class="container-fluid px-4 py-3" x-data="tabBadgeManager()">
    <div class="page-header">
        <div>
            <h1 class="page-title">Notification Badges Configurator</h1>
            <p class="page-subtitle">Configure the circular notification badges with dynamic background colors for each Application Type tab.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('admin.tab-badges.reset') }}" method="POST" onsubmit="return confirm('Reset all sidebar badges back to default settings?');">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm font-weight-bold" style="border-radius: 8px; height: 38px;">
                    <i class="fas fa-undo mr-1"></i> Reset to Defaults
                </button>
            </form>
            <button type="button" @click="submitForm()" class="btn btn-success btn-sm font-weight-bold px-3" style="border-radius: 8px; height: 38px; background: var(--green); border-color: var(--green);">
                <i class="fas fa-save mr-1"></i> Save Changes
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show font-weight-bold" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- LEFT COLUMN: BADGE BUILDER --}}
        <div class="col-lg-7 mb-4">
            <form id="badgeConfigForm" action="{{ route('admin.tab-badges.update') }}" method="POST">
                @csrf
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0">Configured Notification Badges (<span x-text="badges.length"></span>/4)</h5>
                        <p class="small text-muted mb-0">Clean circular count badges (classic notification pill design)</p>
                    </div>
                    <button type="button" @click="addBadge()" x-show="badges.length < 4" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-plus mr-1"></i> Add Another Badge
                    </button>
                </div>

                <template x-for="(badge, index) in badges" :key="index">
                    <div class="badge-card">
                        <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-secondary font-weight-bold px-2 py-1" style="border-radius: 6px;" x-text="'Badge #' + (index + 1)"></span>
                                <input type="text" :name="'badges[' + index + '][label]'" x-model="badge.label" class="form-control form-control-sm font-weight-bold text-dark" style="max-width: 180px; border-radius: 6px;" placeholder="Badge Name">
                                {{-- Live Badge Pill Sample --}}
                                <span :class="'sb-badge sb-badge--' + badge.color" style="margin-left: 0.5rem;" title="Live badge preview">
                                    <span x-text="sampleCount(index)"></span>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" :name="'badges[' + index + '][is_active]'" :id="'switch_' + index" x-model="badge.is_active" class="custom-control-input" value="1">
                                    <label class="custom-control-label small font-weight-bold text-muted cursor-pointer" :for="'switch_' + index" x-text="badge.is_active ? 'Active' : 'Disabled'"></label>
                                </div>
                                <button type="button" @click="removeBadge(index)" x-show="badges.length > 1" class="btn btn-link text-danger p-0 text-decoration-none" title="Delete slot">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Metric Selector --}}
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-muted mb-1">COUNT SOURCE (METRIC)</label>
                                <select :name="'badges[' + index + '][metric]'" x-model="badge.metric" class="form-control form-control-sm font-weight-bold" style="border-radius: 6px;">
                                    @foreach($metrics as $mKey => $mLabel)
                                        <option value="{{ $mKey }}">{{ $mLabel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tooltip Format --}}
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-muted mb-1">HOVER TOOLTIP ({count})</label>
                                <input type="text" :name="'badges[' + index + '][tooltip]'" x-model="badge.tooltip" class="form-control form-control-sm" style="border-radius: 6px;" placeholder="e.g. Today: {count}">
                            </div>

                            {{-- Color Swatches / Theme Selector --}}
                            <div class="col-12">
                                <label class="small font-weight-bold text-muted mb-2 d-block">SELECT CIRCLE BACKGROUND COLOR</label>
                                <input type="hidden" :name="'badges[' + index + '][color]'" x-model="badge.color">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    @foreach($colors as $cKey => $cData)
                                        <button type="button" 
                                                class="color-swatch-btn" 
                                                :class="badge.color === '{{ $cKey }}' ? 'selected' : ''"
                                                style="background-color: {{ $cData['bg'] }};"
                                                @click="badge.color = '{{ $cKey }}'"
                                                title="{{ $cData['name'] }}">
                                            <i x-show="badge.color === '{{ $cKey }}'" class="fas fa-check text-white" style="font-size: 0.65rem;"></i>
                                        </button>
                                    @endforeach
                                    <span class="small font-weight-bold text-dark ml-2" x-text="getColorName(badge.color)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </form>
        </div>

        {{-- RIGHT COLUMN: LIVE SIDEBAR PREVIEW & DATA MATRIX --}}
        <div class="col-lg-5">
            {{-- LIVE PREVIEW CARD --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center" style="border-radius: 14px 14px 0 0;">
                    <div class="font-weight-bold text-dark"><i class="fas fa-eye text-primary mr-1"></i> Live Sidebar Preview</div>
                    <span class="badge badge-success-soft text-xs font-weight-bold px-2 py-1">Typical Notification Style</span>
                </div>
                <div class="card-body p-3 d-flex justify-content-center bg-light">
                    <div class="preview-sidebar w-100 shadow">
                        <div class="preview-section-title">Application Types</div>

                        {{-- Item 1: ITR Filing --}}
                        <div class="preview-item active">
                            <span class="sb-item__icon"><i class="fas fa-file-invoice-dollar"></i></span>
                            <span class="preview-item-label">ITR Filing</span>
                            <div class="preview-badge-cluster">
                                <template x-for="(badge, bIdx) in activeBadges()" :key="bIdx">
                                    <span :class="'sb-badge sb-badge--' + badge.color" :title="formatTooltip(badge, 'itr-filing')">
                                        <span x-text="getTabMetricCount('itr-filing', badge.metric)"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Item 2: GST Registration --}}
                        <div class="preview-item">
                            <span class="sb-item__icon"><i class="fas fa-id-card"></i></span>
                            <span class="preview-item-label">GST Registration</span>
                            <div class="preview-badge-cluster">
                                <template x-for="(badge, bIdx) in activeBadges()" :key="bIdx">
                                    <span :class="'sb-badge sb-badge--' + badge.color" :title="formatTooltip(badge, 'gst-registration')">
                                        <span x-text="getTabMetricCount('gst-registration', badge.metric)"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Item 3: GST Return --}}
                        <div class="preview-item">
                            <span class="sb-item__icon"><i class="fas fa-file-invoice"></i></span>
                            <span class="preview-item-label">GST Return</span>
                            <div class="preview-badge-cluster">
                                <template x-for="(badge, bIdx) in activeBadges()" :key="bIdx">
                                    <span :class="'sb-badge sb-badge--' + badge.color" :title="formatTooltip(badge, 'gst-return-filing')">
                                        <span x-text="getTabMetricCount('gst-return-filing', badge.metric)"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Item 4: Other Apps --}}
                        <div class="preview-item">
                            <span class="sb-item__icon"><i class="fas fa-folder"></i></span>
                            <span class="preview-item-label">Other Apps</span>
                            <div class="preview-badge-cluster">
                                <template x-for="(badge, bIdx) in activeBadges()" :key="bIdx">
                                    <span :class="'sb-badge sb-badge--' + badge.color" :title="formatTooltip(badge, 'other')">
                                        <span x-text="getTabMetricCount('other', badge.metric)"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Item 5: Incomplete Apps --}}
                        <div class="preview-item">
                            <span class="sb-item__icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <span class="preview-item-label">Incomplete Apps</span>
                            <div class="preview-badge-cluster">
                                <template x-for="(badge, bIdx) in activeBadges()" :key="bIdx">
                                    <span :class="'sb-badge sb-badge--' + badge.color" :title="formatTooltip(badge, 'incomplete')">
                                        <span x-text="getTabMetricCount('incomplete', badge.metric)"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LIVE COUNTS REFERENCE MATRIX --}}
            <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom py-3" style="border-radius: 14px 14px 0 0;">
                    <div class="font-weight-bold text-dark"><i class="fas fa-chart-pie text-success mr-1"></i> Current Database Counts</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0 small">
                            <thead>
                                <tr class="text-muted">
                                    <th>Category Tab</th>
                                    <th>Today</th>
                                    <th>Pending</th>
                                    <th>Submitted</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tabCounts as $tabSlug => $counts)
                                    <tr>
                                        <td class="font-weight-bold text-dark">{{ ucfirst(str_replace('-', ' ', $tabSlug)) }}</td>
                                        <td><span class="badge badge-primary px-2">{{ $counts['today'] ?? 0 }}</span></td>
                                        <td><span class="badge badge-warning px-2">{{ $counts['pending'] ?? 0 }}</span></td>
                                        <td><span class="badge badge-info px-2">{{ $counts['submitted'] ?? 0 }}</span></td>
                                        <td><span class="text-muted font-weight-bold">{{ $counts['total_volume'] ?? 0 }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function tabBadgeManager() {
        return {
            badges: @json($configs),
            tabCounts: @json($tabCounts),
            colorMap: @json($colors),
            
            sampleCount(idx) {
                let samples = [5, 12, 3, 1];
                return samples[idx] || 7;
            },
            
            getColorName(colorKey) {
                return this.colorMap[colorKey] ? this.colorMap[colorKey].name : colorKey;
            },
            
            addBadge() {
                if (this.badges.length >= 4) return;
                this.badges.push({
                    id: 'badge_' + (this.badges.length + 1),
                    label: 'Badge Slot',
                    metric: 'submitted',
                    color: 'purple',
                    tooltip: 'Submitted: {count}',
                    is_active: true
                });
            },
            
            removeBadge(index) {
                if (this.badges.length <= 1) return;
                this.badges.splice(index, 1);
            },
            
            activeBadges() {
                return this.badges.filter(b => b.is_active);
            },
            
            getTabMetricCount(tabKey, metric) {
                if (!this.tabCounts[tabKey]) return 0;
                return this.tabCounts[tabKey][metric] || 0;
            },
            
            formatTooltip(badge, tabKey) {
                let cnt = this.getTabMetricCount(tabKey, badge.metric);
                let t = badge.tooltip || '{count}';
                return t.replace('{count}', cnt);
            },
            
            submitForm() {
                document.getElementById('badgeConfigForm').submit();
            }
        };
    }
</script>
@stop
