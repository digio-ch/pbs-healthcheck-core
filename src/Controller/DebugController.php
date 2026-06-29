<?php

namespace App\Controller;

use App\Service\Logger\Messages\ExceptionLogMessage;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DebugController extends AbstractController
{
    public function __construct(private readonly \App\Service\Logger\GelfLogger $logger)
    {
    }

    public function testLogger()
    {
        $this->logger->info(new ExceptionLogMessage(new Exception('test')));
        return $this->json('ok');
    }

    public function emptyTestRoute()
    {
        return $this->json('ok');
    }
}
