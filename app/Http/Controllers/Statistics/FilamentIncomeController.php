<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Models\FilamentSpool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilamentIncomeController extends Controller
{
    /**
     * Отображение статистики прихода филамента
     */
    public function index(Request $request)
    {
        // Параметры фильтрации по дате
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        if (empty($startDate)) {
            $startDate = now()->subMonth()->format('Y-m-d'); // По умолчанию - месяц назад
        }

        // Получаем данные о приходе филамента за указанный период
        $spools = FilamentSpool::with(['filament.vendor', 'packaging'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Добавляем дату (только день) для группировки
        $spools->each(function ($spool) {
            $spool->date = $spool->created_at->format('Y-m-d');
        });

        // Группируем катушки по дате
        $groupedByDay = $spools->groupBy('date')->map(function ($items) {
            return [
                'spools' => $items,
                'total_day_weight' => $items->sum('weight_initial'),
                'total_day_cost' => $items->sum('cost')
            ];
        });

        return view('statistics.income', [
            'incomeData' => $groupedByDay,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalWeight' => $spools->sum('weight_initial'),
            'totalCost' => $spools->sum('cost')
        ]);
    }
}
