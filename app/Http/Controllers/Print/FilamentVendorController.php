<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\FilamentVendorRequest;
use App\Models\FilamentVendor;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FilamentVendorController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create() : View
    {
        $vendor = null;

        return view('filament.vendors.form', compact('vendor'));
    }

    public function edit(FilamentVendor $vendor) : View
    {
        return view('filament.vendors.form', compact('vendor'));
    }

    public function index() : View
    {
        $vendors = FilamentVendor::where('user_id', auth()->id())
            ->orderBy('name')
            ->paginate();

        return view('filament.vendors.index', compact('vendors'));
    }

    public function store(FilamentVendorRequest $request) : JsonResponse
    {
        $vendor          = new FilamentVendor($request->validated());
        $vendor->user_id = auth()->id();
        $vendor->save();

        return response()->json(['success' => true]);
    }

    public function update(FilamentVendorRequest $request, FilamentVendor $vendor) : JsonResponse
    {
        $vendor->update($request->validated());

        return response()->json(['success' => true]);
    }

    public function destroy(FilamentVendor $vendor)
    {
        $vendor->delete();

        return redirect(route('filament.vendors.index'));
    }
}
