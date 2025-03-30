<form id="partTaskForm" method="POST" data-type="modal">
    @if(!empty($part))
        @method('PUT')
        @csrf
    @endif
    <div class="mb-3">
        @if(empty($part))
            <label class="form-label">{{ __('common.model') }}</label>
            <select class="form-select" name="part_id" required>
                <option value="">{{ __('task.model_select') }}</option>
                @foreach($parts as $model)
                    <option value="{{ $model->id }}">
                        #{{ $model->id }} {{ $model->name }} ({{ $model->version }}{{ $model->version_date ? $model->version_date->format('d.m.Y') : ""}})
                    </option>
                @endforeach
            </select>
        @else
            <div class="mb-3">
                <label class="form-label">{{ __('part.name_full') }}</label>
                <input type="text"
                       class="form-control"
                       name=""
                       disabled
                       value="#{{ $part->id }} {{ $part->name }} ({{ $part->version }}{{ $part->version_date ? $part->version_date->format('d.m.Y') : ""}})">
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('task.count_per_set') }}</label>
        <input type="number"
               class="form-control"
               name="count_per_set"
               value="{{ old('count_per_set', $partTask->count_per_set ?? 1) }}"
               min="1"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('task.count_printed') }}</label>
        <input type="number"
               class="form-control"
               name="count_printed"
               value="{{ old('count_printed', $partTask->count_printed ?? 0) }}"
               min="0"
               required>
    </div>

    <div class="alert alert-danger d-none" id="formErrors"></div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
    </div>
</form>
