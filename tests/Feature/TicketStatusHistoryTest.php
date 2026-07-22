<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TicketSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TicketStatusHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_creation_creates_initial_status_history(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();

        $this->actingAs($requester)
            ->post(route('tickets.store'), $this->validTicketPayload($category))
            ->assertRedirect();

        $ticket = Ticket::firstOrFail();
        $history = $ticket->statusHistories()->firstOrFail();

        $this->assertSame($requester->id, $history->changed_by);
        $this->assertNull($history->old_status);
        $this->assertSame(TicketStatus::Open, $history->new_status);
        $this->assertSame('Ticket created', $history->note);
    }

    public function test_ticket_creation_rolls_back_when_initial_history_fails(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            $this->markTestSkipped('This atomic rollback check uses a SQLite trigger.');
        }

        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();

        DB::unprepared(
            "CREATE TRIGGER fail_ticket_history_insert BEFORE INSERT ON ticket_status_histories BEGIN SELECT RAISE(ABORT, 'history failed'); END;"
        );

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($requester)
                ->post(route('tickets.store'), $this->validTicketPayload($category));

            $this->fail('The history insert should have failed.');
        } catch (QueryException) {
            $this->assertDatabaseCount('tickets', 0);
            $this->assertDatabaseCount('ticket_status_histories', 0);
        } finally {
            DB::unprepared('DROP TRIGGER IF EXISTS fail_ticket_history_insert;');
        }
    }

    public function test_ticket_show_displays_status_timeline(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();

        $this->actingAs($requester)
            ->post(route('tickets.store'), $this->validTicketPayload($category));

        $ticket = Ticket::firstOrFail();

        $this->actingAs($requester)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Status Timeline')
            ->assertSee('Ticket created')
            ->assertSee('Open');
    }

    public function test_demo_seeder_creates_consistent_workflow_history_without_duplicates(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('tickets', 24);
        $this->assertGreaterThan(0, Ticket::query()->where('status', TicketStatus::Open->value)->count());
        $this->assertGreaterThan(0, Ticket::query()->where('status', TicketStatus::Assigned->value)->count());
        $this->assertGreaterThan(0, Ticket::query()->where('status', TicketStatus::InProgress->value)->count());
        $this->assertGreaterThan(0, Ticket::query()->where('status', TicketStatus::Resolved->value)->count());
        $this->assertGreaterThan(0, Ticket::query()->where('status', TicketStatus::Closed->value)->count());
        $this->assertGreaterThan(0, Ticket::query()->where('status', TicketStatus::Reopened->value)->count());
        $this->assertGreaterThan(0, Ticket::query()->where('status', TicketStatus::Cancelled->value)->count());

        $this->assertFalse(Ticket::query()->whereDoesntHave('statusHistories')->exists());
        $this->assertGreaterThan(0, TicketComment::count());

        $historyCount = TicketStatusHistory::count();
        $ticketCount = Ticket::count();
        $commentCount = TicketComment::count();

        $this->seed(TicketSeeder::class);

        $this->assertSame($ticketCount, Ticket::count());
        $this->assertSame($historyCount, TicketStatusHistory::count());
        $this->assertSame($commentCount, TicketComment::count());
    }

    /**
     * @return array<string, mixed>
     */
    private function validTicketPayload(TicketCategory $category): array
    {
        return [
            'title' => 'Computer cannot access shared drive',
            'description' => 'The shared drive cannot be accessed from this workstation.',
            'ticket_category_id' => $category->id,
            'priority' => TicketPriority::Medium->value,
            'location' => 'Room 204',
        ];
    }
}
