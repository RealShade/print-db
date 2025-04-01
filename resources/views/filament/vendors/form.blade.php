<form id="vendorForm" method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">{{ __('filament_vendor.name') }}*</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $vendor?->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="rate" class="form-label">{{ __('filament_vendor.rate') }}</label>
        <select class="form-select" id="rate" name="rate">
            @for($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ old('rate', $vendor?->rate) == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor
        </select>
    </div>

    <div class="mb-3">
        <label for="comment" class="form-label">{{ __('filament_vendor.comment') }}</label>
        <textarea class="form-control" id="comment" name="comment" rows="3">{{ old('comment', $vendor?->comment) }}</textarea>
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
