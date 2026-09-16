<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser(): User
    {
        return User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/profile');

        $response
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', 'Test User')
            ->assertJsonPath('email', 'test@example.com');
    }

    public function test_unauthenticated_user_cannot_view_profile(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertUnauthorized();
    }

    public function test_user_can_update_profile(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/profile', [
                'name' => 'Updated User',
                'email' => 'updated@example.com',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('name', 'Updated User')
            ->assertJsonPath('email', 'updated@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_user_cannot_update_profile_with_existing_email(): void
    {
        $user = $this->authenticatedUser();

        User::factory()->create([
            'email' => 'other@example.com',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/profile', [
                'name' => 'Updated User',
                'email' => 'other@example.com',
            ]);

        $response->assertUnprocessable();
    }

    public function test_user_can_update_password(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/profile/password', [
                'current_password' => 'Password123',
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ]);

        $response
            ->assertOk();

        $user->refresh();

        $this->assertTrue(
            Hash::check('NewPassword123', $user->password)
        );
    }

    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/profile/password', [
                'current_password' => 'WrongPassword123',
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ]);

        $response->assertUnprocessable();
    }

    public function test_user_cannot_update_password_with_weak_password(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/profile/password', [
                'current_password' => 'Password123',
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }
}