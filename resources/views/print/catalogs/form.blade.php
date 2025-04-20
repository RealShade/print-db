<form id="catalogForm" method="POST" data-type="modal">
    <div class="mb-3">
        <label for="name" class="form-label">{{ __('part.catalog.name') }}</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $catalog->name ?? null) }}" required>
    </div>

    <div class="mb-3">
        <label for="parent_id" class="form-label">{{ __('part.catalog.parent') }}</label>
        <select class="form-select" id="parent_id" name="parent_id">
            <option value="">{{ __('part.catalog.no_parent') }}</option>
            @foreach($catalogs as $cat)
                <option value="{{ $cat->id }}"
                        @if((isset($catalog) && $catalog->parent_id == $cat->id)
                           || (request()->has('parent_id') && request('parent_id') == $cat->id)
                           || isset($parent) && $parent->id == $cat->id)
                            selected
                    @endif>
                    {{ $cat->name }}
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
