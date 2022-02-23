<?php

namespace App\Controller\Api\Widget;

use App\DTO\Model\WidgetControllerData\DateAndDateRangeRequestData;
use App\Service\DataProvider\QuapDateDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Component\HttpFoundation\JsonResponse;

class QuapController extends WidgetController
{
    public function getQuapAnswers(
        QuapDateDataProvider $dataProvider,
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
