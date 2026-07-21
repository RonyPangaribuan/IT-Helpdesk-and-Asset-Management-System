<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var Collection<int, User> $requesters */
        $requesters = User::query()
            ->where('role', User::ROLE_REQUESTER)
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

        $tickets = [
            ['title' => 'Laptop cannot connect to campus Wi-Fi', 'priority' => TicketPriority::High, 'location' => 'Library Floor 2'],
            ['title' => 'Printer output has faded text', 'priority' => TicketPriority::Medium, 'location' => 'Administration Office'],
            ['title' => 'Projector HDMI signal not detected', 'priority' => TicketPriority::High, 'location' => 'Room A-204'],
            ['title' => 'Password reset request for portal account', 'priority' => TicketPriority::Low, 'location' => 'Student Service Desk'],
            ['title' => 'Accounting software fails to launch', 'priority' => TicketPriority::Critical, 'location' => 'Finance Office'],
            ['title' => 'Mouse and keyboard intermittently disconnect', 'priority' => TicketPriority::Medium, 'location' => 'Computer Lab 1'],
            ['title' => 'Cannot access shared network folder', 'priority' => TicketPriority::High, 'location' => 'HR Department'],
            ['title' => 'Monitor shows flickering image', 'priority' => TicketPriority::Low, 'location' => 'Room B-112'],
            ['title' => 'Email client keeps asking for password', 'priority' => TicketPriority::Medium, 'location' => 'Faculty Office'],
            ['title' => 'New user workstation needs setup', 'priority' => TicketPriority::Low, 'location' => 'IT Office'],
            ['title' => 'Scanner driver is missing', 'priority' => TicketPriority::Medium, 'location' => 'Archive Room'],
            ['title' => 'Classroom speaker has no sound', 'priority' => TicketPriority::High, 'location' => 'Room C-301'],
        ];

        foreach ($tickets as $index => $ticketData) {
            if (Ticket::query()->where('title', $ticketData['title'])->exists()) {
                continue;
            }

            DB::transaction(function () use ($categories, $index, $requesters, $ticketData): void {
                $ticket = Ticket::create([
                    'ticket_code' => Ticket::pendingCode(),
                    'requester_id' => $requesters[$index % $requesters->count()]->id,
                    'technician_id' => null,
                    'ticket_category_id' => $categories[$index % $categories->count()]->id,
                    'title' => $ticketData['title'],
                    'description' => 'Demo report for '.$ticketData['title'].'. This ticket is intentionally open for Milestone 2 CRUD demonstrations.',
                    'location' => $ticketData['location'],
                    'priority' => $ticketData['priority'],
                    'status' => TicketStatus::Open,
                ]);

                $ticket->forceFill([
                    'ticket_code' => Ticket::codeFromId($ticket->id, $ticket->created_at?->year),
                ])->save();
            });
        }
    }
}
