<?php
namespace App\Http\Requests\Print;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PartTask;

class AddPrintedCountRequest extends FormRequest
{
    public function authorize() : bool
    {
        $partTask = PartTask::find($this->input('part_task_id'));
        if (!$partTask || !$partTask->task) {
            return false;
        }
        return $partTask->task->user_id === auth()->id();
    }

    public function rules() : array
    {
        return [
            'printed_count' => 'required|integer',
            'part_task_id'  => 'required|integer|exists:part_task,id'
        ];
    }
}
