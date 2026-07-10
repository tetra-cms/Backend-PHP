<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {}

    /**
     * GET /api/categories
     */
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            $this->categoryService->all()
        );
    }

    /**
     * GET /api/categories/{category}
     */
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource(
            $this->categoryService->find($category)
        );
    }

    /**
     * POST /api/admin/categories
     */
    public function store(
        CreateCategoryRequest $request,
    ): CategoryResource {

        return new CategoryResource(
            $this->categoryService->create(
                $request->validated()
            )
        );
    }

    /**
     * PUT /api/admin/categories/{category}
     */
    public function update(
        UpdateCategoryRequest $request,
        Category $category,
    ): CategoryResource {

        return new CategoryResource(
            $this->categoryService->update(
                $category,
                $request->validated()
            )
        );
    }

    /**
     * DELETE /api/admin/categories/{category}
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return response()->json([], 204);
    }
}
