<form id="packagingTypeForm" method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">{{ __('filament.packaging.name') }}*</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $packagingType?->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="weight" class="form-label">{{ __('filament.packaging.weight') }}</label>
        <input type="number" class="form-control" id="weight" name="weight" min="1"
               value="{{ old('weight', $packagingType?->weight) }}">
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">{{ __('filament.packaging.description') }}</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $packagingType?->description) }}</textarea>
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
