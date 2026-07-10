<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    /**
     * GET /api/admin/users
     */
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(
            $this->userService->all()
        );
    }

    /**
     * GET /api/admin/users/{id}
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * GET /api/users/profile
     */
    public function profile(Request $request): UserResource
    {
        return new UserResource(
            $request->user()
        );
    }

    /**
     * PUT /api/admin/users/{id}
     */
    public function update(
        UpdateUserRequest $request,
        User $user,
    ): UserResource {

        $user = $this->userService->update(
            $user,
            $request->validated(),
        );

        return new UserResource($user);
    }

    /**
     * DELETE /api/admin/users/{id}
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return response()->json([], 204);
    }
}
