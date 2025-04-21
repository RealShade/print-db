<form id="partForm" method="POST" data-type="modal">
    <div class="mb-3">
        <label for="catalog_id" class="form-label">{{ __('part.catalog.name') }}</label>
        <select class="form-select" id="catalog_id" name="catalog_id">
            <option value="">{{ __('part.no_catalog') }}</option>
            @foreach(\App\Models\Catalog::where('user_id', auth()->id())->get() as $cat)
                <option value="{{ $cat->id }}"
                        @if((isset($part) && $part->catalog_id == $cat->id) ||
                           (isset($catalog) && $catalog->id == $cat->id))
                            selected
                    @endif>
                    @if($cat->parent){{ $cat->getFullCatalogPath() }} /@endif {{ $cat->name }}
</option>
@endforeach
</select>
</div>

<div class="mb-3">
<label for="name" class="form-label">{{ __('part.name') }}</label>
<input type="text" class="form-control" id="name" name="name"
value="{{ old('name', $part?->name) }}" required>
</div>

<div class="mb-3">
<label for="version" class="form-label">{{ __('part.version') }}</label>
<input type="text" class="form-control" id="version" name="version"
value="{{ old('version', $part?->version) }}"
placeholder="v0">
</div>

<div class="mb-3">
<label for="version_date" class="form-label">{{ __('part.version_date') }}</label>
<input type="date" class="form-control" id="version_date" name="version_date"
value="{{ old('version_date', $part?->version_date?->format('Y-m-d')) }}">
</div>

<div class="alert alert-danger d-none" id="formErrors"></div>

<div class="modal-footer">
<button type="submit" class="btn btn-primary">{{ __('common.buttons.save') }}</button>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.buttons.cancel') }}</button>
</div>
</form>
