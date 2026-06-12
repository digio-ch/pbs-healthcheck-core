<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Security\PermissionType;
use App\Service\DataProvider\DemographicCampDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CampController extends AbstractController
{
    /**
     * @param DateRangeRequestData $dateRangeRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param DemographicCampDataProvider $demographicCampDataProvider
     * @return JsonResponse
     */
    public function getDemographicCampData(
        DateRangeRequestData $dateRangeRequestData,
        WidgetRequestData $widgetRequestData,
        DemographicCampDataProvider $demographicCampDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());

        $data = $demographicCampDataProvider->getData(
            $widgetRequestData->getGroup(),
            $dateRangeRequestData->getFrom()->format('Y-m-d'),
            $dateRangeRequestData->getTo()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );

        return $this->json($data);
    }

    /**
     * @param DateRangeRequestData $dateRangeRequestData
     * @param WidgetOfDepartmentRequestData $widgetRequestData
     * @param DemographicCampDataProvider $demographicCampDataProvider
     * @return JsonResponse
     */
    public function getDemographicCampDataOfDepartment(
        DateRangeRequestData $dateRangeRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData,
        DemographicCampDataProvider $demographicCampDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = $demographicCampDataProvider->getData(
            $widgetRequestData->getDepartment(),
            $dateRangeRequestData->getFrom()->format('Y-m-d'),
            $dateRangeRequestData->getTo()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );

        return $this->json($data);
    }
}
