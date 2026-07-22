<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Asset;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketAssetIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_requester_can_create_ticket_without_or_with_active_asset(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $asset = Asset::factory()->good()->create(['asset_code' => 'AST-LAP-101']);

        $this->actingAs($requester)
            ->post(route('tickets.store'), $this->validTicketPayload($category))
            ->assertRedirect();

        $this->assertNull(Ticket::firstOrFail()->asset_id);

        $this->actingAs($requester)
            ->post(route('tickets.store'), $this->validTicketPayload($category, [
                'title' => 'Laptop keyboard issue',
                'asset_id' => $asset->id,
            ]))
            ->assertRedirect();

        $ticket = Ticket::query()->where('title', 'Laptop keyboard issue')->firstOrFail();

        $this->assertSame($asset->id, $ticket->asset_id);
    }

    public function test_requester_cannot_select_inactive_archived_retired_or_invalid_asset(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $inactive = Asset::factory()->good()->inactive()->create();
        $archived = Asset::factory()->good()->create();
        $retired = Asset::factory()->retired()->create();
        $archived->archive();

        foreach ([$inactive, $archived, $retired] as $asset) {
            $this->actingAs($requester)
                ->from(route('tickets.create'))
                ->post(route('tickets.store'), $this->validTicketPayload($category, [
                    'asset_id' => $asset->id,
                ]))
                ->assertRedirect(route('tickets.create'))
                ->assertSessionHasErrors('asset_id');
        }

        $this->actingAs($requester)
            ->from(route('tickets.create'))
            ->post(route('tickets.store'), $this->validTicketPayload($category, [
                'asset_id' => 99999,
            ]))
            ->assertRedirect(route('tickets.create'))
            ->assertSessionHasErrors('asset_id');

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_requester_can_update_or_remove_asset_on_own_open_unassigned_ticket_only(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $category = TicketCategory::factory()->create();
        $asset = Asset::factory()->good()->create();
        $newAsset = Asset::factory()->good()->create();
        $ticket = Ticket::factory()->forRequester($requester)->for($category, 'category')->withAsset($asset)->create();

        $this->actingAs($requester)
            ->patch(route('tickets.update', $ticket), $this->validTicketPayload($category, [
                'asset_id' => $newAsset->id,
            ]))
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertSame($newAsset->id, $ticket->refresh()->asset_id);

        $this->actingAs($requester)
            ->patch(route('tickets.update', $ticket), $this->validTicketPayload($category, [
                'asset_id' => null,
            ]))
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertNull($ticket->refresh()->asset_id);

        $assignedTicket = app(TicketWorkflowService::class)->assign($ticket->refresh(), $admin, $technician);

        $this->actingAs($requester)
            ->patch(route('tickets.update', $assignedTicket), $this->validTicketPayload($category, [
                'asset_id' => $asset->id,
            ]))
            ->assertForbidden();
    }

    public function test_admin_can_update_related_asset_on_admin_editable_ticket_and_technician_cannot(): void
    {
        $admin = User::factory()->admin()->create();
        $requester = User::factory()->requester()->create();
        $technician = User::factory()->technician()->create();
        $category = TicketCategory::factory()->create();
        $ticket = Ticket::factory()->forRequester($requester)->assignedTo($technician)->for($category, 'category')->create();
        $asset = Asset::factory()->good()->create();

        $this->actingAs($admin)
            ->patch(route('tickets.update', $ticket), [
                'ticket_category_id' => $category->id,
                'priority' => TicketPriority::High->value,
                'asset_id' => $asset->id,
                'title' => 'Injected title',
                'status' => TicketStatus::Closed->value,
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $ticket->refresh();

        $this->assertSame($asset->id, $ticket->asset_id);
        $this->assertNotSame('Injected title', $ticket->title);
        $this->assertSame(TicketStatus::Assigned, $ticket->status);

        $this->actingAs($technician)
            ->patch(route('tickets.update', $ticket), [
                'ticket_category_id' => $category->id,
                'priority' => TicketPriority::Medium->value,
                'asset_id' => null,
            ])
            ->assertForbidden();
    }

    public function test_current_inactive_or_archived_asset_can_be_retained_on_ticket_edit(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $inactiveAsset = Asset::factory()->good()->inactive()->create();
        $archivedAsset = Asset::factory()->good()->create();
        $archivedAsset->archive();
        $inactiveTicket = Ticket::factory()->forRequester($requester)->for($category, 'category')->withAsset($inactiveAsset)->create();
        $archivedTicket = Ticket::factory()->forRequester($requester)->for($category, 'category')->withAsset($archivedAsset)->create();

        $this->actingAs($requester)
            ->patch(route('tickets.update', $inactiveTicket), $this->validTicketPayload($category, [
                'title' => 'Updated inactive asset ticket',
                'asset_id' => $inactiveAsset->id,
            ]))
            ->assertRedirect(route('tickets.show', $inactiveTicket));

        $this->actingAs($requester)
            ->patch(route('tickets.update', $archivedTicket), $this->validTicketPayload($category, [
                'title' => 'Updated archived asset ticket',
                'asset_id' => $archivedAsset->id,
            ]))
            ->assertRedirect(route('tickets.show', $archivedTicket));

        $this->assertSame($inactiveAsset->id, $inactiveTicket->refresh()->asset_id);
        $this->assertSame($archivedAsset->id, $archivedTicket->refresh()->asset_id);
    }

    public function test_ticket_detail_and_list_display_related_asset_even_after_asset_is_archived(): void
    {
        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $asset = Asset::factory()->good()->create([
            'asset_code' => 'AST-DETAIL-001',
            'name' => 'Detail Laptop',
        ]);
        $ticket = Ticket::factory()->forRequester($requester)->for($category, 'category')->withAsset($asset)->create();

        $asset->archive();

        $this->actingAs($requester)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Related Asset')
            ->assertSee('AST-DETAIL-001')
            ->assertSee('Detail Laptop')
            ->assertSee('(archived)');

        $this->actingAs($requester)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee('AST-DETAIL-001');
    }

    public function test_ticket_search_can_match_asset_code_without_breaking_role_scope(): void
    {
        $requester = User::factory()->requester()->create();
        $otherRequester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $asset = Asset::factory()->good()->create(['asset_code' => 'AST-OWN-001']);
        $otherAsset = Asset::factory()->good()->create(['asset_code' => 'AST-OTHER-001']);
        $ownTicket = Ticket::factory()->forRequester($requester)->for($category, 'category')->withAsset($asset)->create(['title' => 'Own asset ticket']);
        $otherTicket = Ticket::factory()->forRequester($otherRequester)->for($category, 'category')->withAsset($otherAsset)->create(['title' => 'Other asset ticket']);

        $this->actingAs($requester)
            ->get(route('tickets.index', ['q' => 'AST-OWN-001']))
            ->assertOk()
            ->assertSee($ownTicket->ticket_code)
            ->assertDontSee($otherTicket->ticket_code);

        $this->actingAs($requester)
            ->get(route('tickets.index', ['q' => 'AST-OTHER-001']))
            ->assertOk()
            ->assertDontSee($otherTicket->ticket_code);
    }

    public function test_ticket_creation_with_attachment_and_asset_is_atomic(): void
    {
        $disk = (string) config('deskit.attachment_disk');
        Storage::fake($disk);

        $requester = User::factory()->requester()->create();
        $category = TicketCategory::factory()->create();
        $asset = Asset::factory()->good()->create();

        $this->actingAs($requester)
            ->post(route('tickets.store'), $this->validTicketPayload($category, [
                'asset_id' => $asset->id,
                'attachments' => [
                    UploadedFile::fake()->create('supporting.pdf', 64, 'application/pdf'),
                ],
            ]))
            ->assertRedirect();

        $ticket = Ticket::firstOrFail();

        $this->assertSame($asset->id, $ticket->asset_id);
        $this->assertDatabaseCount('ticket_attachments', 1);
        Storage::disk($disk)->assertExists($ticket->attachments()->firstOrFail()->file_path);

        Storage::fake($disk);

        $this->actingAs($requester)
            ->from(route('tickets.create'))
            ->post(route('tickets.store'), $this->validTicketPayload($category, [
                'title' => 'Invalid attachment ticket',
                'asset_id' => $asset->id,
                'attachments' => [
                    UploadedFile::fake()->create('malware.exe', 1, 'application/x-msdownload'),
                ],
            ]))
            ->assertRedirect(route('tickets.create'))
            ->assertSessionHasErrors('attachments.0');

        $this->assertDatabaseMissing('tickets', ['title' => 'Invalid attachment ticket']);
        $this->assertDatabaseCount('ticket_attachments', 1);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validTicketPayload(TicketCategory $category, array $overrides = []): array
    {
        return [
            'title' => 'Computer cannot access shared drive',
            'description' => 'The shared drive cannot be accessed from this workstation.',
            'ticket_category_id' => $category->id,
            'priority' => TicketPriority::Medium->value,
            'location' => 'Room 204',
            ...$overrides,
        ];
    }
}
