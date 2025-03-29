<form id="taskForm" method="POST">
    @if ($task)
        {!! \App\Helpers\FilenamePlaceholderHelper::generateWithWrapper($task) !!}
    @endif
    <div class="mb-3">
        <label for="external_id" class="form-label">{{ __('task.external_id') }}</label>
        <input type="text" class="form-control" id="external_id" name="external_id"
               value="{{ old('external_id', $task?->external_id) }}">
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">{{ __('task.name') }}</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $task?->name) }}">
    </div>

    <div class="mb-3">
        <label for="count_set_planned" class="form-label">{{ __('task.count_set_planned') }}</label>
        <input type="number" class="form-control" id="count_set_planned" name="count_set_planned" min="1"
               value="{{ old('count_set_planned', $task?->count_set_planned) }}">
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">{{ __('common.status') }}</label>
        <select class="form-select" id="status" name="status">
            @foreach(\App\Enums\TaskStatus::cases() as $status)
                <option value="{{ $status->value }}"
                    {{ old('status', $task?->status?->value) === $status->value ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
