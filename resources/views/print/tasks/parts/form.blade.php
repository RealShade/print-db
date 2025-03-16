<form id="partTaskForm" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">{{ __('common.name') }}</label>
        <input type="text" class="form-control" value="{{ $part->name }}" disabled>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('part.version') }}</label>
        <input type="text" class="form-control" value="{{ $part->version }}" disabled>
    </div>

    <div class="mb-3">
        <label for="count_per_set" class="form-label">{{ __('task.count_per_set') }}</label>
        <input type="number" class="form-control" id="count_per_set" name="count_per_set"
               value="{{ old('count_per_set', $part->pivot->count_per_set) }}" min="1" disabled>
    </div>

    <div class="mb-3">
        <label for="count_printed" class="form-label">{{ __('task.count_printed') }}</label>
        <div class="input-group">
            <input type="number" class="form-control" id="count_printed" name="count_printed"
                   value="{{ old('count_printed', $part->pivot->count_printed) }}" min="0" required>
            <span class="input-group-text">/{{ $part->pivot->count_per_set * $task->count_set_planned }}</span>
        </div>
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
