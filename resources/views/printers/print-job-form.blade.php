<form id="printJobForm" method="POST" data-type="modal">
    <div class="mb-3">
        <label for="filename" class="form-label">{{ __('printer.filename') }}</label>
        <input type="text" class="form-control" id="filename" name="filename" required @if ($printJob) readonly disabled @endif
               value="{{ old('filename', $printJob?->filename) }}">
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
