<?php
// app/Http/Controllers/Print/TaskController.php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\TaskRequest;
use App\Models\Task;
use App\Models\Part;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    /* **************************************** Public **************************************** */
    public function archive(Task $task) : JsonResponse
    {
        if ($task->archived) {
            $task->update([
                'archived'    => false,
                'archived_at' => null,
            ]);
        } else {
            $task->update([
                'archived'    => true,
                'archived_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function create() : View
    {
        $task  = null;
        $parts = Part::where('user_id', auth()->id())->get();

        return view('print.tasks.form', compact('parts', 'task'));
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect(route('print.tasks.index'));
    }

    public function edit(Task $task) : View
    {
        return view('print.tasks.form', compact('task'));
    }

    public function index() : View
    {
        $query = Task::where('user_id', auth()->id());

        if (!request()->has('archived') || !request()->archived) {
            $query->where('archived', false);
        }

        $tasks = $query->orderBy('id', 'desc')->paginate();

        return view('print.tasks.index', compact('tasks'));
    }

    public function store(TaskRequest $request) : JsonResponse
    {
        $task          = new Task($request->validated());
        $task->user_id = auth()->id();
        $task->save();

        return response()->json(['success' => true]);
    }

    public function update(TaskRequest $request, Task $task) : JsonResponse
    {
        $task->update($request->validated());

        return response()->json(['success' => true]);
    }

}
