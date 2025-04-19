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

        $filamentSpool  = null;
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

        return view('printers.print-job-spool-form', compact('filamentSpool'));
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
    protected function resolveFilamentSpool(PrintJob $printJob, ?FilamentSpool $filamentSpool = null) : ?FilamentSpool
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
