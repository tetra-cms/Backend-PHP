<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Получить всех пользователей.
     */
    public function all(Request $request): Collection | array
    {
        $query = User::query();

        if ($request->filled('search')) {

            $search = trim($request->string('search'));

            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
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

        $users = $query->paginate(
            $request->integer('perPage', 15)
        );

        return [
            'data' => $users->items(),
            'pagination' => [
                'page' => $users->currentPage(),
                'perPage' => $users->perPage(),
                'total' => $users->total(),
                'lastPage' => $users->lastPage(),
            ],
        ];
    }

    /**
     * Получить пользователя по ID.
     */
    public function find(int $id): User
    {
        return User::query()->findOrFail($id);
    }

    /**
     * Обновить пользователя.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            $user->update([
                'username' => $data['username'],
                'email' => $data['email'],
                'role' => $data['role'],
            ]);

            return $user->fresh();
        });
    }

    /**
     * Удалить пользователя.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {

            // Удаляем все Refresh Token пользователя
            $user->refreshTokens()->delete();

            // Если в будущем будут другие связанные сущности,
            // их можно удалить здесь.

            $user->delete();
        });
    }
}
