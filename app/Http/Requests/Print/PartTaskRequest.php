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
        return [
            'count_printed' => 'required|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'count_printed' => __('task.count_printed'),
        ];
    }
}
