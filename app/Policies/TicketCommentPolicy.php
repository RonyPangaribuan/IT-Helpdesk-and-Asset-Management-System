<?php

namespace App\Policies;

use App\Models\TicketComment;
use App\Models\User;

class TicketCommentPolicy
{
    public function update(User $user, TicketComment $comment): bool
    {
        return $comment->user_id === $user->id
            && $comment->ticket?->isCollaborationOpen();
    }

    public function delete(User $user, TicketComment $comment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $comment->user_id === $user->id
            && $comment->ticket?->isCollaborationOpen();
    }
}
