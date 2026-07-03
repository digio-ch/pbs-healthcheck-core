<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Logger\AppLogger;
use App\Service\Logger\Messages\ExceptionLogMessage;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DebugController extends AbstractController
{
    public function __construct(private readonly AppLogger $logger)
    {
    }

    public function testLogger(): JsonResponse
    {
        $this->logger->info(new ExceptionLogMessage(new Exception('test')));
        return $this->json('ok');
    }

    public function emptyTestRoute(): JsonResponse
    {
        return $this->json('ok');
    }
}
