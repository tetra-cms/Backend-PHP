<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Получить всех пользователей.
     */
    public function all(): Collection
    {
        return User::query()
            ->orderBy('id')
            ->get();
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
