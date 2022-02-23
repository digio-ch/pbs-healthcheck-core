<?php

namespace App\Controller\Api\Widget;

use App\DTO\Model\WidgetControllerData\DateAndDateRangeRequestData;
use App\Service\DataProvider\QuapSubdepartmentDateDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Component\HttpFoundation\JsonResponse;

class QuapOverviewController extends WidgetController
{
    public function getOverview(
        QuapSubdepartmentDateDataProvider $dataProvider,
        DateAndDateRangeRequestData $requestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $requestData->getGroup());

        $data = [];

        if ($requestData->getDate()) {
            $data = $dataProvider->getData(
                $requestData->getGroup(),
                $requestData->getDate()->format('Y-m-d'),
                $requestData->getGroupTypes(),
                $requestData->getPeopleTypes()
            );
        }

        return $this->json($data);
    }
}
