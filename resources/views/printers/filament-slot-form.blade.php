<form id="filamentSlotForm" method="POST">
    @csrf
    @if(isset($filamentSlot))
        @method('PUT')
    @endif

    <input type="hidden" name="printer_id" value="{{ $printer->id }}">

    <div class="mb-3">
        <label for="name" class="form-label">{{ __('printer.filament_slot.name') }}</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ isset($filamentSlot) ? $filamentSlot->name : old('name') }}" required>
        <div class="form-text">{{ __('printer.filament_slot.name_hint') }}</div>
    </div>

    <div class="mb-3">
        <label for="attribute" class="form-label">{{ __('printer.filament_slot.attribute') }}</label>
        <input type="text" class="form-control" id="attribute" name="attribute"
               value="{{ isset($filamentSlot) ? $filamentSlot->attribute : old('attribute') }}" required>
        <div class="form-text">{{ __('printer.filament_slot.attribute_hint') }}</div>
    </div>

    <div class="mb-3">
        <label for="filament_spool_id" class="form-label">{{ __('printer.filament_slot.filament_spool') }}</label>
        <select class="form-select" id="filament_spool_id" name="filament_spool_id">
            <option value="">{{ __('printer.filament_slot.select_filament_spool') }}</option>
            @foreach($filamentSpools as $spool)
                <option value="{{ $spool->id }}" {{ isset($filamentSlot) && $filamentSlot->filament_spool_id == $spool->id ? 'selected' : '' }}>
                    #{{ $spool->id }} {{ $spool->filament->vendor->name }}, {{ $spool->filament->type->name }}, {{ $spool->filament->name }} ({{ $spool->packaging->name }}, {{ $spool->weight_initial - $spool->weight_used }})
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('printer.filament_slot.filament_spool_hint') }}</div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">{{ __('printer.filament_slot.description') }}</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ isset($filamenSlot) ? $filamentSlot->description : old('description') }}</textarea>
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
