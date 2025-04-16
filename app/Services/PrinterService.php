<?php

namespace App\Services;

use App\Enums\PrintJobStatus;
use App\Enums\TaskStatus;
use App\Models\PartTask;
use App\Models\Printer;
use App\Models\PrintJob;
use App\Traits\ParseFilament;
use App\Traits\ParseFilename;
use Illuminate\Support\Facades\DB;
use Throwable;

class PrinterService
{

    use ParseFilename, ParseFilament;

    /* **************************************** Static **************************************** */
    /**
     * @throws Throwable
     */
    public static function completePrintJob(PrintJob $printJob) : void
    {
        DB::transaction(function() use ($printJob) {
            foreach ($printJob->partTasks as $partTask) {
                $partTask->count_printed += $partTask->pivot->count_printed;
                $partTask->save();
            }
            foreach ($printJob->spools as $spool) {
                $spool->weight_used += $spool->pivot->weight_used;
                if (!$spool->date_first_used) {
                    $spool->date_first_used = now();
                }
                $spool->date_last_used = now();
                $spool->save();
            }

            // Обновление статуса печати
            $printJob->update([
                'status'   => PrintJobStatus::COMPLETED,
                'end_time' => now(),
            ]);
        });
    }

    /**
     * @throws Throwable
     */
    public static function createPrintJob(Printer $printer, ?string $filename, ?array $slots = null) : array
    {
        $result   = ['success' => true];
        $filename = $filename ?? 'undefined';

        DB::transaction(function() use (&$result, $printer, $filename, $slots) {
            // Обновляем статус существующих задач на печати
            PrintJob::where('printer_id', $printer->id)
                ->where('status', PrintJobStatus::PRINTING)
                ->update([
                    'status'   => PrintJobStatus::UNKNOWN,
                    'end_time' => now(),
                ]);

            // Создаем новую задачу печати
            $printJob = PrintJob::create([
                'printer_id' => $printer->id,
                'status'     => PrintJobStatus::PRINTING,
                'filename'   => $filename,
            ]);

            $result['printJob'] = $printJob;

            // Парсим имя файла
            $service         = new self();
            $dataFilename    = $service->parseFilename($filename);
            $result['tasks'] = $dataFilename;

            if ($dataFilename['success']) {
                foreach ($dataFilename['data']['old']['tasks'] as $taskData) {
                    foreach ($taskData['parts'] ?? [] as $partData) {
                        if ($partData['count_printing']) {
                            $printJob->partTasks()->attach($partData['part_task_id'], [
                                'count_printed' => $partData['count_printing'],
                            ]);

                            $task = PartTask::find($partData['part_task_id'])?->task;
                            if ($task && $task->status === TaskStatus::NEW) {
                                $task->update(['status' => TaskStatus::IN_PROGRESS]);
                            }
                        }
                    }
                }
            }

            // Если переданы данные о слотах, обрабатываем их
            if ($slots) {
                $dataSlots       = $service->parseFilament($slots, $printer);
                $result['slots'] = $dataSlots;

                if (isset($dataSlots['data']['input'])) {
                    foreach ($dataSlots['data']['input'] as $slotName => $weightUsed) {
                        $slot = $printer->filamentSlots()->where('attribute', $slotName)->first();
                        if (!$slot) {
                            continue;
                        }
                        if ($slot->filamentSpool) {
                            $filamentSpool = $slot->filamentSpool;
                            $printJob->spools()->attach($filamentSpool->id, [
                                'weight_used' => $weightUsed,
                            ]);
                        }
                    }
                }
            }

        });

        return $result;
    }

    public static function getActivePrintJob(Printer $printer, ?string $filename = null, bool $isCreate = false)
    {
        $filename = $filename ?? 'undefined';
        // Проверяем, есть ли активная задача на печать с таким же именем файла
        $printJob = PrintJob::where('printer_id', $printer->id)
            ->where('status', PrintJobStatus::PRINTING)
            ->where('filename', $filename)
            ->first();

        if (!$printJob && $isCreate) {
            // Создаем новую запись о печати
            $printJob = PrintJob::create([
                'printer_id' => $printer->id,
                'status'     => PrintJobStatus::PRINTING,
                'filename'   => $filename,
            ]);
        }

        return $printJob;
    }

    public static function stopAllPrintJobs(Printer $printer) : void
    {
        // Обновляем статус всех задач на печати
        PrintJob::where('printer_id', $printer->id)
            ->where('status', PrintJobStatus::PRINTING)
            ->update([
                'status'   => PrintJobStatus::CANCELLED,
                'end_time' => now(),
            ]);
    }

}
