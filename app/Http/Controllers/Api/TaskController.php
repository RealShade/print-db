<?php

namespace App\Http\Controllers\Api;

use App\Enums\PrintTaskEventSource;
use App\Enums\TaskStatus;
use App\Events\PrintCompleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Print\AfterPrintRequest;
use App\Http\Requests\Api\Print\BeforePrintRequest;
use App\Http\Requests\Api\Print\StopPrintRequest;
use App\Models\PrinterFilamentSlot;
use App\Models\PartTask;
use App\Models\PrintingTask;
use App\Models\PrintingTaskLog;
use App\Models\Task;
use App\Traits\ParseFilament;
use App\Traits\ParseFilename;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Throwable;

class TaskController extends Controller
{
    use ParseFilename, ParseFilament;

    /* **************************************** Public **************************************** */
    /**
     * @throws Throwable
     */
    public function afterPrint(AfterPrintRequest $request) : JsonResponse
    {
        $result = [];

        $printer = $request->getPrinter();
        if (!$printer) {
            return response()->json([
                'success' => false,
                'errors'  => [
                    'printer_id' => [__('printer.validation.printer_id')],
                ],
            ], 422);
        }
        $result['printer'] = [
            'id'   => $printer->id,
            'name' => $printer->name,
        ];

        $dataTasks = $this->parseFilename($request->filename);
        if ($dataTasks['success']) {
            $dataTasks['data']['new'] = $dataTasks['data']['old'];
            DB::transaction(function() use ($request, &$dataTasks, $printer) {
                // Обновление значений count_printed в PartTask
                foreach ($dataTasks['data']['old']['tasks'] ?? [] as $taskID => $taskData) {
                    foreach ($taskData['parts'] ?? [] as $partID => $partData) {
                        if (!$partData['count_printing'] || !$partData['is_printing']) {
                            continue;
                        }

                        $partTask = PartTask::find($partData['part_task_id']);
                        if (!$partTask) {
                            continue;
                        }

                        $partTask->count_printed += $partData['count_printing'];
                        $partTask->save();
                        $dataTasks['data']['new']['tasks'][ $taskID ]['parts'][ $partID ]['count_printed']  = $partTask->count_printed;
                        $dataTasks['data']['new']['tasks'][ $taskID ]['parts'][ $partID ]['count_printing'] = 0;
                        unset($dataTasks['data']['new']['tasks'][ $taskID ]['parts'][ $partID ]['is_printing']);

                        PrintingTaskLog::create([
                            'part_task_id' => $partID,
                            'printer_id'   => $printer->id,
                            'count'        => $partData['count_printing'],
                            'event_source' => PrintTaskEventSource::API,
                        ]);
                    }
                    $task                                                               = Task::find($taskID);
                    $dataTasks['data']['new']['tasks'][ $taskID ]['count_set_printing'] = 0;
                    $dataTasks['data']['new']['tasks'][ $taskID ]['count_set_printed']  = $task->getCompletedSetsCount();
                }

                $printer->printingTasks()->delete();
            });
        }
        $result['tasks'] = $dataTasks;

        $dataSlots = $this->parseFilament($request, $printer);
        if ($dataSlots['success']) {
            DB::transaction(function() use ($printer, &$dataSlots) {
                foreach ($dataSlots['data']['input'] ?? [] as $slotName => $usedWeight) {
                    /** @var PrinterFilamentSlot $slot */
                    $slot = $printer->filamentSlots()->where('attribute', $slotName)->first();
                    if (!$slot) {
                        continue;
                    }
                    if ($slot->filamentSpool) {
                        $filamentSpool = $slot->filamentSpool;

                        $dataSlots['data']['old'][ $slotName ] = [
                            'filament_spool_id' => $filamentSpool->id,
                            'filament_spool'    => sprintf('#%d %s %s (%s)', $filamentSpool->id, $filamentSpool->filament->name, $filamentSpool->filament->type->name, $filamentSpool->filament->vendor->name),
                            'weight_initial'    => $filamentSpool->weight_initial,
                            'weight_used'       => $filamentSpool->weight_used,
                            'weight_remaining'  => $filamentSpool->weight_remaining,
                            'date_last_used'    => $filamentSpool->date_last_used?->format('Y-m-d H:i:s'),
                        ];

                        $filamentSpool->weight_used    = $filamentSpool->weight_used + $usedWeight;
                        $filamentSpool->date_last_used = now();
                        if (!$filamentSpool->date_first_used) {
                            $filamentSpool->date_first_used = now();
                        }
                        $filamentSpool->save();
                        $dataSlots['data']['new'][ $slotName ] = [
                            'filament_spool_id' => $filamentSpool->id,
                            'filament_spool'    => sprintf('#%d %s %s (%s)', $filamentSpool->id, $filamentSpool->filament->name, $filamentSpool->filament->type->name, $filamentSpool->filament->vendor->name),
                            'weight_initial'    => $filamentSpool->weight_initial,
                            'weight_used'       => $filamentSpool->weight_used,
                            'weight_remaining'  => $filamentSpool->weight_remaining,
                            'date_last_used'    => $filamentSpool->date_last_used?->format('Y-m-d H:i:s'),
                        ];
                    } else {
                        $dataSlots['data']['old'][ $slotName ] = [
                            'filament_spool_id' => null,
                            'filament_spool'    => __('printer.filament_slot.empty'),
                            'weight_initial'    => null,
                            'weight_used'       => null,
                            'weight_remaining'  => null,
                            'subtracted'        => $usedWeight,
                            'weight_future'     => null,
                            'date_last_used'    => null,
                        ];
                    }
                }
            });
        }
        $result['slots'] = $dataSlots;

        return response()->json($result);
    }

