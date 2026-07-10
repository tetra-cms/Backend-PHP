<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Получить все товары.
     */
    public function all(): Collection
    {
        return Product::query()
            ->with('category')
            ->orderBy('id')
            ->get();
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
                'image_url' => $data['image_url'],
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
                'image_url' => $data['image_url'],
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
