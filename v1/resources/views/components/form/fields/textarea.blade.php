<div class="form-group">
    <label>{{ $field->label }}</label>

    <textarea name="{{ $field->name }}" class="form-control">{{ old($field->name) }}</textarea>

    @error($field->name)
        <div class="error">{{ $message }}</div>
    @enderror
</div>
