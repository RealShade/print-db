<form id="filamentForm" method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">{{ __('filament.name') }}*</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $filament?->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="filament_vendor_id" class="form-label">{{ __('filament.vendor') }}*</label>
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
        <label for="filament_type_id" class="form-label">{{ __('filament.type') }}*</label>
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
        <label for="colors" class="form-label">{{ __('filament.colors') }}</label>
        <input type="text" class="form-control" id="colors" name="colors"
               value="{{ old('colors', $filament?->colors ? implode(', ', $filament->colors) : '') }}" 
               placeholder="{{ __('filament.colors_placeholder') }}">
        <div class="form-text">{{ __('filament.colors_help') }}</div>
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
    
    <div class="mb-3">
        <label for="cost" class="form-label">{{ __('filament.cost') }}</label>
        <div class="input-group">
            <input type="number" class="form-control" id="cost" name="cost" 
                   step="0.01" min="0" 
                   value="{{ old('cost', $filament?->cost) }}">
            <span class="input-group-text">₽</span>
        </div>
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
