<form method="POST" action="{{ route('applications.store', $form->service()->slug) }}" enctype="multipart/form-data"
    class="agent-form">
    @csrf

    <div class="form-sections-wrapper">
        @foreach ($form->sections() as $section)
            {!! $section->render() !!}
        @endforeach
    </div>

    <div class="form-actions-sticky">
        <div class="actions-inner">
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary btn-submit">
                Initialize Application
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>
</form>
