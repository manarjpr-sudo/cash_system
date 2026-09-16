<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser(): User
    {
        return User::factory()->create([
            'status' => 'active',
        ]);
    }

    private function createCategory(string $type, ?int $parentId = null): Category
    {
        return Category::create([
            'name_ar' => $type === 'income' ? 'دخل' : 'طعام',
            'name_en' => $type === 'income' ? 'Income' : 'Food',
            'type' => $type,
            'parent_id' => $parentId,
        ]);
    }

    public function test_authenticated_user_can_create_income_operation(): void
    {
        $user = $this->authenticatedUser();
        $category = $this->createCategory('income');

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/v1/operations', [
                'type' => 'income',
                'amount' => 1500.50,
                'category_id' => $category->id,
                'operation_date' => '2026-09-12',
                'description' => 'Monthly salary',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('operation.user_id', $user->id)
            ->assertJsonPath('operation.type', 'income')
            ->assertJsonPath('operation.amount', '1500.50')
            ->assertJsonPath('operation.category_id', $category->id);

        $this->assertDatabaseHas('operations', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 1500.50,
            'description' => 'Monthly salary',
        ]);

        $response->assertJsonPath(
            'operation.operation_date',
            '2026-09-12T00:00:00.000000Z'
        );
    }

    public function test_authenticated_user_can_create_expense_operation(): void
    {
        $user = $this->authenticatedUser();
        $category = $this->createCategory('expense');

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/v1/operations', [
                'type' => 'expense',
                'amount' => 25,
                'category_id' => $category->id,
                'operation_date' => '2026-09-12',
                'description' => 'Lunch',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('operation.type', 'expense')
            ->assertJsonPath('operation.category_id', $category->id);
    }

    public function test_user_cannot_create_operation_with_mismatched_category_type(): void
    {
        $user = $this->authenticatedUser();
        $incomeCategory = $this->createCategory('income');

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/v1/operations', [
                'type' => 'expense',
                'amount' => 100,
                'category_id' => $incomeCategory->id,
                'operation_date' => '2026-09-12',
                'description' => 'Invalid category',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('category_id');
    }

    public function test_unauthenticated_user_cannot_access_operations(): void
    {
        $response = $this->getJson('/api/v1/operations');

        $response->assertUnauthorized();
    }

    public function test_user_can_view_only_their_own_operations(): void
    {
        $user = $this->authenticatedUser();
        $otherUser = $this->authenticatedUser();

        $ownOperation = Operation::create([
            'user_id' => $user->id,
            'type' => 'income',
            'amount' => 100,
            'operation_date' => '2026-09-12',
            'description' => 'My operation',
        ]);

        Operation::create([
            'user_id' => $otherUser->id,
            'type' => 'expense',
            'amount' => 200,
            'operation_date' => '2026-09-12',
            'description' => 'Other operation',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/operations');

        $response
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id', $ownOperation->id);
    }

    public function test_user_cannot_view_another_users_operation(): void
    {
        $user = $this->authenticatedUser();
        $otherUser = $this->authenticatedUser();

        $operation = Operation::create([
            'user_id' => $otherUser->id,
            'type' => 'expense',
            'amount' => 200,
            'operation_date' => '2026-09-12',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson("/api/v1/operations/{$operation->id}");

        $response->assertNotFound();
    }

    public function test_user_can_update_their_own_operation(): void
    {
        $user = $this->authenticatedUser();
        $category = $this->createCategory('expense');

        $operation = Operation::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 100,
            'operation_date' => '2026-09-10',
            'description' => 'Old description',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson("/api/v1/operations/{$operation->id}", [
                'type' => 'expense',
                'amount' => 150,
                'category_id' => $category->id,
                'operation_date' => '2026-09-12',
                'description' => 'Updated description',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('operation.amount', '150.00')
            ->assertJsonPath('operation.category_id', $category->id)
            ->assertJsonPath('operation.description', 'Updated description');

        $this->assertDatabaseHas('operations', [
            'id' => $operation->id,
            'amount' => 150,
            'category_id' => $category->id,
            'description' => 'Updated description',
        ]);
    }

    public function test_user_cannot_update_another_users_operation(): void
    {
        $user = $this->authenticatedUser();
        $otherUser = $this->authenticatedUser();

        $operation = Operation::create([
            'user_id' => $otherUser->id,
            'type' => 'expense',
            'amount' => 200,
            'operation_date' => '2026-09-12',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson("/api/v1/operations/{$operation->id}", [
                'amount' => 500,
            ]);

        $response->assertNotFound();
    }

    public function test_user_can_delete_their_own_operation(): void
    {
        $user = $this->authenticatedUser();

        $operation = Operation::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 200,
            'operation_date' => '2026-09-12',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/operations/{$operation->id}");

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Operation deleted successfully',
            ]);

        $this->assertDatabaseMissing('operations', [
            'id' => $operation->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_operation(): void
    {
        $user = $this->authenticatedUser();
        $otherUser = $this->authenticatedUser();

        $operation = Operation::create([
            'user_id' => $otherUser->id,
            'type' => 'expense',
            'amount' => 200,
            'operation_date' => '2026-09-12',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/operations/{$operation->id}");

        $response->assertNotFound();

        $this->assertDatabaseHas('operations', [
            'id' => $operation->id,
        ]);
    }

    public function test_operations_can_be_filtered_by_type(): void
    {
        $user = $this->authenticatedUser();

        Operation::create([
            'user_id' => $user->id,
            'type' => 'income',
            'amount' => 1000,
            'operation_date' => '2026-09-12',
        ]);

        Operation::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 100,
            'operation_date' => '2026-09-12',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/operations?type=income');

        $response
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.type', 'income');
    }
}