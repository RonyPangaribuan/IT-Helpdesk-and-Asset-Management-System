<?php

namespace App\Policies;

use App\Models\TicketAttachment;
use App\Models\User;

class TicketAttachmentPolicy
{
    public function download(User $user, TicketAttachment $attachment): bool
    {
        $ticket = $attachment->ticket;

        if ($ticket === null || $ticket->trashed()) {
            return false;
        }

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
}
