<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Carbon;
use UnexpectedValueException;

class JwtService
{
    private string $secret;

    private string $algorithm = 'HS256';

    public function __construct()
    {
        $this->secret = config('jwt.secret');
    }

    /**
     * Создать Access Token.
     */
    public function generateAccessToken(User $user): string
    {
        $now = Carbon::now();

        $payload = [
            'iss' => config('app.name'),
            'sub' => $user->id,
            'username' => $user->username,
            'role' => $user->role->value,
            'type' => 'access',
            'iat' => $now->timestamp,
            'exp' => $now
                ->copy()
                ->addMinutes(config('jwt.access_ttl'))
                ->timestamp,
        ];

        return JWT::encode(
            $payload,
            $this->secret,
            $this->algorithm
        );
    }

    /**
     * Создать Refresh Token.
     */
    public function generateRefreshToken(User $user): string
    {
        $now = Carbon::now();

        $payload = [
            'iss' => config('app.name'),
            'sub' => $user->id,
            'type' => 'refresh',
            'iat' => $now->timestamp,
            'exp' => $now
                ->copy()
                ->addDays(config('jwt.refresh_ttl'))
                ->timestamp,
        ];

        return JWT::encode(
            $payload,
            $this->secret,
            $this->algorithm
        );
    }

    /**
     * Разобрать JWT.
     */
    public function parse(string $token): object
    {
        return JWT::decode(
            $token,
            new Key($this->secret, $this->algorithm)
        );
    }

    /**
     * Проверить Access Token.
     */
    public function validateAccessToken(string $token): object
    {
        $payload = $this->parse($token);

        if (($payload->type ?? null) !== 'access') {
            throw new UnexpectedValueException('Invalid token type.');
        }

        return $payload;
    }

    /**
     * Проверить Refresh Token.
     */
    public function validateRefreshToken(string $token): object
    {
        $payload = $this->parse($token);

        if (($payload->type ?? null) !== 'refresh') {
            throw new UnexpectedValueException('Invalid token type.');
        }

        return $payload;
    }

    /**
     * Проверить срок действия токена.
     */
    public function isExpired(string $token): bool
    {
        try {
            $this->parse($token);

            return false;
        } catch (ExpiredException) {
            return true;
        }
    }

    /**
     * Проверить валидность JWT.
     */
    public function isValid(string $token): bool
    {
        try {
            $this->parse($token);

            return true;
        } catch (
            ExpiredException|
            SignatureInvalidException|
            UnexpectedValueException
        ) {
            return false;
        }
    }
}
