<form id="filamentTypeForm" method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">{{ __('filament.type.name') }}</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $filamentType?->name) }}">
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
