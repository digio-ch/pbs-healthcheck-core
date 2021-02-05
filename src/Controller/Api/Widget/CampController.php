<?php

namespace App\Controller\Api\Widget;

use App\DTO\Model\WidgetControllerData\DateRangeRequestData;
use App\Service\DataProvider\DemographicCampDataProvider;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\JsonResponse;

class CampController extends WidgetController
{
    /**
     * @param DateRangeRequestData $requestData
     * @param DemographicCampDataProvider $demographicCampDataProvider
     * @return JsonResponse
     * @throws DBALException
     */
    public function getDemographicCampData(
        DateRangeRequestData $requestData,
        DemographicCampDataProvider $demographicCampDataProvider
    ) {
        $data = $demographicCampDataProvider->getData(
            $requestData->getGroup(),
            $requestData->getFrom()->format('Y-m-d'),
            $requestData->getTo()->format('Y-m-d'),
            $requestData->getGroupTypes(),
            $requestData->getPeopleTypes()
        );

        return $this->json($data);
    }
}
