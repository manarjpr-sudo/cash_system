<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser(): User
    {
        return User::factory()->create([
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = $this->authenticatedUser();

        $incomeCategory = Category::create([
            'name_ar' => 'راتب',
            'name_en' => 'Salary',
            'type' => 'income',
            'parent_id' => null,
        ]);

        $expenseCategory = Category::create([
            'name_ar' => 'طعام',
            'name_en' => 'Food',
            'type' => 'expense',
            'parent_id' => null,
        ]);

        Operation::create([
            'user_id' => $user->id,
            'category_id' => $incomeCategory->id,
            'type' => 'income',
            'amount' => 5000,
            'operation_date' => '2026-09-10',
            'description' => 'Salary',
        ]);

        Operation::create([
            'user_id' => $user->id,
            'category_id' => $expenseCategory->id,
            'type' => 'expense',
            'amount' => 1200,
            'operation_date' => '2026-09-11',
            'description' => 'Groceries',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.name', $user->name)
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonPath('stats.totalIncome', 5000)
            ->assertJsonPath('stats.totalExpense', 1200)
            ->assertJsonPath('stats.balance', 3800)
            ->assertJsonPath('stats.operationsCount', 2)
            ->assertJsonCount(2, 'latestOperations')
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'stats' => [
                    'totalIncome',
                    'totalExpense',
                    'balance',
                    'operationsCount',
                ],
                'latestOperations',
            ]);
    }

    public function test_dashboard_contains_only_the_authenticated_users_data(): void
    {
        $user = $this->authenticatedUser();
        $otherUser = $this->authenticatedUser();

        Operation::create([
            'user_id' => $user->id,
            'type' => 'income',
            'amount' => 1000,
            'operation_date' => '2026-09-12',
            'description' => 'My income',
        ]);

        Operation::create([
            'user_id' => $otherUser->id,
            'type' => 'income',
            'amount' => 9000,
            'operation_date' => '2026-09-12',
            'description' => 'Other income',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('stats.totalIncome', 1000)
            ->assertJsonPath('stats.totalExpense', 0)
            ->assertJsonPath('stats.balance', 1000)
            ->assertJsonPath('stats.operationsCount', 1)
            ->assertJsonCount(1, 'latestOperations')
            ->assertJsonPath('latestOperations.0.description', 'My income');
    }

    public function test_dashboard_returns_latest_operations_first(): void
    {
        $user = $this->authenticatedUser();

        for ($i = 1; $i <= 7; $i++) {
            Operation::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => $i * 10,
                'operation_date' => "2026-09-0{$i}",
                'description' => "Operation {$i}",
            ]);
        }

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('stats.operationsCount', 7)
            ->assertJsonCount(5, 'latestOperations')
            ->assertJsonPath('latestOperations.0.description', 'Operation 7')
            ->assertJsonPath('latestOperations.1.description', 'Operation 6')
            ->assertJsonPath('latestOperations.4.description', 'Operation 3');
    }

    public function test_unauthenticated_user_cannot_view_dashboard(): void
    {
        $response = $this->getJson('/api/v1/dashboard');

        $response->assertUnauthorized();
    }

    public function test_dashboard_returns_zero_stats_when_user_has_no_operations(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('stats.totalIncome', 0)
            ->assertJsonPath('stats.totalExpense', 0)
            ->assertJsonPath('stats.balance', 0)
            ->assertJsonPath('stats.operationsCount', 0)
            ->assertJsonCount(0, 'latestOperations');
    }
}