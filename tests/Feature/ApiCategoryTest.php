<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_guest_cannot_get_categories(): void
    {
        $this->getJson('/api/v1/categories')->assertUnauthorized();
    }

    public function test_expired_token_cannot_get_categories(): void
    {
        Carbon::setTestNow(now());

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['categories:read']);

        // Majukan waktu melewati batas kedaluwarsa token (1 hari)
        Carbon::setTestNow(now()->addDays(2));

        $this->getJson('/api/v1/categories')->assertUnauthorized();

        Carbon::setTestNow();
    }

    public function test_token_without_ability_is_forbidden(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['products:read']);

        $this->getJson('/api/v1/categories')->assertForbidden();
    }

    public function test_can_get_categories_with_valid_token_and_ability(): void
    {
        Category::create(['name' => 'Makanan']);
        Category::create(['name' => 'Minuman']);

        Sanctum::actingAs(User::factory()->create(), ['categories:read']);

        $response = $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name'],
                ],
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_filter_categories_by_query(): void
    {
        Category::create(['name' => 'Makanan']);
        Category::create(['name' => 'Minuman']);

        Sanctum::actingAs(User::factory()->create(), ['categories:read']);

        $response = $this->getJson('/api/v1/categories?q=Minum')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Minuman', $response->json('data.0.name'));
    }

    public function test_can_sort_categories(): void
    {
        Category::create(['name' => 'Buku']);
        Category::create(['name' => 'Alat Tulis']);

        Sanctum::actingAs(User::factory()->create(), ['categories:read']);

        $response = $this->getJson('/api/v1/categories?sort=-name')
            ->assertOk();

        $this->assertEquals('Buku', $response->json('data.0.name'));
        $this->assertEquals('Alat Tulis', $response->json('data.1.name'));
    }
}
