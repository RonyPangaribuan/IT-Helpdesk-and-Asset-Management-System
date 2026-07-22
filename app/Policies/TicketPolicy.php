<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLES);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isRequester()) {
            return $ticket->requester_id === $user->id;
        }

        if ($user->isTechnician()) {
            return $ticket->technician_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isRequester();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return $ticket->isAdminEditable();
        }

        return $user->isRequester()
            && $ticket->requester_id === $user->id
            && $ticket->isRequesterEditable();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() && $ticket->isOpenAndUnassigned();
    }

    public function reassign(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin()
            && in_array($ticket->status, [TicketStatus::Assigned, TicketStatus::Reopened], true)
            && $ticket->technician_id !== null;
    }

    public function startWork(User $user, Ticket $ticket): bool
    {
        return $user->isTechnician()
            && in_array($ticket->status, [TicketStatus::Assigned, TicketStatus::Reopened], true)
            && $ticket->technician_id === $user->id;
    }

    public function cancel(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return in_array($ticket->status, [TicketStatus::Open, TicketStatus::Assigned], true);
        }

        return $user->isRequester()
            && $ticket->requester_id === $user->id
            && $ticket->isOpenAndUnassigned();
    }

    public function viewStatusHistory(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    public function comment(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket) && $ticket->isCollaborationOpen();
    }

    public function uploadAttachment(User $user, Ticket $ticket): bool
    {
        if (! $ticket->isCollaborationOpen()) {
            return false;
        }

        if ($user->isRequester()) {
            return $ticket->requester_id === $user->id;
        }

        if ($user->isTechnician()) {
            return $ticket->technician_id === $user->id;
        }

        return false;
    }

    public function resolve(User $user, Ticket $ticket): bool
    {
        return $user->isTechnician()
            && $ticket->status === TicketStatus::InProgress
            && $ticket->technician_id === $user->id;
    }

    public function close(User $user, Ticket $ticket): bool
    {
        if ($ticket->status !== TicketStatus::Resolved) {
            return false;
        }

        return $user->isAdmin()
            || ($user->isRequester() && $ticket->requester_id === $user->id);
    }

    public function reopen(User $user, Ticket $ticket): bool
    {
        return $user->isRequester()
            && $ticket->requester_id === $user->id
            && $ticket->status === TicketStatus::Resolved;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return false;
    }
}
