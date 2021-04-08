<?php


namespace App\Controller\Api\Widget;


use App\DTO\Model\WidgetControllerData\DateAndDateRangeRequestData;
use App\Service\DataProvider\GeoLocationDateDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;

class GeoLocationController extends WidgetController
{
    public function getGeoLocations(
        GeoLocationDateDataProvider $dataProvider,
        DateAndDateRangeRequestData $requestData
    ): JsonResponse
    {
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
