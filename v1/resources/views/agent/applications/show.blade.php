@extends('adminlte::page')

@section('title', 'Application #' . $application->id . ' | EasyTax')

@section('content_header')
    <div class="workspace-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('agent.applications.index') }}" class="text-muted text-sm font-weight-bold mb-2 d-inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Back to Manager
            </a>
            <h1 class="workspace-title d-flex align-items-center gap-2">
                Application #{{ $application->id }}

                {{-- Dynamic Status Badge --}}
                @php
                    $statusClass = match (strtolower($application->status->value)) {
                        'completed' => 'badge-success-soft',
                        'pending' => 'badge-warning-soft',
                        'rejected' => 'badge-danger-soft',
                        default => 'badge-primary-soft',
                    };
                @endphp
                <span class="badge badge-modern {{ $statusClass }} ml-3" style="font-size: 0.8rem;">
                    {{ $application->status->value }}
                </span>
            </h1>
            <p class="workspace-subtitle">Submitted on {{ $application->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>

        <div class="header-actions">
            <button class="btn btn-outline-secondary bg-white shadow-sm mr-2">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <button class="btn btn-primary shadow-sm">
                <i class="fas fa-edit mr-1"></i> Update Status
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="row">

        {{-- LEFT COLUMN: APPLICATION DETAILS --}}
        <div class="col-lg-8">
            <div class="card data-card border-0 mb-4">
                <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-file-invoice text-primary mr-2"></i> Application Overview
                    </h3>
                </div>

                <div class="card-body p-0">
                    <div class="details-grid">

                        <div class="detail-row">
                            <div class="detail-label">Service Type</div>
                            <div class="detail-value font-weight-bold text-dark">
                                {{ $application->service->name }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Payment Status</div>
                            <div class="detail-value">
                                @if (strtolower($application->payment_status->value) == 'paid')
                                    <span class="badge badge-success-soft"><i class="fas fa-check-circle mr-1"></i>
                                        Paid</span>
                                @else
                                    <span class="badge badge-warning-soft"><i class="fas fa-clock mr-1"></i> Pending</span>
                                @endif
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Processing Fee (Amount)</div>
                            <div class="detail-value text-success font-weight-bold" style="font-size: 1.15rem;">
                                ₹{{ number_format($application->amount, 2) }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Application ID</div>
                            <div class="detail-value text-muted font-family-monospace">
                                {{ $application->id }}
                            </div>
                        </div>

                        {{-- If you have dynamic form data saved as JSON, you would loop it here --}}
                        {{-- <div class="detail-row">
                            <div class="detail-label">PAN Number</div>
                            <div class="detail-value">ABCDE1234F</div>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: DOCUMENTS & META --}}
        <div class="col-lg-4">
            <div class="card data-card border-0">
                <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-paperclip text-orange mr-2"></i> Attached Documents
                    </h3>
                </div>

                <div class="card-body p-4">

                    @if ($application->getMedia('documents')->count() > 0)
                        <div class="document-list">
                            @foreach ($application->getMedia('documents') as $doc)
                                <a href="{{ $doc->getUrl() }}" target="_blank" class="document-item">
                                    <div class="document-icon">
                                        {{-- Choose icon based on mime type if possible, defaulting to PDF --}}
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div class="document-info">
                                        <span class="document-name">{{ $doc->name }}</span>
                                        <span class="document-meta">{{ strtoupper($doc->extension) }} •
                                            {{ $doc->human_readable_size }}</span>
                                    </div>
                                    <div class="document-action">
                                        <i class="fas fa-download"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-folder-open text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted m-0">No documents attached to this application.</p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Optional: Agent Help/Notes Card --}}
            <div class="card data-card border-0 bg-light-blue mt-4">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold text-primary mb-2"><i class="fas fa-info-circle mr-1"></i> Agent Notice</h5>
                    <p class="text-muted text-sm m-0">
                        Please verify all attached documents against the provided application details before updating the
                        status to "Completed".
                    </p>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/applications.css') }}">
    <style>
        /* Details Grid */
        .details-grid {
            display: flex;
            flex-direction: column;
        }

        .detail-row {
            display: flex;
            flex-direction: column;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #F1F5F9;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        @media (min-width: 768px) {
            .detail-row {
                flex-direction: row;
                align-items: center;
            }

            .detail-label {
                width: 35%;
                flex-shrink: 0;
                padding-right: 1rem;
            }
        }

        .detail-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1rem;
            color: #334155;
        }

        /* Document Attachments UI */
        .document-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .document-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            text-decoration: none !important;
            transition: all 0.2s ease;
        }

        .document-item:hover {
            background: #ffffff;
            border-color: var(--color-primary);
            box-shadow: 0 4px 10px rgba(0, 68, 178, 0.08);
            transform: translateY(-2px);
        }

        .document-icon {
            width: 40px;
            height: 40px;
            background: rgba(0, 68, 178, 0.1);
            color: var(--color-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .document-info {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            overflow: hidden;
        }

        .document-name {
            font-weight: 600;
            color: #1E293B;
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .document-meta {
            font-size: 0.75rem;
            color: #64748B;
            margin-top: 2px;
        }

        .document-action {
            color: #94A3B8;
            padding-left: 1rem;
            transition: color 0.2s;
        }

        .document-item:hover .document-action {
            color: var(--color-secondary);
        }

        .empty-state-mini {
            text-align: center;
            padding: 2rem 1rem;
            background: #F8FAFC;
            border: 1px dashed #CBD5E1;
            border-radius: 8px;
        }

        .bg-light-blue {
            background-color: rgba(0, 68, 178, 0.04) !important;
        }

        .font-family-monospace {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
    </style>
@stop
