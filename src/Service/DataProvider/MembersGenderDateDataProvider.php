<?php

namespace App\Service\DataProvider;

use App\DTO\Model\PieChartDataDTO;
use App\Entity\Group;
use App\Exception\ApiException;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Repository\WidgetDemographicGroupRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembersGenderDateDataProvider extends WidgetDataProvider
{
    /**
     * @var WidgetDemographicGroupRepository
     */
    protected $widgetDemographicGroupRepository;

    /**
     * MembersGenderDateDataProvider constructor.
     * @param GroupRepository $groupRepository
     * @param GroupTypeRepository $groupTypeRepository
     * @param TranslatorInterface $translator
     * @param WidgetDemographicGroupRepository $widgetDemographicGroupRepository
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        WidgetDemographicGroupRepository $widgetDemographicGroupRepository
    ) {
        $this->widgetDemographicGroupRepository = $widgetDemographicGroupRepository;
        $this->groupRepository = $groupRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    /**
     * @param Group $group
     * @param string $date
     * @param array $peopleTypes
     * @param array $subGroupTypes
     * @return array
     * @throws DBALException
     */
    public function getData(Group $group, string $date, array $peopleTypes, array $subGroupTypes)
    {
        $result = [];

        switch ($peopleTypes) {
            case in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes) &&
                in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes):
                $queryData = $this->widgetDemographicGroupRepository->getAllGenderTotalCountForDate($date, $group->getId(), $subGroupTypes);
                break;
            case in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes):
                $queryData = $this->widgetDemographicGroupRepository->getLeaderCountForDate($date, $group->getId(), $subGroupTypes);
                break;
            case in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes):
                $queryData = $this->widgetDemographicGroupRepository->getAllGenderMemberCountForDate($date, $group->getId(), $subGroupTypes);
                break;
            default:
                return $result;
        }

        $pieChartDataDto = new PieChartDataDTO();
        $pieChartDataDto->setName($this->translator->trans('gender.female'));
        $pieChartDataDto->setValue(!$queryData ? 0 : $queryData[0]['f']);
        $pieChartDataDto->setColor('');
        $result[] = $pieChartDataDto;

        $pieChartDataDto = new PieChartDataDTO();
        $pieChartDataDto->setName($this->translator->trans('gender.unknown'));
        $pieChartDataDto->setValue(!$queryData ? 0 : $queryData[0]['u']);
        $pieChartDataDto->setColor('');
        $result[] = $pieChartDataDto;

        $pieChartDataDto = new PieChartDataDTO();
        $pieChartDataDto->setName($this->translator->trans('gender.male'));
        $pieChartDataDto->setValue(!$queryData ? 0 : $queryData[0]['m']);
        $pieChartDataDto->setColor('');
        $result[] = $pieChartDataDto;

        return $result;
    }
}
