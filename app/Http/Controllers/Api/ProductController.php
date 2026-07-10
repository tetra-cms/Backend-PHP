<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    /**
     * GET /api/products
     */
    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->productService->all()
        );
    }

    /**
     * GET /api/products/{product}
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource(
            $this->productService->find($product)
        );
    }

    /**
     * POST /api/employee/products
     */
    public function store(
        CreateProductRequest $request,
    ): ProductResource {

        return new ProductResource(
            $this->productService->create(
                $request->validated()
            )
        );
    }

    /**
     * PUT /api/employee/products/{product}
     */
    public function update(
        UpdateProductRequest $request,
        Product $product,
    ): ProductResource {

        return new ProductResource(
            $this->productService->update(
                $product,
                $request->validated()
            )
        );
    }

    /**
     * DELETE /api/admin/products/{product}
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return response()->json([], 204);
    }
}
