<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\TaskRequest;
use App\Models\Task;
use App\Models\Part;
use App\Enums\TaskStatus;
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

    public function duplicate(Task $task) : JsonResponse
    {
        // Создаем копию задачи
        $newTask = $task->replicate([
            'archived',
            'archived_at',
            'completed_at'
        ]);
        $newTask->status = TaskStatus::NEW;
        $newTask->archived = false;
        $newTask->archived_at = null;
        $newTask->completed_at = null;
        $newTask->user_id = auth()->id();
        $newTask->save();

        // Копируем связанные части с тем же количеством в комплекте, но сбрасываем count_printed
        foreach ($task->parts as $part) {
            $newTask->parts()->attach($part->id, [
                'count_per_set' => $part->pivot->count_per_set,
                'count_printed' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
