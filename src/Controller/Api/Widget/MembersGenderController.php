<?php

namespace App\Controller\Api\Widget;

use App\DTO\Model\WidgetControllerData\DateAndDateRangeRequestData;
use App\Service\DataProvider\MembersGenderDateRangeDataProvider;
use App\Service\DataProvider\MembersGenderDateDataProvider;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\Response;

class MembersGenderController extends WidgetController
{
    /**
     * @param DateAndDateRangeRequestData $requestData
     * @param MembersGenderDateDataProvider $membersGenderDateDataProvider
     * @param MembersGenderDateRangeDataProvider $membersGenderDateRangeDataProvider
     * @return Response
     * @throws DBALException
     */
    public function getDemographicGroupData(
        DateAndDateRangeRequestData $requestData,
        MembersGenderDateDataProvider $membersGenderDateDataProvider,
        MembersGenderDateRangeDataProvider $membersGenderDateRangeDataProvider
    ) {
        $data = [];

        if ($requestData->getDate()) {
            $data = $membersGenderDateDataProvider->getData(
                $requestData->getGroup(),
                $requestData->getDate()->format('Y-m-d'),
                $requestData->getPeopleTypes(),
                $requestData->getGroupTypes()
            );
        }

        if ($requestData->getFrom() && $requestData->getTo()) {
            $data = $membersGenderDateRangeDataProvider->getData(
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
