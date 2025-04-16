<?php

namespace App\Http\Controllers;

use App\Enums\PrintJobStatus;
use App\Enums\TaskStatus;
use App\Http\Requests\PrintJobFilamentSpoolRequest;
use App\Models\FilamentSpool;
use App\Models\PartTask;
use App\Models\PrintJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PrintJobFilamentSpoolController extends Controller
{

    /* **************************************** Public **************************************** */
    public function create(PrintJob $printJob)
    {
        $this->resolveFilamentSpool($printJob);

        $filamentSpool         = null;
        $filamentSpools = auth()->user()->filamentSpools()->with('filament')->get();

        return view('printers.print-job-spool-form', compact('filamentSpools', 'filamentSpool'));
    }

    public function destroy(PrintJob $printJob, FilamentSpool $filamentSpool) : JsonResponse
    {
        $filamentSpool = $this->resolveFilamentSpool($printJob, $filamentSpool);

        $filamentSpool->pivot->delete();

        return response()->json(['success' => true]);
    }

    public function edit(PrintJob $printJob, FilamentSpool $filamentSpool)
    {
        $filamentSpool = $this->resolveFilamentSpool($printJob, $filamentSpool);

        $filamentSpools = auth()->user()->filamentSpools()->with('filament')->get();

        return view('printers.print-job-spool-form', compact('filamentSpools', 'filamentSpool'));
    }

    public function store(PrintJobFilamentSpoolRequest $request, PrintJob $printJob) : JsonResponse
    {
        $this->resolveFilamentSpool($printJob);

        $printJob->spools()->attach($request->filament_spool_id, [
            'weight_used' => $request->weight_used,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(PrintJobFilamentSpoolRequest $request, PrintJob $printJob, FilamentSpool $filamentSpool) : JsonResponse
    {
        $filamentSpool = $this->resolveFilamentSpool($printJob, $filamentSpool);

        $filamentSpool->pivot->update([
            'weight_used' => $request->weight_used,
        ]);

        return response()->json(['success' => true]);
    }

    /* **************************************** Protected **************************************** */
    protected function getFilamentSpools() : Collection
    {
        $table = app(PartTask::class)->getTable();

        return DB::table($table)
            ->join('tasks', 'tasks.id', '=', $table . '.task_id')
            ->join('parts', 'parts.id', '=', $table . '.part_id')
            ->where('tasks.user_id', auth()->id())
            ->where('tasks.status', '!=', TaskStatus::PRINTED)
            ->select([
                $table . '.id',
                'tasks.id as task_id',
                'tasks.name as task_name',
                'parts.id as part_id',
                'parts.name as part_name',
                $table . '.count_printed',
                DB::raw("$table.count_per_set * tasks.count_set_planned as required_count"),
            ])
            ->orderByDesc('tasks.id')
            ->orderBy('parts.name')
            ->get();
    }

    protected function resolveFilamentSpool(PrintJob $printJob, ?FilamentSpool $filamentSpool = null) : ?PartTask
    {
        // Проверка владельца принтера
        abort_if($printJob->printer->user_id !== auth()->id(), 403);
        abort_if($printJob->status !== PrintJobStatus::PRINTING, 403);

        if (!$filamentSpool) {
            return null;
        }

        // Установление связи через связующую таблицу
        $filamentSpool = $printJob->spools()
            ->wherePivot('filament_spool_id', $filamentSpool->id)
            ->first();

        // Проверка существования связи
        abort_if(!$filamentSpool, 404, 'Filament spool not attached to PrintJob');

        return $filamentSpool;
    }

}
