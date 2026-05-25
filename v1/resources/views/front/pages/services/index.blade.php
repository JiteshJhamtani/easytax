@extends('layouts.front.app')

@section('title', 'Our Services | EasyTax Agent Portal')

@section('content')
    <section class="page-header">
        <div class="header-pattern"></div>
        <div class="container relative z-10">
            <div class="header-breadcrumbs">
                <a href="{{ url('/') }}">Home</a> <span>/</span> Services
            </div>
            <h1 class="page-header__title">Service Catalog</h1>
            <p class="page-header__subtitle">
                Explore our comprehensive suite of financial, compliance, and tax solutions designed for rapid processing through our agent network.
            </p>
        </div>
    </section>

    <section class="services-page section-padding">
        <div class="container">
            @if ($services->count())
                <div class="services-grid">
                    @foreach ($services as $service)
                        <article class="service-card group">
                            <div class="service-card__body">
                                <div class="service-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                </div>
                                <h2 class="service-card__title">{{ $service->name }}</h2>
                                <p class="service-card__desc">
                                    {{ \Illuminate\Support\Str::limit($service->description, 140) }}
                                </p>
                            </div>

                            <div class="service-card__footer">
                                <div class="price-block">
                                    @if ($service->price > 0)
                                        <span class="price-label">Processing Fee</span>
                                        <div class="service-price">{{ money($service->price) }}</div>
                                    @else
                                        <span class="price-label">Fee Structure</span>
                                        <div class="service-price text-success">Custom Quote</div>
                                    @endif
                                </div>

                                <a href="{{ route('services.show', $service->slug) }}" class="btn btn-outline-primary btn-sm btn-apply">
                                    View Details
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrapper">
                    {{ $services->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    </div>
                    <h3>Catalog Updating</h3>
                    <p>We are currently updating our service offerings for the 2024-2025 financial year. Please check back shortly or contact agent support.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
