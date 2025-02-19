<?php

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Service\StatusMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends AbstractController
{
    /**
     * @var StatusMessageService $service
     */
    private StatusMessageService $service;

    /**
     * @param StatusMessageService $service
     */
    public function __construct(StatusMessageService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ApiException
     */
    public function getStatusMessage(
        Request $request
    ): Response {
        $lang = $request->getLocale();

        $state = $this->service->getStatus($lang);
        return $this->json($state);
    }
}
