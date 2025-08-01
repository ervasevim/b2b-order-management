<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Trait\HttpResponse;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly ProductService $productService)
    {
    }

    /**
     * Retrieve a list of all products.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();

        return $this->success(ProductResource::collection($products));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param StoreRequest $request Validated request containing product data.
     * @return JsonResponse JSON response with created product.
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return $this->success(new ProductResource($product), 'Product created successfully!')
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified product.
     *
     * @param UpdateRequest $request Validated request containing updated product data.
     * @param int $id The ID of the product to update.
     * @return JsonResponse JSON response with updated product or error.
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->update($id, $request->validated());

            return $this->success(new ProductResource($product), 'Product updated successfully!')
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (ModelNotFoundException $e) {
            return $this->error([], Response::HTTP_NOT_FOUND, 'Product not found!', $e->getMessage());
        } catch (\Exception $e) {
            return $this->error([], Response::HTTP_INTERNAL_SERVER_ERROR, 'An unexpected error occurred!', $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param int $id The ID of the product to delete.
     * @return JsonResponse JSON response indicating success or failure.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->delete($id);
        } catch (ModelNotFoundException $e) {
            return $this->error([], Response::HTTP_NOT_FOUND, 'Product not found!', $e->getMessage());
        }

        return $this->success([], 'Product deleted successfully!')
            ->setStatusCode(204);
    }
}
