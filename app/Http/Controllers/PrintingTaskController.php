<?php
// app/Http/Controllers/PrintingTaskController.php

namespace App\Http\Controllers;

use App\Http\Requests\PrintingTaskRequest;
use App\Models\PartTask;
use App\Models\Printer;
use App\Models\PrintingTask;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PrintingTaskController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create(Printer $printer)
    {
        abort_if($printer->user_id !== auth()->id(), 403);

        $partsWithTasks = $this->getPartsWithTasks();

        return view('printers.printing-form', compact('partsWithTasks', 'printer'));
    }

    public function destroy(PrintingTask $printingTask) : JsonResponse
    {
        abort_if($printingTask->printer->user_id !== auth()->id(), 403);

        $printingTask->delete();

        return response()->json(['success' => true]);
    }

    public function store(PrintingTaskRequest $request, Printer $printer): JsonResponse
    {
        abort_if($printer->user_id !== auth()->id(), 403);

        $printer->printingTasks()->create([
            'part_task_id' => $request->part_task_id,
            'count' => $request->count
        ]);

        return response()->json(['success' => true]);
    }
    public function edit(PrintingTask $printingTask)
    {
        abort_if($printingTask->printer->user_id !== auth()->id(), 403);

        $partsWithTasks = $this->getPartsWithTasks();

        return view('printers.printing-form', compact('printingTask', 'partsWithTasks'));
    }

    public function update(PrintingTaskRequest $request, PrintingTask $printingTask): JsonResponse
    {
        abort_if($printingTask->printer->user_id !== auth()->id(), 403);

        $printingTask->update($request->validated());

        return response()->json(['success' => true]);
    }

    /* **************************************** Getters **************************************** */
    public function getParts(Task $task) : JsonResponse
    {
        return response()->json([
            'parts' => $task->parts->map(fn($part) => [
                'id'      => $part->id,
                'name'    => $part->name,
                'version' => $part->version,
            ]),
        ]);
    }

    protected function getPartsWithTasks() : Collection
    {
        $table = app(PartTask::class)->getTable();

        return DB::table($table)
            ->join('tasks', 'tasks.id', '=', $table . '.task_id')
            ->join('parts', 'parts.id', '=', $table . '.part_id')
            ->where('tasks.user_id', auth()->id())
            ->select([
                $table . '.id',
                'tasks.id as task_id',
                'tasks.name as task_name',
                'parts.id as part_id',
                'parts.name as part_name',
                $table . '.count_printed',
                DB::raw("$table.count_per_set * tasks.count_set_planned as required_count"),
            ])
            ->orderBy('parts.name')
            ->orderByDesc('tasks.id')
            ->get();
    }
}
