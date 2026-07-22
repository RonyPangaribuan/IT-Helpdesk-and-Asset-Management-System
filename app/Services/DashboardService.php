<?php

namespace App\Services;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Asset;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function forUser(User $user): array
    {
        return match ($user->role) {
            User::ROLE_ADMIN => $this->adminDashboard(),
            User::ROLE_TECHNICIAN => $this->technicianDashboard($user),
            default => $this->requesterDashboard($user),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function requesterDashboard(User $user): array
    {
        $tickets = Ticket::query()->where('requester_id', $user->id);

        return [
            'role' => User::ROLE_REQUESTER,
            'title' => 'Requester Dashboard',
            'subtitle' => 'Your submitted support tickets and recent activity.',
            'cards' => [
                ['label' => 'Total My Tickets', 'value' => (clone $tickets)->count(), 'href' => route('tickets.index')],
                ['label' => 'Open', 'value' => $this->statusCount($tickets, TicketStatus::Open), 'href' => route('tickets.index', ['status' => TicketStatus::Open->value])],
                ['label' => 'In Progress', 'value' => $this->statusCount($tickets, TicketStatus::InProgress), 'href' => route('tickets.index', ['status' => TicketStatus::InProgress->value])],
                ['label' => 'Resolved', 'value' => $this->statusCount($tickets, TicketStatus::Resolved), 'href' => route('tickets.index', ['status' => TicketStatus::Resolved->value])],
            ],
            'recentTickets' => (clone $tickets)
                ->with('asset')
                ->latest()
                ->limit(5)
                ->get(),
            'showRequester' => false,
            'categoryBreakdown' => collect(),
            'priorityBreakdown' => collect(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function technicianDashboard(User $user): array
    {
        $tickets = Ticket::query()->where('technician_id', $user->id);

        return [
            'role' => User::ROLE_TECHNICIAN,
            'title' => 'Technician Dashboard',
            'subtitle' => 'Tickets currently assigned to you and their latest status.',
            'cards' => [
                ['label' => 'Total Assigned Tickets', 'value' => (clone $tickets)->count(), 'href' => route('tickets.index')],
                ['label' => 'Assigned', 'value' => $this->statusCount($tickets, TicketStatus::Assigned), 'href' => route('tickets.index', ['status' => TicketStatus::Assigned->value])],
                ['label' => 'In Progress', 'value' => $this->statusCount($tickets, TicketStatus::InProgress), 'href' => route('tickets.index', ['status' => TicketStatus::InProgress->value])],
                ['label' => 'Resolved', 'value' => $this->statusCount($tickets, TicketStatus::Resolved), 'href' => route('tickets.index', ['status' => TicketStatus::Resolved->value])],
            ],
            'recentTickets' => (clone $tickets)
                ->with(['requester', 'asset'])
                ->latest()
                ->limit(5)
                ->get(),
            'showRequester' => true,
            'categoryBreakdown' => collect(),
            'priorityBreakdown' => collect(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function adminDashboard(): array
    {
        $tickets = Ticket::query();

        return [
            'role' => User::ROLE_ADMIN,
            'title' => 'Administrator Dashboard',
            'subtitle' => 'Operational overview across tickets, categories, priorities, and active assets.',
            'cards' => [
                ['label' => 'Total Tickets', 'value' => (clone $tickets)->count(), 'href' => route('tickets.index')],
                ['label' => 'Open', 'value' => $this->statusCount($tickets, TicketStatus::Open), 'href' => route('tickets.index', ['status' => TicketStatus::Open->value])],
                ['label' => 'In Progress', 'value' => $this->statusCount($tickets, TicketStatus::InProgress), 'href' => route('tickets.index', ['status' => TicketStatus::InProgress->value])],
                ['label' => 'Resolved', 'value' => $this->statusCount($tickets, TicketStatus::Resolved), 'href' => route('tickets.index', ['status' => TicketStatus::Resolved->value])],
                ['label' => 'Closed', 'value' => $this->statusCount($tickets, TicketStatus::Closed), 'href' => route('tickets.index', ['status' => TicketStatus::Closed->value])],
                ['label' => 'Active Assets', 'value' => Asset::query()->selectableForTickets()->count(), 'href' => route('assets.index', ['active' => '1'])],
            ],
            'recentTickets' => (clone $tickets)
                ->with(['requester', 'asset'])
                ->latest()
                ->limit(10)
                ->get(),
            'showRequester' => true,
            'categoryBreakdown' => TicketCategory::withTrashed()
                ->withCount('tickets')
                ->orderByDesc('tickets_count')
                ->orderBy('name')
                ->get()
                ->filter(fn (TicketCategory $category): bool => $category->tickets_count > 0)
                ->values(),
            'priorityBreakdown' => collect(TicketPriority::cases())->map(fn (TicketPriority $priority): array => [
                'priority' => $priority,
                'count' => (clone $tickets)->where('priority', $priority->value)->count(),
            ]),
        ];
    }

    private function statusCount(Builder $query, TicketStatus $status): int
    {
        return (clone $query)->where('status', $status->value)->count();
    }
}
