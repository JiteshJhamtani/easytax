<div class="form-section">
    <h3>{{ $section->label }}</h3>

    <div class="form-grid">
        @foreach ($section->fields as $field)
            {!! $field->render() !!}
        @endforeach
    </div>
</div>
