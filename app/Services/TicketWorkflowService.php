<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Exceptions\InvalidTicketTransitionException;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TicketWorkflowService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createTicket(User $requester, array $attributes): Ticket
    {
        return DB::transaction(function () use ($attributes, $requester): Ticket {
            $ticket = Ticket::create([
                ...$attributes,
                'ticket_code' => Ticket::pendingCode(),
                'requester_id' => $requester->id,
                'technician_id' => null,
                'status' => TicketStatus::Open,
                'resolution_note' => null,
                'resolved_at' => null,
                'closed_at' => null,
            ]);

            $ticket->forceFill([
                'ticket_code' => Ticket::codeFromId($ticket->id, $ticket->created_at?->year),
            ])->save();

            $this->recordHistory($ticket, $requester, null, TicketStatus::Open, 'Ticket created', $ticket->created_at);

            return $ticket;
        });
    }

    public function assign(Ticket $ticket, User $actor, User $technician): Ticket
    {
        return DB::transaction(function () use ($actor, $technician, $ticket): Ticket {
            $lockedTicket = $this->lockedTicket($ticket);

            $this->ensureAdmin($actor);
            $this->ensureActiveTechnician($technician);
            $this->ensureStatus($lockedTicket, TicketStatus::Open, 'This ticket has already been assigned.');

            if ($lockedTicket->technician_id !== null) {
                throw InvalidTicketTransitionException::withMessage('This ticket has already been assigned.');
            }

            return $this->transition(
                ticket: $lockedTicket,
                actor: $actor,
                targetStatus: TicketStatus::Assigned,
                note: 'Assigned to '.$technician->name,
                technician: $technician,
            );
        });
    }

    public function reassign(Ticket $ticket, User $actor, User $technician): Ticket
    {
        return DB::transaction(function () use ($actor, $technician, $ticket): Ticket {
            $lockedTicket = $this->lockedTicket($ticket)->load('technician');

            $this->ensureAdmin($actor);
            $this->ensureActiveTechnician($technician);
            $this->ensureStatus($lockedTicket, TicketStatus::Assigned, 'Only assigned tickets can be reassigned.');

            if ($lockedTicket->technician_id === null) {
                throw InvalidTicketTransitionException::withMessage('Only assigned tickets can be reassigned.');
            }

            if ($lockedTicket->technician_id === $technician->id) {
                throw InvalidTicketTransitionException::withMessage('Choose a different technician for reassignment.');
            }

            $previousTechnician = $lockedTicket->technician?->name ?? 'Unassigned';
            $oldStatus = $lockedTicket->status;

            $lockedTicket->forceFill([
                'technician_id' => $technician->id,
            ])->save();

            $this->recordHistory(
                ticket: $lockedTicket,
                actor: $actor,
                oldStatus: $oldStatus,
                newStatus: $oldStatus,
                note: 'Reassigned from '.$previousTechnician.' to '.$technician->name,
            );

            return $lockedTicket->refresh();
        });
    }

    public function startWork(Ticket $ticket, User $actor): Ticket
    {
        return DB::transaction(function () use ($actor, $ticket): Ticket {
            $lockedTicket = $this->lockedTicket($ticket);

            if (! $actor->isTechnician()) {
                throw InvalidTicketTransitionException::withMessage('Only the assigned technician can start work.');
            }

            $this->ensureStatus($lockedTicket, TicketStatus::Assigned, 'Only assigned tickets can be started.');

            if ($lockedTicket->technician_id !== $actor->id) {
                throw InvalidTicketTransitionException::withMessage('Only the assigned technician can start work.');
            }

            return $this->transition(
                ticket: $lockedTicket,
                actor: $actor,
                targetStatus: TicketStatus::InProgress,
                note: 'Started work',
            );
        });
    }

    public function cancel(Ticket $ticket, User $actor, string $reason): Ticket
    {
        return DB::transaction(function () use ($actor, $reason, $ticket): Ticket {
            $lockedTicket = $this->lockedTicket($ticket);

            if (! $actor->isAdmin() && ! $actor->isRequester()) {
                throw InvalidTicketTransitionException::withMessage('Technicians cannot cancel tickets.');
            }

            if (! in_array($lockedTicket->status, [TicketStatus::Open, TicketStatus::Assigned], true)) {
                throw InvalidTicketTransitionException::withMessage('This ticket cannot be cancelled from its current status.');
            }

            if ($actor->isRequester() && ($lockedTicket->requester_id !== $actor->id || ! $lockedTicket->isOpenAndUnassigned())) {
                throw InvalidTicketTransitionException::withMessage('Only your own open and unassigned tickets can be cancelled.');
            }

            return $this->transition(
                ticket: $lockedTicket,
                actor: $actor,
                targetStatus: TicketStatus::Cancelled,
                note: $reason,
            );
        });
    }

    public function transitionStatus(Ticket $ticket, User $actor, TicketStatus $targetStatus, ?string $note = null): Ticket
    {
        return DB::transaction(function () use ($actor, $note, $targetStatus, $ticket): Ticket {
            return $this->transition(
                ticket: $this->lockedTicket($ticket),
                actor: $actor,
                targetStatus: $targetStatus,
                note: $note,
            );
        });
    }

    public function recordHistory(
        Ticket $ticket,
        User $actor,
        ?TicketStatus $oldStatus,
        TicketStatus $newStatus,
        ?string $note,
        mixed $createdAt = null,
    ): TicketStatusHistory {
        return TicketStatusHistory::create([
            'ticket_id' => $ticket->id,
            'changed_by' => $actor->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note,
            'created_at' => $createdAt ?? now(),
        ]);
    }

    private function transition(
        Ticket $ticket,
        User $actor,
        TicketStatus $targetStatus,
        ?string $note,
        ?User $technician = null,
    ): Ticket {
        $oldStatus = $ticket->status;

        if ($oldStatus->isTerminal()) {
            throw InvalidTicketTransitionException::withMessage($oldStatus->label().' tickets cannot be modified.');
        }

        if (! $oldStatus->canTransitionTo($targetStatus) || ! $oldStatus->canTransitionToInMilestoneThree($targetStatus)) {
            throw InvalidTicketTransitionException::withMessage('This status transition is not allowed.');
        }

        if ($targetStatus === TicketStatus::Assigned && ! ($technician instanceof User) && $ticket->technician_id === null) {
            throw InvalidTicketTransitionException::withMessage('Only an active technician can be assigned.');
        }

        if ($targetStatus === TicketStatus::InProgress && $ticket->technician_id === null) {
            throw InvalidTicketTransitionException::withMessage('Only assigned tickets can be started.');
        }

        $updates = ['status' => $targetStatus];

        if ($technician instanceof User) {
            $updates['technician_id'] = $technician->id;
        }

        $ticket->forceFill($updates)->save();

        $this->recordHistory($ticket, $actor, $oldStatus, $targetStatus, $note);

        return $ticket->refresh();
    }

    private function lockedTicket(Ticket $ticket): Ticket
    {
        return Ticket::query()
            ->whereKey($ticket->id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function ensureActiveTechnician(User $technician): void
    {
        if (! $technician->isTechnician() || ! $technician->is_active) {
            throw InvalidTicketTransitionException::withMessage('Only an active technician can be assigned.');
        }
    }

    private function ensureAdmin(User $actor): void
    {
        if (! $actor->isAdmin()) {
            throw InvalidTicketTransitionException::withMessage('Only administrators can assign tickets.');
        }
    }

    private function ensureStatus(Ticket $ticket, TicketStatus $status, string $message): void
    {
        if ($ticket->status !== $status) {
            throw InvalidTicketTransitionException::withMessage($message);
        }
    }
}
