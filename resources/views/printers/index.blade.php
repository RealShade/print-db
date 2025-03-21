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

        <table class="table table-striped">
            <thead>
            <tr>
                <th></th>
                <th>{{ __('printer.id') }}</th>
                <th>{{ __('printer.name') }}</th>
                <th>{{ __('common.status') }}</th>
                <th>{{ __('common.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($printers as $printer)
                <tr data-printer-id="{{ $printer->id }}">
                    <td>
                        <i class="bi bi-chevron-right toggle-icon"></i>
                    </td>
                    <td class="text-end">{{ $printer->id }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($printer->status->value()) }}">
                            {{ $printer->status->label() }}
                        </span>
                        @if($printer->printingTasks->isNotEmpty())
                            <span class="badge badge-printing ms-1" title="{{ __('printer.status.printing') }}">
                                <i class="bi bi-printer"></i>
                            </span>
                        @endif
                    </td>
                    <td>
                        {{ $printer->name }}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#printerModal"
                                data-action="{{ route('printers.update', $printer) }}"
                                data-edit-route="{{ route('printers.edit', $printer) }}"
                                data-method="PUT"
                                data-id="{{ $printer->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form action="{{ route('printers.toggle-status', $printer) }}"
                              method="POST"
                              class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-{{ $printer->status === \App\Enums\PrinterStatus::ACTIVE ? 'warning' : 'success' }}">
                                <i class="bi bi-{{ $printer->status === \App\Enums\PrinterStatus::ACTIVE ? 'pause' : 'play' }}"></i>
                            </button>
                        </form>

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
                    </td>
                </tr>
                <tr class="detail-row d-none" data-parent-id="{{ $printer->id }}">
                    <td colspan="9" class="p-0">
                        <table class="table mb-0">
                            <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('task.title') }}</th>
                                <th>{{ __('part.title') }}</th>
                                <th class="text-end">{{ __('printer.print_count') }}</th>
                                <th class="text-end">{{ __('task.count_printed') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($printer->printingTasks as $printingTask)
                                <tr>
                                    <td class="ps-5"></td>
                                    <td>
                                        #{{ $printingTask->partTask->task->id }}
                                        {{ $printingTask->partTask->task->name }}
                                    </td>
                                    <td>
                                        #{{ $printingTask->partTask->part->id }}
                                        {{ $printingTask->partTask->part->name }}
                                        <span class="text-muted">
                                            ({{ $printingTask->partTask->part->version }}
                                            {{ $printingTask->partTask->part->version_date ? ', ' . $printingTask->partTask->part->version_date->format('d.m.Y') : '' }})
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $printingTask->count }}</td>
                                    <td class="text-end">
                                        {{ $printingTask->partTask->count_printed }}/{{ $printingTask->partTask->count_per_set * $printingTask->partTask->task->count_set_planned }}
                                    </td>
                                    <td>
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
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="p-2">
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#printingTaskModal"
                                    data-action="{{ route('printing-tasks.store', $printer) }}"
                                    data-create-route="{{ route('printing-tasks.create', $printer) }}"
                                <i class="bi bi-plus-lg"></i> {{ __('printer.add_printing') }}
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initToggleRows({
            toggleSelector: '[data-printer-id]',
            rowSelector   : 'tr.detail-row',
            cookiePrefix  : 'printer',
            idAttribute   : 'printerId'
        });
    });
</script>
@endpush
