<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Service\DataProvider\LeaderOverviewDatePointDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class LeaderOverviewController extends AbstractController
{
    /**
     * @param DateRequestData $dateRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param LeaderOverviewDatePointDataProvider $dataProvider
     * @return JsonResponse
     */
    public function getLeaderOverviewData(
        DateRequestData $dateRequestData,
        WidgetRequestData $widgetRequestData,
        LeaderOverviewDatePointDataProvider $dataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $widgetRequestData->getGroup());

        $data = $dataProvider->getData(
            $widgetRequestData->getGroup(),
            $dateRequestData->getDate()->format('Y-m-d'),
            $widgetRequestData->getGroupTypes(),
            $widgetRequestData->getPeopleTypes()
        );

        return $this->json($data);
    }
}
