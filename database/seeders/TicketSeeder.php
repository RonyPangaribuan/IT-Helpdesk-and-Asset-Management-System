<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workflow = app(TicketWorkflowService::class);

        /** @var Collection<int, User> $requesters */
        $requesters = User::query()
            ->where('role', User::ROLE_REQUESTER)
            ->orderBy('email')
            ->get();

        /** @var Collection<int, User> $technicians */
        $technicians = User::query()
            ->where('role', User::ROLE_TECHNICIAN)
            ->where('is_active', true)
            ->orderBy('email')
            ->get();

        /** @var Collection<int, TicketCategory> $categories */
        $categories = TicketCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($requesters->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $admin = User::where('role', User::ROLE_ADMIN)->first();

        if (! $admin instanceof User) {
            return;
        }

        $tickets = [
            ['title' => 'Laptop cannot connect to campus Wi-Fi', 'priority' => TicketPriority::High, 'location' => 'Library Floor 2', 'target' => TicketStatus::Open],
            ['title' => 'Printer output has faded text', 'priority' => TicketPriority::Medium, 'location' => 'Administration Office', 'target' => TicketStatus::Assigned],
            ['title' => 'Projector HDMI signal not detected', 'priority' => TicketPriority::High, 'location' => 'Room A-204', 'target' => TicketStatus::InProgress],
            ['title' => 'Password reset request for portal account', 'priority' => TicketPriority::Low, 'location' => 'Student Service Desk', 'target' => TicketStatus::Cancelled],
            ['title' => 'Accounting software fails to launch', 'priority' => TicketPriority::Critical, 'location' => 'Finance Office', 'target' => TicketStatus::Assigned],
            ['title' => 'Mouse and keyboard intermittently disconnect', 'priority' => TicketPriority::Medium, 'location' => 'Computer Lab 1', 'target' => TicketStatus::Open],
            ['title' => 'Cannot access shared network folder', 'priority' => TicketPriority::High, 'location' => 'HR Department', 'target' => TicketStatus::InProgress],
            ['title' => 'Monitor shows flickering image', 'priority' => TicketPriority::Low, 'location' => 'Room B-112', 'target' => TicketStatus::Open],
            ['title' => 'Email client keeps asking for password', 'priority' => TicketPriority::Medium, 'location' => 'Faculty Office', 'target' => TicketStatus::Assigned],
            ['title' => 'New user workstation needs setup', 'priority' => TicketPriority::Low, 'location' => 'IT Office', 'target' => TicketStatus::Cancelled],
            ['title' => 'Scanner driver is missing', 'priority' => TicketPriority::Medium, 'location' => 'Archive Room', 'target' => TicketStatus::Open],
            ['title' => 'Classroom speaker has no sound', 'priority' => TicketPriority::High, 'location' => 'Room C-301', 'target' => TicketStatus::InProgress],
        ];

        foreach ($tickets as $index => $ticketData) {
            $ticket = Ticket::query()->where('title', $ticketData['title'])->first();

            if (! $ticket instanceof Ticket) {
                $ticket = $workflow->createTicket($requesters[$index % $requesters->count()], [
                    'ticket_category_id' => $categories[$index % $categories->count()]->id,
                    'title' => $ticketData['title'],
                    'description' => 'Demo report for '.$ticketData['title'].'. This ticket demonstrates Milestone 3 assignment and workflow states.',
                    'location' => $ticketData['location'],
                    'priority' => $ticketData['priority'],
                ]);
            }

            if ($technicians->isEmpty()) {
                continue;
            }

            $technician = $technicians[$index % $technicians->count()];

            if ($ticketData['target'] === TicketStatus::Assigned && $ticket->isOpenAndUnassigned()) {
                $workflow->assign($ticket, $admin, $technician);
            }

            if ($ticketData['target'] === TicketStatus::InProgress) {
                if ($ticket->isOpenAndUnassigned()) {
                    $ticket = $workflow->assign($ticket, $admin, $technician);
                }

                if ($ticket->status === TicketStatus::Assigned && $ticket->technician_id === $technician->id) {
                    $workflow->startWork($ticket, $technician);
                }
            }

            if ($ticketData['target'] === TicketStatus::Cancelled && in_array($ticket->status, [TicketStatus::Open, TicketStatus::Assigned], true)) {
                $workflow->cancel($ticket, $admin, 'Demo cancellation for a duplicate or no longer needed request.');
            }
        }
    }
}
