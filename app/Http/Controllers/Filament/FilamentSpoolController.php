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
    public function create() : View
    {
        $spool     = null;
        $filaments = $this->getFilaments();
        $packaging = FilamentPackaging::where('user_id', auth()->id())->orderBy('name')->get();

        return view('filament.spools.form', compact('spool', 'filaments', 'packaging'));
    }

    public function destroy(FilamentSpool $spool)
    {
        $spool->delete();

        return redirect(route('filament.spools.index'));
    }

    public function edit(FilamentSpool $spool) : View
    {
        $filaments = $this->getFilaments();
        $packaging = FilamentPackaging::where('user_id', auth()->id())->orderBy('name')->get();

        return view('filament.spools.form', compact('spool', 'filaments', 'packaging'));
    }

    public function index() : View
    {
        $spools = FilamentSpool::where('user_id', auth()->id())
            ->with(['filament', 'packaging'])
            ->orderByDesc('date_last_used')
            ->orderByDesc('id')
            ->paginate();

        return view('filament.spools.index', compact('spools'));
    }

    public function store(FilamentSpoolRequest $request) : JsonResponse
    {
        $packaging = FilamentPackaging::find($request->filament_packaging_id);
        $spool          = new FilamentSpool($request->validated());
        $spool->user_id = auth()->id();
        $spool->weight_initial = $packaging->weight;
        $spool->save();

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
