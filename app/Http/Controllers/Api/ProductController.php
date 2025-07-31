<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Trait\HttpResponse;
use App\Models\Product;
use App\Services\ProductService;
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

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->update($product, $request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);
        return response()->json(null, 204);
    }
}
