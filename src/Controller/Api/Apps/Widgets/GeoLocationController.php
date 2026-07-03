<?php

namespace App\Controller\Api\Apps\Widgets;

use Doctrine\DBAL\Exception;
use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\WidgetOfDepartmentRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Security\PermissionType;
use App\Service\DataProvider\GeoLocationDateDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class GeoLocationController extends AbstractController
{
    public function __construct(private readonly GeoLocationDateDataProvider $dataProvider)
    {
    }
    /**
     * @param GeoLocationDateDataProvider $dataProvider
     * @param DateRequestData $dateRequestData
     * @param WidgetRequestData $widgetRequestData
     * @return JsonResponse
     * @throws Exception
     */
    public function getGeoLocations(
        DateRequestData $dateRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $widgetRequestData->getGroup());

        $data = [];

        if ($dateRequestData->getDate()) {
            $data = $this->dataProvider->getData(
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
     * @throws Exception
     */
    public function getGeoLocationsOfDepartment(
        DateRequestData $dateRequestData,
        WidgetOfDepartmentRequestData $widgetRequestData
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::EDITOR_PLUS, $widgetRequestData->getGroup());

        $data = [];

        if ($dateRequestData->getDate()) {
            $data = $this->dataProvider->getData(
                $widgetRequestData->getDepartment(),
                $dateRequestData->getDate()->format('Y-m-d'),
                $widgetRequestData->getGroupTypes(),
                $widgetRequestData->getPeopleTypes()
            );
        }

        return $this->json($data);
    }
}
