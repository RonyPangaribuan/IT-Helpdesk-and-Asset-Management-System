<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTicketTransitionException;
use App\Http\Requests\CancelTicketRequest;
use App\Http\Requests\ReopenTicketRequest;
use App\Http\Requests\ResolveTicketRequest;
use App\Models\Ticket;
use App\Services\TicketWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketWorkflowController extends Controller
{
    public function startWork(Request $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        $this->authorize('startWork', $ticket);

        try {
            $workflow->startWork($ticket, $request->user());
        } catch (InvalidTicketTransitionException $exception) {
            return back()->withErrors(['workflow' => $exception->getMessage()]);
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Work started.');
    }

    public function cancel(CancelTicketRequest $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        try {
            $workflow->cancel($ticket, $request->user(), $request->validated()['reason']);
        } catch (InvalidTicketTransitionException $exception) {
            return back()->withErrors(['workflow' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket cancelled.');
    }

    public function resolve(ResolveTicketRequest $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        try {
            $workflow->resolve($ticket, $request->user(), $request->validated()['resolution_note']);
        } catch (InvalidTicketTransitionException $exception) {
            return back()->withErrors(['workflow' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket resolved.');
    }

    public function close(Request $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        $this->authorize('close', $ticket);

        try {
            $workflow->close($ticket, $request->user());
        } catch (InvalidTicketTransitionException $exception) {
            return back()->withErrors(['workflow' => $exception->getMessage()]);
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket closed.');
    }

    public function reopen(ReopenTicketRequest $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        try {
            $workflow->reopen($ticket, $request->user(), $request->validated()['reason']);
        } catch (InvalidTicketTransitionException $exception) {
            return back()->withErrors(['workflow' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket reopened.');
    }
}
