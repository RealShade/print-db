@if($parts->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('part.name') }}</th>
                    <th>{{ __('part.version') }}</th>
                    <th width="100">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parts as $part)
                    <tr>
                        <td>{{ $part->name }}</td>
                        <td>{{ $part->version }}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#partModal"
                                    data-action="{{ route('print.parts.update', $part) }}"
                                    data-edit-route="{{ route('print.parts.edit', $part) }}"
                                    data-method="PUT"
                                    data-id="{{ $part->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
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
