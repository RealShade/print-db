@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <h1>{{ __('menu.statistics.filament_consumption') }}</h1>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Фільтр</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('statistics.consumption.index') }}" class="row align-items-end">
                    <div class="col-md-4 mb-2">
                        <label for="start_date" class="form-label">Дата початку</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="end_date" class="form-label">Дата кінця</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <button type="submit" class="btn btn-primary">Застосувати</button>
                        <a href="{{ route('statistics.consumption.index') }}" class="btn btn-outline-secondary">Скинути</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Витрата філаменту за період: {{ $startDate }} - {{ $endDate }}</h5>
                <span class="badge bg-primary fs-6">Загальна витрата: {{ number_format($totalConsumption, 1) }} г</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Завдання</th>
                                <th class="text-end">Загальна витрата за день (г)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($consumptionData as $date => $dayData)
                                <tr data-date-id="{{ \Illuminate\Support\Str::slug($date) }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-chevron-right me-2 toggle-icon"></i>
                                            <strong>{{ $date }}</strong>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td class="text-end"><strong>{{ number_format($dayData['total_day_weight'], 1) }} г</strong></td>
                                </tr>
                                <tr class="detail-row d-none" data-parent-id="{{ \Illuminate\Support\Str::slug($date) }}">
                                    <td colspan="3" class="p-0">
                                        <div class="p-2 bg-light">
                                            <table class="table table-sm mb-0">
                                                <tbody>
                                                    @foreach($dayData['jobs'] as $printJob)
                                                        <tr class="{{ $printJob->partTasks->count() > 0 ? 'table-light' : '' }}">
                                                            <td></td>
                                                            <td>
                                                                <div class="px-2 py-1 {{ $printJob->partTasks->count() > 0 ? 'border-start border-4 border-success' : '' }}">
                                                                    @if($printJob->partTasks->count() > 0)
                                                                        <div>
                                                                            @php
                                                                                $groupedTasks = $printJob->partTasks->groupBy(function($task) {
                                                                                    return $task->task->name;
                                                                                });
                                                                            @endphp

                                                                            @foreach($groupedTasks as $taskName => $tasks)
                                                                                <div class="mb-1 fw-bold">
                                                                                    <small class="text-muted">[#{{ $printJob->id }}]</small>
                                                                                    <small class="text-muted">#{{ $tasks->first()->task->id }}</small>
                                                                                    {{ $taskName }}
                                                                                </div>
                                                                                <ul class="list-unstyled ms-3 mb-2">
                                                                                    @foreach($tasks as $partTask)
                                                                                        <li>
                                                                                            <b>x{{ $partTask->pivot->count_printed }}</b>
                                                                                            <small class="text-muted">#{{ $partTask->part->id }}</small>
                                                                                            {{ $partTask->part->name }}
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <small class="text-muted">[#{{ $printJob->id }}]</small> <span>{{ $printJob->filename }}</span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="text-end">{{ number_format($printJob->total_weight_used, 1) }} г</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Немає даних за вказаний період</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initToggleRows({
            toggleSelector: '[data-date-id]',
            rowSelector: 'tr.detail-row',
            cookiePrefix: 'consumption',
            idAttribute: 'dateId'
        });
    });
</script>
@endpush
