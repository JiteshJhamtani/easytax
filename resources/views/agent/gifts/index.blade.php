@extends('layouts.agent')

@section('title', 'Gift Rewards | EasyTax')

@section('content')
    <div class="container-fluid px-0">
        
        {{-- PAGE HEADER & FILTERS --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pt-2">
            <div>
                <h1 class="h3 font-weight-bold mb-1 text-dark">Gift Rewards & Milestones</h1>
                <p class="text-muted mb-0 text-sm">Track your progress and unlock premium rewards.</p>
            </div>
            
            {{-- Filter Tabs --}}
            <div class="mt-3 mt-md-0">
                <ul class="nav nav-pills premium-tabs" id="gift-filters" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-filter="all" href="#">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-filter="monthly" href="#">Monthly</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-filter="quarterly" href="#">Quarterly</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-filter="yearly" href="#">Yearly</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- GIFTS GRID --}}
        <div class="row" id="gifts-grid">
            @forelse ($giftGroups as $group)
                @php
                    $isSingle = $group['type'] === 'single';
                    $milestone = $group['milestones'][0];
                    
                    $giftName = $milestone['name'];
                    $imageUrl = $milestone['image_url'] ?? null;
                    
                    $targetCount = $isSingle && $group['next_milestone'] ? $group['next_milestone']['min_count'] : ($isSingle ? 'Done' : 'Multi');
                    $currentCount = $isSingle ? $group['agent_count'] : '—';
                    $remaining = ($isSingle && $group['next_milestone']) ? $group['next_milestone']['needed'] : '—';
                    $isUnlocked = $isSingle ? ($group['unlocked_count'] > 0) : $milestone['unlocked'];
                    $periodClass = strtolower($group['period_type']);
                @endphp

                <div class="col-xl-6 col-lg-12 mb-4 gift-card-wrapper" data-period="{{ $periodClass }}">
                    <div class="luxury-gift-card h-100">
                        
                        {{-- Image --}}
                        <div class="luxury-gift-image">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $giftName }}">
                            @else
                                <div class="placeholder-image"><i class="fas fa-gift text-muted"></i></div>
                            @endif
                        </div>

                        {{-- Title & Period --}}
                        <div class="luxury-gift-main">
                            <h4 class="luxury-gift-title" title="{{ $giftName }}">{{ Str::limit($giftName, 40) }}</h4>
                            <div class="luxury-gift-period mt-2">
                                <span class="period-badge"><i class="fas fa-calendar-alt mr-1"></i> {{ ucfirst($group['period_type']) }}</span>
                                @if(!$isSingle)
                                    <span class="period-badge bg-light text-secondary border ml-1"><i class="fas fa-layer-group mr-1"></i> Multi-Service</span>
                                @endif
                            </div>
                        </div>

                        {{-- Columns --}}
                        <div class="luxury-gift-stats">
                            <div class="stat-col">
                                <span class="stat-label">Target</span>
                                <span class="stat-value text-dark">{{ $targetCount }}</span>
                            </div>
                            <div class="stat-col">
                                <span class="stat-label">Submitted</span>
                                <span class="stat-value text-dark">{{ $currentCount }}</span>
                            </div>
                            <div class="stat-col">
                                <span class="stat-label">Remaining</span>
                                <span class="stat-value text-dark">{{ $remaining }}</span>
                            </div>
                            <div class="stat-col" style="min-width: 90px;">
                                <span class="stat-label">Status</span>
                                @if($isUnlocked)
                                    <span class="stat-value text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Eligible</span>
                                @else
                                    <span class="stat-value text-warning font-weight-bold"><i class="fas fa-clock mr-1"></i> Pending</span>
                                @endif
                            </div>
                        </div>

                        {{-- Action --}}
                        <div class="luxury-gift-action">
                            <button class="btn-luxury-action btn-view-gift" 
                                    title="View Requirements"
                                    data-milestone="{{ json_encode($milestone) }}"
                                    data-period="{{ ucfirst($group['period_type']) }}">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded-lg border shadow-sm">
                        <i class="fas fa-box-open text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h4 class="font-weight-bold text-dark">No Gifts Available</h4>
                        <p class="text-muted">There are currently no active gift milestones to track.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>


    {{-- ── LUXURY GIFT MODAL ── --}}
        <div class="luxury-modal-overlay" id="giftModal" style="display: none;">
            <div class="luxury-modal-container">
                <div class="luxury-modal-header">
                    <h5 class="luxury-modal-title" id="modalGiftTitle">Gift Name</h5>
                    <button type="button" class="luxury-modal-close" id="closeGiftModal"><i class="fas fa-times"></i></button>
                </div>
                
                <div class="luxury-modal-body">
                    <div class="modal-gift-hero">
                        <div class="modal-image-wrapper">
                            <img src="" id="modalGiftImage" alt="Gift" style="display:none;">
                            <div class="placeholder-image" id="modalGiftPlaceholder"><i class="fas fa-gift text-muted"></i></div>
                        </div>
                        <div class="modal-hero-text">
                            <span class="period-badge mb-2" id="modalGiftPeriod">Quarterly</span>
                            <div id="modalGiftStatus" class="mt-1"></div>
                        </div>
                    </div>
                    
                    <h6 class="font-weight-bold text-dark mt-4 mb-3" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Requirements to Unlock:</h6>
                    
                    <div class="modal-conditions-list" id="modalConditionsList">
                        </div>
                </div>
                
                <div class="luxury-modal-footer">
                    <button type="button" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 8px;" id="closeGiftModalBtn">Close</button>
                </div>
            </div>
        </div>
    @endsection

