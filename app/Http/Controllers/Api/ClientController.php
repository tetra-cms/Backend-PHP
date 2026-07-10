<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService $clientService,
    ) {}

    /**
     * GET /api/clients
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return ClientResource::collection(
            $this->clientService->all($request->user())
        );
    }

    /**
     * GET /api/clients/{client}
     */
    public function show(
        Request $request,
        Client $client,
    ): ClientResource {

        return new ClientResource(
            $this->clientService->find(
                $request->user(),
                $client
            )
        );
    }

    /**
     * POST /api/clients
     */
    public function store(
        CreateClientRequest $request,
    ): ClientResource {

        return new ClientResource(
            $this->clientService->create(
                $request->user(),
                $request->validated()
            )
        );
    }

    /**
     * PUT /api/clients/{client}
     */
    public function update(
        UpdateClientRequest $request,
        Client $client,
    ): ClientResource {

        return new ClientResource(
            $this->clientService->update(
                $request->user(),
                $client,
                $request->validated()
            )
        );
    }

    /**
     * DELETE /api/clients/{client}
     */
    public function destroy(
        Request $request,
        Client $client,
    ): JsonResponse {

        $this->clientService->delete(
            $request->user(),
            $client
        );

        return response()->json([], 204);
    }
}
