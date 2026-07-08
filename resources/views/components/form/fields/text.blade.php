<div class="form-group">
    <label>
        {{ $field->label }}
        @if ($field->required)
            <span class="required">*</span>
        @endif
    </label>

    <input type="text" name="{{ $field->name }}" value="{{ old($field->name) }}" class="form-control" @if($field->placeholder) placeholder="{{ $field->placeholder }}" @endif>

    @error($field->name)
        <div class="error">{{ $message }}</div>
    @enderror
</div>
