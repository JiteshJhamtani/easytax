@extends('layouts.admin')

@section('title', 'Gift Eligibility')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    <style>
        /* ── PAGE HEADER ── */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: var(--slate-dark); margin: 0; letter-spacing: -0.02em; }

        /* ── FILTER BAR ── */
        .filter-bar {
            background: var(--surface);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
        .filter-group { display: flex; flex-direction: column; gap: 0.4rem; }
        .filter-label { font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: var(--text-muted); }
        .filter-control {
            font-size: 0.9rem; border: 1px solid var(--border); border-radius: 8px;
            padding: 0.45rem 0.8rem; color: var(--slate-dark); background: #fafbfc; transition: all 0.2s; height: 42px; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .filter-control:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(30,156,93,0.15); background: #fff; }
        .btn-apply {
            font-size: 0.85rem; font-weight: 700; padding: 0.45rem 1.5rem; border-radius: 8px;
            background: var(--slate-dark); color: #fff; border: none; cursor: pointer; height: 42px; transition: all 0.2s;
        }
        .btn-apply:hover { background: #000; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

        /* ── HERO DISPLAY ── */
        .hero-card {
            background: var(--surface); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid var(--border); border-left: 4px solid var(--green);
        }
        .hero-img {
            width: 100px; height: 100px; object-fit: cover; border-radius: 12px;
            border: 1px solid var(--ink-100); flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .hero-placeholder {
            width: 100px; height: 100px; background: var(--green-light); border-radius: 12px;
            display: flex; align-items: center; justify-content: center; color: var(--green); font-size: 2.5rem; flex-shrink: 0;
        }
        .hero-info { flex: 1; min-width: 0; }
        .hero-name { font-size: 1.3rem; font-weight: 800; color: var(--slate-dark); margin: 0 0 0.25rem; letter-spacing: -0.01em; }
        .hero-desc { font-size: 0.9rem; color: var(--text-muted); margin: 0 0 0.75rem; }
        
        .hero-badges { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .custom-badge {
            display: inline-flex; align-items: center; padding: 0.25rem 0.65rem;
            border-radius: 6px; font-size: 0.7rem; font-weight: 700;
            letter-spacing: 0.05em; text-transform: uppercase;
        }
        .badge-info-soft      { background: #DBEAFE; color: #1E40AF; }
        .badge-success-soft   { background: var(--green-light); color: var(--green-dark); }
        .badge-danger-soft    { background: #FEE2E2; color: #DC2626; }

        .hero-stats { display: flex; gap: 1rem; text-align: center; flex-shrink: 0; padding-left: 1.5rem; border-left: 1px solid var(--ink-100); }
        .stat-box { min-width: 80px; }
        .stat-num { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 0.25rem; }
        .stat-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }

        /* ── CONDITIONS SUMMARY ── */
        .cond-card {
            background: #fafbfc; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1.5rem;
        }
        .cond-header { padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--border); font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: var(--text-muted); }
        .cond-body { padding: 1rem 1.25rem; display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; }
        .cond-group-pill { display: flex; align-items: center; gap: 0.5rem; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; padding: 0.5rem 0.75rem; flex-wrap: wrap; }
        .cond-item { font-size: 0.8rem; font-weight: 600; color: var(--slate-dark); background: var(--ink-100); border-radius: 4px; padding: 0.2rem 0.5rem; white-space: nowrap; }
        .cond-and { font-size: 0.65rem; font-weight: 800; color: var(--text-muted); }
        .cond-or-sep { font-size: 0.7rem; font-weight: 800; color: var(--slate); padding: 0.2rem 0.6rem; border: 1px solid var(--border); border-radius: 20px; background: var(--surface); }

        /* ── DATA TABLE CARD ── */
        .table-card { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        .table-header { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #fafbfc; }
        .table-title { font-size: 1rem; font-weight: 800; color: var(--slate-dark); margin: 0; }
        
        .view-pills { display: flex; gap: 0.4rem; }
        .pill-btn {
            font-size: 0.8rem; font-weight: 700; padding: 0.35rem 1rem; border-radius: 50px;
            border: 1px solid var(--border); color: var(--slate); background: #fff; cursor: pointer; transition: all 0.2s;
        }
        .pill-btn:hover { border-color: var(--slate-dark); color: var(--slate-dark); }
        .pill-btn.active { background: var(--slate-dark); border-color: var(--slate-dark); color: #fff; }
        .pill-btn--eligible.active   { background: var(--green); border-color: var(--green); }
        .pill-btn--ineligible.active { background: #EF4444; border-color: #EF4444; }

        /* DataTable Overrides */
        .dataTables_wrapper { padding: 1rem 1.5rem; }
        table.dataTable { border-collapse: collapse !important; width: 100% !important; margin-bottom: 1rem !important; border-bottom: 1px solid var(--border); }
        table.dataTable thead th {
            background: #f8fafc; color: var(--text-muted); font-size: 0.75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--border) !important; border-top: none !important; padding: 1rem; white-space: nowrap;
        }
        table.dataTable tbody td { padding: 1rem; vertical-align: middle; color: var(--text); font-size: 0.9rem; border-bottom: 1px solid var(--ink-100); }
        table.dataTable tbody tr:hover { background: #f8fafc; }

        .agent-code { font-size: 0.8rem; background: var(--ink-100); color: var(--slate); padding: 0.2rem 0.5rem; border-radius: 4px; font-family: 'Courier New', Courier, monospace; font-weight: 700; border: 1px solid var(--border); }
        .count-met   { font-weight: 800; color: var(--green); }
        .count-unmet { font-weight: 600; color: var(--text-muted); }
        .count-min   { font-size: 0.75rem; color: var(--text-muted); }

        .dataTables_wrapper .dataTables_filter input, .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--border); border-radius: 6px; padding: 0.3rem 0.7rem; outline: none; font-size: 0.85rem;
        }
        .dataTables_info { color: var(--text-muted); font-size: 0.85rem; font-weight: 500; }
        .page-item.active .page-link { background: var(--slate-dark); border-color: var(--slate-dark); color: #fff; border-radius: 6px; }
        .page-link { color: var(--slate); border: 1px solid var(--border); border-radius: 6px; margin: 0 2px; font-size: 0.85rem; font-weight: 600; }
        
        .dt-button {
            font-size: 0.8rem !important; font-weight: 700 !important; border-radius: 6px !important;
            border: 1px solid var(--border) !important; color: var(--slate) !important; background: #fff !important;
            padding: 0.4rem 0.8rem !important; transition: all 0.2s !important;
        }
        .dt-button:hover { background: var(--ink-100) !important; border-color: var(--slate) !important; }

        /* ── EMPTY STATE ── */
        .empty-box {
            text-align: center; padding: 5rem 1rem; background: var(--surface);
            border-radius: 16px; border: 1px dashed var(--border);
        }
        .empty-box i { font-size: 3rem; color: var(--text-muted); opacity: 0.3; margin-bottom: 1rem; display: block; }
        .empty-box p { margin: 0; font-size: 1.05rem; color: var(--text-muted); font-weight: 600; }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Gift Eligibility</h1>
    </div>

    {{-- ── Filter bar ── --}}
    <div class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Select Gift</span>
            <select id="gift-select" class="filter-control" style="width:250px">
                <option value="">— Select a gift —</option>
                @foreach($gifts as $g)
                    <option value="{{ $g->id }}" data-period="{{ $g->period_type }}">{{ $g->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group" id="wrap-year" style="display:none">
            <span class="filter-label">Year</span>
            <input type="number" id="filter-year" class="filter-control" value="{{ now()->year }}" min="2020" max="2099" style="width:100px">
        </div>

        <div class="filter-group" id="wrap-quarter" style="display:none">
            <span class="filter-label">Quarter</span>
            <select id="filter-quarter" class="filter-control" style="width:160px">
                @foreach([1=>'Q1 (Jan–Mar)',2=>'Q2 (Apr–Jun)',3=>'Q3 (Jul–Sep)',4=>'Q4 (Oct–Dec)'] as $q => $lbl)
                    <option value="{{ $q }}" {{ now()->quarter == $q ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group" id="wrap-month" style="display:none">
            <span class="filter-label">Month</span>
            <select id="filter-month" class="filter-control" style="width:140px">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null,$m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn-apply" id="btn-apply" style="display:none">Calculate</button>
    </div>

    {{-- ── Dynamic Content Rendered Here ── --}}
    <div id="gift-hero" style="display:none"></div>
    <div id="conditions-summary" style="display:none"></div>

    {{-- ── Table Section ── --}}
    <div id="table-section" style="display:none">
        <div class="table-card">
            <div class="table-header">
                <p class="table-title">Agent Eligibility Roster</p>
                <div class="view-pills">
                    <button class="pill-btn active" data-filter="all">All Agents</button>
                    <button class="pill-btn pill-btn--eligible" data-filter="yes">Eligible</button>
                    <button class="pill-btn pill-btn--ineligible" data-filter="no">Not Eligible</button>
                </div>
            </div>
            <div>
                <table id="eligibility-table" class="table w-100">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Empty State ── --}}
    <div id="empty-state">
        <div class="empty-box">
            <i class="fas fa-gift"></i>
            <p>Select a gift from the dropdown above to calculate eligibility.</p>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        const giftsData = @json($giftsJson);
        let dt = null;

        function getParams() {
            return {
                gift_id: $('#gift-select').val(),
                year:    $('#filter-year').val(),
                quarter: $('#filter-quarter').val(),
                month:   $('#filter-month').val(),
            };
        }

        function uniqueConditions(gift) {
            const seen = {}, result = [];
            gift.conditionGroups.forEach(g =>
                g.conditions.forEach(c => {
                    if (!seen[c.service_id]) { seen[c.service_id] = true; result.push(c); }
                })
            );
            return result;
        }

        function renderHero(gift) {
            const img = gift.banner_url
                ? `<img src="${gift.banner_url}" class="hero-img" alt="${gift.name}">`
                : `<div class="hero-placeholder"><i class="fas fa-gift"></i></div>`;

            const statusBadge = gift.is_active
                ? `<span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i> Active</span>`
                : `<span class="custom-badge badge-danger-soft"><i class="fas fa-ban mr-1"></i> Inactive</span>`;

            $('#gift-hero').html(`
                <div class="hero-card">
                    ${img}
                    <div class="hero-info">
                        <p class="hero-name">${gift.name}</p>
                        ${gift.description ? `<p class="hero-desc">${gift.description}</p>` : ''}
                        <div class="hero-badges">
                            <span class="custom-badge badge-info-soft"><i class="fas fa-calendar-alt mr-1"></i> ${gift.period_type}</span>
                            ${statusBadge}
                        </div>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-box">
                            <div class="stat-num" style="color: var(--green);" id="stat-eligible">—</div>
                            <div class="stat-label">Eligible</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-num" style="color: #EF4444;" id="stat-not">—</div>
                            <div class="stat-label">Not Yet</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-num" style="color: var(--slate);" id="stat-total">—</div>
                            <div class="stat-label">Total Agents</div>
                        </div>
                    </div>
                </div>
            `).show();
        }

        function renderConditions(gift) {
            const groups = gift.conditionGroups.map((g, i) => {
                const conds = g.conditions.map((c, j) =>
                    `${j > 0 ? '<span class="cond-and">AND</span>' : ''}<span class="cond-item">${c.service_name} &ge; ${c.min_count}</span>`
                ).join('');
                return `${i > 0 ? '<span class="cond-or-sep">OR</span>' : ''}<div class="cond-group-pill">${conds}</div>`;
            }).join('');

            $('#conditions-summary').html(`
                <div class="cond-card">
                    <div class="cond-header">Eligibility Rules</div>
                    <div class="cond-body">${groups}</div>
                </div>
            `).show();
        }

        function loadTable(gift) {
            const cols = uniqueConditions(gift);
            if (dt) dt.destroy();
            $('#eligibility-table').empty();

            const serviceThs = cols.map(c => `<th class="text-center">${c.service_name}</th>`).join('');
            $('#eligibility-table').html(`
                <thead><tr><th>Agent</th><th>Code</th>${serviceThs}<th class="text-center">Status</th></tr></thead><tbody></tbody>
            `);

            dt = $('#eligibility-table').DataTable({
                processing: true, serverSide: true,
                ajax: { url: '{{ route("admin.gifts.eligibility.hub") }}', data: d => Object.assign(d, getParams()) },
                columns: [
                    { data: 'name', className: 'font-weight-bold text-dark' },
                    { data: 'agent_code', render: v => `<span class="agent-code">${v ?? '—'}</span>` },
                    ...cols.map(c => ({
                        data: `counts.${c.service_id}`, orderable: false, searchable: false, className: 'text-center',
                        render: val => {
                            const count = val?.count ?? 0; const minCount = val?.min_count ?? c.min_count;
                            const cls = count >= minCount ? 'count-met' : 'count-unmet';
                            return `<span class="${cls}">${count}</span><span class="count-min"> / ${minCount}</span>`;
                        }
                    })),
                    {
                        data: 'eligible', className: 'text-center', orderable: false, searchable: false,
                        render: v => v === 'yes'
                            ? `<span class="custom-badge badge-success-soft"><i class="fas fa-check"></i> Eligible</span>`
                            : `<span class="custom-badge badge-danger-soft"><i class="fas fa-times"></i> Unmet</span>`
                    }
                ],
                dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between mt-3"il>p',
                buttons: [
                    { extend: 'excelHtml5', className: 'dt-button', text: '<i class="fas fa-file-excel mr-1"></i> Export', title: `Eligibility - ${gift.name}` }
                ],
                pageLength: 25,
                language: { search: "_INPUT_", searchPlaceholder: "Search agents...", processing: '<i class="fas fa-circle-notch fa-spin mr-1"></i> Processing' }
            });

            $('#eligibility-table').on('xhr.dt', function(e, settings, json) {
                if (!json) return;
                const total = json.recordsFiltered ?? 0; const eligible = json.eligible_total ?? 0;
                $('#stat-total').text(total); $('#stat-eligible').text(eligible); $('#stat-not').text(total - eligible);
            });
        }

        function showPeriodFields(periodType) {
            $('#wrap-year, #btn-apply').show();
            $('#wrap-quarter').toggle(periodType === 'quarterly');
            $('#wrap-month').toggle(periodType === 'monthly');
        }

        $('#gift-select').on('change', function() {
            const giftId = $(this).val();
            if (!giftId) {
                $('#gift-hero, #conditions-summary, #table-section, #wrap-year, #wrap-quarter, #wrap-month, #btn-apply').hide();
                $('#empty-state').show(); return;
            }
            const gift = giftsData[giftId];
            showPeriodFields(gift.period_type); renderHero(gift); renderConditions(gift);
            $('#table-section').show(); $('#empty-state').hide(); loadTable(gift);
        });

        $('#btn-apply').on('click', function() {
            const giftId = $('#gift-select').val();
            if (giftId) loadTable(giftsData[giftId]);
        });

        $(document).on('click', '.pill-btn', function() {
            if (!dt) return;
            $('.pill-btn').removeClass('active'); $(this).addClass('active');
            const filterVal = $(this).data('filter');
            dt.column(dt.columns().count() - 1).search(filterVal === 'all' ? '' : filterVal).draw();
        });
    </script>
@endsection