@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('task.title') }}</h1>
            <button type="button"
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#taskModal"
                    data-action="{{ route('print.tasks.store') }}"
                    data-create-route="{{ route('print.tasks.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('task.add') }}
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th class="text-end table-id">ID</th>
                    <th class="table-status">{{ __('common.status') }}</th>
                    <th class="table-date">{{ __('task.created_at') }}</th>
                    <th>{{ __('common.name') }}<br>{{ __('task.external_id') }}</th>
                    <th class="text-end table-count_two">{{ __('task.count_set_planned') }}</th>
                    <th class="text-end table-count_two">{{ __('task.parts_count') }}</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['archived' => request()->has('archived') ? null : 'true']) }}"
                           class="btn btn-sm {{ request()->has('archived') ? 'btn-success' : 'btn-outline-secondary' }}"
                           title="{{ __('filament.spool.show_archived') }}">
                            <i class="bi bi-archive{{ request()->has('archived') ? '-fill' : '' }}"></i>
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($tasks as $task)
                    <tr data-task-id="{{ $task->id }}" data-hover="row" class="{{ $task->archived ? 'opacity-50' : '' }}">
                        <td class="table-chevron">
                            <i class="bi bi-chevron-right toggle-icon"></i>
                        </td>
                        <td class="text-end table-id">
                            {{ $task->id }}
                        </td>
                        <td class="table-status">
                            <span class="badge badge-{{ strtolower($task->status->name) }}">
                                {{ $task->status->label() }}
                            </span>
                            @if($task->isPrinting())
                                <span class="badge badge-printing ms-1" title="{{ __('printer.status.printing') }}">
                                    <i class="bi bi-printer"></i>
                                </span>
                            @endif
                        </td>
                        <td>{{ $task->created_at->format('d.m.Y') }}</td>
                        <td>{{ $task->name }}@if ($task->external_id)
                                <br>{{ $task->external_id }}
                            @endif</td>
                        <td class="text-end">
                            <span @if ($task->count_set_printed >= $task->count_set_planned) class="count-complete" @endif>{{ $task->count_set_printed }}/{{ $task->count_set_planned }}</span>
                        </td>
                        <td class="text-end">{{ $task->parts->count() }}</td>
                        <td class="text-end">
                            <div class="btn-group" data-hover-target="row">
                                <button type="button" class="btn btn-sm btn-secondary"
                                        data-transport="ajax"
                                        data-action="{{ route('print.tasks.archive', $task) }}"
                                        data-method="POST"
                                        data-confirm="true"
                                        data-confirm-title="{{ __('task.action.archive.title') }}"
                                        data-confirm-text="{{ $task->archived ? __('task.action.archive.confirm_archived') : __('task.action.archive.confirm') }}"
                                        data-confirm-button="{{ __('common.buttons.confirm') }}"
                                        data-cancel-button="{{ __('common.buttons.cancel') }}">
                                    @if($task->archived)
                                        <i class="bi bi-archive-fill"></i>
                                    @else
                                        <i class="bi bi-archive"></i>
                                    @endif
                                </button>
                                <button type="button" class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#taskModal"
                                        data-action="{{ route('print.tasks.update', $task) }}"
                                        data-edit-route="{{ route('print.tasks.edit', $task) }}"
                                        data-delete-route="{{ route('print.tasks.destroy', $task) }}"
                                        data-method="PUT"
                                        data-id="{{ $task->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger"
                                        data-transport="ajax"
                                        data-action="{{ route('print.tasks.destroy', $task) }}"
                                        data-method="DELETE"
                                        data-confirm="true"
                                        data-confirm-title="{{ __('common.buttons.delete') }}?"
                                        data-confirm-text="{{ __('task.action.delete.confirm') }}"
                                        data-confirm-button="{{ __('common.buttons.confirm') }}"
                                        data-cancel-button="{{ __('common.buttons.cancel') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="detail-row d-none {{ $task->archived ? 'opacity-50' : '' }}" data-parent-id="{{ $task->id }}" data-hover="row">
                        <td colspan="9" class="p-0">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-end table-id">ID</th>
                                    <th>{{ __('common.name') }}</th>
                                    <th class="text-end table-count">{{ __('task.count_per_set') }}</th>
                                    <th class="text-end table-count_two">{{ __('task.printing_count') }}</th>
                                    <th class="text-end table-count_two">{{ __('task.count_printed') }}</th>
                                    <th class="text-end">
                                        <div data-hover-target="row">
                                            <button type="button" class="btn btn-sm btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#partTaskModal"
                                                    data-action="{{ route('print.task-parts.store', $task) }}"
                                                    data-create-route="{{ route('print.task-parts.create', $task) }}">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($task->parts as $part)
                                    <tr>
                                        <td class="ps-4"></td>
                                        <td class="text-end table-id">{{ $part->id }}</td>
                                        <td>
                                            @if($part->pivot->count_printing > 0)
                                                <span class="badge badge-printing me-1" title="{{ __('printer.status.printing') }}">
                                                    <i class="bi bi-printer"></i>
                                                </span>
                                            @endif
                                            {{ $part->name }}
                                            <span class="small text-muted">{{ $part->version }}@if($part->version_date)
                                                    , {{ $part->version_date->format('d.m.Y') }}
                                                @endif</span>
                                            <div class="small text-muted">{{ $part->getFullCatalogPath() }}</div>
                                        </td>
                                        <td class="text-end table-count">{{ $part->pivot->count_per_set }}</td>
                                        <td class="text-end table-count_two">
                                            <span @if ($part->pivot->count_printed + $part->pivot->count_printing >= $part->pivot->count_planned) class="count-complete" @endif>
                                                {{ $part->pivot->count_printing }}/{{ $part->pivot->count_remaining }}
                                            </span>
                                        </td>
                                        <td class="text-end table-count_two">
                                            <span @if ($part->pivot->count_printed >= $part->pivot->count_planned) class="count-complete" @endif>
                                                {{ $part->pivot->count_printed }}/{{ $part->pivot->count_planned }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" data-hover-target="row">
                                                <button type="button"
                                                        class="btn btn-sm btn-success update-printed-btn"
                                                        data-part-task-id="{{ $part->pivot->id }}">
                                                    <i class="bi bi-plus-circle-fill"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#partTaskModal"
                                                        data-action="{{ route('print.task-parts.update', $part->pivot) }}"
                                                        data-edit-route="{{ route('print.task-parts.edit', $part->pivot) }}"
                                                        data-method="PUT"
                                                        data-id="{{ $part->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        data-transport="ajax"
                                                        data-action="{{ route('print.task-parts.destroy', $part->pivot) }}"
                                                        data-method="DELETE"
                                                        data-confirm="true"
                                                        data-confirm-title="{{ __('common.buttons.delete') }}?"
                                                        data-confirm-text="{{ __('task.action.delete_part.confirm') }}"
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
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $tasks->links() }}
    </div>

    <div class="modal fade" id="taskModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('task.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="partTaskModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('task.edit_part') }}</h5>
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
                toggleSelector: '[data-task-id]',
                rowSelector   : 'tr.detail-row',
                cookiePrefix  : 'task',
                idAttribute   : 'taskId'
            });
        });
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.update-printed-btn');
            if (!btn) {
                return;
            }
            const partTaskId = btn.dataset.partTaskId;
            Swal.fire({
                title: '{{ __('task.part.add_printed_count') }}',
                input: 'number',
                // inputAttributes: { min: 1 },
                showCancelButton : true,
                confirmButtonText: 'Добавить'
            }).then(result => {
                if (result.isConfirmed && result.value) {
                    fetch('/print/task-parts/' + partTaskId + '/add-printed', {
                        method : 'POST',
                        headers: {
                            'Content-Type'    : 'application/json',
                            'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body   : JSON.stringify({
                            printed_count: parseInt(result.value)
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                }
            });
        });

    </script>
@endpush
