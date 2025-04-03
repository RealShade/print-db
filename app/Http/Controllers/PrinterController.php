<?php

namespace App\Http\Controllers;

use App\Enums\PrinterStatus;
use App\Enums\PrintTaskEventSource;
use App\Events\PrintCompleted;
use App\Http\Requests\PrinterRequest;
use App\Models\PartTask;
use App\Models\Printer;
use App\Models\PrintingTaskLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PrinterController extends Controller
{
    /* **************************************** Public **************************************** */
    /**
     * @throws Throwable
     */
    public function complete(Printer $printer) : JsonResponse
    {
        DB::transaction(function() use ($printer) {
            // Обновление счетчиков
            foreach ($printer->printingTasks as $printingTask) {
                $partTask = PartTask::find($printingTask->part_task_id);
                if (!$partTask) {
                    continue;
                }

                $partTask->count_printed += $printingTask->count;
                $partTask->save();

                PrintingTaskLog::create([
                    'part_task_id' => $printingTask->part_task_id,
                    'printer_id'   => $printer->id,
                    'count'        => $printingTask->count,
                    'event_source' => PrintTaskEventSource::MANUAL,
                ]);

            }

            // Очищаем текущие задачи печати
            $printer->printingTasks()->delete();
        });

        return response()->json([
            'success' => true,
            'message' => __('printer.printing_tasks_completed'),
        ]);
    }

    public function create() : View
    {
        $printer = null;

        return view('printers.form', compact('printer'));
    }

    public function destroy(Printer $printer) : RedirectResponse
    {
        $printer->delete();

        return redirect()
            ->route('printers.index')
            ->with('success', __('printer.status.deleted'));
    }

    public function edit(Printer $printer) : View
    {
        return view('printers.form', compact('printer'));
    }

    public function index() : View
    {
        $printers = auth()->user()->printers()->latest()->get();

        return view('printers.index', compact('printers'));
    }

    public function store(PrinterRequest $request) : JsonResponse
    {
        auth()->user()->printers()->create([
            'name'   => $request->name,
            'status' => PrinterStatus::ACTIVE,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(PrinterRequest $request, Printer $printer) : JsonResponse
    {
        $printer->update($request->validated());

        return response()->json(['success' => true]);
    }
}
