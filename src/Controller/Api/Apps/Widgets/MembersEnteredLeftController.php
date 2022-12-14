<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Service\DataProvider\MembersEnteredLeftDateRangeDataProvider;
use App\Service\Security\PermissionVoter;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembersEnteredLeftController extends AbstractController
{
    /**
     * @param DateRangeRequestData $dateRangeRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param MembersEnteredLeftDateRangeDataProvider $membersEnteredLeftDateRangeDataProvider
     * @return JsonResponse
     * @throws DBALException
     */
    public function getEnteredLeftMembersData(
        DateRangeRequestData $dateRangeRequestData,
        WidgetRequestData $widgetRequestData,
        MembersEnteredLeftDateRangeDataProvider $membersEnteredLeftDateRangeDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $widgetRequestData->getGroup());

        $data = $membersEnteredLeftDateRangeDataProvider->getData(
            $widgetRequestData->getGroup(),
            $dateRangeRequestData->getFrom()->format('Y-m-d'),
            $dateRangeRequestData->getTo()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );

        return $this->json($data);
    }
}
