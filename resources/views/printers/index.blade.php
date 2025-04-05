@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('printer.title') }}</h1>
            <button type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#printerModal"
                    data-action="{{ route('printers.store') }}"
                    data-create-route="{{ route('printers.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('printer.add') }}
            </button>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($printers as $printer)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge badge-{{ strtolower($printer->status->value()) }} me-2">
                                    {{ $printer->status->label() }}
                                </span>
                                @if($printer->printingTasks->isNotEmpty())
                                    <span class="badge badge-printing" title="{{ __('printer.status.printing') }}">
                                        <i class="bi bi-printer"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#printerModal"
                                        data-action="{{ route('printers.update', $printer) }}"
                                        data-edit-route="{{ route('printers.edit', $printer) }}"
                                        data-method="PUT"
                                        data-id="{{ $printer->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('printers.destroy', $printer) }}"
                                      method="POST"
                                      class="d-inline-block confirm-delete"
                                      data-confirm-title="{{ __('common.buttons.delete') }}?"
                                      data-confirm-text="{{ __('printer.confirm_delete') }}"
                                      data-confirm-button="{{ __('common.buttons.confirm') }}"
                                      data-cancel-button="{{ __('common.buttons.cancel') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><span class="card-text small text-muted">#{{ $printer->id }}</span> <strong>{{ $printer->name }}</strong></h5>
                            <hr>
                            <!-- Загруженный филамент -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6>{{ __('printer.filament_loaded.title') }}:</h6>
                                    <button type="button" class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#filamentLoadedModal"
                                            data-action="{{ route('filament-loaded.store', $printer) }}"
                                            data-create-route="{{ route('filament-loaded.create', $printer) }}"
                                            data-printer-id="{{ $printer->id }}">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>

                                @if($printer->filamentLoaded->isNotEmpty())
                                    <ul class="list-group list-group-flush mb-3">
                                        @foreach($printer->filamentLoaded as $loaded)
                                            <li class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $loaded->name }}</strong>
                                                        @if($loaded->filamentSpool)
                                                            <div class="small d-flex">
                                                                <div class="color-badge me-2" style="background-color: {{ $loaded->filamentSpool->filament->colors[0] ?? '' }}; width: 18px; height: 100%; min-height: 36px; border-radius: 3px;"></div>
                                                                <div>
                                                                    <span class="card-text small text-muted">#{{ $loaded->filamentSpool->id }}</span> {{ $loaded->filamentSpool->filament->name }} {{ $loaded->filamentSpool->filament->type->name }}
                                                                    <br>
                                                                    {{ $loaded->filamentSpool->filament->vendor->name }}, ({{ $loaded->filamentSpool->weight_initial - $loaded->filamentSpool->weight_used }}/{{ $loaded->filamentSpool->packaging->weight }})
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="small text-muted">{{ __('printer.filament_loaded.empty') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#filamentLoadedModal"
                                                                data-action="{{ route('filament-loaded.update', [$printer, $loaded]) }}"
                                                                data-edit-route="{{ route('filament-loaded.edit', [$printer, $loaded]) }}"
                                                                data-method="PUT"
                                                                data-id="{{ $loaded->id }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('filament-loaded.destroy', [$printer, $loaded]) }}"
                                                              method="POST"
                                                              class="confirm-delete"
                                                              data-confirm-title="{{ __('common.buttons.delete') }}?"
                                                              data-confirm-text="{{ __('printer.filament_loaded.confirm_delete') }}"
                                                              data-confirm-button="{{ __('common.buttons.confirm') }}"
                                                              data-cancel-button="{{ __('common.buttons.cancel') }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger rounded-start-0">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted small">{{ __('printer.filament_loaded.none') }}</p>
                                @endif
                            </div>
                            <hr>
                            <!-- Задачи печати -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6>{{ __('printer.printing') }}:</h6>
                                    <div class="btn-group">
                                        @if($printer->printingTasks->isNotEmpty())
                                            <button class="btn btn-sm btn-success"
                                                    data-transport="ajax"
                                                    data-action="{{ route('printers.complete-print', $printer) }}"
                                                    data-method="POST"
                                                    data-confirm="true"
                                                    data-confirm-title="{{ __('printer.confirm.complete_print.title') }}"
                                                    data-confirm-text="{{ __('printer.confirm.complete_print.text') }}"
                                                    data-confirm-button="{{ __('common.buttons.confirm') }}"
                                                    data-cancel-button="{{ __('common.buttons.cancel') }}">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#printingTaskModal"
                                                data-action="{{ route('printing-tasks.store', $printer) }}"
                                                data-create-route="{{ route('printing-tasks.create', $printer) }}">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                @if($printer->printingTasks->isNotEmpty())
                                    <ul class="list-group list-group-flush">
                                        @foreach($printer->printingTasks as $printingTask)
                                            <li class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div>
                                                            <span class="card-text small text-muted">#{{ $printingTask->partTask->task->id }}</span>
                                                            <strong>{{ $printingTask->partTask->task->name }}</strong> ({{ $printingTask->partTask->count_printed }}/{{ $printingTask->partTask->count_per_set * $printingTask->partTask->task->count_set_planned }})
                                                        </div>
                                                        <div class="small">
                                                            {{ $printingTask->partTask->part->name }}
                                                            (<span class="card-text small text-muted">#{{ $printingTask->partTask->part->id }}</span>, {{ $printingTask->partTask->part->version }}{{ $printingTask->partTask->part->version_date ? ', ' . $printingTask->partTask->part->version_date->format('d.m.Y') : '' }})
                                                        </div>
                                                        <div class="small">
                                                            {{ $printingTask->count }} ({{ $printingTask->partTask->count_printed }}/{{ $printingTask->partTask->count_per_set * $printingTask->partTask->task->count_set_planned }})
                                                        </div>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#printingTaskModal"
                                                                data-action="{{ route('printing-tasks.update', $printingTask) }}"
                                                                data-edit-route="{{ route('printing-tasks.edit', $printingTask) }}"
                                                                data-method="PUT"
                                                                data-id="{{ $printingTask->id }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                data-transport="ajax"
                                                                data-action="{{ route('printing-tasks.destroy', $printingTask) }}"
                                                                data-method="DELETE"
                                                                data-confirm="true"
                                                                data-confirm-text="{{ __('printer.confirm_delete_task') }}"
                                                                data-confirm-button="{{ __('common.buttons.confirm') }}"
                                                                data-cancel-button="{{ __('common.buttons.cancel') }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted small">{{ __('printer.no_tasks') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="printerModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('printer.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="printingTaskModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('printer.printing') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filamentLoadedModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('printer.filament_loaded.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection
