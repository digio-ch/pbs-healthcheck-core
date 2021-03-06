<?php

namespace App\Controller\Api\Widget;

use App\DTO\Model\WidgetControllerData\DateRangeRequestData;
use App\Service\DataProvider\MembersEnteredLeftDateRangeDataProvider;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembersEnteredLeftController extends WidgetController
{
    /**
     * @param DateRangeRequestData $requestData
     * @param MembersEnteredLeftDateRangeDataProvider $membersEnteredLeftDateRangeDataProvider
     * @return JsonResponse
     * @throws DBALException
     */
    public function getEnteredLeftMembersData(
        DateRangeRequestData $requestData,
        MembersEnteredLeftDateRangeDataProvider $membersEnteredLeftDateRangeDataProvider
    ) {
        $data = $membersEnteredLeftDateRangeDataProvider->getData(
            $requestData->getGroup(),
            $requestData->getFrom()->format('Y-m-d'),
            $requestData->getTo()->format('Y-m-d'),
            $requestData->getGroupTypes(),
            $requestData->getPeopleTypes()
        );

        return $this->json($data);
    }
}
