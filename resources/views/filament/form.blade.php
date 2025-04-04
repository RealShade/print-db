<form id="filamentForm" method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">{{ __('filament.name') }}*</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $filament?->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="filament_vendor_id" class="form-label">{{ __('filament.vendor.field') }}*</label>
        <select class="form-select" id="filament_vendor_id" name="filament_vendor_id" required>
            <option value="">{{ __('common.select.placeholder') }}</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" {{ old('filament_vendor_id', $filament?->filament_vendor_id) == $vendor->id ? 'selected' : '' }}>
                    {{ $vendor->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="filament_type_id" class="form-label">{{ __('filament.type.field') }}*</label>
        <select class="form-select" id="filament_type_id" name="filament_type_id" required>
            <option value="">{{ __('common.select.placeholder') }}</option>
            @foreach($types as $type)
                <option value="{{ $type->id }}" {{ old('filament_type_id', $filament?->filament_type_id) == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('filament.colors') }}</label>
        <div id="color-blocks" class="d-flex flex-wrap gap-2">
            @if(old('colors', $filament?->colors))
                @foreach(old('colors', $filament->colors) as $index => $color)
                    <div class="color-block">
                        <div class="color-picker" data-default-color="{{ $color }}"></div>
                        <input type="hidden" name="colors[]" value="{{ $color }}" class="color-value">
                        <button type="button" class="btn btn-sm btn-danger remove-color-block">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" id="add-color-block" class="btn btn-primary btn-sm mt-2">
            <i class="bi bi-plus"></i>
        </button>
    </div>
    <div class="mb-3">
        <label for="density" class="form-label">{{ __('filament.density') }}</label>
        <div class="input-group">
            <input type="number" class="form-control" id="density" name="density"
                   step="0.0001" min="0"
                   value="{{ old('density', $filament?->density) }}">
            <span class="input-group-text">г/см³</span>
        </div>
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
