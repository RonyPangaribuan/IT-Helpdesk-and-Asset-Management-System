<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetCategoryRequest;
use App\Http\Requests\UpdateAssetCategoryRequest;
use App\Models\AssetCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.asset-categories.index', [
            'categories' => AssetCategory::withTrashed()
                ->withCount('assets')
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.asset-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssetCategoryRequest $request): RedirectResponse
    {
        AssetCategory::create([
            ...$request->validated(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.asset-categories.index')
            ->with('success', 'Asset category created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetCategory $assetCategory): View
    {
        return view('admin.asset-categories.edit', [
            'category' => $assetCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssetCategoryRequest $request, AssetCategory $assetCategory): RedirectResponse
    {
        $assetCategory->update($request->validated());

        return redirect()
            ->route('admin.asset-categories.index')
            ->with('success', 'Asset category updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetCategory $assetCategory): RedirectResponse
    {
        $assetCategory->archive();

        return redirect()
            ->route('admin.asset-categories.index')
            ->with('success', 'Asset category archived.');
    }
}
