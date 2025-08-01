<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Trait\HttpResponse;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();

        return $this->success(ProductResource::collection($products));
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return $this->success(new ProductResource($product), 'Ürün oluşturuldu!')
            ->setStatusCode(201);
    }


    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->update($id, $request->validated());

            return $this->success(new ProductResource($product), 'Ürün başarıyla güncellendi!')
                ->setStatusCode(201);
        } catch (ModelNotFoundException $e) {
            return $this->error([], 404, 'Ürün bulunamadı!', $e->getMessage());
        } catch (\Throwable $e) {
            return $this->error([], 500, 'Bir hata oluştu!', $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->delete($id);
        } catch (ModelNotFoundException $e) {
            return $this->error([], 404, 'Ürün bulunamadı!', $e->getMessage());
        }

        return $this->success([], 'Ürün başarıyla silindi!')
            ->setStatusCode(204);
    }
}
