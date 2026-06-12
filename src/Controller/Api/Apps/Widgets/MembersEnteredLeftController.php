<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Security\PermissionType;
use App\Service\DataProvider\MembersEnteredLeftDateRangeDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembersEnteredLeftController extends AbstractController
{
    /**
     * @param DateRangeRequestData $dateRangeRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param MembersEnteredLeftDateRangeDataProvider $membersEnteredLeftDateRangeDataProvider
     * @return JsonResponse
     */
    public function getEnteredLeftMembersData(
        DateRangeRequestData $dateRangeRequestData,
        WidgetRequestData $widgetRequestData,
        MembersEnteredLeftDateRangeDataProvider $membersEnteredLeftDateRangeDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());

        $data = $membersEnteredLeftDateRangeDataProvider->getData(
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
     * @param MembersEnteredLeftDateRangeDataProvider $membersEnteredLeftDateRangeDataProvider
     * @return JsonResponse
     */
    public function getEnteredLeftMembersDataOfDepartment(
        DateRangeRequestData $dateRangeRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData,
        MembersEnteredLeftDateRangeDataProvider $membersEnteredLeftDateRangeDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = $membersEnteredLeftDateRangeDataProvider->getData(
            $widgetRequestData->getDepartment(),
            $dateRangeRequestData->getFrom()->format('Y-m-d'),
            $dateRangeRequestData->getTo()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );

        return $this->json($data);
    }
}
