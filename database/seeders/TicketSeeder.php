<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Asset;
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
            ['title' => 'Laptop cannot connect to campus Wi-Fi', 'priority' => TicketPriority::High, 'location' => 'Library Floor 2', 'target' => TicketStatus::Open, 'asset_code' => 'AST-NET-002'],
            ['title' => 'Printer output has faded text', 'priority' => TicketPriority::Medium, 'location' => 'Administration Office', 'target' => TicketStatus::Assigned, 'asset_code' => 'AST-PRN-001'],
            ['title' => 'Projector HDMI signal not detected', 'priority' => TicketPriority::High, 'location' => 'Room A-204', 'target' => TicketStatus::InProgress, 'asset_code' => 'AST-PRJ-001'],
            ['title' => 'Password reset request for portal account', 'priority' => TicketPriority::Low, 'location' => 'Student Service Desk', 'target' => TicketStatus::Cancelled, 'asset_code' => null],
            ['title' => 'Accounting software fails to launch', 'priority' => TicketPriority::Critical, 'location' => 'Finance Office', 'target' => TicketStatus::Resolved, 'asset_code' => 'AST-DES-002'],
            ['title' => 'Mouse and keyboard intermittently disconnect', 'priority' => TicketPriority::Medium, 'location' => 'Computer Lab 1', 'target' => TicketStatus::Closed, 'asset_code' => 'AST-PER-002'],
            ['title' => 'Cannot access shared network folder', 'priority' => TicketPriority::High, 'location' => 'HR Department', 'target' => TicketStatus::Reopened, 'asset_code' => 'AST-NET-001'],
            ['title' => 'Monitor shows flickering image', 'priority' => TicketPriority::Low, 'location' => 'Room B-112', 'target' => TicketStatus::Open, 'asset_code' => 'AST-PER-001'],
            ['title' => 'Email client keeps asking for password', 'priority' => TicketPriority::Medium, 'location' => 'Faculty Office', 'target' => TicketStatus::Assigned, 'asset_code' => 'AST-LAP-002'],
            ['title' => 'New user workstation needs setup', 'priority' => TicketPriority::Low, 'location' => 'IT Office', 'target' => TicketStatus::Cancelled, 'asset_code' => 'AST-DES-001'],
            ['title' => 'Scanner driver is missing', 'priority' => TicketPriority::Medium, 'location' => 'Archive Room', 'target' => TicketStatus::Resolved, 'asset_code' => 'AST-PRN-002'],
            ['title' => 'Classroom speaker has no sound', 'priority' => TicketPriority::High, 'location' => 'Room C-301', 'target' => TicketStatus::Closed, 'asset_code' => 'AST-PRJ-002'],
            ['title' => 'Lab PC cannot boot after update', 'priority' => TicketPriority::Critical, 'location' => 'Computer Lab 1', 'target' => TicketStatus::Open, 'asset_code' => 'AST-DES-001'],
            ['title' => 'Access point coverage drops near reading area', 'priority' => TicketPriority::High, 'location' => 'Library Floor 2', 'target' => TicketStatus::Assigned, 'asset_code' => 'AST-NET-002'],
            ['title' => 'Faculty laptop battery drains quickly', 'priority' => TicketPriority::Medium, 'location' => 'Faculty Office', 'target' => TicketStatus::InProgress, 'asset_code' => 'AST-LAP-002'],
            ['title' => 'Student account cannot access LMS', 'priority' => TicketPriority::High, 'location' => 'Student Service Desk', 'target' => TicketStatus::Resolved, 'asset_code' => null],
            ['title' => 'Archive scanner paper feed jams', 'priority' => TicketPriority::Medium, 'location' => 'Archive Room', 'target' => TicketStatus::Closed, 'asset_code' => 'AST-PRN-002'],
            ['title' => 'Room C projector lamp warning appears', 'priority' => TicketPriority::Low, 'location' => 'Room C-301', 'target' => TicketStatus::Reopened, 'asset_code' => 'AST-PRJ-002'],
            ['title' => 'Temporary software install request cancelled', 'priority' => TicketPriority::Low, 'location' => 'Room B-112', 'target' => TicketStatus::Cancelled, 'asset_code' => null],
            ['title' => 'Finance desktop blue screen during startup', 'priority' => TicketPriority::Critical, 'location' => 'Finance Office', 'target' => TicketStatus::InProgress, 'asset_code' => 'AST-DES-002'],
            ['title' => 'Main router CPU usage spike', 'priority' => TicketPriority::Critical, 'location' => 'Server Room', 'target' => TicketStatus::Resolved, 'asset_code' => 'AST-NET-001'],
            ['title' => 'Lab monitor color calibration issue', 'priority' => TicketPriority::Low, 'location' => 'Room B-112', 'target' => TicketStatus::Assigned, 'asset_code' => 'AST-PER-001'],
            ['title' => 'Printer queue stuck for administration staff', 'priority' => TicketPriority::High, 'location' => 'Administration Office', 'target' => TicketStatus::Closed, 'asset_code' => 'AST-PRN-001'],
            ['title' => 'Classroom HDMI cable replacement request', 'priority' => TicketPriority::Medium, 'location' => 'Room A-204', 'target' => TicketStatus::Open, 'asset_code' => 'AST-PRJ-001'],
        ];

        $assetsByCode = Asset::withTrashed()
            ->whereIn('asset_code', collect($tickets)->pluck('asset_code')->filter()->all())
            ->get()
            ->keyBy('asset_code');

        foreach ($tickets as $index => $ticketData) {
            $asset = is_string($ticketData['asset_code']) ? $assetsByCode->get($ticketData['asset_code']) : null;
            $ticket = Ticket::query()->where('title', $ticketData['title'])->first();

            if (! $ticket instanceof Ticket) {
                $ticket = $workflow->createTicket($requesters[$index % $requesters->count()], [
                    'ticket_category_id' => $categories[$index % $categories->count()]->id,
                    'asset_id' => $asset?->id,
                    'title' => $ticketData['title'],
                    'description' => 'Demo report for '.$ticketData['title'].'. This ticket demonstrates DelDesk ticket collaboration and workflow states.',
                    'location' => $ticketData['location'],
                    'priority' => $ticketData['priority'],
                ]);
            } elseif ($ticket->asset_id === null && $asset instanceof Asset) {
                $ticket->forceFill(['asset_id' => $asset->id])->save();
            }

            if ($technicians->isEmpty()) {
                continue;
            }

            $technician = $technicians[$index % $technicians->count()];

            if (in_array($ticketData['target'], [
                TicketStatus::Assigned,
                TicketStatus::InProgress,
                TicketStatus::Resolved,
                TicketStatus::Closed,
                TicketStatus::Reopened,
            ], true)) {
                if ($ticket->isOpenAndUnassigned()) {
                    $ticket = $workflow->assign($ticket, $admin, $technician);
                }
            }

            if (in_array($ticketData['target'], [
                TicketStatus::InProgress,
                TicketStatus::Resolved,
                TicketStatus::Closed,
                TicketStatus::Reopened,
            ], true)) {
                if ($ticket->refresh()->status === TicketStatus::Assigned && $ticket->technician_id === $technician->id) {
                    $ticket = $workflow->startWork($ticket, $technician);
                }
            }

            if (in_array($ticketData['target'], [
                TicketStatus::Resolved,
                TicketStatus::Closed,
                TicketStatus::Reopened,
            ], true)) {
                if ($ticket->refresh()->status === TicketStatus::InProgress && $ticket->technician_id === $technician->id) {
                    $ticket = $workflow->resolve($ticket, $technician, 'Demo resolution: verified the issue and restored normal service.');
                }
            }

            if ($ticketData['target'] === TicketStatus::Closed && $ticket->refresh()->status === TicketStatus::Resolved) {
                $this->seedComments($ticket, $admin, $technicians);
                $ticket = $workflow->close($ticket, $admin);
            }

            if ($ticketData['target'] === TicketStatus::Reopened && $ticket->refresh()->status === TicketStatus::Resolved) {
                $ticket = $workflow->reopen($ticket, $ticket->requester, 'The same issue returned during validation.');
            }

            if ($ticketData['target'] === TicketStatus::Cancelled && in_array($ticket->refresh()->status, [TicketStatus::Open, TicketStatus::Assigned], true)) {
                $this->seedComments($ticket, $admin, $technicians);
                $ticket = $workflow->cancel($ticket, $admin, 'Demo cancellation for a duplicate or no longer needed request.');
            }

            if (! $ticket->refresh()->status->isTerminal()) {
                $this->seedComments($ticket, $admin, $technicians);
            }
        }
    }

    /**
     * @param  Collection<int, User>  $technicians
     */
    private function seedComments(Ticket $ticket, User $admin, Collection $technicians): void
    {
        $ticket->comments()->firstOrCreate([
            'user_id' => $ticket->requester_id,
            'body' => 'Could you please check this when available?',
        ]);

        if ($ticket->technician_id !== null) {
            $ticket->comments()->firstOrCreate([
                'user_id' => $ticket->technician_id,
                'body' => 'I have reviewed the report and will update the ticket after troubleshooting.',
            ]);
        }

        if ($technicians->isNotEmpty() && ! $ticket->status->isTerminal()) {
            $ticket->comments()->firstOrCreate([
                'user_id' => $admin->id,
                'body' => 'Please keep this thread focused on the reported issue and next action.',
            ]);
        }
    }
}
