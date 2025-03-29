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
    public function create() : View
    {
        $task  = null;
        $parts = Part::where('user_id', auth()->id())->get();

        return view('print.tasks.form', compact('parts', 'task'));
    }

    public function edit(Task $task) : View
    {
        $parts = Part::where('user_id', auth()->id())->get();

        return view('print.tasks.form', compact('task', 'parts'));
    }

    public function index() : View
    {
        $tasks = Task::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate();
        $parts = Part::all();

        return view('print.tasks.index', compact('tasks', 'parts'));
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

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect(route('print.tasks.index'));
    }

}
