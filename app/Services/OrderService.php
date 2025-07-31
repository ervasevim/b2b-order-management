<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getOrdersForUser(User $user)
    {
        if ($user->role === 'admin') {
            return Order::with('items.product')->get();
        }

        return $user->orders()->with('items.product')->get();
    }

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

    public function findOrderById(int $id): Order
    {
        return Order::with('products')->findOrFail($id);
    }
}
