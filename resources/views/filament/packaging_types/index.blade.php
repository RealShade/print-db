@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('filament_packaging_type.title') }}</h1>
            <button type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#packagingTypeModal"
                    data-action="{{ route('filament.packaging.store') }}"
                    data-create-route="{{ route('filament.packaging.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('filament_packaging.add') }}
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-end table-id">ID</th>
                    <th>{{ __('common.name') }}</th>
                    <th class="text-center">{{ __('filament_packaging.weight') }}</th>
                    <th>{{ __('filament_packaging.description') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($packagingTypes as $packagingType)
                    <tr>
                        <td class="text-end table-id">{{ $packagingType->id }}</td>
                        <td>{{ $packagingType->name }}</td>
                        <td class="text-center">{{ $packagingType->weight ? $packagingType->weight . ' г' : '' }}</td>
                        <td>{{ $packagingType->description }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#packagingTypeModal"
                                    data-action="{{ route('filament.packaging.update', $packagingType) }}"
                                    data-edit-route="{{ route('filament.packaging.edit', $packagingType) }}"
                                    data-method="PUT"
                                    data-id="{{ $packagingType->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('filament.packaging.destroy', $packagingType) }}"
                                  method="POST"
                                  class="d-inline-block confirm-delete"
                                  data-confirm-title="{{ __('common.buttons.delete') }}?"
                                  data-confirm-text="{{ __('filament_packaging.action.delete.confirm') }}"
                                  data-confirm-button="{{ __('common.buttons.confirm') }}"
                                  data-cancel-button="{{ __('common.buttons.cancel') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $packagingTypes->links() }}
    </div>

    <div class="modal fade" id="packagingTypeModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('filament_packaging.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection
