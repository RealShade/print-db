<?php

namespace App\Http\Controllers\Filament;

use App\Http\Controllers\Controller;
use App\Http\Requests\Filament\FilamentRequest;
use App\Models\Filament;
use App\Models\FilamentType;
use App\Models\FilamentVendor;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FilamentController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create() : View
    {
        $filament = null;
        $vendors  = FilamentVendor::where('user_id', auth()->id())->orderBy('name')->get();
        $types    = FilamentType::where('user_id', auth()->id())->orderBy('name')->get();

        return view('filament.form', compact('filament', 'vendors', 'types', 'palette'));
    }

    public function destroy(Filament $filament)
    {
        $filament->delete();

        return redirect(route('filament.index'));
    }

    public function edit(Filament $filament) : View
    {
        $vendors = FilamentVendor::where('user_id', auth()->id())->orderBy('name')->get();
        $types   = FilamentType::where('user_id', auth()->id())->orderBy('name')->get();
        $palette = Filament::getAllUniqueColors();

        return view('filament.form', compact('filament', 'vendors', 'types', 'palette'));
    }

    public function index() : View
    {
        $filaments = Filament::where('user_id', auth()->id())
            ->with(['vendor', 'type'])
            ->orderBy('name')
            ->paginate();
        $palette  = Filament::getAllUniqueColors();

        return view('filament.index', compact('filaments', 'palette'));
    }

    public function store(FilamentRequest $request) : JsonResponse
    {
        $filament          = new Filament($request->validated());
        $filament->user_id = auth()->id();
        $filament->save();

        return response()->json(['success' => true]);
    }

    public function update(FilamentRequest $request, Filament $filament) : JsonResponse
    {
        $data = $request->validated();

        // Если colors отсутствует в валидированных данных, явно добавляем пустой массив
        if (!isset($data['colors'])) {
            $data['colors'] = [];
        }

        $filament->update($data);

        return response()->json(['success' => true]);
    }
}
