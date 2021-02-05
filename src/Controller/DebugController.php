<?php

namespace App\Controller;

use App\Service\Aggregator\DemographicCampAggregator;
use App\Service\Aggregator\DemographicGroupAggregator;
use Digio\Logging\GelfLogger;
use Digio\Logging\Messages\ExceptionLogMessage;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class DebugController extends AbstractController
{
    /**
     * @param KernelInterface $kernel
     * @return Response
     * @throws Exception
     */
    public function debugInsertCommand(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            [
                'command' => 'app:import-data'
            ]
        );

        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);

        return new Response("");
    }

    /**
     * @param DemographicCampAggregator $demographicCampAggregator
     * @return JsonResponse
     * @throws Exception
     */
    public function runCampAggregation(DemographicCampAggregator $demographicCampAggregator)
    {
        $demographicCampAggregator->aggregate();
        return $this->json('ok');
    }

    /**
     * @param DemographicGroupAggregator $demographicGroupAggregator
     * @return JsonResponse
     * @throws Exception
     */
    public function runGroupAggregator(DemographicGroupAggregator $demographicGroupAggregator)
    {
        $demographicGroupAggregator->aggregate();
        return $this->json('ok');
    }

    public function testLogger(GelfLogger $logger)
    {
        $logger->info(new ExceptionLogMessage(new Exception('test')));
        return $this->json('ok');
    }

    public function emptyTestRoute()
    {
        return $this->json('ok');
    }
}
