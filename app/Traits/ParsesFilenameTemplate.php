<?php

namespace App\Traits;

use App\Models\Task;

trait ParsesFilenameTemplate
{
    /* **************************************** Protected **************************************** */
    protected function parseFilename(string $filename, int $userID) : array
    {
        $pattern = '/\[(pid_(\d+)(\(x(\d+)\))?@(\d+))]|\[(tid_(\d+)(\(x(\d+)\))?)]/';
        preg_match_all($pattern, $filename, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return [
                'success' => false,
                'errors'  => [__('api.validation.pattern_not_found')],
            ];
        }

        $parsedData = [];
        foreach ($matches as $match) {
            if ($match[2] && $match[5]) {
                $parsedData[] = [
                    'part_id' => (int)$match[2],
                    'task_id' => (int)$match[5],
                    'count'   => isset($match[4]) ? (int)$match[4] : 1,
                ];
            } elseif ($match[7]) {
                $parsedData[] = [
                    'task_id' => (int)$match[7],
                    'count'   => isset($match[9]) ? (int)$match[9] : 1,
                ];
            } else {
                return [
                    'success' => false,
                    'errors'  => [__('api.validation.invalid_format')],
                ];
            }
        }

        return $this->validateParsedData($parsedData, $userID);
    }

    /* **************************************** Private **************************************** */
    private function validateParsedData(array $parsedData, int $userId) : array
    {
        $result = [
            'success' => false,
            'data'    => null,
            'errors'  => [],
        ];

        $dataResult = [];
        foreach ($parsedData as $item) {
            if (!isset($item['task_id'])) {
                $result['errors'][] = __('api.validation.task_id_missing');
                continue;
            }

            if (!isset($dataResult[ $item['task_id'] ])) {
                // заповнюємо дані завдання, якщо їх ще немає
                $task = Task::where('id', $item['task_id'])
                    ->where('user_id', $userId)
                    ->with('parts')
                    ->first();

                if (!$task) {
                    $result['errors'][] = __('api.validation.task_not_found', ['task_id' => $item['task_id']]);
                    continue;
                }

                $parts                   = $task->parts;
                $dataResult[ $task->id ] = [
                    'id'                 => $task->id,
                    'name'               => $task->name,
                    'status'             => $task->status,
                    'external_id'        => $task->external_id,
                    'count_set_planned'  => $task->count_set_planned,
                    'count_set_printed'  => $parts->map(function($part) {
                        return (int)($part->pivot->count_printed / $part->pivot->count_per_set);
                    })->min(),
                    'count_set_printing' => 0,
                    'count_set_future'   => 0,
                ];
                // ... також заповнюємо дані частин
                foreach ($parts as $part) {
                    if (!isset($dataResult[ $task->id ]['parts'][ $part->id ])) {
                        $dataResult[ $task->id ]['parts'][ $part->id ] = [
                            'id'             => $part->id,
                            'part_task_id'   => $part->pivot->id,
                            'name'           => $part->name,
                            'version'        => $part->version,
                            'is_printing'    => false,
                            'count_per_set'  => $part->pivot->count_per_set,
                            'count_required' => $part->pivot->count_per_set * $task->count_set_planned,
                            'count_printed'  => $part->pivot->count_printed,
                            'count_printing' => empty($item['part_id']) ? $item['count'] * $part->pivot->count_per_set : 0, // якщо частина не вказана, то вважаємо, що це друкується вся партія
                            'count_future'   => $part->pivot->count_printed + $item['count'] * $part->pivot->count_per_set,
                        ];
                    }
                }
            }

            if (!empty($item['part_id'])) {
                // якщо частина вказана, то перевіряємо, чи вона є в даних завдання
                if (!array_key_exists($item['part_id'], $dataResult[ $item['task_id'] ]['parts'])) {
                    $result['errors'][] = __('api.validation.parts_not_found', ['part_id' => $item['part_id']]);
                }
                // якщо частина є, то додаємо до неї дані
                $dataResult[ $item['task_id'] ]['parts'][ $item['part_id'] ]['count_printing'] += $item['count'];
                $dataResult[ $item['task_id'] ]['parts'][ $item['part_id'] ]['count_future']   += $item['count'];
            }
        }
        unset($item);

        // якщо є помилки, то повертаємо їх
        if (!empty($result['errors'])) {
            return $result;
        }

        // розраховуємо кількість надрукованих комплектів для кожної частини
        foreach ($dataResult as &$item) {
            $item['count_set_printing'] = min(array_map(function($part) {
                return (int)($part['count_printing'] / $part['count_per_set']);
            }, $item['parts']));

            $item['count_set_future'] = min(array_map(function($part) {
                return (int)($part['count_future'] / $part['count_per_set']);
            }, $item['parts']));
        }
        unset($item);

        $result['success'] = true;
        $result['data']    = $dataResult;

        return $result;
    }

}
