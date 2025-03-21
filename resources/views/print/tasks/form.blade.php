<form id="taskForm" method="POST">
    @if ($task)
        {!! FilenamePlaceholder::generateWithWrapper($task) !!}
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

    <div class="mb-3">
        <label class="form-label">{{ __('task.parts') }}</label>
        <div class="d-flex gap-2 mb-2">
            <button type="button" class="btn btn-outline-primary" id="addPartBtn">
                <i class="bi bi-plus-lg"></i> {{ __('common.buttons.add') }}
            </button>
        </div>
        <div id="selectedParts" class="list-group mt-2">
            @if($task)
                @foreach($task->parts as $part)
                    <div class="list-group-item" data-part-id="{{ $part->id }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $part->name }}</strong>
                                <span class="text-muted">({{ $part->version }})</span>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="parts[{{ $loop->index }}][id]" value="{{ $part->id }}">
                                <input type="number" class="form-control form-control-sm w-auto"
                                       name="parts[{{ $loop->index }}][count_per_set]"
                                       value="{{ $part->pivot->count_per_set }}"
                                       placeholder="{{ __('task.count_per_set') }}"
                                       style="width: 100px !important;">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-part">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>

<div class="modal fade" id="partsModal" tabindex="-1" data-type="formModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('task.select_part') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach($parts as $part)
                        <button type="button" class="list-group-item list-group-item-action select-part"
                                data-part-id="{{ $part->id }}"
                                data-part-name="{{ $part->name }}"
                                data-part-version="{{ $part->version }}"
                                data-part-version-date="{{ $part->version_date?->format('d.m.Y') }}">
                            <strong>#{{ $part->id }}</strong>
                            {{ $part->name }}
                            <span class="text-muted">({{ $part->version }}
                                @if($part->version_date)
                                    от {{ $part->version_date->format('d.m.Y') }}
                                @endif
                                )</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
