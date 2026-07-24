<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ReleaseReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_route_renders_branded_landing_page(): void
    {
        config(['app.name' => 'deskIT']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>deskIT</title>', false)
            ->assertSeeText('deskIT')
            ->assertSeeText('IT Helpdesk & Asset Management', false)
            ->assertSeeText('Open deskIT')
            ->assertSee('branding/deskit-logo-full.png', false)
            ->assertSee('favicon-32x32.png', false)
            ->assertDontSeeText('DelDesk');
    }

    public function test_brand_assets_are_available(): void
    {
        foreach ([
            public_path('branding/deskit-logo-source.png'),
            public_path('branding/deskit-logo-full.png'),
            public_path('branding/deskit-mark.png'),
            public_path('branding/deskit-mark-192.png'),
            public_path('branding/deskit-mark-512.png'),
            public_path('favicon-32x32.png'),
            public_path('favicon.ico'),
        ] as $path) {
            $this->assertFileExists($path);
            $this->assertGreaterThan(0, filesize($path));
        }
    }

    public function test_application_and_guest_layouts_use_accessible_deskit_branding(): void
    {
        config(['app.name' => 'deskIT']);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<title>deskIT</title>', false)
            ->assertSee('aria-label="deskIT"', false)
            ->assertSee('branding/deskit-mark.png', false)
            ->assertSee('favicon.ico', false);

        $this->post(route('logout'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<title>deskIT</title>', false)
            ->assertSee('aria-label="deskIT"', false)
            ->assertSee('branding/deskit-mark.png', false)
            ->assertSee('branding/deskit-logo-full.png', false)
            ->assertSee('favicon-32x32.png', false);
    }

    public function test_web_responses_include_security_headers(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_route_cache_is_compatible(): void
    {
        try {
            $this->artisan('route:cache')->assertExitCode(0);
        } finally {
            Artisan::call('optimize:clear');
        }
    }

    public function test_ticket_attachments_use_configured_private_disk(): void
    {
        config(['deskit.attachment_disk' => 'configured-attachments']);
        Storage::fake('configured-attachments');

        $requester = User::factory()->requester()->create();

        $this->actingAs($requester)
            ->post(route('tickets.store'), [
                'title' => 'Upload private evidence',
                'description' => 'The ticket includes a private attachment stored on the configured disk.',
                'ticket_category_id' => TicketCategory::factory()->create()->id,
                'priority' => 'medium',
                'location' => 'Room 401',
                'attachments' => [
                    UploadedFile::fake()->create('evidence.pdf', 16, 'application/pdf'),
                ],
            ])
            ->assertRedirect();

        $attachment = TicketAttachment::firstOrFail();

        Storage::disk('configured-attachments')->assertExists($attachment->file_path);
    }

    public function test_custom_403_and_404_pages_render_without_internal_details(): void
    {
        $requester = User::factory()->requester()->create();

        $this->actingAs($requester)
            ->get(route('assets.index'))
            ->assertForbidden()
            ->assertSeeText('Access denied')
            ->assertSee('favicon-32x32.png', false)
            ->assertDontSeeText('SQLSTATE');

        $this->get('/missing-deskit-page')
            ->assertNotFound()
            ->assertSeeText('Page not found')
            ->assertSee('favicon.ico', false)
            ->assertDontSeeText(base_path());
    }

    public function test_custom_500_page_does_not_expose_debug_details_when_debug_is_disabled(): void
    {
        config(['app.debug' => false]);

        Route::get('/__deskit-error-test', function (): void {
            throw new RuntimeException('SQLSTATE secret at C:\\server\\path');
        });

        $this->get('/__deskit-error-test')
            ->assertStatus(500)
            ->assertSeeText('Something went wrong')
            ->assertDontSeeText('SQLSTATE')
            ->assertDontSeeText('C:\\server\\path');
    }

    public function test_missing_attachment_returns_404(): void
    {
        $disk = (string) config('deskit.attachment_disk');
        Storage::fake($disk);

        $requester = User::factory()->requester()->create();
        $ticket = Ticket::factory()->forRequester($requester)->open()->create();
        $attachment = TicketAttachment::factory()
            ->for($ticket)
            ->uploadedBy($requester)
            ->create();

        Storage::disk($disk)->delete($attachment->file_path);

        $this->actingAs($requester)
            ->get(route('ticket-attachments.download', $attachment))
            ->assertNotFound()
            ->assertSeeText('Page not found');
    }
}
