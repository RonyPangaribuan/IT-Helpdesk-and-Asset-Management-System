<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketCommentRequest;
use App\Http\Requests\UpdateTicketCommentRequest;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    public function store(StoreTicketCommentRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated()['body'],
        ]);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Comment added.');
    }

    public function update(UpdateTicketCommentRequest $request, Ticket $ticket, TicketComment $comment): RedirectResponse
    {
        abort_unless($comment->ticket_id === $ticket->id, 404);

        $comment->update($request->validated());

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Comment updated.');
    }

    public function destroy(Request $request, Ticket $ticket, TicketComment $comment): RedirectResponse
    {
        abort_unless($comment->ticket_id === $ticket->id, 404);

        $this->authorize('delete', $comment);

        $comment->delete();

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Comment deleted.');
    }
}
