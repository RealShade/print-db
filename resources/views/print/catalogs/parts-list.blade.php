@if($parts->count() > 0)
    <div class="table-responsive" data-hover="card">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{ __('part.name') }}</th>
                <th>{{ __('part.version') }}</th>
                <th>{{ __('part.version_date') }}</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($parts as $part)
                <tr>
                    <td>
                        @php
                            $preview = $part->stl_filename ? asset('storage/parts/' . preg_replace('/\.stl$/i', '.png', $part->stl_filename)) : null;
//                            dd($fileService->getPreviewUrl($part->stl_filename));
                        @endphp
                        @if(isset($fileService) && $fileService->hasPreview($part->stl_filename))
                            <span class="d-inline-block" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true" data-bs-content="<img src='{{ $fileService->getPreviewUrl($part->stl_filename) }}' style='max-width:400px;max-height:400px;'>">
                                <img src="{{ $fileService->getPreviewUrl($part->stl_filename) }}" alt="preview" width="60" height="60" style="object-fit:contain; border:1px solid #ccc;">
                            </span>
                        @endif
                        {{ $part->name }}
                    </td>
                    <td>{{ $part->version }}</td>
                    <td>{{ $part->version_date?->format('d.m.Y') }}</td>
                    <td class="text-end">
                        <div class="btn-group" data-hover-target="card">
                            <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#partModal"
                                    data-action="{{ route('print.parts.update', $part) }}"
                                    data-edit-route="{{ route('print.parts.edit', $part) }}"
                                    data-method="PUT"
                                    data-id="{{ $part->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger"
                                    data-transport="ajax"
                                    data-action="{{ route('print.parts.destroy', $part) }}"
                                    data-method="DELETE"
                                    data-confirm="true"
                                    data-confirm-text="{{ __('task.action.delete_part.confirm') }}"
                                    data-confirm-button="{{ __('common.buttons.confirm') }}"
                                    data-cancel-button="{{ __('common.buttons.cancel') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center p-4 text-muted">
        {{ __('part.no_parts') }}
    </div>
@endif
