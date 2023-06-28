<?php

namespace App\Controller\Api\Apps\Widgets;

use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Service\DataProvider\MembersGroupDateDataProvider;
use App\Service\DataProvider\MembersGroupDateRangeDataProvider;
use App\Service\DataProvider\RoleOverviewDateRangeDataProvider;
use App\Service\Security\PermissionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RoleOverviewController extends AbstractController
{
    public function getRoleOverview
    (
        RoleOverviewDateRangeDataProvider $roleOverviewDateRangeDataProvider,
        DateAndDateRangeRequestData $dateAndDateRangeRequestData,
        WidgetRequestData $widgetRequestData
    ) {
        $this->denyAccessUnlessGranted(PermissionVoter::VIEWER, $widgetRequestData->getGroup());
        $result = $roleOverviewDateRangeDataProvider->getData($widgetRequestData->getGroup(), $dateAndDateRangeRequestData->getFrom()->format('Y-m-d'), $dateAndDateRangeRequestData->getTo()->format('Y-m-d'));
        return $this->json($result);
    }
}
