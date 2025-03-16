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
                                <th class="text-end">ID</th>
                                <th>{{ __('task.external_id') }}</th>
                                <th>{{ __('common.name') }}</th>
                                <th class="text-end">{{ __('task.count_set_planned') }}</th>
                                <th class="text-end">{{ __('task.parts_count') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td class="text-end">{{ $task->id }}</td>
                                    <td>{{ $task->external_id }}</td>
                                    <td>{{ $task->name }}</td>
                                    <td class="text-end">{{ $task->count_set_planned }}</td>
                                    <td class="text-end">{{ $task->parts->count() }}</td>
                                    <td>
                                        <span class="badge badge-{{ strtolower($task->status->name) }}">
                                            {{ $task->status->label() }}
                                        </span>
                                    </td>
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
@endsection
