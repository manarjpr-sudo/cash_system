<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser(): User
    {
        return User::factory()->create([
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_can_get_settings(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/settings');

        $response->assertOk();
    }

    public function test_unauthenticated_user_cannot_get_settings(): void
    {
        $response = $this->getJson('/api/v1/settings');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_update_settings(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/settings', [
                'currency_symbol' => '€',
                'date_format' => 'dd/mm/yyyy',
                'default_language' => 'en',
                'timezone' => 'Europe/Amsterdam',
            ]);

        $response->assertOk();
    }

    public function test_unauthenticated_user_cannot_update_settings(): void
    {
        $response = $this->putJson('/api/v1/settings', [
            'currency_symbol' => '€',
            'date_format' => 'dd/mm/yyyy',
            'default_language' => 'en',
            'timezone' => 'Europe/Amsterdam',
        ]);

        $response->assertUnauthorized();
    }

    public function test_settings_reject_invalid_date_format(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/settings', [
                'currency_symbol' => '€',
                'date_format' => 'invalid-format',
                'default_language' => 'en',
                'timezone' => 'Europe/Amsterdam',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('date_format');
    }
}