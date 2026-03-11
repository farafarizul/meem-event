<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCheckin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = Event::factory()->create([
            'category_event'    => 'onsite',
            'event_name'        => 'Test Conference',
            'location'          => 'Kuala Lumpur',
            'start_date'        => '2026-04-01',
            'end_date'          => '2026-04-02',
            'unique_identifier' => 'EVENT-TEST000001',
        ]);

        $this->user = User::factory()->create([
            'meem_code' => 'MEEM000001',
            'is_admin'  => false,
        ]);
    }

    // ── Public Check-in Page ──────────────────────────────────────────────────

    public function test_public_checkin_page_loads_for_valid_event(): void
    {
        $response = $this->get(route('checkin.show', $this->event->unique_identifier));

        $response->assertStatus(200);
        $response->assertSee($this->event->event_name);
        $response->assertSee($this->event->unique_identifier);
        $response->assertSee($this->user->fullname);
        $response->assertSee($this->user->meem_code);
    }

    public function test_public_checkin_page_returns_404_for_unknown_identifier(): void
    {
        $response = $this->get(route('checkin.show', 'EVENT-DOESNOTEXIST'));

        $response->assertStatus(404);
    }

    // ── Check-in Submission ───────────────────────────────────────────────────

    public function test_user_can_check_in_successfully(): void
    {
        $response = $this->post(route('checkin.store', $this->event->unique_identifier), [
            'user_id' => $this->user->id,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Check-in Successful');

        $this->assertDatabaseHas('event_checkins', [
            'event_id' => $this->event->id,
            'user_id'  => $this->user->id,
        ]);
    }

    public function test_duplicate_checkin_shows_already_checked_in_message(): void
    {
        // First check-in
        EventCheckin::create([
            'event_id'      => $this->event->id,
            'user_id'       => $this->user->id,
            'checked_in_at' => now(),
        ]);

        // Second attempt
        $response = $this->post(route('checkin.store', $this->event->unique_identifier), [
            'user_id' => $this->user->id,
        ]);

        $response->assertStatus(200);
        $response->assertSee('already checked in');

        // Still only one record
        $this->assertDatabaseCount('event_checkins', 1);
    }

    public function test_checkin_with_invalid_user_id_fails_validation(): void
    {
        $response = $this->post(route('checkin.store', $this->event->unique_identifier), [
            'user_id' => 99999,
        ]);

        $response->assertSessionHasErrors('user_id');
    }

    public function test_checkin_without_user_id_fails_validation(): void
    {
        $response = $this->post(route('checkin.store', $this->event->unique_identifier), []);

        $response->assertSessionHasErrors('user_id');
    }

    // ── Admin Event Show + QR ─────────────────────────────────────────────────

    public function test_admin_can_view_event_detail_page_with_qr(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->get(route('admin.events.show', $this->event));

        $response->assertStatus(200);
        $response->assertSee($this->event->event_name);
        $response->assertSee($this->event->unique_identifier);
        // QR download button is present
        $response->assertSee('qr-download');
    }

    public function test_admin_can_download_qr_png(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->get(route('admin.events.qr-download', $this->event));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertStringContainsString(
            'qr_' . $this->event->unique_identifier,
            $response->headers->get('Content-Disposition')
        );
    }

    public function test_non_admin_cannot_access_admin_event_show(): void
    {
        $user = $this->user;

        $this->actingAs($user)
            ->get(route('admin.events.show', $this->event))
            ->assertStatus(403);
    }
}
