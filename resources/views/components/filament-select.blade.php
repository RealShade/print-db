<select name="{{ $name }}" id="{{ $id }}" class="form-control" {{ $required ? 'required' : '' }}>
    <option value="">{{ __('filament.select_prompt') }}</option>
    @foreach ($filaments as $vendorName => $vendorFilaments)
        <optgroup label="{{ $vendorName }}">
            @foreach ($vendorFilaments as $filamentId => $filamentLabel)
                <option value="{{ $filamentId }}" {{ $value == $filamentId ? 'selected' : '' }}>
                    {{ $filamentLabel }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>
