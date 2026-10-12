<form method="POST" action="{{ route('applications.store', $form->service()->slug) }}" enctype="multipart/form-data"
    class="agent-form">
    @csrf

    <div class="form-sections-wrapper">
        @foreach ($form->sections() as $section)
            {!! $section->render() !!}
        @endforeach

        <div class="form-section mt-4 mb-4">
            <h3 class="font-weight-bold" style="font-size: 1.15rem; color: #111827; margin-bottom: 0.75rem;">
                <i class="fas fa-comment-dots text-primary mr-2"></i> Special Instructions / Message for Operator <span class="badge badge-light text-muted border ml-2" style="font-size: 0.75rem;">Optional</span>
            </h3>
            <p class="text-muted small mb-3">Attach any special remarks, client requests, or instructions for the processing team.</p>
            <div class="form-group" style="grid-column: span 6;">
                <textarea name="agent_notes" class="form-control" rows="3" placeholder="e.g. Please process on priority, client requested old tax regime, alternate contact number is 9876543210..." style="min-height: 80px; resize: vertical; border-radius: 8px; font-size: 0.9rem;">{{ old('agent_notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-actions-sticky">
        <div class="actions-inner">
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary btn-submit">
                Proceed
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>
</form>
