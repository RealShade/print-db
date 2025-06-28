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

            // Подход 2: Получаем данные о каждой печати с детализацией по заданиям
            $printJobs = DB::table('print_jobs')
                ->join('print_job_spool', 'print_job_spool.print_job_id', '=', 'print_jobs.id')
                ->select(
                    DB::raw('DATE(print_jobs.end_time) as date'),
                    'print_jobs.id as print_job_id',
                    'print_jobs.filename',
                    DB::raw('SUM(print_job_spool.weight_used) as weight_used')
                )
                ->whereNotNull('print_jobs.end_time')
                ->whereBetween(DB::raw('DATE(print_jobs.end_time)'), [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(print_jobs.end_time)'), 'print_jobs.id', 'print_jobs.filename')
                ->orderBy(DB::raw('DATE(print_jobs.end_time)'), 'desc')
                ->get();

            // Восстановим исходный режим SQL
            DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'))");

            // Получаем информацию о задачах, связанных с печатными заданиями
            $jobIds = $printJobs->pluck('print_job_id')->toArray();

            // Получаем связи печатных заданий с задачами
            $printJobTasks = DB::table('print_job_part_task')
                ->join('part_task', 'print_job_part_task.part_task_id', '=', 'part_task.id')
                ->join('tasks', 'part_task.task_id', '=', 'tasks.id')
                ->whereIn('print_job_part_task.print_job_id', $jobIds)
                ->select('print_job_part_task.print_job_id', 'tasks.name as task_name')
                ->get()
                ->keyBy('print_job_id');

            // Добавляем информацию о названии задачи (если есть) или используем имя файла
            foreach ($printJobs as $job) {
                $jobId = $job->print_job_id;
                $job->job_name = isset($printJobTasks[$jobId])
                    ? $printJobTasks[$jobId]->task_name
                    : $job->filename;
            }

            // Группируем задания по дате
            $groupedByDay = collect();
            foreach ($printJobs->groupBy('date') as $date => $jobs) {
                $groupedByDay[$date] = [
                    'jobs' => $jobs,
                    'total_day_weight' => isset($dailyTotals[$date]) ? $dailyTotals[$date]->total_day_weight : $jobs->sum('weight_used')
                ];
            }

            return view('statistics.consumption', [
                'consumptionData' => $groupedByDay,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalConsumption' => $dailyTotals->sum('total_day_weight')
            ]);

        } catch (\Exception $e) {
            // В случае ошибки восстановим режим SQL
            DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'))");
            return back()->withErrors(['error' => 'Ошибка при получении статистики: ' . $e->getMessage()]);
        }
    }
}
