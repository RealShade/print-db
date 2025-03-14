<?php
// app/Http/Requests/Print/TaskRequest.php

namespace App\Http\Requests\Print;

use App\Enums\TaskStatus;
use App\Models\Part;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        // Проверяем владельца задачи при редактировании
        if ($this->route('task') && $this->route('task')->user_id !== auth()->id()) {
            return false;
        }

        // Проверяем владельца деталей
        if ($this->has('parts')) {
            $partIds = collect($this->parts)->pluck('id')->toArray();

            // Считаем количество деталей текущего пользователя
            $userPartsCount = Part::where('user_id', auth()->id())
                ->whereIn('id', $partIds)
                ->count();

            // Если количество не совпадает, значит есть чужие детали
            return count($partIds) === $userPartsCount;
        }

        return true;
    }

    public function rules() : array
    {
        return [
            'external_id'              => 'required|string|max:255',
            'name'                     => 'required|string|max:255',
            'sets_count'               => 'required|integer|min:1',
            'status'                   => 'required|string|in:' . implode(',', array_column(TaskStatus::cases(), 'value')),
            'parts'                    => 'required|array',
            'parts.*.id'               => 'required|exists:parts,id',
            'parts.*.quantity_per_set' => 'required|integer|min:1',
        ];
    }

    /* **************************************** Getters **************************************** */
    public function getParts() : array
    {
        return collect($this->parts)->mapWithKeys(function($part) {
            return [$part['id'] => ['quantity_per_set' => $part['quantity_per_set']]];
        })->toArray();
    }
}
