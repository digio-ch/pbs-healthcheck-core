<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Service\DataProvider\DemographicCampDataProvider;
use App\Service\Security\PermissionVoter;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CampController extends AbstractController
{
    /**
     * @param DateRangeRequestData $dateRangeRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param DemographicCampDataProvider $demographicCampDataProvider
     * @return JsonResponse
     * @throws DBALException
     */
    public function getDemographicCampData(
        DateRangeRequestData $dateRangeRequestData,
        WidgetRequestData $widgetRequestData,
        DemographicCampDataProvider $demographicCampDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $widgetRequestData->getGroup());

        $data = $demographicCampDataProvider->getData(
            $widgetRequestData->getGroup(),
            $dateRangeRequestData->getFrom()->format('Y-m-d'),
            $dateRangeRequestData->getTo()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );

        return $this->json($data);
    }
}
