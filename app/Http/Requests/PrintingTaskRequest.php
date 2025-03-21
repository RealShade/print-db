<?php
// app/Http/Requests/PrintingTaskRequest.php

namespace App\Http\Requests;

use App\Models\PartTask;
use Illuminate\Foundation\Http\FormRequest;

class PrintingTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $printer = $this->route('printer');
        return $printer && $printer->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'part_task_id' => [
                'required',
                'exists:' . app(PartTask::class)->getTable() . ',id',
                function($attribute, $value, $fail) {
                    $partTask = PartTask::find($value);
                    if ($partTask->task->user_id !== auth()->id()) {
                        $fail(__('validation.exists', ['attribute' => __('task.title')]));
                    }
                },
            ],
            'count' => 'required|integer|min:1',
        ];
    }

    public function getData(): array
    {
        $partTask = PartTask::findOrFail($this->part_task_id);

        return [
            'task_id' => $partTask->task_id,
            'part_id' => $partTask->part_id,
            'count' => $this->count,
        ];
    }

    public function attributes(): array
    {
        return [
            'printer_id' => __('printer.title'),
            'part_task_id' => __('part.title'),
            'count' => __('printer.print_count'),
        ];
    }
}
