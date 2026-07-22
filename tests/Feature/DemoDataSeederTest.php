<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_creates_release_quality_dataset_from_empty_database(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, User::query()->where('role', User::ROLE_ADMIN)->where('is_active', true)->count());
        $this->assertSame(2, User::query()->where('role', User::ROLE_TECHNICIAN)->where('is_active', true)->count());
        $this->assertSame(3, User::query()->where('role', User::ROLE_REQUESTER)->where('is_active', true)->count());
        $this->assertSame([
            'admin@deskit.test',
            'requester1@deskit.test',
            'requester2@deskit.test',
            'requester3@deskit.test',
            'technician1@deskit.test',
            'technician2@deskit.test',
        ], User::query()->orderBy('email')->pluck('email')->all());

        $this->assertSame(7, TicketCategory::count());
        $this->assertSame(7, AssetCategory::count());
        $this->assertGreaterThanOrEqual(12, Asset::count());
        $this->assertGreaterThanOrEqual(24, Ticket::count());

        foreach (TicketStatus::cases() as $status) {
            $this->assertGreaterThan(0, Ticket::query()->where('status', $status->value)->count(), $status->value.' status is missing.');
        }

        foreach (TicketPriority::cases() as $priority) {
            $this->assertGreaterThan(0, Ticket::query()->where('priority', $priority->value)->count(), $priority->value.' priority is missing.');
        }

        $this->assertSame(3, Ticket::query()->distinct('requester_id')->count('requester_id'));
        $this->assertSame(2, Ticket::query()->whereNotNull('technician_id')->distinct('technician_id')->count('technician_id'));
        $this->assertGreaterThan(0, Ticket::query()->whereNull('asset_id')->count());
        $this->assertGreaterThan(0, Ticket::query()->whereNotNull('asset_id')->count());

        $this->assertFalse(Ticket::query()->whereDoesntHave('statusHistories')->exists());
        $this->assertFalse(Ticket::query()
            ->whereDoesntHave('statusHistories', function ($query): void {
                $query
                    ->whereNull('old_status')
                    ->where('new_status', TicketStatus::Open->value);
            })
            ->exists());

        $this->assertFalse(Ticket::query()
            ->whereIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
            ->where(function ($query): void {
                $query
                    ->whereNull('resolution_note')
                    ->orWhereNull('resolved_at');
            })
            ->exists());

        $this->assertFalse(Ticket::query()
            ->where('status', TicketStatus::Closed->value)
            ->whereNull('closed_at')
            ->exists());

        $this->assertFalse(Ticket::query()
            ->whereIn('status', [
                TicketStatus::Assigned->value,
                TicketStatus::InProgress->value,
                TicketStatus::Reopened->value,
            ])
            ->whereNull('technician_id')
            ->exists());

        $this->assertFalse(Ticket::query()
            ->whereHas('requester', fn ($query) => $query->where('role', '!=', User::ROLE_REQUESTER))
            ->exists());

        $this->assertFalse(Ticket::query()
            ->whereNotNull('technician_id')
            ->whereHas('technician', fn ($query) => $query->where('role', '!=', User::ROLE_TECHNICIAN))
            ->exists());

        $this->assertFalse(Ticket::query()
            ->whereNotNull('asset_id')
            ->whereDoesntHave('asset')
            ->exists());

        $ticketCount = Ticket::count();
        $historyCount = TicketStatusHistory::count();

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($ticketCount, Ticket::count());
        $this->assertSame($historyCount, TicketStatusHistory::count());
    }
}