    public function beforePrint(BeforePrintRequest $request) : JsonResponse
    {
        $result  = [];
        $printer = $request->getPrinter();
        if (!$printer) {
            return response()->json([
                'success' => false,
                'errors'  => [
                    'printer_id' => [__('printer.validation.printer_id')],
                ],
            ], 422);
        }

        $result['printer'] = [
            'id'   => $printer->id,
            'name' => $printer->name,
        ];

        $dataFilename    = $this->parseFilename($request->filename);
        $result['tasks'] = $dataFilename;

        if ($dataFilename['success']) {
            foreach ($dataFilename['data']['old']['tasks'] as $taskData) {
                foreach ($taskData['parts'] ?? [] as $partData) {
                    if ($partData['count_printing']) {
                        PrintingTask::create([
                            'part_task_id' => $partData['part_task_id'],
                            'printer_id'   => $printer->id,
                            'count'        => $partData['count_printing'],
                        ]);

                        $task = PartTask::find($partData['part_task_id'])?->task;
                        if ($task && $task->status === TaskStatus::NEW) {
                            $task->update(['status' => TaskStatus::IN_PROGRESS]);
                        }
                    }
                }
            }
        }

        return response()->json($result);
    }

    public function index()
    {
        $profile = auth()->user();
        $tasks    = auth()->user()->tasks()
            ->whereIn('status', [TaskStatus::NEW, TaskStatus::IN_PROGRESS, TaskStatus::PRINTED])
            ->with([
                'partTask',
                'partTask.task',
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($task) {
                // Сохраняем оригинальный статус
                $statusEnum = $task->status;
                // Создаем массив с нужным порядком полей
                $taskArray = $task->toArray();
                // Вставляем status_name сразу после status
                $result = [];
                foreach ($taskArray as $key => $value) {
                    $result[$key] = $value;
                    if ($key === 'status') {
                        $result['status'] = $statusEnum->name;
                    }
                }
                return $result;
            });
        $printers = auth()->user()->printers()
            ->with([
                'printingTasks',
                'printingTasks.partTask',
                'printingTasks.partTask.task',
                'printingTasks.partTask.part',
                'filamentSlots',
                'filamentSlots.filamentSpool',
                'filamentSlots.filamentSpool.packaging',
                'filamentSlots.filamentSpool.filament',
                'filamentSlots.filamentSpool.filament.vendor',
                'filamentSlots.filamentSpool.filament.type',
            ])
            ->orderBy('name')
            ->get()
            ->map(function ($task) {
                // Сохраняем оригинальный статус
                $statusEnum = $task->status;
                // Создаем массив с нужным порядком полей
                $taskArray = $task->toArray();
                // Вставляем status_name сразу после status
                $result = [];
                foreach ($taskArray as $key => $value) {
                    $result[$key] = $value;
                    if ($key === 'status') {
                        $result['status'] = $statusEnum->name;
                    }
                }
                return $result;
            });

        return response()->json([
            'success' => true,
            'data'    => [
                'profile'  => $profile,
                'printers' => $printers,
                'tasks'    => $tasks,
            ],
        ]);
    }

    public function stopPrint(StopPrintRequest $request) : JsonResponse
    {
        $printer = $request->getPrinter();

        $printer->printingTasks()->delete();

        return response()->json([
            'success' => true,
            'printer' => [
                $printer->id => $printer->name,
            ],
            'message' => __('printer.printing_tasks_purged'),
        ]);
    }

}
