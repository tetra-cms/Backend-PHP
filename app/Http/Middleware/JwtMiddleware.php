<?php

namespace App\Http\Middleware;

use App\Models\User;
use Firebase\JWT\ExpiredException;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->unauthorized('Missing access token');
        }

        try {
            $payload = app(\App\Services\JwtService::class)
                ->validateAccessToken($token);

            $user = User::find($payload->sub);

            if (!$user) {
                return $this->unauthorized('User not found');
            }

            $request->setUserResolver(fn () => $user);

            return $next($request);

        } catch (ExpiredException) {

            return $this->unauthorized('Access token expired');

        } catch (Throwable $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'class' => get_class($e),
            ], 500);
        }
    }

    private function unauthorized(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }
}
