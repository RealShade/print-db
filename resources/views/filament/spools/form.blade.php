<form id="spoolForm" method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">{{ __('filament_spool.name') }}*</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $spool?->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="filament_id" class="form-label">{{ __('filament_spool.filament') }}*</label>
        <select class="form-select" id="filament_id" name="filament_id" required>
            <option value="">{{ __('common.select') }}</option>
            @foreach($filaments as $filament)
                <option value="{{ $filament->id }}" {{ old('filament_id', $spool?->filament_id) == $filament->id ? 'selected' : '' }}>
                    {{ $filament->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="filament_packaging_id" class="form-label">{{ __('filament_spool.packaging') }}*</label>
        <select class="form-select" id="filament_packaging_id" name="filament_packaging_id" required>
            <option value="">{{ __('common.select') }}</option>
            @foreach($packagings as $packaging)
                <option value="{{ $packaging->id }}" {{ old('filament_packaging_id', $spool?->filament_packaging_id) == $packaging->id ? 'selected' : '' }}>
                    {{ $packaging->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="weight_initial" class="form-label">{{ __('filament_spool.weight_initial') }}* (г)</label>
        <input type="number" class="form-control" id="weight_initial" name="weight_initial" step="0.0001"
               value="{{ old('weight_initial', $spool?->weight_initial) }}" required>
    </div>

    <div class="mb-3">
        <label for="weight_used" class="form-label">{{ __('filament_spool.weight_used') }} (г)</label>
        <input type="number" class="form-control" id="weight_used" name="weight_used" step="0.0001"
               value="{{ old('weight_used', $spool?->weight_used) }}">
    </div>

    <div class="mb-3">
        <label for="date_first_used" class="form-label">{{ __('filament_spool.date_first_used') }}</label>
        <input type="date" class="form-control" id="date_first_used" name="date_first_used"
               value="{{ old('date_first_used', $spool?->date_first_used?->format('Y-m-d')) }}">
    </div>

    <div class="mb-3">
        <label for="date_last_used" class="form-label">{{ __('filament_spool.date_last_used') }}</label>
        <input type="date" class="form-control" id="date_last_used" name="date_last_used"
               value="{{ old('date_last_used', $spool?->date_last_used?->format('Y-m-d')) }}">
    </div>

    <div class="mb-3">
        <label for="cost" class="form-label">{{ __('filament_spool.cost') }}</label>
        <input type="number" class="form-control" id="cost" name="cost" step="0.01"
               value="{{ old('cost', $spool?->cost) }}">
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
