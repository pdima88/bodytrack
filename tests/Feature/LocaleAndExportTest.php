<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleAndExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_switch_changes_interface_language(): void
    {
        $user = User::factory()->has(Profile::factory())->create();

        $this->actingAs($user)->get('/locale/en')->assertRedirect();
        $this->assertSame('en', $user->fresh()->locale);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertDontSee('Сводка');

        $this->actingAs($user)->get('/locale/ru');
        $this->actingAs($user)->get('/dashboard')->assertSee('Сводка');
    }

    public function test_unknown_locale_is_rejected(): void
    {
        $this->get('/locale/de')->assertNotFound();
    }

    public function test_login_page_is_english_for_guest_after_switch(): void
    {
        $this->get('/locale/en');
        $this->get('/login')->assertSee('Log in');
    }

    public function test_csv_export_contains_measurements(): void
    {
        $user = User::factory()->has(Profile::factory())->create();
        Measurement::factory()->for($user)->create([
            'measured_at' => now()->subDay()->setTime(7, 30),
            'weight_kg' => 82.4,
        ]);

        $response = $this->actingAs($user)->get('/measurements/export');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));

        $csv = $response->streamedContent();
        $this->assertStringContainsString('82,4', $csv);
        $this->assertStringContainsString('Вес', $csv);
    }
}
