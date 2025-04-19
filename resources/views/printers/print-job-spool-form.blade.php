<form id="printJobSpool" method="POST" data-type="modal">
    <div class="mb-3">
        <label for="filament_spool_id" class="form-label">{{ __('filament.spool.title') }}</label>
        <x-filament-spool-select :value="$filamentSpool?->filament_spool_id" name="filament_spool_id" required />
    </div>
    <div class="mb-3">
        <label for="weight_used" class="form-label">{{ __('filament.spool.weight_used') }}</label>
        <input type="number" class="form-control" id="weight_used" name="weight_used" min="0" required
               value="{{ old('count', $filamentSpool?->pivot->weight_used) ?: 0 }}">
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
