<div class="small d-flex">
    <div class="d-flex flex-wrap gap-1 me-2">
        {{-- <div class="color-badge me-2" style="background-color: {{ $filamentSpool->filament->colors[0] ?? '' }}; width: 18px; height: 100%; min-height: 36px; border-radius: 3px;"></div> --}}
        @if($filamentSpool->filament->colors)
            @foreach($filamentSpool->filament->colors as $color)
                <div class="filament-color-preview" style="background-color: {{ $color }};" title="{{ $color }}"></div>
            @endforeach
        @endif
    </div>
{{--    <div class="color-badge me-2" style="background-color: {{ $filamentSpool->filament->colors[0] ?? '' }}; width: 18px; height: 100%; min-height: 36px; border-radius: 3px;"></div>--}}
    <div>
        <span class="card-text small text-muted">#{{ $filamentSpool->id }}</span> {{ $filamentSpool->filament->name }} {{ $filamentSpool->filament->type->name }},
        {{ $filamentSpool->filament->vendor->name }}, {{ $filamentSpool->packaging->name }}
        <x-filament-spool-weight-state :filamentSpool="$filamentSpool" />
    </div>
</div>
