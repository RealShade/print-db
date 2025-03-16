@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('task.title') }}</h1>
            <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#taskModal"
                    data-action="{{ route('print.tasks.store') }}"
                    data-create-route="{{ route('print.tasks.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('task.add') }}
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="text-end">ID</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('task.created_at') }}</th>
                            <th>{{ __('task.external_id') }}</th>
                            <th>{{ __('common.name') }}</th>
                            <th class="text-end">{{ __('task.count_set_planned') }}</th>
                            <th class="text-end">{{ __('task.parts_count') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tasks as $task)
                            <tr data-task-id="{{ $task->id }}">
                                <td>
                                    <button class="btn btn-sm btn-link toggle-parts" data-task-id="{{ $task->id }}">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </td>
                                <td class="text-end">{{ $task->id }}</td>
                                <td>
                                        <span class="badge badge-{{ strtolower($task->status->name) }}">
                                            {{ $task->status->label() }}
                                        </span>
                                </td>
                                <td>{{ $task->created_at->format('d.m.Y') }}</td>
                                <td>{{ $task->external_id }}</td>
                                <td>{{ $task->name }}</td>
                                <td class="text-end">{{ $task->getCompletedSetsCount() }}/{{ $task->count_set_planned }}</td>
                                <td class="text-end">{{ $task->parts->count() }}</td>
                                <td>
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
                                </td>
                            </tr>
                            <tr class="parts-row d-none" data-parent-id="{{ $task->id }}">
                                <td colspan="9" class="p-0">
                                    <table class="table mb-0">
                                        <thead class="table">
                                        <tr>
                                            <th></th>
                                            <th class="text-end">ID</th>
                                            <th>{{ __('common.name') }}</th>
                                            <th>{{ __('part.version') }}</th>
                                            <th class="text-end">{{ __('task.count_per_set') }}</th>
                                            <th class="text-end">{{ __('task.count_printed') }}</th>
                                            <th>{{ __('common.actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($task->parts as $part)
                                            <tr>
                                                <td class="ps-5"></td>
                                                <td class="text-end pe-3">{{ $part->id }}</td>
                                                <td>{{ $part->name }}</td>
                                                <td>{{ $part->version }}</td>
                                                <td class="text-end">{{ $part->pivot->count_per_set }}</td>
                                                <td class="text-end">{{ $part->pivot->count_printed }}/{{ $part->pivot->count_per_set * $task->count_set_planned }}</td>
                                                <td>
                                                    <button type="button"
                                                            class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#partTaskModal"
                                                            data-action="{{ route('print.task-parts.update', [$task, $part]) }}"
                                                            data-edit-route="{{ route('print.task-parts.edit', [$task, $part]) }}"
                                                            data-method="PUT"
                                                            data-id="{{ $part->id }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
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
        </div>
    </div>

    <div class="modal fade" id="taskModal" tabindex="-1">
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

    <div class="modal fade" id="partTaskModal" tabindex="-1">
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
