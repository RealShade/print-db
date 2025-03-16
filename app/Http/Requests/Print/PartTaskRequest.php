<?php

namespace App\Http\Requests\Print;

use Illuminate\Foundation\Http\FormRequest;

class PartTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('task')->user_id === auth()->id();
    }

    public function rules(): array
    {
        $task = $this->route('task');

        return [
            'count_per_set' => 'required|integer|min:1',
            'count_printed' => 'required|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'count_per_set' => __('task.count_per_set'),
            'count_printed' => __('task.count_printed'),
        ];
    }
}
