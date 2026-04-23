@extends('layouts.front.app')

@section('title', $page->title)

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="font-weight-bold mb-4 text-dark">{{ $page->title }}</h1>
                        <hr class="mb-5">
                        <div class="page-content text-secondary" style="line-height: 1.8; font-size: 1.05rem;">
                            {{-- We output unescaped content because it's managed via TinyMCE WYSIWYG by the Admin --}}
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .page-content h1, .page-content h2, .page-content h3, .page-content h4, .page-content h5, .page-content h6 {
        color: #1a202c;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .page-content a {
        color: #0044b2;
        text-decoration: underline;
    }
    .page-content a:hover {
        color: #00368c;
    }
    .page-content ul, .page-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    .page-content p {
        margin-bottom: 1.5rem;
    }
</style>
@endpush
