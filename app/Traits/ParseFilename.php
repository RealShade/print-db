<?php

namespace App\Traits;

use App\Models\Task;

trait ParseFilename
{
    /* **************************************** Protected **************************************** */
    protected function parseFilename(?string $filename) : array
    {
        if (empty($filename)) {
            return [
                'success' => false,
                'errors'  => [
                    'filename' => [__('api.validation.filename_empty')],
                ],
            ];
        }
        $pattern = '/\((pid_(\d+)(\(x(\d+)\))?_(\d+))\)|\((tid_(\d+)(\(x(\d+)\))?)\)/';
        preg_match_all($pattern, $filename, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return [
                'success' => false,
                'errors'  => [
                    'filename' => [__('api.validation.pattern_not_found')],
                ],
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
                    'errors'  => [
                        'filename' => [__('api.validation.invalid_format')],
                    ],
                ];
            }
        }

        return $this->validateParsedData($parsedData);
    }

    /* **************************************** Private **************************************** */
    private function validateParsedData(array $parsedData) : array
    {
        $result = [
            'success' => false,
            'data'    => null,
            'errors'  => [],
        ];

        $taskData = [];
        foreach ($parsedData as $item) {
            if (!isset($item['task_id'])) {
                $result['errors']['tasks'][] = __('api.validation.task_id_missing');
                continue;
            }

            if (!isset($taskData[ $item['task_id'] ])) {
                // заповнюємо дані завдання, якщо їх ще немає
                $task = Task::where('id', $item['task_id'])
                    ->where('user_id', auth()->id())
                    ->with('parts')
                    ->first();

                if (!$task) {
                    $result['errors']['tasks'][] = __('api.validation.task_not_found', ['task_id' => $item['task_id']]);
                    continue;
                }

                $parts                 = $task->parts;
                $taskData[ $task->id ] = [
                    'id'                 => $task->id,
                    'name'               => $task->name,
                    'status'             => $task->status,
                    'external_id'        => $task->external_id,
                    'count_set_planned'  => $task->count_set_planned,
                    'count_set_printed'  => $parts->map(function($part) {
                            return (int)($part->pivot->count_printed / $part->pivot->count_per_set);
                        })->min() ?? 0,
                    'count_set_printing' => 0,
                    'count_set_future'   => 0,
                ];
                // ... також заповнюємо дані частин
                foreach ($parts as $part) {
                    if (!isset($taskData[ $task->id ]['parts'][ $part->id ])) {
                        $taskData[ $task->id ]['parts'][ $part->id ] = [
                            'id'             => $part->id,
                            'part_task_id'   => $part->pivot->id,
                            'name'           => $part->name,
                            'version'        => $part->version,
                            'is_printing'    => false, // якщо частина не вказана, то вважаємо, що це друкується вся партія
                            'count_per_set'  => $part->pivot->count_per_set,
                            'count_required' => $part->pivot->count_per_set * $task->count_set_planned,
                            'count_printed'  => $part->pivot->count_printed,
                            'count_printing' => 0,
                            'count_future'   => $part->pivot->count_printed,
                        ];
                    }
                }
                unset($part);
            }

            if (!empty($item['part_id'])) {
                // якщо частина вказана, то перевіряємо, чи вона є в даних завдання
                if (empty($taskData[ $item['task_id'] ]['parts']) || !array_key_exists($item['part_id'], $taskData[ $item['task_id'] ]['parts'])) {
                    $result['errors']['parts'][] = __('api.validation.parts_not_found', ['part_id' => $item['part_id']]);
                    continue;
                }
                // якщо частина є, то додаємо до неї дані
                $part                   = &$taskData[ $item['task_id'] ]['parts'][ $item['part_id'] ];
                $part['count_printing'] += $item['count'];
                $part['count_future']   += $item['count'];
                $part['is_printing']    = true; // если часть указана, то считаем, что она печатается
            } else {
                if (!empty($taskData[ $item['task_id'] ]['parts'])) {
                    foreach ($taskData[ $item['task_id'] ]['parts'] as &$part) {
                        // якщо частина не вказана, то вважаємо, що це друкується вся партія
                        $part['count_printing'] += $item['count'] * $part['count_per_set'];
                        $part['count_future']   += $item['count'] * $part['count_per_set'];
                        $part['is_printing']    = true;
                    }
                }
            }
            unset($part);
        }
        unset($item);

        // якщо є помилки, то повертаємо їх
        if (!empty($result['errors'])) {
            return $result;
        }

        // розраховуємо кількість надрукованих комплектів для кожної частини
        foreach ($taskData as &$item) {
            if (!empty($item['parts'])) {
                $item['count_set_printing'] = min(array_map(function($part) {
                    return (int)($part['count_printing'] / $part['count_per_set']);
                }, $item['parts']));

                $item['count_set_future'] = min(array_map(function($part) {
                    return (int)($part['count_future'] / $part['count_per_set']);
                }, $item['parts']));
            }
        }
        unset($item);

        $result['success']       = true;
        $result['data']['tasks'] = $taskData;

        return $result;
    }

}
