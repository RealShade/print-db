<form id="filamentLoadedForm" method="POST">
    @csrf
    @if(isset($filamentLoaded))
        @method('PUT')
    @endif

    <input type="hidden" name="printer_id" value="{{ $printer->id }}">

    <div class="mb-3">
        <label for="name" class="form-label">{{ __('printer.filament_loaded.name') }}</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ isset($filamentLoaded) ? $filamentLoaded->name : old('name') }}" required>
        <div class="form-text">{{ __('printer.filament_loaded.name_hint') }}</div>
    </div>

    <div class="mb-3">
        <label for="attribute" class="form-label">{{ __('printer.filament_loaded.attribute') }}</label>
        <input type="text" class="form-control" id="attribute" name="attribute"
               value="{{ isset($filamentLoaded) ? $filamentLoaded->attribute : old('attribute') }}" required>
        <div class="form-text">{{ __('printer.filament_loaded.attribute_hint') }}</div>
    </div>

    <div class="mb-3">
        <label for="filament_spool_id" class="form-label">{{ __('printer.filament_loaded.filament_spool') }}</label>
        <select class="form-select" id="filament_spool_id" name="filament_spool_id">
            <option value="">{{ __('printer.filament_loaded.select_filament_spool') }}</option>
            @foreach($filamentSpools as $spool)
                <option value="{{ $spool->id }}" {{ isset($filamentLoaded) && $filamentLoaded->filament_spool_id == $spool->id ? 'selected' : '' }}>
                    #{{ $spool->id }} {{ $spool->filament->vendor->name }}, {{ $spool->filament->type->name }}, {{ $spool->filament->name }} ({{ $spool->packaging->name }}, {{ $spool->weight_initial - $spool->weight_used }})
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('printer.filament_loaded.filament_spool_hint') }}</div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">{{ __('printer.filament_loaded.description') }}</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ isset($filamentLoaded) ? $filamentLoaded->description : old('description') }}</textarea>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">
            {{ __('common.buttons.save') }}
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            {{ __('common.buttons.cancel') }}
        </button>
    </div>
</form>
