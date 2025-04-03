@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('filament.vendor.title') }}</h1>
            <button type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#vendorModal"
                    data-action="{{ route('filament.vendors.store') }}"
                    data-create-route="{{ route('filament.vendors.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('filament.vendor.add') }}
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-end table-id">ID</th>
                    <th>{{ __('common.name') }}</th>
                    <th class="text-center">{{ __('filament.vendor.rate') }}</th>
                    <th>{{ __('filament.vendor.comment') }}</th>
                    <th class="text-center">{{ __('filament.vendor.filaments_count') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($vendors as $vendor)
                    <tr>
                        <td class="text-end table-id">{{ $vendor->id }}</td>
                        <td>{{ $vendor->name }}</td>
                        <td class="text-center">
                            @if($vendor->rate)
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $vendor->rate ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            @endif
                        </td>
                        <td>{{ $vendor->comment }}</td>
                        <td class="text-center">{{ $vendor->filaments->count() }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#vendorModal"
                                    data-action="{{ route('filament.vendors.update', $vendor) }}"
                                    data-edit-route="{{ route('filament.vendors.edit', $vendor) }}"
                                    data-method="PUT"
                                    data-id="{{ $vendor->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('filament.vendors.destroy', $vendor) }}"
                                  method="POST"
                                  class="d-inline-block confirm-delete"
                                  data-confirm-title="{{ __('common.buttons.delete') }}?"
                                  data-confirm-text="{{ __('filament.vendor.action.delete.confirm') }}"
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
        {{ $vendors->links() }}
    </div>

    <div class="modal fade" id="vendorModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('filament.vendor.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection
