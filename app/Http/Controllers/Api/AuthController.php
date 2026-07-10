<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * POST /api/auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $tokens = $this->authService->register(
            $request->validated(),
            $request->ip(),
        );

        return response()->json($tokens, 201);
    }

    /**
     * POST /api/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $tokens = $this->authService->login(
            $request->validated(),
        );

        return response()->json($tokens);
    }

    /**
     * GET /api/auth/me
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * POST /api/auth/refresh
     */
    public function refresh(RefreshRequest $request): JsonResponse
    {
        $tokens = $this->authService->refresh(
            $request->validated('refreshToken'),
        );

        return response()->json($tokens);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(RefreshRequest $request): JsonResponse
    {
        $this->authService->logout(
            $request->validated('refreshToken'),
        );

        return response()->json([], 204);
    }
}
