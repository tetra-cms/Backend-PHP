<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    /**
     * Получить все категории.
     */
    public function all(Request $request): Collection | array
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = trim($request->string('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $query->orderBy('id');

        if (
            !$request->has('page') &&
            !$request->has('perPage') &&
            !$request->has('search')
        ) {
            return $query->get();
        }

        $categories = $query->paginate(
            $request->integer('perPage', 15)
        );

        return [
            'data' => $categories->items(),
            'pagination' => [
                'page' => $categories->currentPage(),
                'perPage' => $categories->perPage(),
                'total' => $categories->total(),
                'lastPage' => $categories->lastPage(),
            ],
        ];
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
