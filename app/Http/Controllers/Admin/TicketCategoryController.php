<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketCategoryRequest;
use App\Http\Requests\UpdateTicketCategoryRequest;
use App\Models\TicketCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.ticket-categories.index', [
            'categories' => TicketCategory::withTrashed()
                ->withCount('tickets')
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.ticket-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketCategoryRequest $request): RedirectResponse
    {
        TicketCategory::create([
            ...$request->validated(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.ticket-categories.index')
            ->with('success', 'Ticket category created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketCategory $ticketCategory): View
    {
        return view('admin.ticket-categories.edit', [
            'category' => $ticketCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketCategoryRequest $request, TicketCategory $ticketCategory): RedirectResponse
    {
        $ticketCategory->update($request->validated());

        return redirect()
            ->route('admin.ticket-categories.index')
            ->with('success', 'Ticket category updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketCategory $ticketCategory): RedirectResponse
    {
        $ticketCategory->archive();

        return redirect()
            ->route('admin.ticket-categories.index')
            ->with('success', 'Ticket category archived.');
    }
}
