<form id="taskForm" method="POST" action="{{ $task ? route('print.tasks.update', $task) : route('print.tasks.store') }}">
    @csrf
    @if($task) @method('PUT') @endif

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
        <label for="sets_count" class="form-label">{{ __('task.sets_count') }}</label>
        <input type="number" class="form-control" id="sets_count" name="sets_count" min="1"
               value="{{ old('sets_count', $task?->sets_count) }}">
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

    <div class="mb-3" id="partsContainer">
        <label class="form-label">{{ __('task.parts') }}</label>
        @foreach($parts as $part)
            <div class="d-flex gap-2 mb-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input"
                           name="parts[{{ $loop->index }}][id]"
                           value="{{ $part->id }}"
                           {{ $task?->parts->contains($part) ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $part->name }}</label>
                </div>
                <input type="number" class="form-control form-control-sm w-25"
                       name="parts[{{ $loop->index }}][quantity_per_set]"
                       placeholder="{{ __('task.quantity_per_set') }}"
                       value="{{ $task?->parts->find($part->id)?->pivot?->quantity_per_set }}">
            </div>
        @endforeach
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
    </div>
</form>
