<?php

namespace App\Http\Requests\Print;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PartTask;

class PartTaskAddPrintedRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        $partTask = $this->route('partTask');
        if (!$partTask || !$partTask->task) {
            return false;
        }

        return $partTask->task->user_id === auth()->id();
    }

    public function rules() : array
    {
        return [
            'printed_count' => 'required|integer',
        ];
    }
}
