{{-- resources/views/print/tasks/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('task.title') }}</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal">
            {{ __('task.add') }}
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('task.external_id') }}</th>
                    <th>{{ __('task.name') }}</th>
                    <th>{{ __('task.sets_count') }}</th>
                    <th>{{ __('task.status') }}</th>
                    <th>{{ __('task.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->external_id }}</td>
                    <td>{{ $task->name }}</td>
                    <td>{{ $task->sets_count }}</td>
                    <td><span class="badge bg-{{ $task->status->value }}">{{ $task->status->label() }}</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary edit-task"
                                data-bs-toggle="modal"
                                data-bs-target="#taskModal"
                                data-task-id="{{ $task->id }}">
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

<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('task.form_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Форма будет загружаться динамически --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('taskModal');

    // Загрузка формы при открытии модального окна
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const taskId = button.getAttribute('data-task-id');
        const url = taskId ? `/print/tasks/${taskId}/edit` : '/print/tasks/create';

        fetch(url)
            .then(response => response.text())
            .then(html => {
                modal.querySelector('.modal-body').innerHTML = html;
            });
    });

    // Обработка отправки формы
    modal.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'taskForm') {
            e.preventDefault();

            fetch(e.target.action, {
                method: e.target.method,
                body: new FormData(e.target),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    const errors = modal.querySelector('#formErrors');
                    errors.innerHTML = Object.values(data.errors).flat().join('<br>');
                    errors.classList.remove('d-none');
                }
            });
        }
    });
});
</script>
@endpush
