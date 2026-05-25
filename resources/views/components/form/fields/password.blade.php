<div class="form-group">
    <label>
        {{ $field->label }}
        @if ($field->required)
            <span class="required">*</span>
        @endif
    </label>

    <input type="password" name="{{ $field->name }}" value="{{ old($field->name) }}" class="form-control">

    @error($field->name)
        <div class="error">{{ $message }}</div>
    @enderror
</div>
