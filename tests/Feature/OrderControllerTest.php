<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class
OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_orders()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Order::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        $this->assertCount(3, $response['data']);
    }

    public function test_user_can_store_an_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $product = Product::factory()->create([
            'price' => 100,
            'stock_quantity' => 10
        ]);

        $payload = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]

        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Sipariş oluşturuldu.']);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
    }

    public function test_user_can_view_own_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
    }
}


