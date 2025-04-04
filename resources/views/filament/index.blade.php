@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('filament.title') }}</h1>
            <button type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#filamentModal"
                    data-action="{{ route('filament.filament.store') }}"
                    data-create-route="{{ route('filament.filament.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('filament.add') }}
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-end table-id">ID</th>
                    <th>{{ __('common.name') }}</th>
                    <th>{{ __('filament.vendor.field') }}</th>
                    <th>{{ __('filament.type.field') }}</th>
                    <th>{{ __('filament.color') }}</th>
                    <th>{{ __('filament.density') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($filaments as $filament)
                    <tr>
                        <td class="text-end table-id">{{ $filament->id }}</td>
                        <td>{{ $filament->name }}</td>
                        <td>{{ $filament->vendor->name }}</td>
                        <td>{{ $filament->type->name }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @if($filament->colors)
                                    @foreach($filament->colors as $color)
                                        <div class="filament-color-preview" style="background-color: {{ $color }};" title="{{ $color }}"></div>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td>{{ $filament->density }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#filamentModal"
                                    data-action="{{ route('filament.filament.update', $filament) }}"
                                    data-edit-route="{{ route('filament.filament.edit', $filament) }}"
                                    data-method="PUT"
                                    data-id="{{ $filament->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('filament.filament.destroy', $filament) }}"
                                  method="POST"
                                  class="d-inline-block confirm-delete"
                                  data-confirm-title="{{ __('common.buttons.delete') }}?"
                                  data-confirm-text="{{ __('filament.action.delete.confirm') }}"
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
        {{ $filaments->links() }}
    </div>

    <div class="modal fade" id="filamentModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('filament.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection
