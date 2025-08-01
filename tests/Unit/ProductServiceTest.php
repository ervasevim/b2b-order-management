<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
    }

    public function test_get_all_returns_products_and_uses_cache()
    {
        // Cache boşken veritabanından çeker
        Cache::shouldReceive('remember')
            ->once()
            ->with('products', 60, \Closure::class)
            ->andReturn(collect());

        $products = $this->productService->getAll();

        $this->assertIsIterable($products);
    }

    public function test_create_creates_product_and_forgets_cache()
    {
        Cache::shouldReceive('forget')->once()->with('products');

        $data = ['name' => 'Test Product', 'price' => 100, 'sku' => 'TESTSKU123', 'stock_quantity' => 1];

        $product = $this->productService->create($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_update_updates_product_and_forgets_cache()
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        Cache::shouldReceive('forget')->once()->with('products');

        $updatedProduct = $this->productService->update($product->id, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updatedProduct->name);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'New Name']);
    }

    public function test_update_throws_model_not_found_exception()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->productService->update(9999, ['name' => 'No Product']);
    }

    public function test_delete_deletes_product_and_forgets_cache()
    {
        $product = Product::factory()->create();

        Cache::shouldReceive('forget')->once()->with('products');

        $this->productService->delete($product->id);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_delete_throws_model_not_found_exception()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->productService->delete(9999);
    }
}
