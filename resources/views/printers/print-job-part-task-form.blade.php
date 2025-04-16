<form id="printingTaskForm" method="POST" data-type="modal">
    <div class="mb-3">
        <label for="part_task_id" class="form-label">{{ __('part.title') }}</label>
        <select class="form-select" id="part_task_id" name="part_task_id" required>
            <option value="">{{ __('printer.select_part') }}</option>
            @foreach($partsWithTasks as $partTaskEach)
                <option value="{{ $partTaskEach->id }}"
                        data-task-id="{{ $partTaskEach->task_id }}"
                        data-part-id="{{ $partTaskEach->part_id }}"
                    {{ old('part_task_id', $partTask?->id) === $partTaskEach->id ? 'selected' : '' }}>
                    #{{ $partTaskEach->part_id }} {{ $partTaskEach->part_name }}
                    {{ $partTaskEach->count_printed }}/{{ $partTaskEach->required_count }}
                    (#{{ $partTaskEach->task_id }} {{ $partTaskEach->task_name }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="count" class="form-label">{{ __('printer.print_count') }}</label>
        <input type="number" class="form-control" id="count" name="count" min="1" required
               value="{{ old('count', $partTask?->pivot->count_printed) ?: 1 }}">
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
