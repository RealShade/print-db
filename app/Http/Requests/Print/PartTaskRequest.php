<?php

namespace App\Http\Requests\Print;

use App\Models\PartTask;
use Illuminate\Foundation\Http\FormRequest;

class PartTaskRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function attributes() : array
    {
        return [
            'count_printed' => __('task.count_printed'),
            'count_per_set' => __('task.count_per_set'),
            'part_id'       => __('common.model'),
        ];
    }

    public function authorize() : bool
    {
        // Если редактируем привязанную модель, проверяем принадлежность через маршрут,
        // иначе — по выбранной модели
        if ($this->route('partTask')) {
            /** @var PartTask $partTask */
            $partTask = $this->route('partTask');

            return $partTask->task->user_id === auth()->id()
                && $partTask->part->user_id === auth()->id();
        } elseif ($this->route('task')) {
            $task = $this->route('task');

            return $task->user_id === auth()->id();
        }

        return false;
    }

    public function rules() : array
    {
        $rules = [
            'count_printed' => 'required|integer|min:0',
            'count_per_set' => 'required|integer|min:1',
        ];

        // Если новая привязка (нет модели в маршруте), добавляем правило для part_id
        if (!$this->route('partTask')) {
            $rules['part_id'] = 'required|exists:parts,id,user_id,' . auth()->id();
        }

        return $rules;
    }
}
