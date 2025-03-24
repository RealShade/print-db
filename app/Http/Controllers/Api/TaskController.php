<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Print\AfterPrintRequest;
use App\Http\Requests\Api\Print\BeforePrintRequest;
use App\Models\PartTask;
use App\Models\PrintingTask;
use App\Models\Task;
use App\Traits\ParsesFilenameTemplate;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    use ParsesFilenameTemplate;

    /* **************************************** Public **************************************** */
    public function afterPrint(AfterPrintRequest $request) : JsonResponse
    {
        $validationResult = $this->parseFilename($request->filename, $request->user()->id);

        if (!$validationResult['success']) {
            return response()->json([
                'success' => false,
                'errors'  => $validationResult['errors'],
            ], 422);
        }

        $printer = $request->getPrinter();

        // Обновление значений count_printed в PartTask
        foreach ($validationResult['data'] as $taskData) {
            foreach ($taskData['parts'] ?? [] as $partData) {
                if ($partData['count_printing']) {
                    $partTask = PartTask::find($partData['part_task_id']);
                    if ($partTask) {
                        $partTask->count_printed = $partData['count_future'];
                        $partTask->save();
                    }
                }
            }
        }

        $printer->printingTasks()->delete();

        return response()->json($validationResult);
    }

    public function beforePrint(BeforePrintRequest $request) : JsonResponse
    {
        $validationResult = $this->parseFilename($request->filename, $request->user()->id);

        if (!$validationResult['success']) {
            return response()->json([
                'success' => false,
                'errors'  => $validationResult['errors'],
            ], 422);
        }

        $printer = $request->getPrinter();

        foreach ($validationResult['data'] as $taskData) {
            foreach ($taskData['parts'] ?? [] as $partData) {
                if ($partData['count_printing']) {
                    PrintingTask::create([
                        'part_task_id' => $partData['part_task_id'],
                        'printer_id'   => $printer->id,
                        'count'        => $partData['count_printing'],
                    ]);

                    $partTask = PartTask::find($partData['part_task_id']);
                    if ($partTask) {
                        $task = $partTask->task;
                        if ($task->status === TaskStatus::NEW) {
                            $task->update(['status' => TaskStatus::IN_PROGRESS]);
                        }
                    }
                }
            }
        }

        $validationResult['data']['printer'] = [
            'id'   => $printer->id,
            'name' => $printer->name,
        ];

        return response()->json($validationResult);
    }

    public function index() : JsonResponse
    {
        $tasks = Task::query()
            ->whereIn('status', [
                TaskStatus::NEW->value,
                TaskStatus::IN_PROGRESS->value,
                TaskStatus::PRINTED->value,
            ])
            ->with(['parts' => function($query) {
                $query->select('parts.id', 'name', 'version', 'count_per_set');
            }])
            ->latest()
            ->get()
            ->map(function($task) {
                return [
                    'id'                => $task->id,
                    'name'              => $task->name,
                    'status'            => $task->status->value,
                    'external_id'       => $task->external_id,
                    'count_set_planned' => $task->count_set_planned,
                    'parts'             => $task->parts->map(function($part) {
                        return [
                            'id'            => $part->id,
                            'name'          => $part->name,
                            'version'       => $part->version,
                            'count_per_set' => $part->pivot->count_per_set,
                        ];
                    }),
                ];
            });

        return response()->json($tasks);
    }

    public function show(Task $task) : JsonResponse
    {
        $task->load(['parts' => function($query) {
            $query->select('parts.id', 'name', 'version', 'count_per_set');
        }]);

        return response()->json([
            'id'                => $task->id,
            'name'              => $task->name,
            'status'            => $task->status->value,
            'external_id'       => $task->external_id,
            'count_set_planned' => $task->count_set_planned,
            'parts'             => $task->parts->map(function($part) {
                return [
                    'id'            => $part->id,
                    'name'          => $part->name,
                    'version'       => $part->version,
                    'count_per_set' => $part->pivot->count_per_set,
                ];
            }),
        ]);
    }
}
