<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Service\DataProvider\GeoLocationDateDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class GeoLocationController extends AbstractController
{
    /**
     * @param GeoLocationDateDataProvider $dataProvider
     * @param DateRequestData $dateRequestData
     * @param WidgetRequestData $widgetRequestData
     * @return JsonResponse
     * @throws \Doctrine\DBAL\Exception
     */
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

    /**
     * @param GeoLocationDateDataProvider $dataProvider
     * @param DateRequestData $dateRequestData
     * @param WidgetOfDepartmentRequestData $widgetRequestData
     * @return JsonResponse
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGeoLocationsOfDepartment(
        GeoLocationDateDataProvider $dataProvider,
        DateRequestData $dateRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = [];

        if ($dateRequestData->getDate()) {
            $data = $dataProvider->getData(
                $widgetRequestData->getDepartment(),
                $dateRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getGroupTypes(),
                $widgetRequestData->getPeopleTypes()
            );
        }

        return $this->json($data);
    }
}
