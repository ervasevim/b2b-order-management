<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getAll()
    {
        return Cache::remember('products', 60, fn() => Product::all());
    }

    public function create(array $data): Product
    {
        $product = Product::create($data);
        Cache::forget('products');
        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        Cache::forget('products');
        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
        Cache::forget('products');
    }
}
