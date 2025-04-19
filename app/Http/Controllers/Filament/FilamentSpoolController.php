<?php

namespace App\Http\Controllers\Filament;

use App\Http\Controllers\Controller;
use App\Http\Requests\Filament\FilamentSpoolRequest;
use App\Models\Filament;
use App\Models\FilamentPackaging;
use App\Models\FilamentSpool;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FilamentSpoolController extends Controller
{
    /* **************************************** Public **************************************** */
    public function archive(FilamentSpool $spool) : JsonResponse
    {
        if ($spool->archived) {
            $spool->update([
                'archived'    => false,
                'archived_at' => null,
            ]);
        } else {
            $spool->update([
                'archived'    => true,
                'archived_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function create() : View
    {
        $spool     = null;
        $packaging = FilamentPackaging::where('user_id', auth()->id())->orderBy('name')->get();

        return view('filament.spools.form', compact('spool', 'packaging'));
    }

    public function destroy(FilamentSpool $spool) : JsonResponse
    {
        $spool->delete();

        return response()->json(['success' => true]);
    }

    public function edit(FilamentSpool $spool) : View
    {
        $filaments = $this->getFilaments();
        $packaging = FilamentPackaging::where('user_id', auth()->id())->orderBy('name')->get();

        return view('filament.spools.form', compact('spool', 'filaments', 'packaging'));
    }

    public function index() : View
    {
        $query = FilamentSpool::where('user_id', auth()->id())
            ->with(['filament', 'packaging']);
        if (!request()->has('archived') || !request()->archived) {
            $query->where('archived', false);
        }
        $spools = $query
            ->orderByDesc('date_last_used')
            ->orderBy('id')
            ->paginate();

        return view('filament.spools.index', compact('spools'));
    }

    public function store(FilamentSpoolRequest $request) : JsonResponse
    {
        $validatedData = $request->validated();
        $packaging     = FilamentPackaging::find($request->filament_packaging_id);
        $quantity      = $validatedData['quantity'] ?? 1;

        // Удаляем quantity из данных, чтобы не передавать его в модель
        unset($validatedData['quantity']);

        // Создаем указанное количество катушек
        for ($i = 0; $i < $quantity; $i++) {
            $spool          = new FilamentSpool($validatedData);
            $spool->user_id = auth()->id();
            $spool->cost    = $validatedData['cost'] ?? null;
            $spool->save();
        }

        return response()->json(['success' => true]);
    }

    public function update(FilamentSpoolRequest $request, FilamentSpool $spool) : JsonResponse
    {
        $spool->update($request->validated());

        return response()->json(['success' => true]);
    }

    /* **************************************** Protected **************************************** */
    protected function getFilaments() : Collection
    {
        return Filament::where('filaments.user_id', auth()->id())
            ->join('filament_vendors', 'filaments.filament_vendor_id', '=', 'filament_vendors.id')
            ->join('filament_types', 'filaments.filament_type_id', '=', 'filament_types.id')
            ->select('filaments.*')
            ->with(['vendor', 'type'])
            ->orderBy('filament_vendors.name')
            ->orderBy('filament_types.name')
            //            ->orderBy(function($query) {
            //                // Извлекаем первый цвет из массива для сортировки
            //                $query->selectRaw("JSON_EXTRACT(colors, '$[0]')");
            //            })
            ->get();
    }
}
