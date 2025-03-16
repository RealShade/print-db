<?php
// app/Http/Requests/Print/TaskRequest.php

namespace App\Http\Requests\Print;

use App\Enums\TaskStatus;
use App\Models\Part;
use Illuminate\Foundation\Http\FormRequest;

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
        if ($this->route('task') && $this->route('task')->user_id !== auth()->id()) {
            return false;
        }

        if ($this->has('parts')) {
            $partIds        = collect($this->parts)->pluck('id')->toArray();
            $userPartsCount = Part::where('user_id', auth()->id())
                ->whereIn('id', $partIds)
                ->count();

            return count($partIds) === $userPartsCount;
        }

        return true;
    }

    public function rules() : array
    {
        return [
            'external_id'           => 'nullable|string|max:255',
            'name'                  => 'required|string|max:255',
            'count_set_planned'     => 'required|integer|min:1',
            'status'                => 'required|string|in:' . implode(',', array_column(TaskStatus::cases(), 'value')),
            'parts'                 => 'nullable|array',
            'parts.*.id'            => 'required_with:parts|exists:parts,id',
            'parts.*.count_per_set' => 'required_with:parts|integer|min:1',
        ];
    }

    /* **************************************** Getters **************************************** */
    public function getParts() : array
    {
        if (!$this->has('parts')) {
            return [];
        }

        return collect($this->parts)->mapWithKeys(function($part) {
            return [$part['id'] => ['count_per_set' => $part['count_per_set']]];
        })->toArray();
    }

    /* **************************************** Protected **************************************** */
    protected function prepareForValidation() : void
    {
        if (!$this->input('count_set_planned')) {
            $this->merge([
                'count_set_planned' => 1,
                'status'            => $this->input('status', 'new'),
            ]);
        }
    }
}
