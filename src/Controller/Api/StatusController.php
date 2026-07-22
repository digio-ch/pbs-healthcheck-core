<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Service\StatusMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends AbstractController
{
    private StatusMessageService $service;

    public function __construct(StatusMessageService $service)
    {
        $this->service = $service;
    }

    /**
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
