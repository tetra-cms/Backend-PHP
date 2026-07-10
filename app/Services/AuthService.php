<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly JwtService $jwtService,
    ) {}

    /**
     * Регистрация пользователя.
     */
    public function register(array $data, string $ip): array
    {
        if (User::where('username', $data['username'])->exists()) {
            throw ValidationException::withMessages([
                'username' => ['Username already exists.'],
            ]);
        }

        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email already exists.'],
            ]);
        }

        return DB::transaction(function () use ($data, $ip) {

            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => Role::USER,
                'reg_ip' => $ip,
            ]);

            $accessToken = $this->jwtService
                ->generateAccessToken($user);

            $refreshToken = $this->jwtService
                ->generateRefreshToken($user);

            RefreshToken::create([
                'user_id' => $user->id,
                'token' => $refreshToken,
                'expires_at' => now()
                    ->addDays(config('jwt.refresh_ttl')),
                'revoked' => false,
            ]);

            return [
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
            ];
        });
    }

    /**
     * Авторизация пользователя.
     */
    public function login(array $credentials): array
    {
        $user = User::where(
            'username',
            $credentials['username']
        )->first();

        if (!$user) {
            throw new AuthenticationException(
                'Invalid credentials.'
            );
        }

        if (!Hash::check(
            $credentials['password'],
            $user->password
        )) {
            throw new AuthenticationException(
                'Invalid credentials.'
            );
        }

        return DB::transaction(function () use ($user) {

            $accessToken = $this->jwtService
                ->generateAccessToken($user);

            $refreshToken = $this->jwtService
                ->generateRefreshToken($user);

            RefreshToken::create([
                'user_id' => $user->id,
                'token' => $refreshToken,
                'expires_at' => now()
                    ->addDays(config('jwt.refresh_ttl')),
                'revoked' => false,
            ]);

            return [
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
            ];
        });
    }

    /**
     * Обновить Access/Refresh Token.
     */
    public function refresh(string $refreshToken): array
    {
        $payload = $this->jwtService
            ->validateRefreshToken($refreshToken);

        $stored = RefreshToken::where(
            'token',
            $refreshToken
        )->first();

        if (!$stored) {
            throw new AuthenticationException(
                'Refresh token not found.'
            );
        }

        if ($stored->revoked) {
            throw new AuthenticationException(
                'Refresh token revoked.'
            );
        }

        if ($stored->expires_at->isPast()) {
            throw new AuthenticationException(
                'Refresh token expired.'
            );
        }

        $user = User::find($payload->sub);

        if (!$user) {
            throw new ModelNotFoundException(
                'User not found.'
            );
        }

        return DB::transaction(function () use (
            $stored,
            $user
        ) {

            $accessToken = $this->jwtService
                ->generateAccessToken($user);

            $newRefresh = $this->jwtService
                ->generateRefreshToken($user);

            $stored->update([
                'token' => $newRefresh,
                'expires_at' => now()
                    ->addDays(config('jwt.refresh_ttl')),
                'revoked' => false,
            ]);

            return [
                'accessToken' => $accessToken,
                'refreshToken' => $newRefresh,
            ];
        });
    }

    /**
     * Выход из системы.
     */
    public function logout(string $refreshToken): void
    {
        RefreshToken::where(
            'token',
            $refreshToken
        )->update([
            'revoked' => true,
        ]);
    }
}
