@props([
    'id' => null,
    'name',
    'value' => '',
    'required' => false,
    'error' => null,
    'disabled' => false,
    'autofocus' => false,
    'inline' => false,
    'parts' => [] // Передаем список моделей
])

@php
    $id = $id ?? $name;
    $error = $error ?? $name;
    $value = old($name, $value);

    $selectedName = '';
@endphp
<div class="dropdown custom-select-dropdown">
    <div class="input-container">
        <ul class="dropdown-menu w-100" aria-labelledby="customSelect">
            @foreach ($parts as $part)
                <li>
                    <button
                        class="dropdown-item d-flex flex-column text-start"
                        type="button"
                        data-id="{{ $part->id }}">
                        <strong>#{{ $part->id }} {{ $part->name }}</strong>
                        <small class="text-muted">{{ $part->getFullCatalogPath() }}</small>
                    </button>
                </li>
                @if($value && $part->id == $value)
                    @php($selectedName = "#{$part->id} {$part->name}")
                @endif
            @endforeach
        </ul>
        <input
            type="text"
            class="form-control"
            id="customSelect"
            placeholder=""
            readonly
            data-bs-toggle="dropdown"
            value="{{ $selectedName }}"
        />
        <button
            type="button"
            class="btn-clear-input"
            title="{{ __('common.buttons.clear') }}"
            aria-label="{{ __('common.buttons.clear') }}"
        >
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <input
        type="hidden"
        name="{{ $name }}"
        value="{{ $value ?? '' }}"
    />
</div>
