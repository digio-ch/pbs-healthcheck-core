<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Security\PermissionType;
use App\Service\DataProvider\DemographicStatsDataProvider;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembersBirthyearController extends AbstractController
{
    public function __construct(private readonly \App\Service\DataProvider\DemographicStatsDataProvider $demographicStatsProvider)
    {
    }
    /***
     * @param DateRequestData $dateRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param DemographicStatsDataProvider $demographicStatsProvider
     * @return JsonResponse
     * @throws Exception
     */
    public function getMembersBirthyearData(
        DateRequestData $dateRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());

        $data = $this->demographicStatsProvider->getDataForDepartment(
            $widgetRequestData->getGroup(),
            $dateRequestData->getDate(),
            $widgetRequestData->getPeopleTypes(),
            $widgetRequestData->getGroupTypes()
        );
        return $this->json($data);
    }

    /**
     * @param DateRequestData $dateRequestData
     * @param WidgetOfDepartmentRequestData $widgetRequestData
     * @param DemographicStatsDataProvider $demographicStatsProvider
     * @return JsonResponse
     * @throws Exception
     */
    public function getMembersBirthyearDataOfDepartment(
        DateRequestData $dateRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = $this->demographicStatsProvider->getDataForDepartment(
            $widgetRequestData->getDepartment(),
            $dateRequestData->getDate(),
            $widgetRequestData->getPeopleTypes(),
            $widgetRequestData->getGroupTypes()
        );
        return $this->json($data);
    }
}
