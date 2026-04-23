@extends('layouts.admin')

@section('title', 'Create Page')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <h1 class="m-0 text-dark font-weight-bold">Create New Page</h1>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary font-weight-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Pages
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">
        <form method="POST" action="{{ route('admin.pages.store') }}">
            @csrf

            <div class="card modern-card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-file-contract text-primary mr-2"></i> Page Details
                    </h3>
                </div>

                <div class="card-body pt-0 px-4 pb-4">
                    {{-- Title & Slug --}}
                    <div class="row">
                        <div class="col-md-6 form-group mb-4">
                            <label class="form-label-custom">Page Title <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                        <i class="fas fa-heading text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" name="title" id="pageTitle"
                                    class="form-control custom-input border-left-0 @error('title') is-invalid @enderror"
                                    value="{{ old('title') }}" placeholder="e.g. Privacy Policy" required>
                            </div>
                            @error('title')
                                <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group mb-4">
                            <label class="form-label-custom">Slug</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0 custom-icon-box">
                                        <i class="fas fa-link text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" name="slug" id="pageSlug"
                                    class="form-control custom-input border-left-0 @error('slug') is-invalid @enderror"
                                    value="{{ old('slug') }}" placeholder="privacy-policy"
                                    style="font-family: monospace;">
                            </div>
                            <small class="text-muted mt-1 d-block">Leave empty to auto-generate from title.</small>
                            @error('slug')
                                <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="form-group mb-4">
                        <label class="form-label-custom">Content</label>
                        <textarea name="content" id="pageContent" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                        @error('content')
                            <span class="text-danger small font-weight-bold mt-1 d-block"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch custom-switch-lg">
                            <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold text-dark pt-1 pl-2" for="isActive">Publish Page (Active)</label>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Submit --}}
            <div class="d-flex justify-content-end mt-4 mb-4">
                <a href="{{ route('admin.pages.index') }}" class="btn btn-light font-weight-bold mr-2 text-muted">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4">
                    <i class="fas fa-save mr-1"></i> Save Page
                </button>
            </div>
        </form>
    </div>
@endsection

@section('css')
    <style>
        .modern-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }
        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }
        .custom-icon-box {
            border-color: #cbd5e1;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .custom-input {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
            height: 42px;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.2s ease;
        }
        .custom-input:focus {
            border-color: #0044b2;
            box-shadow: 0 0 0 3px rgba(0, 68, 178, 0.15);
            outline: none;
        }
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important;
        }
        .btn-primary {
            background-color: #0044b2;
            border-color: #0044b2;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #00368c;
            border-color: #00368c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 68, 178, 0.2) !important;
        }
        .btn-light {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            border-radius: 8px;
        }
        .btn-light:hover {
            background-color: #f1f5f9;
            color: #1e293b !important;
        }
        .custom-switch-lg .custom-control-label::before {
            height: 1.5rem;
            width: 2.5rem;
            border-radius: 2rem;
        }
        .custom-switch-lg .custom-control-label::after {
            height: calc(1.5rem - 4px);
            width: calc(1.5rem - 4px);
            border-radius: 2rem;
        }
        .custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after {
            transform: translateX(1rem);
        }
    </style>
    @stack('css')
@endsection

@section('js')
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#pageContent',
            height: 500,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
        });

        // Auto-generate slug from title
        document.getElementById('pageTitle').addEventListener('input', function() {
            let slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            document.getElementById('pageSlug').value = slug;
        });
    </script>
    @stack('js')
@endsection
