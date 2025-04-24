@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('filament.type.title') }}</h1>
            <button type="button"
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#filamentTypeModal"
                    data-action="{{ route('filament.types.store') }}"
                    data-create-route="{{ route('filament.types.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('filament.type.add') }}
            </button>
        </div>

        <div class="table-responsive" data-hover="card">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-end table-id">ID</th>
                    <th>{{ __('common.name') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($filamentTypes as $filamentType)
                    <tr>
                        <td class="text-end table-id">
                            {{ $filamentType->id }}
                        </td>
                        <td>{{ $filamentType->name }}</td>
                        <td class="text-end">
                            <div class="btn-group" data-hover-target="card">
                                <button type="button" class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#filamentTypeModal"
                                        data-action="{{ route('filament.types.update', $filamentType) }}"
                                        data-edit-route="{{ route('filament.types.edit', $filamentType) }}"
                                        data-method="PUT"
                                        data-id="{{ $filamentType->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger"
                                        data-transport="ajax"
                                        data-action="{{ route('filament.types.destroy', $filamentType) }}"
                                        data-method="DELETE"
                                        data-confirm="true"
                                        data-confirm-title="{{ __('common.buttons.delete') }}?"
                                        data-confirm-text="{{ __('filament.type.action.delete.confirm') }}"
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
        {{ $filamentTypes->links() }}
    </div>

    <div class="modal fade" id="filamentTypeModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('filament.type.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection
