@extends('adminlte::page')

@section('title', 'Application #' . $application->id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Application #{{ $application->id }}</h1>
            <p class="text-muted mb-0 mt-1">Submitted on {{ $application->submitted_at?->format('d M Y, h:i A') ?? 'N/A' }}
            </p>
        </div>
        <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>     
@stop

@section('content')
    <div class="row">
        {{-- LEFT COLUMN --}}
        <div class="col-lg-8">

            {{-- APPLICATION INFO --}}
            <div class="card modern-card mb-4">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-info-circle text-primary mr-2"></i>
                        Overview</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Agent</span>
                            <span class="info-value">{{ $application->agent->name ?? 'Unassigned' }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Service</span>
                            <span class="info-value">{{ $application->service->name ?? 'N/A' }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                <span class="badge badge-status badge-{{ strtolower($application->status->value) }}">
                                    {{ $application->status->value }}
                                </span>
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Payment</span>
                            <span class="info-value">
                                <span
                                    class="badge badge-payment badge-{{ strtolower($application->payment_status->value) }}">
                                    {{ $application->payment_status->value }}
                                </span>
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Amount</span>
                            <span class="info-value font-weight-bold text-success">
                                ₹{{ number_format($application->amount, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM DATA --}}
            <div class="card modern-card mb-4">
                <div class="card-header bg-white pt-4 pb-3">
                    <h3 class="card-title font-weight-bold text-dark"><i
                            class="fas fa-clipboard-list text-primary mr-2"></i> Client Information</h3>
                </div>
                <div class="card-body p-0">
                    @php
                        $formData = $application->form_data ?? [];
                    @endphp

                    @if (count($formData))
                        <div class="table-responsive">
                            <table class="responsive-card-table table mb-0 form-data-table">
                                <tbody>
                                    @foreach ($formData as $field => $value)
                                        <tr>
                                            <td class="form-label">
                                                {{ Str::title(str_replace('_', ' ', $field)) }}
                                            </td>
                                            <td class="form-value">
                                                @if (is_array($value))
                                                    {{ implode(', ', $value) }}
                                                @elseif(is_bool($value))
                                                    <span
                                                        class="badge badge-{{ $value ? 'success' : 'secondary' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                                @elseif(empty($value))
                                                    <span class="text-muted fst-italic">Not provided</span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No client form data available for this application.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-4">

            {{-- DOCUMENTS --}}
            <div class="card modern-card mb-4">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-folder-open text-primary mr-2"></i>
                        Documents</h3>
                </div>
                <div class="card-body">
                    @if ($application->getMedia('documents')->count())
                        <div class="document-list">
                            @foreach ($application->getMedia('documents') as $doc)
                                <a href="{{ $doc->getUrl() }}" target="_blank" class="document-item">
                                    <div class="doc-icon">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    </div>
                                    <div class="doc-info">
                                        <span class="doc-name">{{ $doc->name }}</span>
                                        <span class="doc-size">{{ round($doc->size / 1024, 2) }} KB</span>
                                    </div>
                                    <div class="doc-action">
                                        <i class="fas fa-external-link-alt"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-file-excel fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0 small">No documents uploaded</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ADMIN ACTIONS --}}
            <div class="card modern-card mb-4">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-cogs text-primary mr-2"></i> Actions
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.applications.updateStatus', $application->id) }}">
                        @csrf
                        {{-- @method('PATCH') --}}

                        <button type="submit" name="status" value="IN_PROGRESS"
                            class="btn btn-warning btn-block mb-3 d-flex justify-content-center align-items-center font-weight-bold">
                            <i class="fas fa-spinner mr-2"></i> Mark In Progress
                        </button>

                        <button type="submit" name="status" value="COMPLETED"
                            class="btn btn-success btn-block d-flex justify-content-center align-items-center font-weight-bold">
                            <i class="fas fa-check-circle mr-2"></i> Mark Completed
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/application-show.css') }}">
@endsection
