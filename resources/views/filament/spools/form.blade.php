<form id="spoolForm" method="POST">
    <div class="mb-3">
        <label for="filament_id" class="form-label">{{ __('filament.spool.filament') }}*</label>
        @if ($spool)
            <input type="text" class="form-control" id="filament_id" name="filament_id"
                   value="{{ $spool->filament->vendor->name ?? 'н/д' }}, {{ $spool->filament->type->name ?? 'н/д' }}, {{ $spool->filament->name }}"
                   disabled>
        @else
            <x-filament-select name="filament_id" required />
        @endif
    </div>
    <div class="mb-3">
        <label for="filament_packaging_id" class="form-label">{{ __('filament.spool.packaging') }}*</label>
        @if ($spool)
            <input type="text" class="form-control" id="filament_packaging_id" name="filament_packaging_id"
                   value="{{ $spool->packaging->name ?? 'н/д' }}" disabled>
        @else
            <select class="form-select" id="filament_packaging_id" name="filament_packaging_id" required>
                <option value="">{{ __('common.select') }}</option>
                @foreach($packaging as $packagingEach)
                    <option value="{{ $packagingEach->id }}" {{ old('filament_packaging_id', $spool?->filament_packaging_id) == $packagingEach->id ? 'selected' : '' }}>
                        {{ $packagingEach->name }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>

    <div class="mb-3">
        <label for="cost" class="form-label">{{ __('filament.spool.cost') }}</label>
        <input type="number" class="form-control" id="cost" name="cost" step="0.01"
               value="{{ old('cost', $spool?->cost) }}">
    </div>

    @if ($spool)
        <div class="mb-3">
            <label for="weight_initial" class="form-label">{{ __('filament.spool.weight_initial') }}* (г)</label>
            <input type="number" class="form-control" id="weight_initial" name="weight_initial" step="0.0001"
                   value="{{ old('weight_initial', $spool?->weight_initial) }}" required>
        </div>

        <div class="mb-3">
            <label for="weight_used" class="form-label">{{ __('filament.spool.weight_used') }} (г)</label>
            <input type="number" class="form-control" id="weight_used" name="weight_used" step="0.0001"
                   value="{{ old('weight_used', $spool?->weight_used) }}">
        </div>

        <div class="mb-3">
            <label for="date_first_used" class="form-label">{{ __('filament.spool.date_first_used') }}</label>
            <input type="datetime-local" class="form-control" id="date_first_used" name="date_first_used"
                   value="{{ old('date_first_used', $spool?->date_first_used?->format('Y-m-d H:i:s')) }}">
        </div>

        <div class="mb-3">
            <label for="date_last_used" class="form-label">{{ __('filament.spool.date_last_used') }}</label>
            <input type="datetime-local" class="form-control" id="date_last_used" name="date_last_used"
                   value="{{ old('date_last_used', $spool?->date_last_used?->format('Y-m-d H:i:s')) }}">
        </div>
    @else
        <div class="mb-3">
            <label for="quantity" class="form-label">{{ __('filament.spool.quantity') }}</label>
            <input type="number" class="form-control" id="quantity" name="quantity"
                   value="{{ old('quantity', 1) }}" min="1" max="100" required>
            <div class="form-text">{{ __('filament.spool.quantity_hint') }}</div>
        </div>
    @endif

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
