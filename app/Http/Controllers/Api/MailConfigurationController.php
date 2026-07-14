<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailConfigurationRequest;
use App\Services\MailConfigurationService;

class MailConfigurationController extends Controller
{
    public function __construct(
        private readonly MailConfigurationService $service
    ) {}

    public function show()
    {
        return response()->json(
            $this->service->get()
        );
    }

    public function update(
        MailConfigurationRequest $request
    ) {
        $this->service->update(
            $request->validated()
        );

        return response()->json([
            'message' => 'Mail configuration updated.',
        ]);
    }
}
