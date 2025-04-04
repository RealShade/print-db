@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('filament.spool.title') }}</h1>
            <button type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#spoolModal"
                    data-action="{{ route('filament.spools.store') }}"
                    data-create-route="{{ route('filament.spools.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('filament.spool.add') }}
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-end table-id">ID</th>
                    <th></th>
                    <th>{{ __('filament.spool.filament') }}</th>
                    <th>{{ __('filament.type.field') }}</th>
                    <th>{{ __('filament.vendor.field') }}</th>
                    <th>{{ __('filament.spool.weight_used') }}</th>
                    <th>{{ __('filament.spool.remaining_weight') }}</th>
                    <th>{{ __('filament.spool.date_last_used') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($spools as $spool)
                    <tr>
                        <td class="text-end table-id">{{ $spool->id }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @if($spool->filament->colors)
                                    @foreach($spool->filament->colors as $color)
                                        <div class="filament-color-preview" style="background-color: {{ $color }};" title="{{ $color }}"></div>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td>{{ $spool->filament->name }}</td>
                        <td>{{ $spool->filament->type->name }}</td>
                        <td>{{ $spool->filament->vendor->name }}</td>
                        <td>{{ number_format($spool->weight_used ?? 0, 2) }} г</td>
                        <td>{{ number_format($spool->remaining_weight, 2) }} г</td>
                        <td>{{ $spool->date_last_used ? $spool->date_last_used->format('Y-m-d H:m:s') : '' }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#spoolModal"
                                    data-action="{{ route('filament.spools.update', $spool) }}"
                                    data-edit-route="{{ route('filament.spools.edit', $spool) }}"
                                    data-method="PUT"
                                    data-id="{{ $spool->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('filament.spools.destroy', $spool) }}"
                                  method="POST"
                                  class="d-inline-block confirm-delete"
                                  data-confirm-title="{{ __('common.buttons.delete') }}?"
                                  data-confirm-text="{{ __('filament.spool.action.delete.confirm') }}"
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
        {{ $spools->links() }}
    </div>

    <div class="modal fade" id="spoolModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('filament.spool.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection
