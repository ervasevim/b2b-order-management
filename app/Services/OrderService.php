<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderService
 *
 * This service handles business logic related to orders such as creating,
 * retrieving, and finding orders by ID.
 *
 * @package App\Services
 */
class OrderService
{
    /**
     * Retrieve orders for a given user.
     * Admin users receive all orders; others receive only their own.
     *
     * @param User $user The user whose orders are to be retrieved.
     * @return Collection A collection of Order models.
     */
    public function getOrdersForUser(User $user): Collection
    {
        if ($user->role === 'admin') {
            return Order::with('items.product')->get();
        }

        return $user->orders()->with('items.product')->get();
    }

    /**
     * Create a new order with associated products.
     * Calculates total price based on products and quantities.
     *
     * @param array $data The order data, including product IDs and quantities.
     * @return Order The newly created Order model.
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'user_id' => auth()->id()
            ]);

            $totalPrice = 0;

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unitPrice = $product->price;
                $quantity = $item['quantity'];

                $order->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $totalPrice += $unitPrice * $quantity;
                $order->update(['total_price' => $totalPrice]);

                $order->load('products');
            }

            return $order;
        });
    }

    /**
     * Find a specific order by its ID with related products.
     *
     * @param int $id The ID of the order.
     * @return Order The corresponding Order model.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrderById(int $id): Order
    {
        return Order::with('products')->findOrFail($id);
    }
}
