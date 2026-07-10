<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ClientService
{
    /**
     * Получить всех клиентов текущего пользователя.
     */
    public function all(User $user): Collection
    {
        return Client::query()
            ->where('user_id', $user->id)
            ->orderBy('id')
            ->get();
    }

    /**
     * Получить клиента текущего пользователя.
     */
    public function find(User $user, Client $client): Client
    {
        $this->ensureOwner($user, $client);

        return $client;
    }

    /**
     * Создать клиента.
     */
    public function create(User $user, array $data): Client
    {
        return DB::transaction(function () use ($user, $data) {

            return Client::create([
                'user_id' => $user->id,
                'fcs' => $data['fcs'],
                'city' => $data['city'],
                'address' => $data['address'],
                'phone' => $data['phone'],
            ]);

        });
    }

    /**
     * Обновить клиента.
     */
    public function update(User $user, Client $client, array $data): Client
    {
        $this->ensureOwner($user, $client);

        return DB::transaction(function () use ($client, $data) {

            $client->update([
                'fcs' => $data['fcs'],
                'city' => $data['city'],
                'address' => $data['address'],
                'phone' => $data['phone'],
            ]);

            return $client->fresh();

        });
    }

    /**
     * Удалить клиента.
     */
    public function delete(User $user, Client $client): void
    {
        $this->ensureOwner($user, $client);

        DB::transaction(function () use ($client) {

            $client->delete();

        });
    }

    /**
     * Проверяет, принадлежит ли клиент пользователю.
     *
     * @throws AuthorizationException
     */
    private function ensureOwner(User $user, Client $client): void
    {
        if ($client->user_id !== $user->id) {
            throw new AuthorizationException(
                'Вы не можете управлять этим клиентом.'
            );
        }
    }
}
