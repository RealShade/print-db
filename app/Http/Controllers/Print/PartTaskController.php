<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\PartTaskRequest;
use App\Http\Requests\Print\PartTaskAddPrintedRequest;
use App\Models\Task;
use App\Models\Part;
use App\Models\PartTask;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PartTaskController extends Controller
{

    /* **************************************** Public **************************************** */
    public function addPrinted(PartTaskAddPrintedRequest $request, PartTask $partTask)
    {
        $this->authorizePartTask($partTask);

        $addedCount = $request->validated('printed_count');
        $partTask->update([
            'count_printed' => $partTask->count_printed + $addedCount,
        ]);

        return response()->json([
            'success'   => true,
            'new_count' => $partTask->count_printed,
        ]);
    }

    public function create(Task $task) : View
    {
        $part  = null;
        $parts = Part::where('user_id', auth()->id())->orderByDesc('id')->get();

        return view('print.tasks.parts.form', compact('task', 'part', 'parts'));
    }

    public function destroy(PartTask $partTask) : JsonResponse
    {
        $this->authorizePartTask($partTask);

        $partTask->delete();

        return response()->json(['success' => true]);
    }

    public function edit(PartTask $partTask) : View
    {
        $this->authorizePartTask($partTask);

        $task = $partTask->task;
        $part = $partTask->part;

        return view('print.tasks.parts.form', compact('partTask', 'part', 'task'));
    }

    public function store(PartTaskRequest $request, Task $task) : JsonResponse
    {
        $partTask          = new PartTask($request->validated());
        $partTask->task_id = $task->id;
        $partTask->save();

        return response()->json(['success' => true]);
    }

    public function update(PartTaskRequest $request, PartTask $partTask) : JsonResponse
    {
        $partTask->update($request->validated());

        return response()->json(['success' => true]);
    }

    /* **************************************** Protected **************************************** */
    protected function authorizePartTask(PartTask $partTask) : void
    {
        abort_if($partTask->task->user_id !== auth()->id() || $partTask->part->user_id !== auth()->id(), 403);
    }

}

