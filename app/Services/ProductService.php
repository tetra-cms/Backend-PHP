<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Получить все товары.
     */
    public function all(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {

            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->integer('category_id')
            );
        }

        if (
            !$request->has('page') &&
            !$request->has('perPage')
        ) {
            return $query
                ->orderByDesc('id')
                ->get();
        }

        $products = $query
            ->orderByDesc('id')
            ->paginate(
                $request->integer('perPage', 15)
            );

        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'page' => $products->currentPage(),
                'perPage' => $products->perPage(),
                'total' => $products->total(),
                'lastPage' => $products->lastPage(),
            ],
        ]);
    }

    /**
     * Получить товар.
     */
    public function find(Product $product): Product
    {
        return $product->load('category');
    }

    /**
     * Создать товар.
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {

            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'supply_quantum' => $data['supply_quantum'],
                'category_id' => $data['category_id'],
            ]);

            return $product->load('category');
        });
    }

    /**
     * Обновить товар.
     */
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {

            $product->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'supply_quantum' => $data['supply_quantum'],
                'category_id' => $data['category_id'],
            ]);

            return $product->fresh()->load('category');
        });
    }

    /**
     * Удалить товар.
     */
    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->delete();
        });
    }
}
