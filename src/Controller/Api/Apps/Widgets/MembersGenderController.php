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
    public function __construct(private readonly MembersGenderDateDataProvider $membersGenderDateDataProvider, private readonly MembersGenderDateRangeDataProvider $membersGenderDateRangeDataProvider)
    {
    }
    /**
     * @param MembersGenderDateDataProvider $membersGenderDateDataProvider
     * @param MembersGenderDateRangeDataProvider $membersGenderDateRangeDataProvider
     * @throws Exception
     */
    public function getDemographicGroupData(
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetRequestData $widgetRequestData
    ): Response {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());

        $data = [];

        if ($dateAndDateRangeRequestData->getDate()) {
            $data = $this->membersGenderDateDataProvider->getData(
                $widgetRequestData->getGroup(),
                $dateAndDateRangeRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getPeopleTypes(),
                $widgetRequestData->getGroupTypes()
            );
        }

        if ($dateAndDateRangeRequestData->getFrom() && $dateAndDateRangeRequestData->getTo()) {
            $data = $this->membersGenderDateRangeDataProvider->getData(
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
     * @param MembersGenderDateDataProvider $membersGenderDateDataProvider
     * @param MembersGenderDateRangeDataProvider $membersGenderDateRangeDataProvider
     */
    public function getDemographicGroupDataOfDepartment(
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData
    ): Response {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = [];

        if ($dateAndDateRangeRequestData->getDate()) {
            $data = $this->membersGenderDateDataProvider->getData(
                $widgetRequestData->getDepartment(),
                $dateAndDateRangeRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getPeopleTypes(),
                $widgetRequestData->getGroupTypes()
            );
        }

        if ($dateAndDateRangeRequestData->getFrom() && $dateAndDateRangeRequestData->getTo()) {
            $data = $this->membersGenderDateRangeDataProvider->getData(
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
