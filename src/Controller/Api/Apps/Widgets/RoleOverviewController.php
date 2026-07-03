<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Security\PermissionType;
use App\Service\DataProvider\RoleOverviewDateRangeDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class RoleOverviewController extends AbstractController
{
    public function __construct(private readonly RoleOverviewDateRangeDataProvider $roleOverviewDateRangeDataProvider)
    {
    }

    /**
     * @param DateAndDateRangeRequestData $dateAndDateRangeRequestData
     * @param WidgetRequestData $widgetRequestData
     * @return JsonResponse
     */
    public function getRoleOverview(
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());
        $result = $this->roleOverviewDateRangeDataProvider->getData(
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
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());
        $result = $this->roleOverviewDateRangeDataProvider->getData(
            $widgetRequestData->getDepartment(),
            $dateAndDateRangeRequestData->getFrom()->format('Y-m-d'),
            $dateAndDateRangeRequestData->getTo()->format('Y-m-d'),
        );

        return $this->json($result);
    }
}
