<?php

namespace App\Traits;

use App\Models\Part;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

trait ParsesFilenameTemplate
{
    /* **************************************** Protected **************************************** */
    protected function calculatePrintForecast(Task $task, array $requestData) : array
    {
        $totalSetsToPrint = !empty($requestData['parts'])
            ? 0  // Если указаны части, общее количество комплектов не важно
            : $requestData['task_sets'];

        $partsData = $task->parts->map(function($part) use ($task, $requestData, $totalSetsToPrint) {
            $currentPrinted = $part->pivot->count_printed;
            $requiredTotal  = $part->pivot->count_per_set * $task->count_set_planned;
            $countWaiting   = 0;

            // Сколько будет напечатано после текущей операции
            $willBePrinted = $currentPrinted;

            if (!empty($requestData['parts'])) {
                if (isset($requestData['parts'][ $part->id ])) {
                    $countWaiting  = $requestData['parts'][ $part->id ];
                    $willBePrinted += $countWaiting;
                }
            } else {
                $countWaiting  = $part->pivot->count_per_set * $totalSetsToPrint;
                $willBePrinted += $countWaiting;
            }

            return [
                'id'             => $part->id,
                'name'           => $part->name,
                'version'        => $part->version,
                'count_per_set'  => $part->pivot->count_per_set,
                'count_printed'  => $currentPrinted,
                'count_waiting'  => $countWaiting,
                'count_required' => $requiredTotal,
                'forecast'       => "{$currentPrinted}/{$requiredTotal} -> {$willBePrinted}/{$requiredTotal}",
            ];
        });

        // Расчет текущих и будущих полных комплектов
        $currentSets = $task->parts->map(function($part) {
            return (int)($part->pivot->count_printed / $part->pivot->count_per_set);
        })->min();

        $futureSets = $task->parts->map(function($part) use ($requestData, $totalSetsToPrint) {
            $futureCount = $part->pivot->count_printed;
            if (!empty($requestData['parts'])) {
                $futureCount += $requestData['parts'][ $part->id ] ?? 0;
            } else {
                $futureCount += $part->pivot->count_per_set * $totalSetsToPrint;
            }

            return (int)($futureCount / $part->pivot->count_per_set);
        })->min();

        $setsWaiting = max(0, $futureSets - $currentSets);

        return [
            'task'  => [
                'id'                => $task->id,
                'name'              => $task->name,
                'status'            => $task->status,
                'external_id'       => $task->external_id,
                'count_set_planned' => $task->count_set_planned,
                'count_waiting'     => $setsWaiting,
                'forecast'          => "{$currentSets}/{$task->count_set_planned} -> {$futureSets}/{$task->count_set_planned}",
            ],
            'parts' => $partsData,
        ];
    }

    protected function parseFilename(string $filename) : array
    {
        $result = [
            'task_id'   => null,
            'task_sets' => 1,
            'parts'     => [],
        ];

        // Ищем все плашки в имени файла
        preg_match_all('/\[(tid_(\d+)(?:\(x(\d+)\))?|pid_(\d+)(?:\(x(\d+)\))?)\]/', $filename, $matches);

        foreach ($matches[0] as $index => $placeholder) {
            // Если это плашка задачи
            if (str_starts_with($placeholder, '[tid_')) {
                $result['task_id'] = (int)$matches[2][ $index ];
                if (!empty($matches[3][ $index ])) {
                    $result['task_sets'] = (int)$matches[3][ $index ];
                }
            } // Если это плашка детали
            elseif (str_starts_with($placeholder, '[pid_')) {
                $partId                     = (int)$matches[4][ $index ];
                $count                      = !empty($matches[5][ $index ]) ? (int)$matches[5][ $index ] : 1;
                $result['parts'][ $partId ] = $count;
            }
        }

        return $result;
    }

    protected function validateParsedData(array $data, int $userId) : array
    {
        $result = [
            'success' => false,
            'data'    => null,
            'errors'  => [],
        ];

        if (!$data['task_id']) {
            $result['errors'][] = __('api.validation.task_id_missing');

            return $result;
        }

        $task = Task::where('id', $data['task_id'])
            ->where('user_id', $userId)
            ->with('parts')
            ->first();

        if (!$task) {
            $result['errors'][] = __('api.validation.task_not_found');

            return $result;
        }

        if (!empty($data['parts'])) {
            $requestedParts = collect($data['parts']);
            $foundParts     = $task->parts->whereIn('id', $requestedParts->keys());

            if ($foundParts->isEmpty()) {
                $result['errors'][] = __('api.validation.parts_not_found');

                return $result;
            }
        }

        $result['success'] = true;
        $result['data']    = $this->calculatePrintForecast($task, $data);

        return $result;
    }


}
