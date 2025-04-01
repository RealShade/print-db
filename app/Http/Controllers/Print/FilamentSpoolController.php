<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\FilamentSpoolRequest;
use App\Models\Filament;
use App\Models\FilamentPackaging;
use App\Models\FilamentSpool;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FilamentSpoolController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create() : View
    {
        $spool = null;
        $filaments = Filament::where('user_id', auth()->id())->orderBy('name')->get();
        $packagings = FilamentPackaging::where('user_id', auth()->id())->orderBy('name')->get();

        return view('filament.spools.form', compact('spool', 'filaments', 'packagings'));
    }

    public function edit(FilamentSpool $spool) : View
    {
        $filaments = Filament::where('user_id', auth()->id())->orderBy('name')->get();
        $packagings = FilamentPackaging::where('user_id', auth()->id())->orderBy('name')->get();
        
        return view('filament.spools.form', compact('spool', 'filaments', 'packagings'));
    }

    public function index() : View
    {
        $spools = FilamentSpool::where('user_id', auth()->id())
            ->with(['filament', 'packaging'])
            ->orderBy('name')
            ->paginate();

        return view('filament.spools.index', compact('spools'));
    }

    public function store(FilamentSpoolRequest $request) : JsonResponse
    {
        $spool = new FilamentSpool($request->validated());
        $spool->user_id = auth()->id();
        $spool->save();

        return response()->json(['success' => true]);
    }

    public function update(FilamentSpoolRequest $request, FilamentSpool $spool) : JsonResponse
    {
        $spool->update($request->validated());

        return response()->json(['success' => true]);
    }

    public function destroy(FilamentSpool $spool)
    {
        $spool->delete();

        return redirect(route('filament.spools.index'));
    }
}
