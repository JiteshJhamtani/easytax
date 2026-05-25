<div class="form-group">
    <label>{{ $field->label }}</label>

    <select name="{{ $field->name }}" class="form-control">
        <option value="">Select</option>

        @foreach ($field->options as $value => $label)
            <option value="{{ $value }}" {{ old($field->name) == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

    @error($field->name)
        <div class="error">{{ $message }}</div>
    @enderror
</div>
