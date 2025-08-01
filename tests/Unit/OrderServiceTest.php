<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }

    public function test_get_orders_for_admin_returns_all_orders()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'customer']);

        // Her iki kullanıcı için sipariş oluştur
        Order::factory()->count(2)->create(['user_id' => $user->id]);
        Order::factory()->count(3)->create(['user_id' => $admin->id]);

        $orders = $this->orderService->getOrdersForUser($admin);

        // Admin tüm siparişleri görmeli
        $this->assertCount(5, $orders);
    }

    public function test_get_orders_for_customer_returns_only_own_orders()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        Order::factory()->count(2)->create(['user_id' => $customer->id]);
        Order::factory()->count(3)->create(['user_id' => $admin->id]);

        $orders = $this->orderService->getOrdersForUser($customer);

        // Customer sadece kendi siparişlerini görmeli
        $this->assertCount(2, $orders);
        foreach ($orders as $order) {
            $this->assertEquals($customer->id, $order->user_id);
        }
    }

    public function test_create_order_creates_order_with_items_and_calculates_total_price()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->be($user); // auth()->id() için

        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 50]);

        $data = [
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 3],
            ],
        ];

        $order = $this->orderService->createOrder($data);

        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(100 * 2 + 50 * 3, $order->total_price);

        $this->assertCount(2, $order->products);
        $this->assertEquals($product1->id, $order->products[0]->id);
        $this->assertEquals($product2->id, $order->products[1]->id);
    }

    public function test_find_order_by_id_returns_order()
    {
        $order = Order::factory()->create();

        $foundOrder = $this->orderService->findOrderById($order->id);

        $this->assertEquals($order->id, $foundOrder->id);
    }

    public function test_find_order_by_id_throws_exception_when_not_found()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->orderService->findOrderById(9999); // olmayan id
    }
}
