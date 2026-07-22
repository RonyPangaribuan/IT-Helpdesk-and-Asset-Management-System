<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Asset;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\TicketAttachmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $user = $request->user();
        $query = Ticket::query()
            ->with(['requester', 'technician', 'category', 'asset'])
            ->latest();

        if ($user->isRequester()) {
            $query->where('requester_id', $user->id);
        } elseif ($user->isTechnician()) {
            $query->where('technician_id', $user->id);
        }

        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query
                    ->where('ticket_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('asset', function (Builder $query) use ($search): void {
                        $query->where('asset_code', 'like', "%{$search}%");
                    });
            });
        }

        $status = $request->query('status');

        if (is_string($status) && in_array($status, TicketStatus::values(), true)) {
            $query->where('status', $status);
        }

        $priority = $request->query('priority');

        if (is_string($priority) && in_array($priority, TicketPriority::values(), true)) {
            $query->where('priority', $priority);
        }

        $categoryId = $request->integer('ticket_category_id');

        if ($categoryId > 0 && TicketCategory::withTrashed()->whereKey($categoryId)->exists()) {
            $query->where('ticket_category_id', $categoryId);
        }

        return view('tickets.index', [
            'tickets' => $query->paginate(10)->withQueryString(),
            'categories' => TicketCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'priorities' => TicketPriority::cases(),
            'statuses' => TicketStatus::cases(),
            'filters' => [
                'q' => $search,
                'status' => $status,
                'priority' => $priority,
                'ticket_category_id' => $categoryId ?: null,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Ticket::class);

        return view('tickets.create', [
            'categories' => TicketCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'priorities' => TicketPriority::cases(),
            'assets' => $this->assetsForTicketForm(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request, TicketAttachmentService $attachments): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedFiles = $request->file('attachments', []);
        unset($validated['attachments']);

        $ticket = $attachments->createTicketWithAttachments($request->user(), $validated, (array) $uploadedFiles);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'requester',
            'technician',
            'category',
            'asset.category',
            'statusHistories.changedBy',
            'comments.author',
            'attachments.uploader',
        ]);

        $technicians = collect();

        if (request()->user()->can('assign', $ticket) || request()->user()->can('reassign', $ticket)) {
            $technicians = User::query()
                ->where('role', User::ROLE_TECHNICIAN)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('tickets.show', [
            'ticket' => $ticket,
            'technicians' => $technicians,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

        $ticket->load(['category', 'asset.category']);

        return view('tickets.edit', [
            'ticket' => $ticket,
            'categories' => TicketCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'priorities' => TicketPriority::cases(),
            'assets' => $this->assetsForTicketForm($ticket),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->user()->isAdmin()) {
            $ticket->update([
                'ticket_category_id' => $validated['ticket_category_id'],
                'priority' => $validated['priority'],
                'asset_id' => $validated['asset_id'] ?? null,
            ]);
        } else {
            $ticket->update($validated);
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()
            ->route('tickets.index')
            ->with('success', 'Ticket archived.');
    }

    private function assetsForTicketForm(?Ticket $ticket = null)
    {
        $assets = Asset::query()
            ->with('category')
            ->selectableForTickets()
            ->orderBy('asset_code')
            ->get();

        if ($ticket?->asset_id !== null && ! $assets->contains('id', $ticket->asset_id)) {
            $currentAsset = $ticket->asset;

            if ($currentAsset instanceof Asset) {
                $assets->prepend($currentAsset);
            }
        }

        return $assets;
    }
}
