<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\FilamentPackagingRequest;
use App\Models\FilamentPackaging;
use App\Models\FilamentPackagingType;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FilamentPackagingTypeController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create() : View
    {
        $packagingType = null;

        return view('filament.packaging_types.form', compact('packagingType'));
    }

    public function edit(FilamentPackaging $packagingType) : View
    {
        return view('filament.packaging_types.form', compact('packagingType'));
    }

    public function index() : View
    {
        $packagingTypes = FilamentPackaging::where('user_id', auth()->id())
            ->orderBy('name')
            ->paginate();

        return view('filament.packaging_types.index', compact('packagingTypes'));
    }

    public function store(FilamentPackagingRequest $request) : JsonResponse
    {
        $packagingType          = new FilamentPackaging($request->validated());
        $packagingType->user_id = auth()->id();
        $packagingType->save();

        return response()->json(['success' => true]);
    }

    public function update(FilamentPackagingRequest $request, FilamentPackaging $packagingType) : JsonResponse
    {
        $packagingType->update($request->validated());

        return response()->json(['success' => true]);
    }

    public function destroy(FilamentPackaging $packagingType)
    {
        $packagingType->delete();

        return redirect(route('filament.packaging_types.index'));
    }
}
