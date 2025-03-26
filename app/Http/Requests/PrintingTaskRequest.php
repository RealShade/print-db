<?php
// app/Http/Requests/PrintingTaskRequest.php

namespace App\Http\Requests;

use App\Models\PartTask;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $part_task_id
 * @property mixed $count
 */
class PrintingTaskRequest extends FormRequest
{

    /* **************************************** Public **************************************** */
    public function attributes() : array
    {
        return [
            'part_task_id' => __('part.title'),
            'count'        => __('printer.print_count'),
        ];
    }

    public function authorize() : bool
    {
        $printer      = $this->route('printer');
        $printingTask = $this->route('printingTask');

        return ($printer === null || $printer->user_id === auth()->id())
            && ($printingTask === null || $printingTask->printer->user_id === auth()->id());
    }

    public function rules() : array
    {
        return [
            'part_task_id' => [
                'required',
                'exists:' . app(PartTask::class)->getTable() . ',id',
                function($attribute, $value, $fail) {
                    $partTask = PartTask::find($value);
                    if ($partTask->task->user_id !== auth()->id() || $partTask->part->user_id !== auth()->id()) {
                        $fail(__('validation.exists', ['attribute' => __('task.title')]));
                    }
                },
            ],
            'count'        => 'required|integer|min:1',
        ];
    }

    /* **************************************** Getters **************************************** */
    public function getData() : array
    {
        $partTask = PartTask::findOrFail($this->part_task_id);

        return [
            'task_id' => $partTask->task_id,
            'part_id' => $partTask->part_id,
            'count'   => $this->count,
        ];
    }

}
