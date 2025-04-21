@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('printer.title') }}</h1>
            <button type="button"
                    class="btn btn-success"
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
                                @if($printer->activeJobs->isNotEmpty())
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
                            <h6 class="card-title"><span class="card-text small text-muted">#{{ $printer->id }}</span>
                                <strong>{{ $printer->name }}</strong></h6>
                            <!-- Загруженный филамент -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2 bg-light p-2 rounded">
                                    <h6 class="mb-0">{{ __('printer.filament_slot.title') }}:</h6>
                                    <button type="button" class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#filamentSlotModal"
                                            data-action="{{ route('filament-slot.store', $printer) }}"
                                            data-create-route="{{ route('filament-slot.create', $printer) }}"
                                            data-printer-id="{{ $printer->id }}">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>

                                @if($printer->filamentSlots->isNotEmpty())
                                    <ul class="list-group list-group-flush mb-3">
                                        @foreach($printer->filamentSlots as $slot)
                                            <li class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $slot->name }}</strong>
                                                        @if($slot->filamentSpool)
                                                            <x-filament-plate :filamentSpool="$slot->filamentSpool" />
                                                        @else
                                                            <div class="small text-muted">{{ __('printer.filament_slot.empty') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#filamentSlotModal"
                                                                data-action="{{ route('filament-slot.update', [$printer, $slot]) }}"
                                                                data-edit-route="{{ route('filament-slot.edit', [$printer, $slot]) }}"
                                                                data-method="PUT"
                                                                data-id="{{ $slot->id }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('filament-slot.destroy', [$printer, $slot]) }}"
                                                              method="POST"
                                                              class="confirm-delete"
                                                              data-confirm-title="{{ __('common.buttons.delete') }}?"
                                                              data-confirm-text="{{ __('printer.filament_slot.confirm_delete') }}"
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
                                    <p class="text-muted small">{{ __('printer.filament_slot.none') }}</p>
                                @endif
                            </div>

                            <!-- Задачи печати -->
                            <div class="mt-3">
                                @if($printer->activeJobs->isEmpty())
                                    <div>
                                        <button type="button" class="btn btn-success w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#printJobModal"
                                                data-action="{{ route('print-job.store', $printer) }}"
                                                data-create-route="{{ route('print-job.create', $printer) }}">
                                            <i class="bi bi-plus-lg"></i> {{ __('printer.add_printing') }}
                                        </button>
                                    </div>
                                @endif

                                @foreach($printer->activeJobs as $printJob)
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center rounded">
                                                {{ $printJob->filename }}
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-success"
                                                            data-transport="ajax"
                                                            data-action="{{ route('print-job.complete', [$printer, $printJob]) }}"
                                                            data-method="POST"
                                                            data-confirm="true"
                                                            data-confirm-title="{{ __('printer.confirm.complete_print.title') }}"
                                                            data-confirm-text="{{ __('printer.confirm.complete_print.text') }}"
                                                            data-confirm-button="{{ __('common.buttons.confirm') }}"
                                                            data-cancel-button="{{ __('common.buttons.cancel') }}">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#printJobPartTaskModal"
                                                            data-action="{{ route('print-job.task.store', $printJob) }}"
                                                            data-create-route="{{ route('print-job.task.create', $printJob) }}">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                            data-transport="ajax"
                                                            data-action="{{ route('print-job.destroy', [$printer, $printJob]) }}"
                                                            data-method="DELETE"
                                                            data-confirm="true"
                                                            data-confirm-text="{{ __('printer.confirm_delete_job') }}"
                                                            data-confirm-button="{{ __('common.buttons.confirm') }}"
                                                            data-cancel-button="{{ __('common.buttons.cancel') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @php($taskGroups = $printJob->partTasks->groupBy('task_id'))
                                            @foreach($taskGroups as $taskId => $taskPartTasks)
                                                @php($task = $taskPartTasks->first()->task)
                                                <div class="mb-3">
                                                    <div class="bg-light p-2 rounded">
                                                        <span class="card-text small text-muted">#{{ $task->id }}</span>
                                                        <strong>{{ $task->name }}</strong> ({{ $task->count_set_printed }}/{{ $task->count_set_planned }})
                                                    </div>

                                                    <ul class="list-group list-group-flush">
                                                        @foreach($taskPartTasks as $partTask)
                                                            @php($printJobPartTask = $partTask->pivot)
                                                            <li class="list-group-item px-0">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="small">
                                                                            <span class="card-text small text-muted">#{{ $partTask->part->id }}</span> {{ $partTask->part->name }}
                                                                            <span class="card-text small text-muted">({{ $partTask->part->version }}{{ $partTask->part->version_date ? ', ' . $partTask->part->version_date->format('d.m.Y') : '' }})</span>
                                                                            ({{ $partTask->count_printed }}/{{ $partTask->count_planned }})
                                                                        </div>
                                                                        <div class="ms-5">
                                                                                <span @if ($partTask->count_printing >= $partTask->count_remaining) class="count-complete" @endif>
                                                                                {{ $partTask->pivot->count_printed }}@if($partTask->count_printing > $partTask->pivot->count_printed)
                                                                                        <span class="text-muted">(+{{ $partTask->count_printing - $partTask->pivot->count_printed }})</span>
                                                                                    @endif/{{ $partTask->count_remaining }}
                                                                                </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-sm btn-primary"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#printJobPartTaskModal"
                                                                                data-action="{{ route('print-job.task.update', [$printJob, $partTask]) }}"
                                                                                data-edit-route="{{ route('print-job.task.edit', [$printJob, $partTask]) }}"
                                                                                data-method="PUT"
                                                                                data-id="{{ $partTask->id }}">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                                data-transport="ajax"
                                                                                data-action="{{ route('print-job.task.destroy', [$printJob, $partTask]) }}"
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
                                                </div>
                                            @endforeach
                                            <div class="d-flex justify-content-between align-items-center mb-2 bg-light p-2 rounded">
                                                <h6 class="mb-0">{{ __('printer.filament_slot.title') }}:</h6>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#printJobSpoolModal"
                                                            data-action="{{ route('print-job.spool.store', $printJob) }}"
                                                            data-create-route="{{ route('print-job.spool.create', $printJob) }}">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @foreach($printJob->spools as $spool)
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <x-filament-plate :filamentSpool="$spool" />
                                                            <div class="fw-bold">
                                                                <x-number :value="$spool->pivot->weight_used" />/<x-number :value="$spool->weight_remaining" />
                                                            </div>
                                                        </div>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-primary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#printJobSpoolModal"
                                                                    data-action="{{ route('print-job.spool.update', [$printJob, $spool]) }}"
                                                                    data-edit-route="{{ route('print-job.spool.edit', [$printJob, $spool]) }}"
                                                                    data-method="PUT"
                                                                    data-id="{{ $spool->id }}">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                    data-transport="ajax"
                                                                    data-action="{{ route('print-job.spool.destroy', [$printJob, $spool]) }}"
                                                                    data-method="DELETE"
                                                                    data-confirm="true"
                                                                    data-confirm-text="{{ __('printer.confirm_delete_spool') }}"
                                                                    data-confirm-button="{{ __('common.buttons.confirm') }}"
                                                                    data-cancel-button="{{ __('common.buttons.cancel') }}">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
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

    <div class="modal fade" id="printJobPartTaskModal" tabindex="-1" data-type="formModal">
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

    <div class="modal fade" id="printJobModal" tabindex="-1" data-type="formModal">
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

    <div class="modal fade" id="filamentSlotModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('printer.filament_slot.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="printJobSpoolModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('printer.filament_slot.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

@endsection
