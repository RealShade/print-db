@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <h1>{{ __('menu.statistics.filament_income') }}</h1>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Фільтр</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('statistics.income.index') }}" class="row align-items-end">
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
                        <a href="{{ route('statistics.income.index') }}" class="btn btn-outline-secondary">Скинути</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Надходження філаменту за період: {{ $startDate }} - {{ $endDate }}</h5>
                <div>
                    <span class="badge bg-primary fs-6 me-2">Загальна вага: {{ number_format($totalWeight, 1) }} г</span>
                    <span class="badge bg-success fs-6">Загальна вартість: {{ number_format($totalCost, 2) }} грн</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Філамент</th>
                                <th class="text-end">Вага (г)</th>
                                <th class="text-end">Вартість (грн)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($incomeData as $date => $dayData)
                                <tr data-income-id="{{ \Illuminate\Support\Str::slug($date) }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-chevron-right me-2 toggle-icon"></i>
                                            <strong>{{ $date }}</strong>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td class="text-end"><strong>{{ number_format($dayData['total_day_weight'], 1) }} г</strong></td>
                                    <td class="text-end"><strong>{{ number_format($dayData['total_day_cost'], 2) }} грн</strong></td>
                                </tr>
                                <tr class="detail-row d-none" data-parent-id="{{ \Illuminate\Support\Str::slug($date) }}">
                                    <td colspan="4" class="p-0">
                                        <div class="p-2 bg-light">
                                            <table class="table table-sm mb-0">
                                                <tbody>
                                                    @foreach($dayData['spools'] as $spool)
                                                        <tr>
                                                            <td></td>
                                                            <td>
                                                                @foreach($spool->filament->colors as $color)
                                                                    <span class="filament-color-preview" style="background-color: {{ $color }}; width: 20px; height: 20px; display: inline-block;"></span>
                                                                @endforeach
                                                                {{ $spool->filament->vendor->name }}, {{ $spool->filament->type->name }}, {{ $spool->packaging->name }}
                                                            </td>
                                                            <td class="text-end">{{ number_format($spool->weight_initial, 1) }}</td>
                                                            <td class="text-end">{{ number_format($spool->cost, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Немає даних за вказаний період</td>
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
            toggleSelector: '[data-income-id]',
            rowSelector: 'tr.detail-row',
            cookiePrefix: 'income',
            idAttribute: 'incomeId'
        });
    });
</script>
@endpush
