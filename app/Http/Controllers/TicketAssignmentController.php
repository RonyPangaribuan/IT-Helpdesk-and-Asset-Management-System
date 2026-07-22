<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTicketTransitionException;
use App\Http\Requests\AssignTicketRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Http\RedirectResponse;

class TicketAssignmentController extends Controller
{
    public function store(AssignTicketRequest $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        $validated = $request->validated();
        $technician = User::findOrFail($validated['technician_id']);

        try {
            $workflow->assign($ticket, $request->user(), $technician);
        } catch (InvalidTicketTransitionException $exception) {
            return back()->withErrors(['workflow' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket assigned.');
    }

    public function update(AssignTicketRequest $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        $validated = $request->validated();
        $technician = User::findOrFail($validated['technician_id']);

        try {
            $workflow->reassign($ticket, $request->user(), $technician);
        } catch (InvalidTicketTransitionException $exception) {
            return back()->withErrors(['workflow' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket reassigned.');
    }
}
