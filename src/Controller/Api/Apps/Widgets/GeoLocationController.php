<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Service\DataProvider\GeoLocationDateDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class GeoLocationController extends AbstractController
{
    public function getGeoLocations(
        GeoLocationDateDataProvider $dataProvider,
        DateRequestData $dateRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $widgetRequestData->getGroup());

        $data = [];

        if ($dateRequestData->getDate()) {
            $data = $dataProvider->getData(
                $widgetRequestData->getGroup(),
                $dateRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getGroupTypes(),
                $widgetRequestData->getPeopleTypes()
            );
        }

        return $this->json($data);
    }
}
