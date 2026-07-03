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
    public function __construct(private readonly LeaderOverviewDatePointDataProvider $dataProvider)
    {
    }
    /**
     * @param LeaderOverviewDatePointDataProvider $dataProvider
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
     * @param LeaderOverviewDatePointDataProvider $dataProvider
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
