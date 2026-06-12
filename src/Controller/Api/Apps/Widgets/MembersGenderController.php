<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Security\PermissionType;
use App\Service\DataProvider\MembersGenderDateDataProvider;
use App\Service\DataProvider\MembersGenderDateRangeDataProvider;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MembersGenderController extends AbstractController
{
    /**
     * @param DateAndDateRangeRequestData $dateAndDateRangeRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param MembersGenderDateDataProvider $membersGenderDateDataProvider
     * @param MembersGenderDateRangeDataProvider $membersGenderDateRangeDataProvider
     * @return Response
     * @throws Exception
     */
    public function getDemographicGroupData(
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetRequestData $widgetRequestData,
        MembersGenderDateDataProvider $membersGenderDateDataProvider,
        MembersGenderDateRangeDataProvider $membersGenderDateRangeDataProvider
    ): Response {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());

        $data = [];

        if ($dateAndDateRangeRequestData->getDate()) {
            $data = $membersGenderDateDataProvider->getData(
                $widgetRequestData->getGroup(),
                $dateAndDateRangeRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getPeopleTypes(),
                $widgetRequestData->getGroupTypes()
            );
        }

        if ($dateAndDateRangeRequestData->getFrom() && $dateAndDateRangeRequestData->getTo()) {
            $data = $membersGenderDateRangeDataProvider->getData(
                $widgetRequestData->getGroup(),
                $dateAndDateRangeRequestData->getFrom()->format('Y-m-d'),
                $dateAndDateRangeRequestData->getTo()->format('Y-m-d'),
                $widgetRequestData->getGroupTypes(),
                $widgetRequestData->getPeopleTypes()
            );
        }

        return $this->json($data);
    }

    /**
     * @param DateAndDateRangeRequestData $dateAndDateRangeRequestData
     * @param WidgetOfDepartmentRequestData $widgetRequestData
     * @param MembersGenderDateDataProvider $membersGenderDateDataProvider
     * @param MembersGenderDateRangeDataProvider $membersGenderDateRangeDataProvider
     * @return Response
     */
    public function getDemographicGroupDataOfDepartment(
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData,
        MembersGenderDateDataProvider $membersGenderDateDataProvider,
        MembersGenderDateRangeDataProvider $membersGenderDateRangeDataProvider
    ): Response {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = [];

        if ($dateAndDateRangeRequestData->getDate()) {
            $data = $membersGenderDateDataProvider->getData(
                $widgetRequestData->getDepartment(),
                $dateAndDateRangeRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getPeopleTypes(),
                $widgetRequestData->getGroupTypes()
            );
        }

        if ($dateAndDateRangeRequestData->getFrom() && $dateAndDateRangeRequestData->getTo()) {
            $data = $membersGenderDateRangeDataProvider->getData(
                $widgetRequestData->getDepartment(),
                $dateAndDateRangeRequestData->getFrom()->format('Y-m-d'),
                $dateAndDateRangeRequestData->getTo()->format('Y-m-d'),
                $widgetRequestData->getGroupTypes(),
                $widgetRequestData->getPeopleTypes()
            );
        }

        return $this->json($data);
    }
}
