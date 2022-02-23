<?php

namespace App\Controller\Api\Widget;

use App\DTO\Model\WidgetControllerData\DateRequestData;
use App\Service\DataProvider\LeaderOverviewDatePointDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Component\HttpFoundation\JsonResponse;

class LeaderOverviewController extends WidgetController
{
    /**
     * @param DateRequestData $requestData
     * @param LeaderOverviewDatePointDataProvider $dataProvider
     * @return JsonResponse
     */
    public function getLeaderOverviewData(
        DateRequestData $requestData,
        LeaderOverviewDatePointDataProvider $dataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $requestData->getGroup());

        $data = $dataProvider->getData(
            $requestData->getGroup(),
            $requestData->getDate()->format('Y-m-d'),
            $requestData->getGroupTypes(),
            $requestData->getPeopleTypes()
        );

        return $this->json($data);
    }
}
