@extends('adminlte::page')

@section('title', 'Service: ' . $service->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">{{ $service->name }}</h1>
            <p class="text-muted mb-0 mt-1"><code>{{ $service->slug }}</code></p>
        </div>
        <div>
            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning font-weight-bold shadow-sm mr-2">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary font-weight-bold shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">

        {{-- Stats Row --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-info shadow-sm" style="border-radius: 12px;">
                    <div class="inner">
                        <h3>₹{{ number_format($service->price, 2) }}</h3>
                        <p>Service Price</p>
                    </div>
                    <div class="icon"><i class="fas fa-tag"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success shadow-sm" style="border-radius: 12px;">
                    <div class="inner">
                        <h3>{{ $service->commission_type === 'percentage' ? $service->commission_value . '%' : '₹' . number_format($service->commission_value, 2) }}</h3>
                        <p>Commission ({{ ucfirst($service->commission_type) }})</p>
                    </div>
                    <div class="icon"><i class="fas fa-coins"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-primary shadow-sm" style="border-radius: 12px;">
                    <div class="inner">
                        <h3>{{ $stats['total_applications'] }}</h3>
                        <p>Total Applications</p>
                    </div>
                    <div class="icon"><i class="fas fa-file-alt"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning shadow-sm" style="border-radius: 12px;">
                    <div class="inner">
                        <h3>₹{{ number_format($stats['total_revenue'], 2) }}</h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon"><i class="fas fa-rupee-sign"></i></div>
                </div>
            </div>
        </div>

        {{-- Service Details --}}
        <div class="row">
            <div class="col-md-5">
                <div class="card modern-card border-0 shadow-sm">
                    <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-info-circle text-primary mr-2"></i> Service Details
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <table class="responsive-card-table table table-borderless mb-0">
                            <tr>
                                <td class="text-muted font-weight-bold" style="width: 40%;">Status</td>
                                <td>
                                    @if($service->active)
                                        <span class="custom-badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i> Active</span>
                                    @else
                                        <span class="custom-badge badge-danger-soft"><i class="fas fa-ban mr-1"></i> Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold">Description</td>
                                <td>{{ $service->description ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold">Created</td>
                                <td>{{ $service->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold">Updated</td>
                                <td>{{ $service->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Form Schema Preview --}}
            <div class="col-md-7">
                <div class="card modern-card border-0 shadow-sm">
                    <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-wpforms text-primary mr-2"></i> Application Form Fields
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        @if($formConfig && isset($formConfig['sections']))
                            @foreach($formConfig['sections'] as $sectionKey => $section)
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                        <i class="fas fa-layer-group text-muted mr-1"></i> {{ $section['label'] }}
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="responsive-card-table table table-sm table-borderless mb-0">
                                            <thead>
                                                <tr class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">
                                                    <th>Field</th>
                                                    <th>Type</th>
                                                    <th class="text-center">Required</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($section['fields'] as $field)
                                                    <tr>
                                                        <td>{{ $field['label'] }}</td>
                                                        <td><code>{{ $field['type'] }}</code></td>
                                                        <td class="text-center">
                                                            @if($field['required'] ?? false)
                                                                <i class="fas fa-check text-success"></i>
                                                            @else
                                                                <i class="fas fa-minus text-muted"></i>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            @if(isset($formConfig['documents']) && count($formConfig['documents']))
                                <div class="mb-3">
                                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                        <i class="fas fa-paperclip text-muted mr-1"></i> Required Documents
                                    </h6>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($formConfig['documents'] as $doc)
                                            <li class="mb-2">
                                                @if($doc['required'] ?? false)
                                                    <i class="fas fa-check-circle text-success mr-1"></i>
                                                @else
                                                    <i class="far fa-circle text-muted mr-1"></i>
                                                @endif
                                                {{ $doc['label'] }}
                                                <small class="text-muted ml-1">({{ implode(', ', $doc['mimes'] ?? []) }})</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @else
                            <p class="text-muted mb-0"><i class="fas fa-info-circle mr-1"></i> No form fields configured for this service.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('css')
    <style>
        .modern-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }

        .custom-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .badge-success-soft {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-danger-soft {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .small-box {
            border-radius: 12px !important;
            overflow: hidden;
        }
    </style>
@endsection
