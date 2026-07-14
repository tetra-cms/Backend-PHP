<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Content\CreateContentRequest;
use App\Http\Requests\Content\UpdateContentRequest;
use App\Http\Resources\ContentResource;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends Controller
{
    public function __construct(
        private readonly ContentService $service
    ) {}

    public function index(Request $request)
    {
        $result = $this->service->all($request);

        return $result instanceof JsonResponse
            ? $result
            : ContentResource::collection($result);
    }

    public function show(string $route)
    {
        $content = $this->service->getByRoute($route);

        abort_if(!$content, Response::HTTP_NOT_FOUND);

        return new ContentResource($content);
    }

    public function getById(int $id)
    {
        $content = $this->service->getById($id);

        abort_if(!$content, Response::HTTP_NOT_FOUND);

        return new ContentResource($content);
    }

    public function route(Request $request)
    {
        $content = $this->service->getByRoute(
            $request->string('route')
        );

        abort_if(!$content, Response::HTTP_NOT_FOUND);

        return new ContentResource($content);
    }

    public function store(CreateContentRequest $request)
    {
        return new ContentResource(
            $this->service->create(
                $request->validated()
            )
        );
    }

    public function update(
        UpdateContentRequest $request,
        int $id
    ) {
        $content = $this->service->getById($id);

        abort_if(!$content, Response::HTTP_NOT_FOUND);

        return new ContentResource(
            $this->service->update(
                $content,
                $request->validated()
            )
        );
    }

    public function destroy(int $id)
    {
        $content = $this->service->getById($id);

        abort_if(!$content, Response::HTTP_NOT_FOUND);

        $this->service->delete($content);

        return response()->noContent();
    }
}
