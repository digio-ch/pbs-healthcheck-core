<?php

namespace App\Controller\Api\Apps;

use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Model\TimeFrame;
use App\Service\DataProvider\FilterDataProvider;
use App\Service\DataProvider\MyOrganization\GenderStatsDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MyOrganizationController extends AbstractController
{
    /**
     * @param Request $request
     * @param Group $group
     * @param FilterDataProvider $filterDataProvider
     * @return JsonResponse
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     */
    public function getFilter(
        Request $request,
        Group $group,
        FilterDataProvider $filterDataProvider
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        if (!$this->isAssociation($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }

        $data = $filterDataProvider->getMyOrganizationData(
            $group,
            $request->getLocale()
        );

        return $this->json($data);
    }

    /**
     * @param DateAndDateRangeRequestData $datesRequestData
     * @param WidgetRequestData $widgetRequestData
     * @param GenderStatsDataProvider $genderStatsProvider
     * @return JsonResponse
     */
    public function getGenderStats(
        DateAndDateRangeRequestData $datesRequestData,
        WidgetRequestData $widgetRequestData,
        GenderStatsDataProvider $genderStatsProvider
    ): JsonResponse {
        $group = $widgetRequestData->getGroup();

        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        if (!$this->isAssociation($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }

        $timeframe = $this->requestToTimeFrame($datesRequestData);

        $data = $genderStatsProvider->getData(
            $group,
            $timeframe,
            $widgetRequestData->getPeopleTypes(),
            $widgetRequestData->getGroupTypes()
        );

        return $this->json($data);
    }

    private function isAssociation(Group $group): bool
    {
        $groupType = $group->getGroupType()->getGroupType();
        return $groupType === GroupType::CANTON || $groupType === GroupType::REGION;
    }

    private function requestToTimeFrame(DateAndDateRangeRequestData $req): TimeFrame
    {
        if ($req->getDate()) {
            return TimeFrame::fromDate($req->getDate());
        }

        return TimeFrame::fromPeriod(
            $req->getFrom(),
            $req->getTo()
        );
    }
}
