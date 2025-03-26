<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\PartTaskRequest;
use App\Models\Task;
use App\Models\Part;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PartTaskController extends Controller
{
    public function edit(Task $task, Part $part): View
    {
        $part = $task->parts()->findOrFail($part->id);

        return view('print.tasks.parts.form', compact('task', 'part'));
    }

    public function update(PartTaskRequest $request, Task $task, Part $part): JsonResponse
    {
        $task->parts()->updateExistingPivot($part->id, $request->validated());

        return response()->json(['success' => true]);
    }
}
