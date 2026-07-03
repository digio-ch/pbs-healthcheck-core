<?php

namespace App\Controller\Api\Apps;

use DateTime;
use App\DTO\Model\FilterRequestData\DateAndDateRangeRequestData;
use App\DTO\Model\FilterRequestData\DateRequestData;
use App\DTO\Model\FilterRequestData\WidgetRequestData;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Security\PermissionType;
use App\Exception\ApiException;
use App\Model\TimeFrame;
use App\Service\DataProvider\DemographicStatsDataProvider;
use App\Service\DataProvider\FilterDataProvider;
use App\Service\DataProvider\MyOrganization\DepartmentNamesDataProvider;
use App\Service\DataProvider\MyOrganization\GenderStatsDataProvider;
use App\Service\DataProvider\MyOrganization\PreviewDataProvider;
use App\Service\DataProvider\MyOrganization\StageStatsDataProvider;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MyOrganizationController extends AbstractController
{
    public function __construct(private readonly FilterDataProvider $filterDataProvider, private readonly GenderStatsDataProvider $genderStatsProvider, private readonly StageStatsDataProvider $statsDataProvider, private readonly DemographicStatsDataProvider $demographicStatsProvider, private readonly DepartmentNamesDataProvider $departmentNamesProvider, private readonly PreviewDataProvider $previewProvider)
    {
    }
    /**
     * @param FilterDataProvider $filterDataProvider
     */
    public function getFilter(
        Request $request,
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        if (!$this->isAssociation($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }
        $data = $this->filterDataProvider->getMyOrganizationData(
            $group,
            $request->getLocale()
        );
        return $this->json($data);
    }

    /**
     * @param GenderStatsDataProvider $genderStatsProvider
     */
    public function getGenderStats(
        DateAndDateRangeRequestData $datesRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $group = $widgetRequestData->getGroup();

        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        if (!$this->isAssociation($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }

        $timeframe = $this->requestToTimeFrame($datesRequestData);

        $data = $this->genderStatsProvider->getData(
            $group,
            $timeframe,
            $widgetRequestData->getPeopleTypes(),
            $widgetRequestData->getGroupTypes()
        );

        return $this->json($data);
    }

    /**
     * @param StageStatsDataProvider $statsDataProvider
     */
    public function getStageStats(
        DateAndDateRangeRequestData $datesRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $group = $widgetRequestData->getGroup();

        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        if (!$this->isAssociation($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }

        $timeframe = $this->requestToTimeFrame($datesRequestData);

        $data = $this->statsDataProvider->getData(
            $group,
            $timeframe,
            $widgetRequestData->getPeopleTypes(),
            $widgetRequestData->getGroupTypes()
        );

        return $this->json($data);
    }

    /**
     * @param DemographicStatsDataProvider $demographicStatsProvider
     */
    public function getDemographicStats(
        DateRequestData $dateRequestData,
        WidgetRequestData $widgetRequestData
    ): JsonResponse {
        $group = $widgetRequestData->getGroup();

        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);

        if (!$this->isAssociation($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }

        $data = $this->demographicStatsProvider->getDataForAssociation(
            $group,
            $dateRequestData->getDate(),
            $widgetRequestData->getPeopleTypes(),
            $widgetRequestData->getGroupTypes()
        );

        return $this->json($data);
    }

    /**
     * @param DepartmentNamesDataProvider $departmentNamesProvider
     */
    public function getDepartmentNames(
        DateRequestData $dateRequestData,
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        if (!$this->isAssociation($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }
        $names = $this->departmentNamesProvider->getDepartmentNames(
            $group,
            $dateRequestData->getDate()
        );
        return $this->json($names);
    }

    /**
     * @param PreviewDataProvider $previewProvider
     * @throws Exception
     */
    public function getPreview(
        #[MapEntity(mapping: ['groupId' => 'id'])]
        Group $group
    ): JsonResponse {
        $this->denyAccessUnlessGranted(PermissionType::VIEWER, $group);
        if (!$this->isAssociation($group)) {
            throw new ApiException(400, "Only for regions and cantons");
        }
        $data = $this->previewProvider->getPreview(
            $group,
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
        if ($req->getDate() instanceof DateTime) {
            return TimeFrame::fromDate($req->getDate());
        }

        return TimeFrame::fromPeriod(
            $req->getFrom(),
            $req->getTo()
        );
    }
}
