<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Models\PrintJob;
use App\Models\PrintJobSpool;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilamentConsumptionController extends Controller
{
    /**
     * Отображение статистики расхода филамента
     */
    public function index(Request $request)
    {
        // Параметры фильтрации по дате
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        if (empty($startDate)) {
            $startDate = now()->subMonth()->format('Y-m-d'); // По умолчанию - месяц назад
        }

        try {
            // Отключим ONLY_FULL_GROUP_BY на время выполнения запроса
            DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

            // Подход 1: Сначала группируем только по дате для получения общих сумм расхода филамента за день
            $dailyTotals = PrintJobSpool::select(
                    DB::raw('DATE(print_jobs.end_time) as date'),
                    DB::raw('SUM(print_job_spool.weight_used) as total_day_weight')
                )
                ->join('print_jobs', 'print_jobs.id', '=', 'print_job_spool.print_job_id')
                ->whereNotNull('print_jobs.end_time')
                ->whereBetween(DB::raw('DATE(print_jobs.end_time)'), [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(print_jobs.end_time)'))
                ->orderBy('date', 'desc')
                ->get()
                ->keyBy('date');

            // Подход 2: Получаем все задания печати с расходом филамента для указанного периода
            $printJobIds = PrintJobSpool::join('print_jobs', 'print_jobs.id', '=', 'print_job_spool.print_job_id')
                ->whereNotNull('print_jobs.end_time')
                ->whereBetween(DB::raw('DATE(print_jobs.end_time)'), [$startDate, $endDate])
                ->select('print_jobs.id')
                ->distinct()
                ->pluck('id')
                ->toArray();

            // Загружаем задания печати со всеми связями
            $printJobs = PrintJob::with(['partTasks.part', 'partTasks.task'])
                ->whereIn('id', $printJobIds)
                ->whereNotNull('end_time')
                ->orderBy('end_time', 'desc')
                ->get();

            // Группируем задания по дате завершения
            $jobsByDate = $printJobs->groupBy(function ($job) {
                return $job->end_time->format('Y-m-d');
            });

            // Для каждого задания вычисляем общий расход филамента
            foreach ($printJobs as $job) {
                $job->total_weight_used = $job->spools->sum('pivot.weight_used');
            }

            // Формируем финальный массив данных для представления
            $groupedByDay = collect();
            foreach ($jobsByDate as $date => $jobs) {
                $groupedByDay[$date] = [
                    'jobs' => $jobs,
                    'total_day_weight' => $dailyTotals[$date]->total_day_weight ?? $jobs->sum('total_weight_used')
                ];
            }

            // Восстановим исходный режим SQL
            DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'))");

            $totalConsumption = $dailyTotals->sum('total_day_weight');

            return view('statistics.consumption', [
                'consumptionData' => $groupedByDay,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalConsumption' => $totalConsumption
            ]);

        } catch (\Exception $e) {
            // В случае ошибки восстановим режим SQL
            DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'))");
            return back()->withErrors(['error' => 'Ошибка при получении статистики: ' . $e->getMessage()]);
        }
    }
}
