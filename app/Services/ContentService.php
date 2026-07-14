<?php

namespace App\Services;

use App\Models\Content;
use Illuminate\Http\Request;

class ContentService
{
    public function all(Request $request)
    {
        $query = Content::query();

        if ($request->filled('search')) {

            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where('route', 'like', "%{$search}%");
            });
        }

        if (
            !$request->has('page') &&
            !$request->has('perPage')
        ) {
            return $query
                ->orderBy('route')
                ->get();
        }

        $contents = $query
            ->orderBy('route')
            ->paginate(
                $request->integer('perPage', 15)
            );

        return response()->json([
            'data' => $contents->items(),
            'pagination' => [
                'page' => $contents->currentPage(),
                'perPage' => $contents->perPage(),
                'total' => $contents->total(),
                'lastPage' => $contents->lastPage(),
            ],
        ]);
    }

    public function getById(int $id): ?Content
    {
        return Content::find($id);
    }

    public function getByRoute(string $route): ?Content
    {
        return Content::where('route', $route)
            ->first();
    }

    public function create(array $data): Content
    {
        return Content::create($data);
    }

    public function update(Content $content, array $data): Content
    {
        $content->update($data);

        return $content->refresh();
    }

    public function delete(Content $content): void
    {
        $content->delete();
    }
}
