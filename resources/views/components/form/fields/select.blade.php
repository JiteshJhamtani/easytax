<div class="form-group">
    <label>{{ $field->label }}</label>

    @if(count($field->options) <= 3)
        <div class="mt-2 d-flex flex-wrap" style="gap: 1.5rem;">
            @foreach ($field->options as $value => $label)
                <div class="custom-control custom-radio">
                    <input type="radio" id="{{ $field->name }}_{{ $value }}" name="{{ $field->name }}" value="{{ $value }}" class="custom-control-input" {{ old($field->name) == $value ? 'checked' : '' }}>
                    <label class="custom-control-label" for="{{ $field->name }}_{{ $value }}" style="cursor: pointer; font-weight: 500; font-size: 0.9rem; color: #374151; padding-top: 2px;">
                        {{ $label }}
                    </label>
                </div>
            @endforeach
        </div>
    @else
        <select name="{{ $field->name }}" class="form-control">
            <option value="">Select</option>

            @foreach ($field->options as $value => $label)
                <option value="{{ $value }}" {{ old($field->name) == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    @endif

    @error($field->name)
        <div class="error text-danger" style="font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
    @enderror
</div>
