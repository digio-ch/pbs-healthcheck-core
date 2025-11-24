<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Service\DataProvider\MembersGroupDateDataProvider;
use App\Service\DataProvider\MembersGroupDateRangeDataProvider;
use App\Service\DataProvider\RoleOverviewDateRangeDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class RoleOverviewController extends AbstractController
{
    /**
     * @param RoleOverviewDateRangeDataProvider $roleOverviewDateRangeDataProvider
     * @param DateAndDateRangeRequestData $dateAndDateRangeRequestData
     * @param WidgetRequestData $widgetRequestData
     * @return JsonResponse
     */
    public function getRoleOverview(
        RoleOverviewDateRangeDataProvider $roleOverviewDateRangeDataProvider,
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $widgetRequestData->getGroup());
        $result = $roleOverviewDateRangeDataProvider->getData(
            $widgetRequestData->getGroup(),
            $dateAndDateRangeRequestData->getFrom()->format('Y-m-d'),
            $dateAndDateRangeRequestData->getTo()->format('Y-m-d'),
        );

        return $this->json($result);
    }

    /**
     * @param RoleOverviewDateRangeDataProvider $roleOverviewDateRangeDataProvider
     * @param DateAndDateRangeRequestData $dateAndDateRangeRequestData
     * @param WidgetOfDepartmentRequestData $widgetRequestData
     * @return JsonResponse
     */
    public function getRoleOverviewOfDepartment(
        RoleOverviewDateRangeDataProvider $roleOverviewDateRangeDataProvider,
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $widgetRequestData->getGroup());
        $result = $roleOverviewDateRangeDataProvider->getData(
            $widgetRequestData->getDepartment(),
            $dateAndDateRangeRequestData->getFrom()->format('Y-m-d'),
            $dateAndDateRangeRequestData->getTo()->format('Y-m-d'),
        );

        return $this->json($result);
    }
}
