<?php
// app/Http/Controllers/PrintingTaskController.php

namespace App\Http\Controllers;

use App\Enums\PrintJobStatus;
use App\Enums\TaskStatus;
use App\Http\Requests\PrintJobPartTaskRequest;
use App\Models\PartTask;
use App\Models\PrintJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PrintJobPartTaskController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create(PrintJob $printJob)
    {
        abort_if($printJob->printer->user_id !== auth()->id(), 403);
        abort_if($printJob->status !== PrintJobStatus::PRINTING, 403);

        $partTask         = null;
        $partsWithTasks   = $this->getPartsWithTasks();

        return view('printers.print-job-part-task-form', compact('partsWithTasks', 'partTask'));
    }

    public function destroy(PrintJob $printJob, PartTask $partTask) : JsonResponse
    {
        $partTask = $this->resolvePartTask($printJob, $partTask);

        $partTask->pivot->delete();

        return response()->json(['success' => true]);
    }

    public function edit(PrintJob $printJob, PartTask $partTask)
    {
        $partTask = $this->resolvePartTask($printJob, $partTask);

        $partsWithTasks = $this->getPartsWithTasks();

        return view('printers.print-job-part-task-form', compact('partsWithTasks', 'partTask'));
    }

    public function store(PrintJobPartTaskRequest $request, PrintJob $printJob) : JsonResponse
    {
        abort_if($printJob->printer->user_id !== auth()->id(), 403);
        abort_if($printJob->status !== PrintJobStatus::PRINTING, 403);

        $printJob->partTasks()->attach($request->part_task_id, [
            'count_printed' => $request->count,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(PrintJobPartTaskRequest $request, PrintJob $printJob, PartTask $partTask) : JsonResponse
    {
        $partTask = $this->resolvePartTask($printJob, $partTask);

        $partTask->pivot->update([
            'count_printed' => $request->count,
        ]);

        return response()->json(['success' => true]);
    }

    /* **************************************** Protected **************************************** */
    protected function getPartsWithTasks() : Collection
    {
        $table = app(PartTask::class)->getTable();

        return DB::table($table)
            ->join('tasks', 'tasks.id', '=', $table . '.task_id')
            ->join('parts', 'parts.id', '=', $table . '.part_id')
            ->where('tasks.user_id', auth()->id())
            ->where('tasks.status', '!=', TaskStatus::PRINTED)
            ->select([
                $table . '.id',
                'tasks.id as task_id',
                'tasks.name as task_name',
                'parts.id as part_id',
                'parts.name as part_name',
                $table . '.count_printed',
                DB::raw("$table.count_per_set * tasks.count_set_planned as required_count"),
            ])
            ->orderByDesc('tasks.id')
            ->orderBy('parts.name')
            ->get();
    }

    protected function resolvePartTask(PrintJob $printJob, PartTask $partTask) : PartTask
    {
        // Проверка владельца принтера
        abort_if($printJob->printer->user_id !== auth()->id(), 403);
        abort_if($printJob->status !== PrintJobStatus::PRINTING, 403);

        // Установление связи через связующую таблицу
        $partTask = $printJob->partTasks()
            ->wherePivot('part_task_id', $partTask->id)
            ->first();

        // Проверка существования связи
        abort_if(!$partTask, 404, 'PartTask not attached to PrintJob');

        return $partTask;
    }

}
