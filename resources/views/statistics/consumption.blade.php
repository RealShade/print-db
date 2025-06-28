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
                                <tr class="table-group-divider">
                                    <td class="align-middle"><strong>{{ $date }}</strong></td>
                                    <td></td>
                                    <td class="text-end align-middle"><strong>{{ number_format($dayData['total_day_weight'], 1) }} г</strong></td>
                                </tr>
                                @foreach($dayData['jobs'] as $job)
                                    <tr>
                                        <td></td>
                                        <td>
                                            #{{ $job->print_job_id }} -
                                            @if($job->job_name)
                                                {{ $job->job_name }}
                                            @else
                                                {{ $job->file_name ?? 'Без назви' }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($job->weight_used, 1) }} г</td>
                                    </tr>
                                @endforeach
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
