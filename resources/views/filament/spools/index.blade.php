@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('filament.spool.spools') }}</h1>
            <button type="button"
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#spoolModal"
                    data-action="{{ route('filament.spools.store') }}"
                    data-create-route="{{ route('filament.spools.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('filament.spool.add') }}
            </button>
        </div>

        <div class="table-responsive" data-hover="card">
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
                    <th>{{ __('printer.filament_slot.title') }}</th>
                    <th>{{ __('filament.spool.date_last_used') }}</th>
                    <th class="text-end">
                        <a href="{{ request()->fullUrlWithQuery(['archived' => request()->has('archived') ? null : 'true']) }}"
                           class="btn btn-sm {{ request()->has('archived') ? 'btn-success' : 'btn-outline-secondary' }}"
                           title="{{ __('filament.spool.show_archived') }}">
                            <i class="bi bi-archive{{ request()->has('archived') ? '-fill' : '' }}"></i>
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($spools as $spool)
                    <tr class="{{ $spool->archived ? 'opacity-50' : '' }}">
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
                        <td>
                            <x-number :value="$spool->weight_used" precision="4" />
                        </td>
                        <td>
                            <x-number :value="$spool->weight_remaining" precision="4" />
                        </td>
                        <td>
                            {!! nl2br(e($spool->slots()->with('printer')->get()->map(function($slot) {
                                return $slot->printer->name . ': ' . $slot->name;
                            })->implode("\n"))) !!}
                        </td>
                        <td>{{ $spool->date_last_used ? $spool->date_last_used->format('Y-m-d H:m:s') : '' }}</td>
                        <td class="text-end">
                            <div class="btn-group" data-hover-target="card">
                                <button type="button" class="btn btn-sm btn-secondary"
                                        data-transport="ajax"
                                        data-action="{{ route('filament.spools.archive', $spool) }}"
                                        data-method="POST"
                                        data-confirm="true"
                                        data-confirm-title="{{ __('filament.spool.action.archive.title') }}"
                                        data-confirm-text="{{ $spool->archived ? __('filament.spool.action.archive.confirm_archived') : __('filament.spool.action.archive.confirm') }}"
                                        data-confirm-button="{{ __('common.buttons.confirm') }}"
                                        data-cancel-button="{{ __('common.buttons.cancel') }}">
                                    @if($spool->archived)
                                        <i class="bi bi-archive-fill"></i>
                                    @else
                                        <i class="bi bi-archive"></i>
                                    @endif
                                </button>
                                <button type="button" class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#spoolModal"
                                        data-action="{{ route('filament.spools.update', $spool) }}"
                                        data-edit-route="{{ route('filament.spools.edit', $spool) }}"
                                        data-method="PUT"
                                        data-id="{{ $spool->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger"
                                        data-transport="ajax"
                                        data-action="{{ route('filament.spools.destroy', $spool) }}"
                                        data-method="DELETE"
                                        data-confirm="true"
                                        data-confirm-title="{{ __('common.buttons.delete') }}?"
                                        data-confirm-text="{{ __('filament.spool.action.delete.confirm') }}"
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
