<?php

namespace App\Http\Controllers\Api\Print;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Print\AfterPrintRequest;
use App\Http\Requests\Api\Print\BeforePrintRequest;
use App\Models\Task;
use App\Traits\ParsesFilenameTemplate;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    use ParsesFilenameTemplate;

    public function index(): JsonResponse
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
                    'id' => $task->id,
                    'name' => $task->name,
                    'status' => $task->status->value,
                    'external_id' => $task->external_id,
                    'count_set_planned' => $task->count_set_planned,
                    'parts' => $task->parts->map(function($part) {
                        return [
                            'id' => $part->id,
                            'name' => $part->name,
                            'version' => $part->version,
                            'count_per_set' => $part->pivot->count_per_set,
                        ];
                    }),
                ];
            });

        return response()->json($tasks);
    }

    public function show(Task $task): JsonResponse
    {
        $task->load(['parts' => function($query) {
            $query->select('parts.id', 'name', 'version', 'count_per_set');
        }]);

        return response()->json([
            'id' => $task->id,
            'name' => $task->name,
            'status' => $task->status->value,
            'external_id' => $task->external_id,
            'count_set_planned' => $task->count_set_planned,
            'parts' => $task->parts->map(function($part) {
                return [
                    'id' => $part->id,
                    'name' => $part->name,
                    'version' => $part->version,
                    'count_per_set' => $part->pivot->count_per_set,
                ];
            }),
        ]);
    }

    public function beforePrint(BeforePrintRequest $request): JsonResponse
    {
        $parsedData = $this->parseFilename($request->filename);
        $validationResult = $this->validateParsedData($parsedData, $request->user()->id);

        if (!$validationResult['success']) {
            return response()->json([
                'success' => false,
                'errors' => $validationResult['errors'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $validationResult['data']
        ]);
    }

    public function afterPrint(AfterPrintRequest $request): JsonResponse
    {
        return response()->json([
            'message' => __('api.print.completed'),
            'filename' => $request->filename,
            'printer_id' => $request->printer_id,
            'print_time' => $request->print_time,
            'material_used' => $request->material_used,
        ]);
    }
}
