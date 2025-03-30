<?php
// app/Http/Requests/Print/TaskRequest.php

namespace App\Http\Requests\Print;

use App\Enums\TaskStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $parts
 */
class TaskRequest extends FormRequest
{

    /* **************************************** Public **************************************** */
    public function attributes() : array
    {
        return [
            'external_id'           => __('task.external_id'),
            'name'                  => __('task.name'),
            'count_set_planned'     => __('task.count_set_planned'),
            'parts'                 => __('task.parts'),
            'parts.*.id'            => __('task.part'),
            'parts.*.count_per_set' => __('task.count_per_set'),
            'status'                => __('common.status'),
        ];
    }

    public function authorize() : bool
    {
        $task = $this->route('task');

        return $task === null || $task->user_id === auth()->id();
    }

    public function failedAuthorization()
    {
        throw new AuthorizationException(__('task.not_found_or_not_owned'));
    }

    public function messages() : array
    {
        return [
            'parts.*.id.exists' => __('part.not_found_or_not_owned'),
        ];
    }

    public function rules() : array
    {
        return [
            'external_id'       => 'nullable|string|max:255',
            'name'              => 'required|string|max:255',
            'count_set_planned' => 'required|integer|min:1',
            'status'            => 'required|integer|in:' . implode(',', array_column(TaskStatus::cases(), 'value')),
        ];
    }

}
