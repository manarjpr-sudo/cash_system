<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser(): User
    {
        return User::factory()->create([
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_can_get_main_income_categories(): void
    {
        $user = $this->authenticatedUser();

        Category::create([
            'name_ar' => 'راتب',
            'name_en' => 'Salary',
            'type' => 'income',
            'parent_id' => null,
        ]);

        Category::create([
            'name_ar' => 'طعام',
            'name_en' => 'Food',
            'type' => 'expense',
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/categories?type=income');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.name_ar', 'راتب')
            ->assertJsonPath('0.name_en', 'Salary')
            ->assertJsonPath('0.type', 'income')
            ->assertJsonPath('0.parent_id', null);
    }

    public function test_categories_endpoint_returns_only_main_categories_by_default(): void
    {
        $user = $this->authenticatedUser();

        $parent = Category::create([
            'name_ar' => 'راتب',
            'name_en' => 'Salary',
            'type' => 'income',
            'parent_id' => null,
        ]);

        Category::create([
            'name_ar' => 'راتب أساسي',
            'name_en' => 'Base Salary',
            'type' => 'income',
            'parent_id' => $parent->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/categories');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $parent->id);
    }

    public function test_authenticated_user_can_get_subcategories(): void
    {
        $user = $this->authenticatedUser();

        $parent = Category::create([
            'name_ar' => 'طعام',
            'name_en' => 'Food',
            'type' => 'expense',
            'parent_id' => null,
        ]);

        $child = Category::create([
            'name_ar' => 'مطاعم',
            'name_en' => 'Restaurants',
            'type' => 'expense',
            'parent_id' => $parent->id,
        ]);

        Category::create([
            'name_ar' => 'راتب',
            'name_en' => 'Salary',
            'type' => 'income',
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson("/api/v1/categories?parent_id={$parent->id}");

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $child->id)
            ->assertJsonPath('0.parent_id', $parent->id)
            ->assertJsonPath('0.type', 'expense');
    }

    public function test_unauthenticated_user_cannot_access_categories(): void
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_main_category(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/v1/categories', [
                'name_ar' => 'استثمار',
                'name_en' => 'Investment',
                'type' => 'income',
                'parent_id' => null,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name_ar', 'استثمار')
            ->assertJsonPath('name_en', 'Investment')
            ->assertJsonPath('type', 'income')
            ->assertJsonPath('parent_id', null);

        $this->assertDatabaseHas('categories', [
            'name_ar' => 'استثمار',
            'name_en' => 'Investment',
            'type' => 'income',
            'parent_id' => null,
        ]);
    }

    public function test_authenticated_user_can_create_subcategory(): void
    {
        $user = $this->authenticatedUser();

        $parent = Category::create([
            'name_ar' => 'طعام',
            'name_en' => 'Food',
            'type' => 'expense',
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/v1/categories', [
                'name_ar' => 'بقالة',
                'name_en' => 'Groceries',
                'type' => 'expense',
                'parent_id' => $parent->id,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name_ar', 'بقالة')
            ->assertJsonPath('parent_id', $parent->id)
            ->assertJsonPath('type', 'expense');

        $this->assertDatabaseHas('categories', [
            'name_ar' => 'بقالة',
            'name_en' => 'Groceries',
            'type' => 'expense',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_category_cannot_have_parent_with_different_type(): void
    {
        $user = $this->authenticatedUser();

        $incomeParent = Category::create([
            'name_ar' => 'راتب',
            'name_en' => 'Salary',
            'type' => 'income',
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/v1/categories', [
                'name_ar' => 'طعام',
                'name_en' => 'Food',
                'type' => 'expense',
                'parent_id' => $incomeParent->id,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'The parent category must have the same type.',
            ]);
    }

    public function test_category_cannot_be_its_own_parent_when_updated(): void
    {
        $user = $this->authenticatedUser();

        $category = Category::create([
            'name_ar' => 'طعام',
            'name_en' => 'Food',
            'type' => 'expense',
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson("/api/v1/categories/{$category->id}", [
                'name_ar' => 'طعام',
                'name_en' => 'Food',
                'type' => 'expense',
                'parent_id' => $category->id,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'A category cannot be its own parent.',
            ]);
    }

    public function test_authenticated_user_can_update_category(): void
    {
        $user = $this->authenticatedUser();

        $category = Category::create([
            'name_ar' => 'طعام',
            'name_en' => 'Food',
            'type' => 'expense',
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson("/api/v1/categories/{$category->id}", [
                'name_ar' => 'تسوق',
                'name_en' => 'Shopping',
                'type' => 'expense',
                'parent_id' => null,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('name_ar', 'تسوق')
            ->assertJsonPath('name_en', 'Shopping');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name_ar' => 'تسوق',
            'name_en' => 'Shopping',
            'type' => 'expense',
        ]);
    }

    public function test_authenticated_user_can_delete_category(): void
    {
        $user = $this->authenticatedUser();

        $category = Category::create([
            'name_ar' => 'مصروف آخر',
            'name_en' => 'Other Expense',
            'type' => 'expense',
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/categories/{$category->id}");

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Category deleted successfully.',
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}