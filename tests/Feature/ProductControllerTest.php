<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // User login için passport kullanımı
        Passport::actingAs(User::factory()->create());
    }

    public function test_user_can_list_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_user_can_create_product()
    {
        $payload = [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
            'price' => 199.99,
            'stock_quantity' => 10,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Product']);
    }

    public function test_user_can_update_product()
    {
        $product = Product::factory()->create();

        $payload = [
            'name' => 'Updated Product',
            'price' => 299.99,
            'stock' => 5,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Updated Product']);
    }

    public function test_user_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
    }

    public function test_update_nonexistent_product_returns_404()
    {
        $payload = [
            'name' => 'Does Not Exist',
            'price' => 100,
            'stock' => 1,
        ];

        $response = $this->putJson('/api/products/999999', $payload);

        $response->assertStatus(404);
    }
}
