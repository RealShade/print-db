<?php
// app/Http/Requests/PrintingTaskRequest.php

namespace App\Http\Requests;

use App\Models\PartTask;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $part_task_id
 * @property mixed $count
 */
class PrintJobPartTaskRequest extends FormRequest
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
        $printJob = $this->route('printJob');
        if ($printJob === null) {
            return false;
        }

        $partTask = $this->route('partTask');
        return $printJob->printer->user_id === auth()->id()
            && ($partTask === null || $printJob->partTasks->contains($partTask));
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

}
