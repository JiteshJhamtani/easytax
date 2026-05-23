@extends('layouts.admin')

@section('title', 'Gifts & Rewards')

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

        /* ── PREMIUM BUTTON ── */
        .btn-premium {
            background-color: var(--slate-dark);
            color: #ffffff;
            font-weight: 700;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .btn-premium:hover {
            background-color: #000000;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* ── ALERT ── */
        .custom-alert {
            background: var(--green-light);
            border: 1px solid rgba(30, 156, 93, 0.2);
            border-left: 4px solid var(--green);
            border-radius: 8px;
            color: var(--green-dark);
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* ── GIFT GRID ── */
        .gift-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        /* ── GIFT CARD ── */
        .gift-card {
            background: var(--surface);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: all 0.2s ease;
        }
        .gift-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.06);
            border-color: rgba(30,156,93,0.3);
        }

        /* Image Area */
        .gift-card__img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
            border-bottom: 1px solid var(--ink-100);
        }
        .gift-card__img-placeholder {
            width: 100%;
            height: 180px;
            background: var(--green-light); /* Changed to brand mint */
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--ink-100);
        }
        .gift-card__img-placeholder i {
            font-size: 3.5rem;
            color: var(--green);
            opacity: 0.5;
        }

        /* Card Body */
        .gift-card__body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .gift-card__name {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--slate-dark);
            line-height: 1.3;
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.01em;
        }
        .gift-card__desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 0 0 1rem 0;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Badges */
        .gift-card__meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: auto;
        }
        .custom-badge {
            display: inline-flex; align-items: center; padding: 0.25rem 0.65rem;
            border-radius: 6px; font-size: 0.7rem; font-weight: 700;
            letter-spacing: 0.05em; text-transform: uppercase;
        }
        .badge-success-soft   { background: var(--green-light); color: var(--green-dark); }
        .badge-danger-soft    { background: #FEE2E2; color: #DC2626; }
        .badge-info-soft      { background: #DBEAFE; color: #1E40AF; }
        .badge-secondary-soft { background: var(--ink-100); color: var(--slate); border: 1px solid var(--border); }

        /* Card Footer Actions */
        .gift-card__footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--ink-100);
            display: flex;
            gap: 0.5rem;
            align-items: center;
            background: #fafbfc;
        }
        .btn-gaction {
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border: 1px solid transparent;
        }
        .btn-gaction:hover { text-decoration: none; }
        
        .btn-gaction--eligibility { background: var(--surface); border-color: var(--border); color: var(--slate); }
        .btn-gaction--eligibility:hover { background: var(--slate-dark); color: #fff; border-color: var(--slate-dark); }
        
        .btn-gaction--edit { background: #DBEAFE; color: #1E40AF; }
        .btn-gaction--edit:hover { background: #1E40AF; color: #fff; }
        
        .btn-gaction--delete { background: #FEE2E2; color: #DC2626; margin-left: auto; }
        .btn-gaction--delete:hover { background: #DC2626; color: #fff; }

        /* ── EMPTY STATE ── */
        .gift-empty {
            grid-column: 1/-1;
            text-align: center;
            padding: 5rem 1rem;
            background: var(--surface);
            border-radius: 16px;
            border: 1px dashed var(--border);
        }
        .gift-empty i { font-size: 3.5rem; color: var(--text-muted); opacity: 0.3; margin-bottom: 1rem; display: block; }
        .gift-empty p { margin: 0; font-size: 1.05rem; color: var(--text-muted); font-weight: 600; }

        /* ── PAGINATION ── */
        .gift-pagination { margin-top: 2rem; display: flex; justify-content: center; }
        .pagination { gap: 0.25rem; }
        .page-link {
            border-radius: 8px !important; border: 1px solid var(--border);
            color: var(--slate); padding: 0.4rem 0.85rem; font-weight: 600; font-size: 0.85rem;
        }
        .page-item.active .page-link { background: var(--slate-dark); border-color: var(--slate-dark); color: #fff; }
    </style>
@endsection

@section('content')

    <div class="page-header">
        <h1 class="page-title">Gifts & Rewards</h1>
        <a href="{{ route('admin.gifts.create') }}" class="btn-premium">
            <i class="fas fa-plus"></i> Add Gift
        </a>
    </div>

    @if (session('success'))
        <div class="custom-alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="gift-grid">
        @forelse($gifts as $gift)
            <div class="gift-card">

                {{-- Gift image --}}
                @if ($gift->hasMedia('gift_banner'))
                    <img src="{{ $gift->getFirstMediaUrl('gift_banner') }}" alt="{{ $gift->name }}" class="gift-card__img">
                @else
                    <div class="gift-card__img-placeholder">
                        <i class="fas fa-gift"></i>
                    </div>
                @endif

                <div class="gift-card__body">
                    <h3 class="gift-card__name">{{ $gift->name }}</h3>
                    
                    @if ($gift->description)
                        <p class="gift-card__desc">{{ $gift->description }}</p>
                    @endif
                    
                    <div class="gift-card__meta">
                        <span class="custom-badge badge-info-soft">
                            <i class="fas fa-calendar-alt mr-1"></i> {{ $gift->period_type }}
                        </span>
                        
                        <span class="custom-badge badge-secondary-soft">
                            {{ $gift->condition_groups_count }} {{ Str::plural('Group', $gift->condition_groups_count) }}
                        </span>
                        
                        @if ($gift->is_active)
                            <span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i> Active</span>
                        @else
                            <span class="custom-badge badge-danger-soft"><i class="fas fa-ban mr-1"></i> Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="gift-card__footer">
                    <a href="{{ route('admin.gifts.eligibility', $gift) }}" class="btn-gaction btn-gaction--eligibility">
                        <i class="fas fa-users"></i> Eligibility
                    </a>
                    
                    <a href="{{ route('admin.gifts.edit', $gift) }}" class="btn-gaction btn-gaction--edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    
                    <form action="{{ route('admin.gifts.destroy', $gift) }}" method="POST" class="d-inline ml-auto"
                        onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('confirm-action', { detail: { form: this, title: 'Delete Gift?', message: 'Are you sure you want to delete {{ $gift->name }}?' } }));">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-gaction btn-gaction--delete" title="Delete Gift">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="gift-empty">
                <i class="fas fa-box-open"></i>
                <p>No gifts or rewards have been created yet.</p>
            </div>
        @endforelse
    </div>

    @if ($gifts->hasPages())
        <div class="gift-pagination">
            {{ $gifts->links() }}
        </div>
    @endif

@endsection