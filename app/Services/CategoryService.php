<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    /**
     * Получить все категории.
     */
    public function all(): Collection
    {
        return Category::query()
            ->orderBy('id')
            ->get();
    }

    /**
     * Получить категорию.
     */
    public function find(Category $category): Category
    {
        return $category;
    }

    /**
     * Создать категорию.
     */
    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {

            return Category::create([
                'name' => $data['name'],
                'title' => $data['title'],
                'icon_url' => $data['icon_url'],
            ]);

        });
    }

    /**
     * Обновить категорию.
     */
    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {

            $category->update([
                'name' => $data['name'],
                'title' => $data['title'],
                'icon_url' => $data['icon_url'],
            ]);

            return $category->fresh();

        });
    }

    /**
     * Удалить категорию.
     */
    public function delete(Category $category): void
    {
        DB::transaction(function () use ($category) {

            $category->delete();

        });
    }
}
