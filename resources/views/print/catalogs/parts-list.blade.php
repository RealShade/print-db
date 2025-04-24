@if($parts->count() > 0)
    <div class="table-responsive" data-hover="card">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{ __('part.name') }}</th>
                <th>{{ __('part.version') }}</th>
                <th>{{ __('part.version_date') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($parts as $part)
                <tr>
                    <td>{{ $part->name }}</td>
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