@section('css')
    <style>
        /* ── PREMIUM FILTER TABS ── */
        .premium-tabs {
            background: #ffffff;
            padding: 0.3rem;
            border-radius: 10px;
            border: 1px solid #e8ecf0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            display: inline-flex;
        }
        .premium-tabs .nav-link {
            color: #7a8799;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .premium-tabs .nav-link:hover {
            color: #2E3D4E;
        }
        .premium-tabs .nav-link.active {
            background-color: #1E9C5D;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(30, 156, 93, 0.2);
        }

  /* ── LUXURY GIFT CARD (Highly Responsive) ── */
        .luxury-gift-card {
            background: #ffffff;
            border: 1px solid #e8ecf0;
            border-radius: 16px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem; /* Tightened gap */
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            position: relative;
        }
        .luxury-gift-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            border-color: #d1d5db;
        }

        .luxury-gift-image {
            width: 70px;
            height: 70px;
            flex-shrink: 0;
            border-radius: 10px;
            background: #f8f9fa;
            border: 1px solid #e8ecf0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .luxury-gift-image img { width: 100%; height: 100%; object-fit: cover; }
        .placeholder-image { font-size: 1.5rem; opacity: 0.4; }

        .luxury-gift-main { 
            flex: 1 1 0%; /* 👈 THE MAGIC FIX: Forces container to shrink properly */
            min-width: 0; /* Required for flex shrinking */
            padding-right: 0.5rem;
        }
        .luxury-gift-title {
            font-size: 1rem;
            font-weight: 700;
            color: #2E3D4E;
            margin: 0;
            line-height: 1.3;
            /* Force title to max 2 lines and add ... if it's too long */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .period-badge {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #1E9C5D;
            background: #EDF7F4;
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
        }

        .luxury-gift-stats {
            display: flex;
            gap: 1.25rem;
            flex-shrink: 0;
        }
        .stat-col {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            min-width: 50px;
        }
        .stat-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #7a8799;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-value {
            font-size: 0.95rem;
            font-weight: 800;
             /* Prevents values from breaking into two lines */
        }

        .luxury-gift-action { flex-shrink: 0; }
        .btn-luxury-action {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #1E9C5D;
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 1rem;
        }
        .btn-luxury-action:hover { background: #157a48; }

        /* ── RESPONSIVE BREAKPOINTS ── */
        @media (max-width: 1450px) {
            /* On slightly smaller laptops, wrap the stats below the title cleanly */
            .luxury-gift-card { flex-wrap: wrap; }
            .luxury-gift-main { min-width: 200px; } /* Ensure title has some space before wrapping */
            .luxury-gift-stats { 
                width: 100%; 
                justify-content: flex-start; 
                order: 3; 
                margin-top: 0.5rem; 
                padding-top: 0.75rem;
                border-top: 1px dashed #e8ecf0;
            }
            .luxury-gift-action { order: 2; margin-left: auto; }
        }

        @media (max-width: 575px) {
            /* Mobile View */
            .premium-tabs { width: 100%; justify-content: space-between; overflow-x: auto; flex-wrap: nowrap; }
            .premium-tabs .nav-link { padding: 0.4rem 0.8rem;  }
            
            .luxury-gift-card { flex-direction: column; align-items: flex-start; gap: 1rem; padding-top: 3rem;}
            
            /* Pin the action button to the top right on mobile */
            .luxury-gift-action { position: absolute; right: 1.25rem; top: 1.25rem; }
            
            /* Make stats a 2x2 grid on mobile so it doesn't squish horizontally */
            .luxury-gift-stats { width: 100%; flex-wrap: wrap; gap: 1rem; justify-content: space-between; }
            .stat-col { width: 45%; } 
        
        }

        /* ── LUXURY MODAL CSS ── */
        .luxury-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);
            z-index: 1050; display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .luxury-modal-overlay.show { opacity: 1; }
        
        .luxury-modal-container {
            background: #fff; width: 100%; max-width: 450px;
            border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transform: translateY(20px) scale(0.95); transition: all 0.3s ease;
            overflow: hidden; margin: 1rem; border: 1px solid #e8ecf0;
        }
        .luxury-modal-overlay.show .luxury-modal-container { transform: translateY(0) scale(1); }
        
        .luxury-modal-header {
            padding: 1.25rem 1.5rem; border-bottom: 1px solid #e8ecf0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .luxury-modal-title { margin: 0; font-size: 1.1rem; font-weight: 800; color: #2E3D4E; }
        .luxury-modal-close {
            background: #f8f9fa; border: none; width: 32px; height: 32px;
            border-radius: 50%; color: #7a8799; cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center;
        }
        .luxury-modal-close:hover { background: #fee2e2; color: #DC2626; }
        
        .luxury-modal-body { padding: 1.5rem; }
        .modal-gift-hero { display: flex; gap: 1.25rem; align-items: center; }
        .modal-image-wrapper {
            width: 70px; height: 70px; border-radius: 12px; background: #f8f9fa;
            border: 1px solid #e8ecf0; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;
        }
        .modal-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        
        .modal-condition-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.85rem 1.25rem; background: #f8f9fa; border-radius: 12px; 
            margin-bottom: 0.5rem; border: 1px solid #e8ecf0;
        }
        .modal-condition-name { font-weight: 700; color: #4a5568; font-size: 0.9rem; }
        .modal-condition-progress { font-weight: 800; font-size: 1rem; }
        .progress-success { color: #1E9C5D; }
        .progress-pending { color: #D97706; }
        
        .luxury-modal-footer {
            padding: 1rem 1.5rem; border-top: 1px solid #e8ecf0; background: #fafbfc; text-align: right;
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterLinks = document.querySelectorAll('.premium-tabs .nav-link');
            const giftCards = document.querySelectorAll('.gift-card-wrapper');

            filterLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all, add to clicked
                    filterLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    const filterValue = this.getAttribute('data-filter');

                    // Filter logic
                    giftCards.forEach(card => {
                        if (filterValue === 'all' || card.getAttribute('data-period') === filterValue) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });

        // ── LUXURY MODAL LOGIC ──
        const modalOverlay = document.getElementById('giftModal');
        const closeBtns = document.querySelectorAll('#closeGiftModal, #closeGiftModalBtn');
        
        document.querySelectorAll('.btn-view-gift').forEach(btn => {
            btn.addEventListener('click', function() {
                // Parse the data we attached to the button
                const milestone = JSON.parse(this.getAttribute('data-milestone'));
                const period = this.getAttribute('data-period');
                
                // 1. Populate Header & Image
                document.getElementById('modalGiftTitle').textContent = milestone.name;
                document.getElementById('modalGiftPeriod').innerHTML = `<i class="fas fa-calendar-alt mr-1"></i> ${period}`;
                
                const imgEl = document.getElementById('modalGiftImage');
                const placeholder = document.getElementById('modalGiftPlaceholder');
                
                if (milestone.image_url) {
                    imgEl.src = milestone.image_url;
                    imgEl.style.display = 'block';
                    placeholder.style.display = 'none';
                } else {
                    imgEl.style.display = 'none';
                    placeholder.style.display = 'flex';
                }

                // 2. Populate Status Badge
                const statusEl = document.getElementById('modalGiftStatus');
                if (milestone.unlocked) {
                    statusEl.innerHTML = '<span class="text-success" style="font-weight: 800; font-size: 0.9rem;"><i class="fas fa-check-circle mr-1"></i> Eligible!</span>';
                } else {
                    statusEl.innerHTML = '<span class="text-warning" style="font-weight: 800; font-size: 0.9rem;"><i class="fas fa-clock mr-1"></i> In Progress</span>';
                }

                // 3. Populate Requirements List
                const conditionsList = document.getElementById('modalConditionsList');
                conditionsList.innerHTML = ''; // Clear old data
                
                // Check if it's a multi-service gift (it will have a 'conditions' array from your Controller)
                if (milestone.conditions && milestone.conditions.length > 0) {
                    milestone.conditions.forEach(cond => {
                        const isDone = cond.agent_count >= cond.min_count;
                        const progressClass = isDone ? 'progress-success' : 'progress-pending';
                        const checkIcon = isDone ? '<i class="fas fa-check-circle text-success ml-2"></i>' : '';
                        
                        conditionsList.innerHTML += `
                            <div class="modal-condition-item">
                                <span class="modal-condition-name">${cond.service_name}</span>
                                <span class="modal-condition-progress ${progressClass}">
                                    ${cond.agent_count} / ${cond.min_count} ${checkIcon}
                                </span>
                            </div>
                        `;
                    });
                } else {
                    // Fallback for Single-Service Gifts
                    const needed = milestone.min_count;
                    const have = milestone.unlocked ? needed : (needed - (milestone.needed || 0));
                    const isDone = have >= needed;
                    const progressClass = isDone ? 'progress-success' : 'progress-pending';
                    const checkIcon = isDone ? '<i class="fas fa-check-circle text-success ml-2"></i>' : '';

                    conditionsList.innerHTML = `
                        <div class="modal-condition-item">
                            <span class="modal-condition-name">Total Applications Required</span>
                            <span class="modal-condition-progress ${progressClass}">
                                ${have} / ${needed} ${checkIcon}
                            </span>
                        </div>
                    `;
                }

                // 4. Show Modal with animation
                modalOverlay.style.display = 'flex';
                
                // Small delay to allow CSS transitions to trigger
                setTimeout(() => { modalOverlay.classList.add('show'); }, 10);
            });
        });

         //
        // Close Modal Logic (Clicking X, Close Button, or outside the box)
        const closeModal = () => {
            modalOverlay.classList.remove('show');
            setTimeout(() => { modalOverlay.style.display = 'none'; }, 300); // Wait for fade out
        };

        closeBtns.forEach(btn => btn.addEventListener('click', closeModal));
        modalOverlay.addEventListener('click', e => {
            if (e.target === modalOverlay) closeModal();
        });
    </script>
@endsection