@extends('layouts.front.app')

@section('title', $service->name . ' Application | Agent Portal')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
@endpush

@section('content')
    <header class="page-header-minimal">
        <div class="container">
            <div class="header-breadcrumbs">
                <a href="{{ route('services.index') }}">Service Catalog</a>
                <span>/</span>
                <span class="current">{{ $service->name }}</span>
            </div>

            <div class="header-split">
                <div class="header-title-area">
                    <h1 class="page-title">{{ $service->name }}</h1>
                    <p class="page-subtitle">New Application Initialization</p>
                </div>

                @if ($service->price > 0)
                    <div class="header-meta-area">
                        <div class="meta-card">
                            <span class="meta-label">Standard Processing Fee</span>
                            <span class="meta-value">{{ money($service->price) }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </header>

    <section class="form-workspace section-padding">
        <div class="container container-sm">
            <div class="workspace-card">
                <div class="workspace-card-header">
                    <h2>Application Details</h2>
                    <p>Please ensure all client data is verified before submission.</p>
                </div>

                <div class="workspace-card-body">
                    {!! $form->render() !!}
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/form.js') }}"></script>
    <script>
        // Initialize Select2 with a custom theme wrapper
        $(document).ready(function() {
            $('.select2-init').select2({
                width: '100%',
                minimumResultsForSearch: 6 // Only show search box if there are more than 6 options
            });
        });
    </script>
@endpush
