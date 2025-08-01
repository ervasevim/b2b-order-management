<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

/**
 * Class ProductService
 *
 * Handles product-related operations such as retrieval, creation,
 * update, and deletion. Caches the product list for performance.
 *
 * @package App\Services
 */
class ProductService
{
    /**
     * Retrieve all products from cache or database.
     * The product list is cached for 60 minutes.
     *
     * @return \Illuminate\Database\Eloquent\Collection<Product>
     */
    public function getAll()
    {
        return Cache::remember('products', 60, fn() => Product::all());
    }

    /**
     * Create a new product and clear the cache.
     *
     * @param array $data The product data (name, price, etc.).
     * @return Product The newly created product instance.
     */
    public function create(array $data): Product
    {
        $product = Product::create($data);
        Cache::forget('products');
        return $product;
    }

    /**
     * Update an existing product by ID and clear the cache.
     *
     * @param int $id The ID of the product to update.
     * @param array $data The new product data.
     * @return Product The updated product instance.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If product not found.
     */
    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);

        $product->update($data);
        Cache::forget('products');
        return $product;
    }

    /**
     * Delete a product by ID and clear the cache.
     *
     * @param int $id The ID of the product to delete.
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If product not found.
     */
    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);

        $product->delete();
        Cache::forget('products');
    }
}
