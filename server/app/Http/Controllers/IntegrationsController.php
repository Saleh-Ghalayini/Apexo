<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\IntegrationsService;
use App\Http\Requests\UpdateIntegrationStatusRequest;

class IntegrationsController extends Controller
{
    use ResponseTrait;
    protected IntegrationsService $integrationsService;

    public function __construct(IntegrationsService $integrationsService)
    {
        $this->integrationsService = $integrationsService;
    }

    public function getIntegrations(Request $request)
    {
        $user = $request->user();
        $integrations = $this->integrationsService->getUserIntegrations($user);
        return $this->successResponse($integrations);
    }

    public function getProviders()
    {
        $providers = $this->integrationsService->getProviders();
        return $this->successResponse($providers);
    }

    public function connect(Request $request, $providerId)
    {
        $url = $this->integrationsService->getConnectUrl($providerId);
        return $this->successResponse(['url' => $url]);
    }

    public function updateStatus(UpdateIntegrationStatusRequest $request, $integrationId)
    {
        $user = $request->user();
        $status = $request->input('status');
        $success = $this->integrationsService->updateStatus($user, $integrationId, $status);
        if (!$success) return $this->errorResponse('Integration not found', 404);

        return $this->successResponse(['status' => $status]);
    }

    public function disconnect(Request $request, $integrationId)
    {
        $user = $request->user();
        $success = $this->integrationsService->disconnect($user, $integrationId);
        if (!$success) return $this->errorResponse('Integration not found', 404);

        return $this->successResponse(['status' => 'inactive']);
    }
}
