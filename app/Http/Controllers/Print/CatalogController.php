<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\CatalogRequest;
use App\Models\Catalog;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{

    /* **************************************** Public **************************************** */
    public function create(?Catalog $catalog)
    {
        $catalogs = Catalog::where('user_id', Auth::id())->get();
        $parent = $catalog;

        return view('print.catalogs.form', compact('catalogs', 'parent'));
    }

    public function parts(Catalog $catalog)
    {
        $parts = $catalog->parts;
        return view('print.catalogs.parts-list', compact('parts'));
    }

    public function destroy(Catalog $catalog)
    {
        // Проверяем, есть ли у каталога дочерние каталоги или детали
        if ($catalog->children()->exists() || $catalog->parts()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('part.catalog.cannot_delete'),
            ], 412);
        }
        $catalog->delete();

        return response()->json([
            'success' => true,
            'message' => __('catalog.deleted'),
        ]);
    }

    public function edit(Catalog $catalog)
    {
        $catalogs = Catalog::where('user_id', Auth::id())
            ->where('id', '!=', $catalog->id)
            ->whereNotIn('id', $this->getAllChildrenIds($catalog))
            ->get();

        return view('print.catalogs.form', compact('catalog', 'catalogs'));
    }

    public function index()
    {
        $rootCatalogs = Catalog::with(['children.parts', 'parts'])
            ->where('user_id', Auth::id())
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('print.catalogs.index', compact('rootCatalogs'));
    }

    public function store(CatalogRequest $request)
    {
        $catalog          = new Catalog($request->validated());
        $catalog->user_id = Auth::id();
        $catalog->save();

        return response()->json([
            'success' => true,
            'message' => __('catalog.created'),
        ]);
    }

    public function update(CatalogRequest $request, Catalog $catalog)
    {
        $catalog->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('catalog.updated'),
        ]);
    }

    /* **************************************** Private **************************************** */
    private function getAllChildrenIds(Catalog $catalog, array &$ids = [])
    {
        foreach ($catalog->children as $child) {
            $ids[] = $child->id;
            $this->getAllChildrenIds($child, $ids);
        }

        return $ids;
    }
}
