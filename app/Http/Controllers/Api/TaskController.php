<?php

namespace App\Http\Controllers\Api;

use App\Enums\PrintJobStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Print\AfterPrintRequest;
use App\Http\Requests\Api\Print\BeforePrintRequest;
use App\Http\Requests\Api\Print\StopPrintRequest;
use App\Models\FilamentSpool;
use App\Models\PrinterFilamentSlot;
use App\Models\PartTask;
use App\Models\PrintJob;
use App\Models\Task;
use App\Services\PrinterService;
use App\Traits\ParseFilament;
use App\Traits\ParseFilename;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
        $result = [
            'success' => true,
        ];

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

        DB::transaction(function() use (&$result, $request, $printer) {
            $printJob = PrinterService::getActivePrintJob($printer, $request->filename, true);

            $dataTasks = $this->parseFilename($request->filename);
            if ($dataTasks['success']) {
                $dataTasks['data']['new'] = $dataTasks['data']['old'];
                $syncData                 = [];
                // Обновление значений count_printed в PartTask
                foreach ($dataTasks['data']['old']['tasks'] ?? [] as $taskID => $taskData) {
                    $task = Task::find($taskID);
                    foreach ($taskData['parts'] ?? [] as $partID => $partData) {
                        if (!$partData['count_printing'] || !$partData['is_printing']) {
                            continue;
                        }

                        $partTask = PartTask::find($partData['part_task_id']);
                        if (!$partTask || $partTask->task_id !== $taskID) {
                            continue;
                        }

                        $syncData[ $partTask->id ] = [
                            'count_printed' => $partData['count_printing'],
                        ];
                    }
                }
                $printJob->partTasks()->sync($syncData);
            }

            $dataSlots = $this->parseFilament($request->slots ?? [], $printer);
            if (isset($dataSlots['data']['input'])) {
                $syncData = [];
                foreach ($dataSlots['data']['input'] as $slotName => $usedWeight) {
                    /** @var PrinterFilamentSlot $slot */
                    $slot = $printer->filamentSlots()->where('attribute', $slotName)->first();
                    if (!$slot) {
                        continue;
                    }
                    if ($slot->filamentSpool) {
                        $filamentSpool = $slot->filamentSpool;

                        $dataSlots['data']['old'][ $slotName ] = $this->getFilamentSpoolAsArray($filamentSpool);

                        $syncData[ $filamentSpool->id ] = [
                            'weight_used' => $usedWeight,
                        ];
                    } else {
                        $dataSlots['data']['old'][ $slotName ] = $this->getFilamentSpoolAsArray(null);
                    }
                }
                $printJob->spools()->sync($syncData);
            }
            PrinterService::completePrintJob($printJob);
            foreach ($dataSlots['data']['old'] ?? [] as $slotName => $spool) {
                if ($spool['filament_spool_id'] && $filamentSpool = FilamentSpool::find($spool['filament_spool_id'])) {
                    $dataSlots['data']['new'][ $slotName ] = $this->getFilamentSpoolAsArray($filamentSpool);
                }
            }
            foreach ($dataTasks['data']['old']['tasks'] ?? [] as $taskID => $taskData) {
                $task = Task::find($taskID);
                foreach ($taskData['parts'] ?? [] as $partID => $partData) {
                    if (!$partData['count_printing'] || !$partData['is_printing']) {
                        unset($dataTasks['data']['new']['tasks'][ $taskID ]['parts'][ $partID ]);
                    }

                    $partTask = PartTask::find($partData['part_task_id']);
                    if (!$partTask || $partTask->task_id !== $taskID) {
                        unset($dataTasks['data']['new']['tasks'][ $taskID ]['parts'][ $partID ]);
                    }

                    $dataTasks['data']['new']['tasks'][ $taskID ]['parts'][ $partID ]['count_printed']  = $partTask->count_printed;
                    $dataTasks['data']['new']['tasks'][ $taskID ]['parts'][ $partID ]['count_printing'] = 0;
                    unset($dataTasks['data']['new']['tasks'][ $taskID ]['parts'][ $partID ]['is_printing']);
                }

                $dataTasks['data']['new']['tasks'][ $taskID ]['count_set_printing'] = 0;
                $dataTasks['data']['new']['tasks'][ $taskID ]['count_set_printed']  = $task->count_set_printed;
            }
            $result['print_job'] = [
                'printer_id' => $printJob->printer_id,
                'status'     => $printJob->status,
                'filename'   => $printJob->filename,
                'id'         => $printJob->id,
            ];
            $result['tasks']     = $dataTasks;
            $result['slots']     = $dataSlots;
        });

        return response()->json($result);
    }

    /**
     * @throws Throwable
     */
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

        $resultPrintJob = PrinterService::createPrintJob(
            $printer,
            $request->filename,
            $request->slots
        );

        $result['success']   = $resultPrintJob['success'];
        $result['print_job'] = $resultPrintJob['printJob'] ?? null;
        $result['tasks']     = $resultPrintJob['tasks'] ?? null;
        $result['slots']     = $resultPrintJob['slots'] ?? null;

        return response()->json($result);
    }

    public function index()
    {
        $profile  = auth()->user();
        $tasks    = auth()->user()->tasks()
            ->whereIn('status', [TaskStatus::NEW, TaskStatus::IN_PROGRESS, TaskStatus::PRINTED])
            ->with([
                'partTask',
                'partTask.task',
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($task) {
                // Сохраняем оригинальный статус
                $statusEnum = $task->status;
                // Создаем массив с нужным порядком полей
                $taskArray = $task->toArray();
                // Вставляем status_name сразу после status
                $result = [];
                foreach ($taskArray as $key => $value) {
                    $result[ $key ] = $value;
                    if ($key === 'status') {
                        $result['status'] = $statusEnum->name;
                    }
                }

                return $result;
            });
        $printers = auth()->user()->printers()
            ->with([
                'filamentSlots',
                'filamentSlots.filamentSpool',
                'filamentSlots.filamentSpool.packaging',
                'filamentSlots.filamentSpool.filament',
                'filamentSlots.filamentSpool.filament.vendor',
                'filamentSlots.filamentSpool.filament.type',
            ])
            ->orderBy('name')
            ->get()
            ->map(function($task) {
                // Сохраняем оригинальный статус
                $statusEnum = $task->status;
                // Создаем массив с нужным порядком полей
                $taskArray = $task->toArray();
                // Вставляем status_name сразу после status
                $result = [];
                foreach ($taskArray as $key => $value) {
                    $result[ $key ] = $value;
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

        $printJobs = [];
        foreach ($printer->activeJobs as $printJob) {
            $printJobs[ $printJob->id ] = [
                'printer_id' => $printJob->printer_id,
                'filename'   => $printJob->filename,
                'status'     => PrintJobStatus::CANCELLED,
                'id'         => $printJob->id,
            ];
        }

        PrinterService::stopAllPrintJobs($printer);

        return response()->json([
            'success'    => true,
            'printer'    => [
                'id'   => $printer->id,
                'name' => $printer->name,
            ],
            'print_jobs' => $printJobs,
            'message'    => __('printer.printing_tasks_purged'),
        ]);
    }

    /* **************************************** Protected **************************************** */
    protected function getFilamentSpoolAsArray(?FilamentSpool $filamentSpool) : array
    {
        return [
            'filament_spool_id' => $filamentSpool?->id ?? null,
            'filament_spool'    => $filamentSpool ? sprintf('#%d %s %s (%s)', $filamentSpool->id, $filamentSpool->filament->name, $filamentSpool->filament->type->name, $filamentSpool->filament->vendor->name) : __('printer.filament_slot.empty'),
            'weight_initial'    => $filamentSpool?->weight_initial ?? null,
            'weight_used'       => $filamentSpool?->weight_used ?? null,
            'weight_remaining'  => $filamentSpool?->weight_remaining ?? null,
            'date_last_used'    => $filamentSpool?->date_last_used?->format('Y-m-d H:i:s') ?? null,
        ];
    }

}
