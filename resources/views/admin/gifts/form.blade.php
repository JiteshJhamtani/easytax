@extends('layouts.admin')

@section('title', isset($gift) ? 'Edit Gift' : 'Create Gift')

@section('css')
<style>
    /* ── PAGE HEADER ── */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .page-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--slate-dark);
        margin: 0;
        letter-spacing: -0.02em;
    }

    /* ── FORM CARD ── */
    .form-wrapper {
        max-width: 950px;
        margin: 0 auto;
    }
    .premium-card {
        background: var(--surface);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .premium-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--ink-100);
        background: #fafbfc;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .header-icon {
        width: 44px; height: 44px;
        background: var(--green-light);
        color: var(--green);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
    }
    .header-title { font-size: 1.15rem; font-weight: 800; color: var(--slate-dark); margin: 0; }
    .header-sub { font-size: 0.85rem; color: var(--text-muted); margin: 0; }
    .premium-card-body { padding: 2rem; }

    /* ── FORM CONTROLS ── */
    .section-label {
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--ink-100);
    }
    .form-group { margin-bottom: 1.5rem; }
    .custom-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--slate-dark);
        margin-bottom: 0.5rem;
        display: block;
    }
    .custom-label .req { color: #EF4444; }
    
    .custom-input {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 0.65rem 1rem;
        font-size: 0.95rem;
        color: var(--text);
        background: var(--surface);
        transition: all 0.2s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .custom-input:focus {
        outline: none;
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(30,156,93,0.15);
    }
    .custom-input.is-invalid { border-color: #EF4444; }
    textarea.custom-input { resize: vertical; min-height: 80px; }

    .invalid-feedback { font-size: 0.8rem; color: #EF4444; margin-top: 0.35rem; display: block; font-weight: 600; }
    
    /* ── TOGGLE SWITCH ── */
    .toggle-wrap { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.35rem; }
    .toggle-switch { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; inset: 0; background: #d1d5db; border-radius: 24px;
        cursor: pointer; transition: background 0.2s;
    }
    .toggle-slider:before {
        content: ''; position: absolute; width: 18px; height: 18px; left: 3px; top: 3px;
        background: #fff; border-radius: 50%; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .toggle-switch input:checked + .toggle-slider { background: var(--green); }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }
    .toggle-label { font-size: 0.9rem; font-weight: 600; color: var(--slate); }

    /* ── IMAGE UPLOAD ── */
    .image-upload-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        background: #fafbfc;
    }
    .image-upload-zone:hover { border-color: var(--green); background: var(--green-light); }
    .image-upload-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
    .upload-icon { font-size: 2rem; color: #9ca3af; margin-bottom: 0.75rem; }
    .image-upload-zone:hover .upload-icon { color: var(--green); }
    .upload-text { font-size: 0.9rem; color: var(--text-muted); }
    .upload-text strong { color: var(--slate-dark); }
    
    .image-preview { margin-bottom: 1rem; display: inline-block; position: relative; }
    .image-preview img { max-height: 160px; border-radius: 8px; border: 1px solid var(--border); display: block; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

    /* ── DYNAMIC CONDITIONS UI ── */
    .cond-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
    .cond-title { font-size: 0.95rem; font-weight: 800; color: var(--slate-dark); margin: 0; }
    .cond-hint { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; }
    
    .btn-action-outline {
        font-size: 0.8rem; font-weight: 700; padding: 0.4rem 1rem; border-radius: 8px;
        border: 1px solid var(--border); color: var(--slate-dark); background: #fff;
        cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.4rem;
    }
    .btn-action-outline:hover { background: var(--ink-100); border-color: var(--slate); }

    /* Group Card */
    .group-card {
        background: #fafbfc; border: 1px solid var(--border);
        border-radius: 12px; margin-bottom: 1rem; overflow: hidden;
    }
    .group-card.has-error { border-color: #EF4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
    .group-header {
        background: var(--ink-100); padding: 0.75rem 1.25rem;
        display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);
    }
    .group-title { font-size: 0.85rem; font-weight: 800; color: var(--slate-dark); margin: 0; }
    
    .btn-remove-sm {
        font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 6px;
        border: 1px solid #fca5a5; color: #DC2626; background: #fff; cursor: pointer; transition: all 0.2s;
    }
    .btn-remove-sm:hover { background: #FEE2E2; }
    
    .group-body { padding: 1.25rem; }
    
    /* Condition Row */
    .cond-row {
        display: grid; grid-template-columns: 1fr 160px 40px; gap: 0.75rem;
        align-items: center; margin-bottom: 0.75rem;
    }
    .btn-add-cond {
        font-size: 0.8rem; font-weight: 700; color: var(--green); background: none; border: none;
        cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 0 0 0;
    }
    .btn-add-cond:hover { color: var(--green-dark); text-decoration: underline; }

    /* OR Divider */
    .or-divider { display: flex; align-items: center; gap: 1rem; margin: 0.5rem 0 1.25rem; }
    .or-line { flex: 1; height: 1px; background: var(--border); }
    .or-label { font-size: 0.7rem; font-weight: 800; color: var(--text-muted); background: var(--ink-100); padding: 0.2rem 0.75rem; border-radius: 50px; letter-spacing: 0.05em; }

    /* ── FOOTER ── */
    .card-footer {
        padding: 1.5rem 2rem; background: #fafbfc; border-top: 1px solid var(--ink-100);
        display: flex; align-items: center; gap: 1rem;
    }
    .btn-submit {
        background: var(--slate-dark); color: #fff; font-weight: 700; padding: 0.75rem 2rem;
        border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .btn-submit:hover { background: #000; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .btn-cancel { color: var(--text-muted); font-weight: 700; text-decoration: none; padding: 0.75rem 1rem; transition: color 0.2s; }
    .btn-cancel:hover { color: var(--slate-dark); }
</style>
@endsection

@section('content')
<div class="form-wrapper">
    <div class="page-header">
        <h1 class="page-title">{{ isset($gift) ? 'Edit Gift' : 'Create New Gift' }}</h1>
    </div>

    <div class="premium-card">
        <div class="premium-card-header">
            <div class="header-icon"><i class="fas fa-gift"></i></div>
            <div>
                <p class="header-title">{{ isset($gift) ? $gift->name : 'Gift Details' }}</p>
                <p class="header-sub">Configure the gift properties and auto-award logic below.</p>
            </div>
        </div>

        <div class="premium-card-body">
            @if ($errors->any())
                <div class="alert alert-danger" style="border-radius: 8px; font-size: 0.9rem;">
                    <strong><i class="fas fa-exclamation-circle"></i> Please correct the following errors:</strong>
                    <ul class="mb-0 mt-2 pl-3">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" id="gift-form" enctype="multipart/form-data" action="{{ isset($gift) ? route('admin.gifts.update', $gift) : route('admin.gifts.store') }}">
                @csrf
                @isset($gift) @method('PUT') @endisset

                <div class="section-label">Basic Information</div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="custom-label" for="name">Gift Name <span class="req">*</span></label>
                            <input type="text" id="name" name="name" class="custom-input @error('name') is-invalid @enderror" value="{{ old('name', $gift->name ?? '') }}" placeholder="e.g. iPad Pro 256GB" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="custom-label" for="period_type">Period Type <span class="req">*</span></label>
                            <select id="period_type" name="period_type" class="custom-input @error('period_type') is-invalid @enderror" required>
                                <option value="">— Select —</option>
                                @foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly', 'session' => 'Per Session (S1/S2)'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('period_type', $gift->period_type ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('period_type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="custom-label">Status</label>
                            <div class="toggle-wrap">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $gift->is_active ?? true))>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label">Active</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="custom-label" for="description">Description (Optional)</label>
                    <textarea id="description" name="description" class="custom-input" placeholder="Add any specific details or terms for this gift...">{{ old('description', $gift->description ?? '') }}</textarea>
                </div>

                <div class="section-label mt-5">Visual Assets</div>

                <div class="form-group">
                    @isset($gift)
                        @if($gift->hasMedia('gift_banner'))
                            <div class="image-preview">
                                <img src="{{ $gift->getFirstMediaUrl('gift_banner') }}" alt="Current image">
                            </div>
                        @endif
                    @endisset

                    <div class="image-upload-zone" id="upload-zone">
                        <input type="file" name="banner" id="banner" accept="image/jpeg,image/png,image/webp">
                        <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="upload-text" id="upload-label">
                            <strong>Click to upload</strong> or drag & drop<br>
                            <span style="font-size:0.8rem">JPG, PNG, WEBP (Max 2MB)</span>
                        </div>
                    </div>
                </div>

                <div class="section-label mt-5">Award Logic & Eligibility</div>

                <div class="cond-header">
                    <div>
                        <p class="cond-title">Condition Groups</p>
                        <p class="cond-hint">Groups are <strong>OR</strong>-ed &bull; Rules inside a group are <strong>AND</strong>-ed</p>
                    </div>
                    <button type="button" class="btn-action-outline" id="add-group">
                        <i class="fas fa-plus"></i> Add Group
                    </button>
                </div>

                <div id="groups-wrapper"></div>
                
                <div id="empty-state" class="text-center p-5" style="display: none; border: 1px dashed #d1d5db; border-radius: 12px;">
                    <i class="fas fa-project-diagram" style="font-size: 2rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                    <p style="color: var(--text-muted); font-weight: 600;">No eligibility rules set. Click "Add Group" above.</p>
                </div>
            </form>
        </div>

        <div class="card-footer">
            <button type="submit" form="gift-form" class="btn-submit" id="submit-btn">
                <i class="fas fa-save"></i> {{ isset($gift) ? 'Save Changes' : 'Create Gift' }}
            </button>
            <a href="{{ route('admin.gifts.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
(function() {
    'use strict';
    
    // ── CONFIGURATION ──
    const services = @json($services->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));
    const existingGroups = @json(
        isset($gift) ? $gift->conditionGroups->map(fn($g) => [
            'conditions' => $g->conditions->map(fn($c) => ['service_id' => $c->service_id, 'min_count' => $c->min_count])
        ]) : []
    );

    let groupIndex = 0;

    const wrapper = document.getElementById('groups-wrapper');
    const addGroupBtn = document.getElementById('add-group');
    const emptyState = document.getElementById('empty-state');
    const bannerInput = document.getElementById('banner');
    const uploadLabel = document.getElementById('upload-label');

    function serviceOptions(selectedId = null) {
        let html = '<option value="">— Select Service —</option>';
        services.forEach(s => {
            const sel = s.id == selectedId ? 'selected' : '';
            html += `<option value="${s.id}" ${sel}>${s.name}</option>`;
        });
        return html;
    }

    function addConditionRow(groupIdx, conditionIdx, serviceId = null, minCount = 1) {
        const row = document.createElement('div');
        row.className = 'cond-row condition-row';
        row.dataset.conditionIndex = conditionIdx;
        row.innerHTML = `
            <select name="groups[${groupIdx}][conditions][${conditionIdx}][service_id]" class="custom-input condition-service" required>
                ${serviceOptions(serviceId)}
            </select>
            <input type="number" name="groups[${groupIdx}][conditions][${conditionIdx}][min_count]" class="custom-input condition-count" placeholder="Target count" value="${minCount}" min="1" required>
            <button type="button" class="btn-remove-sm remove-condition" style="height: 42px; width: 42px; padding: 0;"><i class="fas fa-times"></i></button>`;
        return row;
    }

    function addGroup(conditions = []) {
        const idx = groupIndex++;
        const groupNum = wrapper.querySelectorAll('.group-card').length + 1;

        if (groupNum > 1) {
            const divider = document.createElement('div');
            divider.className = 'or-divider';
            divider.innerHTML = `<div class="or-line"></div><span class="or-label">OR</span><div class="or-line"></div>`;
            wrapper.appendChild(divider);
        }

        const card = document.createElement('div');
        card.className = 'group-card';
        card.dataset.groupIndex = idx;
        card.innerHTML = `
            <div class="group-header">
                <p class="group-title"><span class="group-number">Group ${groupNum}</span></p>
                <button type="button" class="btn-remove-sm btn-remove-group">Remove</button>
            </div>
            <div class="group-body">
                <div class="conditions-wrapper"></div>
                <button type="button" class="btn-add-cond"><i class="fas fa-plus-circle"></i> Add rule</button>
            </div>`;

        const condWrapper = card.querySelector('.conditions-wrapper');
        const conditionsToAdd = conditions.length > 0 ? conditions : [{}];
        conditionsToAdd.forEach((c, cIdx) => condWrapper.appendChild(addConditionRow(idx, cIdx, c.service_id, c.min_count ?? 1)));

        card.querySelector('.btn-add-cond').addEventListener('click', () => {
            condWrapper.appendChild(addConditionRow(idx, condWrapper.querySelectorAll('.condition-row').length));
        });

        card.querySelector('.btn-remove-group').addEventListener('click', () => removeGroup(card));
        
        card.addEventListener('click', e => {
            const btn = e.target.closest('.remove-condition');
            if (btn) removeCondition(btn, card);
        });

        wrapper.appendChild(card);
        updateEmptyState();
        renumberGroups();
    }

    function removeGroup(card) {
        const prev = card.previousElementSibling;
        if (prev && prev.classList.contains('or-divider')) prev.remove();
        const next = card.nextElementSibling;
        if (next && next.classList.contains('or-divider') && !card.previousElementSibling) next.remove();
        card.remove();
        renumberGroups();
        updateEmptyState();
    }

    function removeCondition(btn, groupCard) {
        const row = btn.closest('.condition-row');
        const condWrapper = groupCard.querySelector('.conditions-wrapper');
        if (condWrapper.querySelectorAll('.condition-row').length <= 1) {
            alert('Each group must have at least one rule.'); return;
        }
        row.remove();
        
        // Reindex
        const groupIdx = groupCard.dataset.groupIndex;
        groupCard.querySelectorAll('.condition-row').forEach((r, i) => {
            r.dataset.conditionIndex = i;
            r.querySelector('.condition-service').name = `groups[${groupIdx}][conditions][${i}][service_id]`;
            r.querySelector('.condition-count').name = `groups[${groupIdx}][conditions][${i}][min_count]`;
        });
    }

    function renumberGroups() {
        wrapper.querySelectorAll('.group-card').forEach((card, i) => {
            const span = card.querySelector('.group-number');
            if (span) span.textContent = `Group ${i + 1}`;
        });
    }

    function updateEmptyState() {
        emptyState.style.display = wrapper.querySelectorAll('.group-card').length > 0 ? 'none' : 'block';
    }

    addGroupBtn.addEventListener('click', () => addGroup());

    // Image Upload UX
    if (bannerInput && uploadLabel) {
        bannerInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const size = (file.size / 1024 / 1024).toFixed(2);
                if (file.size > 2 * 1024 * 1024) {
                    uploadLabel.innerHTML = `<strong style="color:#EF4444">${file.name}</strong><br><span style="font-size:.8rem;color:#EF4444">Too large (${size} MB). Max 2MB.</span>`;
                    this.value = ''; return;
                }
                uploadLabel.innerHTML = `<strong>${file.name}</strong><br><span style="font-size:.8rem;color:var(--green)">Ready (${size} MB)</span>`;
            }
        });
    }

    // Init
    if (existingGroups.length > 0) existingGroups.forEach(g => addGroup(g.conditions));
    else addGroup();

})();
</script>
@endsection