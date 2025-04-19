<select name="{{ $name }}" id="{{ $id }}" class="form-control" {{ $required ? 'required' : '' }}>
    <option value="">{{ __('filament.spool.select_prompt') }}</option>
    @foreach ($spools as $vendorName => $vendorSpools)
        <optgroup label="{{ $vendorName }}">
            @foreach ($vendorSpools as $spoolId => $spoolLabel)
                <option value="{{ $spoolId }}" {{ $value == $spoolId ? 'selected' : '' }}>
                    {{ $spoolLabel }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>
