@extends('layouts.agent') 

@section('title', 'Service Catalog | EasyTax Agent Portal')

@section('css')
    <style>
        /* ── THEME VARIABLES & RESET ── */
        .content-body { 
            padding: 0 !important; 
            background-color: #F8F9FA !important; 
        }
        
        .catalog-wrapper {
            --brand-green: #1E9C5D;
            --brand-green-hover: #157a48;
            --brand-mint: #EDF7F4;
            --brand-slate: #2E3D4E;
            --text-dark: #333333;
            --text-muted: #7a8799;
            --border-color: #e8ecf0;
            --card-shadow: 0 4px 20px rgba(0,0,0,0.03);
            --card-shadow-hover: 0 12px 30px rgba(0,0,0,0.06);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .catalog-wrapper * { box-sizing: border-box; }

        /* ── HERO SECTION (Mint Area) ── */
        .cat-hero {
            background-color: var(--brand-mint);
            padding: 3rem 3rem 7rem; /* Deep padding for the overlap effect */
            border-bottom: 1px solid #e2efe9;
            text-align: center;
        }

        .cat-breadcrumbs {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }
        .cat-breadcrumbs a { color: var(--brand-green); text-decoration: none; }
        .cat-breadcrumbs span.sep { color: var(--text-muted); }
        .cat-breadcrumbs span.current { color: var(--text-muted); }

        .cat-hero h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0 0 0.8rem;
            letter-spacing: -0.02em;
        }

        .cat-hero p {
            font-size: 1rem;
            color: var(--text-muted);
            margin: 0 auto;
            max-width: 600px;
            line-height: 1.6;
        }

        /* ── MAIN LAYOUT (Overlapping Grid) ── */
        .cat-main {
            max-width: 1200px;
            margin: -4rem auto 3rem; /* Pulls content up over the mint background */
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }

        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        /* ── SERVICE CARDS ── */
        .cat-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .cat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .cat-card-body {
            padding: 2rem 2rem 1.5rem;
            flex-grow: 1;
        }

        .cat-icon {
            width: 48px;
            height: 48px;
            background-color: var(--brand-mint);
            color: var(--brand-green);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
        }

        .cat-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0 0 0.6rem;
            line-height: 1.3;
        }

        .cat-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin: 0;
        }

        /* ── CARD FOOTER (Price & Button) ── */
        .cat-card-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fcfdfd; /* Very subtle off-white for the footer */
            border-radius: 0 0 16px 16px;
        }

        .cat-price-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
            margin-bottom: 0.2rem;
        }

        .cat-price-value {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1;
        }
        
        .cat-price-value.custom {
            color: var(--brand-green);
            font-size: 1.1rem;
        }

        .btn-buy {
            background-color: var(--brand-green);
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-buy:hover {
            background-color: var(--brand-green-hover);
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30,156,93,0.2);
        }

        /* ── EMPTY STATE ── */
        .cat-empty {
            background: #fff;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            padding: 4rem 2rem;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .cat-empty-icon {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }

        .cat-empty h3 {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0 0 0.5rem;
        }

        .cat-empty p {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin: 0;
        }

        /* ── PAGINATION ── */
        .cat-pagination {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
        }

        /* Responsive Tweaks */
        @media (max-width: 768px) {
            .cat-hero { padding: 2rem 1.5rem 5rem; }
            .cat-main { margin-top: -3rem; padding: 0 1rem; }
            .cat-hero h1 { font-size: 1.8rem; }
            .cat-card-body { padding: 1.5rem 1.5rem 1rem; }
            .cat-card-footer { padding: 1.2rem 1.5rem; }
            .cat-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
<div class="catalog-wrapper">
   
    {{-- ── HERO SECTION ── --}}
    <header class="cat-hero">
        <div class="cat-breadcrumbs">
            <a href="{{ url('/') }}">Home</a>
            <span class="sep">/</span>
            <span class="current">Services</span>
        </div>
        <h1>Service Catalog</h1>
        <p>Explore our comprehensive suite of financial, compliance, and tax solutions designed for rapid processing through our agent network.</p>
    </header>


    {{-- ── MAIN CONTENT ── --}}
    <div class="cat-main">
        @if ($services->count())
            <div class="cat-grid">
                @foreach ($services as $service)
                    <article class="cat-card">
                        <div class="cat-card-body">
                            <div class="cat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            </div>
                            <h2 class="cat-title">{{ $service->name }}</h2>
                            <p class="cat-desc">
                                {{ \Illuminate\Support\Str::limit($service->description, 140) }}
                            </p>
                        </div>

                        <div class="cat-card-footer">
                            <div class="cat-price-block">
                                @if ($service->price > 0)
                                    <span class="cat-price-label">Processing Fee</span>
                                    <div class="cat-price-value">{{ money($service->price) }}</div>
                                @else
                                    <span class="cat-price-label">Fee Structure</span>
                                    <div class="cat-price-value custom">Custom Quote</div>
                                @endif
                            </div>

                            <a href="{{ route('services.show', $service->slug) }}" class="btn-buy">
                                Buy Now
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="cat-pagination">
                {{ $services->links() }}
            </div>
        @else
            <div class="cat-empty">
                <div class="cat-empty-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                </div>
                <h3>Catalog Updating</h3>
                <p>We are currently updating our service offerings for the 2024-2025 financial year. Please check back shortly or contact agent support.</p>
            </div>
        @endif
    </div>
    
</div>
@endsection