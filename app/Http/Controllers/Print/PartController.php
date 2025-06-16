<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\PartRequest;
use App\Models\Catalog;
use App\Models\Part;
use App\Services\PartFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PartController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create(Catalog $catalog) : View
    {
        $part = null;

        return view('print.parts.form', compact('part', 'catalog'));
    }

    public function destroy(Part $part) : JsonResponse
    {
        if ($part->tasks()->exists()) {
            abort(412, __('part.cannot_delete'));
        }
        $part->delete();

        return response()->json(['success' => true]);
    }

    public function edit(Part $part) : View
    {
        return view('print.parts.form', compact('part'));
    }

    public function store(PartRequest $request, PartFileService $fileService) : JsonResponse
    {
        $part             = new Part($request->validated());
        $part->user_id    = auth()->id();
        $part->catalog_id = $request->catalog_id;
        $part->save();

        // Загрузка STL-файла
        if ($request->hasFile('stl_file')) {
            $file = $request->file('stl_file');
            $filename = $fileService->saveStlFile($part, $file);
            $part->stl_filename = $filename;
            $part->stl_original_name = $file->getClientOriginalName();
            $part->save();
        }

        return response()->json(['success' => true]);
    }

    public function update(PartRequest $request, Part $part, PartFileService $fileService) : JsonResponse
    {
        $part->update($request->validated());

        // Удаление STL-файла по запросу пользователя
        if ($request->input('delete_stl') === '1' && $part->stl_filename) {
            $fileService->deleteStlFile($part->stl_filename);
            $part->stl_filename = null;
            $part->stl_original_name = null;
            $part->save();
        }

        // Загрузка нового STL-файла (если есть)
        if ($request->hasFile('stl_file')) {
            $file = $request->file('stl_file');
            // Удалить старый файл, если был
            if ($part->stl_filename) {
                $fileService->deleteStlFile($part->stl_filename);
            }
            $filename = $fileService->saveStlFile($part, $file);
            $part->stl_filename = $filename;
            $part->stl_original_name = $file->getClientOriginalName();
            $part->save();
        }

        return response()->json(['success' => true]);
    }
}
