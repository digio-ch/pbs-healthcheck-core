<?php

namespace App\Controller\Api\Widget;

use App\DTO\Model\WidgetControllerData\DateAndDateRangeRequestData;
use App\Service\DataProvider\MembersGroupDateRangeDataProvider;
use App\Service\DataProvider\MembersGroupDateDataProvider;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembersGroupController extends WidgetController
{
    /**
     * @param MembersGroupDateRangeDataProvider $membersGroupDateRangeDataProvider
     * @param MembersGroupDateDataProvider $membersGroupDateDataProvider
     * @param DateAndDateRangeRequestData $requestData
     * @return JsonResponse
     * @throws DBALException
     */
    public function getGroupMembersData(
        MembersGroupDateRangeDataProvider $membersGroupDateRangeDataProvider,
        MembersGroupDateDataProvider $membersGroupDateDataProvider,
        DateAndDateRangeRequestData $requestData
    ) {
        $data = [];

        if ($requestData->getDate()) {
            $data = $membersGroupDateDataProvider->getData(
                $requestData->getGroup(),
                $requestData->getDate()->format('Y-m-d'),
                $requestData->getGroupTypes(),
                $requestData->getPeopleTypes()
            );
        }

        if ($requestData->getFrom() && $requestData->getTo()) {
            $data = $membersGroupDateRangeDataProvider->getData(
                $requestData->getGroup(),
                $requestData->getFrom()->format('Y-m-d'),
                $requestData->getTo()->format('Y-m-d'),
                $requestData->getGroupTypes(),
                $requestData->getPeopleTypes()
            );
        }

        return $this->json($data);
    }
}
