<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Service\DataProvider\MembersGroupDateDataProvider;
use App\Service\DataProvider\MembersGroupDateRangeDataProvider;
use App\Service\Security\PermissionVoter;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembersGroupController extends AbstractController
{
    /**
     * @param MembersGroupDateRangeDataProvider $membersGroupDateRangeDataProvider
     * @param MembersGroupDateDataProvider $membersGroupDateDataProvider
     * @param DateAndDateRangeRequestData $dateAndDateRangeRequestData
     * @param WidgetRequestData $widgetRequestData
     * @return JsonResponse
     * @throws DBALException
     */
    public function getGroupMembersData(
        MembersGroupDateRangeDataProvider $membersGroupDateRangeDataProvider,
        MembersGroupDateDataProvider $membersGroupDateDataProvider,
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $widgetRequestData->getGroup());

        $data = [];

        if ($dateAndDateRangeRequestData->getDate()) {
            $data = $membersGroupDateDataProvider->getData(
                $widgetRequestData->getGroup(),
                $dateAndDateRangeRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getGroupTypes(),
                $widgetRequestData->getPeopleTypes()
            );
        }

        if ($dateAndDateRangeRequestData->getFrom() && $dateAndDateRangeRequestData->getTo()) {
            $data = $membersGroupDateRangeDataProvider->getData(
                $widgetRequestData->getGroup(),
                $dateAndDateRangeRequestData->getFrom()->format('Y-m-d'),
                $dateAndDateRangeRequestData->getTo()->format('Y-m-d'),
                $widgetRequestData->getGroupTypes(),
                $widgetRequestData->getPeopleTypes()
            );
        }

        return $this->json($data);
    }
}
