<div class="form-group">
    <label>
        {{ $field->label }}
        @if ($field->required)
            <span class="required">*</span>
        @endif
    </label>

    <div class="file-input-wrapper">
        <input type="file" name="{{ $field->name }}" class="form-control">
        <div class="file-help">
            Supported formats: PDF, JPG, PNG
        </div>
    </div>

    @error($field->name)
        <div class="error">{{ $message }}</div>
    @enderror
</div>
