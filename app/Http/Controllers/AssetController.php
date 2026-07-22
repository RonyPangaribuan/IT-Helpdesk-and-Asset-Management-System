<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Asset::class);

        $query = Asset::query()
            ->with('category')
            ->latest();

        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query
                    ->where('asset_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $categoryId = $request->integer('asset_category_id');

        if ($categoryId > 0 && AssetCategory::withTrashed()->whereKey($categoryId)->exists()) {
            $query->where('asset_category_id', $categoryId);
        }

        $condition = $request->query('condition');

        if (is_string($condition) && in_array($condition, AssetCondition::values(), true)) {
            $query->where('condition', $condition);
        }

        $active = $request->query('active');

        if (in_array($active, ['0', '1'], true)) {
            $query->where('is_active', $active === '1');
        }

        return view('assets.index', [
            'assets' => $query->paginate(10)->withQueryString(),
            'categories' => AssetCategory::withTrashed()->orderBy('name')->get(),
            'conditions' => AssetCondition::cases(),
            'filters' => [
                'q' => $search,
                'asset_category_id' => $categoryId ?: null,
                'condition' => $condition,
                'active' => in_array($active, ['0', '1'], true) ? $active : null,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Asset::class);

        return view('assets.create', [
            'categories' => AssetCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'conditions' => AssetCondition::cases(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $asset = Asset::create($request->assetData());

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Asset $asset): View
    {
        $this->authorize('view', $asset);

        $asset->load('category');

        $relatedTickets = $asset->tickets()
            ->with(['requester', 'category'])
            ->latest();

        if ($request->user()->isTechnician()) {
            $relatedTickets->where('technician_id', $request->user()->id);
        }

        return view('assets.show', [
            'asset' => $asset,
            'relatedTickets' => $relatedTickets->paginate(10, ['*'], 'tickets_page')->withQueryString(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset): View
    {
        $this->authorize('update', $asset);

        $asset->load('category');

        return view('assets.edit', [
            'asset' => $asset,
            'categories' => $this->categoriesForForm($asset),
            'conditions' => AssetCondition::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        $asset->update($request->assetData());

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset): RedirectResponse
    {
        $this->authorize('delete', $asset);

        $asset->archive();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset archived.');
    }

    private function categoriesForForm(Asset $asset)
    {
        $categories = AssetCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if (! $categories->contains('id', $asset->asset_category_id)) {
            $categories->prepend($asset->category);
        }

        return $categories;
    }
}
