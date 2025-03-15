@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('task.title') }}</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal"
                    data-action="{{ route('print.tasks.store') }}">
                {{ __('task.add') }}
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('task.external_id') }}</th>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('task.sets_count') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td>{{ $task->external_id }}</td>
                                <td>{{ $task->name }}</td>
                                <td>{{ $task->sets_count }}</td>
                                <td>{{ $task->status->label() }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#taskModal"
                                            data-action="{{ route('print.tasks.update', $task) }}"
                                            data-method="PUT"
                                            data-id="{{ $task->id }}">
                                        {{ __('common.buttons.edit') }}
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('[data-bs-target="#taskModal"]');
    const createRoute = '{{ route('print.tasks.create') }}';
    const editRoute = '{{ route('print.tasks.edit', '') }}';

    buttons.forEach(button => {
        button.dataset.createRoute = createRoute;
        button.dataset.editRoute = editRoute;
    });
});
</script>
@endpush
