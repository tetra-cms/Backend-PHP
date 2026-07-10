<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    /**
     * Обработка входящего запроса.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (
            $user->role !== Role::EMPLOYEE &&
            $user->role !== Role::ADMIN
        ) {
            return response()->json([
                'message' => 'Forbidden.',
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
