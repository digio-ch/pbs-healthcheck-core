<?php

namespace App\Controller\Api\Apps\Widgets;

use Exception;
use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Midata\Group;
use App\Entity\Security\PermissionType;
use App\Service\Apps\Widgets\MembersGroupPreviewService;
use App\Service\DataProvider\MembersGroupDateDataProvider;
use App\Service\DataProvider\MembersGroupDateRangeDataProvider;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MembersGroupController extends AbstractController
{
    public function __construct(private readonly MembersGroupDateDataProvider $membersGroupDateDataProvider, private readonly MembersGroupPreviewService $membersGroupPreviewService, private readonly MembersGroupDateRangeDataProvider $membersGroupDateRangeDataProvider)
    {
    }
    /**
     * @param MembersGroupDateDataProvider $membersGroupDateDataProvider
     * @param MembersGroupPreviewService $membersGroupPreviewService
     */
    public function getPreview(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): Response
    {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        $data = [];
        if ($date = $this->membersGroupPreviewService->getNewestDate()) {
            $data = $this->membersGroupDateDataProvider->getData(
                $group,
                $date->format('Y-m-d'),
                $this->membersGroupPreviewService->getGroupTypes($group->getId()),
                ['members', 'leaders']
            );
        }
        return $this->json($data);
    }

    /**
     * @param MembersGroupDateRangeDataProvider $membersGroupDateRangeDataProvider
     * @param MembersGroupDateDataProvider $membersGroupDateDataProvider
     * @throws DBALException
     */
    public function getGroupMembersData(
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());

        $data = [];

        if ($dateAndDateRangeRequestData->getDate()) {
            $data = $this->membersGroupDateDataProvider->getData(
                $widgetRequestData->getGroup(),
                $dateAndDateRangeRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getGroupTypes(),
                $widgetRequestData->getPeopleTypes()
            );
        }

        if ($dateAndDateRangeRequestData->getFrom() && $dateAndDateRangeRequestData->getTo()) {
            $data = $this->membersGroupDateRangeDataProvider->getData(
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
     * @param MembersGroupDateRangeDataProvider $membersGroupDateRangeDataProvider
     * @param MembersGroupDateDataProvider $membersGroupDateDataProvider
     * @throws Exception
     */
    public function getGroupMembersDataOfDepartment(
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = [];

        if ($dateAndDateRangeRequestData->getDate()) {
            $data = $this->membersGroupDateDataProvider->getData(
                $widgetRequestData->getDepartment(),
                $dateAndDateRangeRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getGroupTypes(),
                $widgetRequestData->getPeopleTypes()
            );
        }

        if ($dateAndDateRangeRequestData->getFrom() && $dateAndDateRangeRequestData->getTo()) {
            $data = $this->membersGroupDateRangeDataProvider->getData(
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
