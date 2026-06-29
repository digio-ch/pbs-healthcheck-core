<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Security\PermissionType;
use App\Service\DataProvider\LeaderOverviewDatePointDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class LeaderOverviewController extends AbstractController
{
    public function __construct(private readonly \App\Service\DataProvider\LeaderOverviewDatePointDataProvider $dataProvider)
    {
    }
    /**
     * @param DateRequestData $dateRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param LeaderOverviewDatePointDataProvider $dataProvider
     * @return JsonResponse
     */
    public function getLeaderOverviewData(
        DateRequestData $dateRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());

        $data = $this->dataProvider->getData(
            $widgetRequestData->getGroup(),
            $dateRequestData->getDate()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );

        return $this->json($data);
    }

    /**
     * @param DateRequestData $dateRequestData
     * @param WidgetOfDepartmentRequestData $widgetRequestData
     * @param LeaderOverviewDatePointDataProvider $dataProvider
     * @return JsonResponse
     *
     */
    public function getLeaderOverviewDataOfDepartment(
        DateRequestData $dateRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = $this->dataProvider->getData(
            $widgetRequestData->getDepartment(),
            $dateRequestData->getDate()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );

        return $this->json($data);
    }
}
