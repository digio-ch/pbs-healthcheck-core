<?php

namespace App\Service\DataProvider;

use App\DTO\Model\LineChartDataDTO;
use App\DTO\Model\LineChartDataPointDTO;
use App\Entity\midata\Group;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Repository\WidgetDemographicGroupRepository;
use DateTime;
use Doctrine\DBAL\DBALException;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembersGenderDateRangeDataProvider extends WidgetDataProvider
{
    /**
     * @var WidgetDemographicGroupRepository
     */
    protected $widgetDemographicGroupRepository;

    /**
     * MembersGenderDateRangeDataProvider constructor.
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
     * @param string $from
     * @param string $to
     * @param array $subGroupTypes
     * @param array $peopleTypes
     * @return array
     * @throws DBALException
     */
    public function getData(Group $group, string $from, string $to, array $subGroupTypes, array $peopleTypes)
    {
        $result = [];

        switch ($peopleTypes) {
            case in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes) &&
                in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes):
                $queryData = $this->widgetDemographicGroupRepository->findAllGenderTotalCountForDatePeriodByGroupType(
                    $from,
                    $to,
                    $group->getId(),
                    $subGroupTypes
                );
                break;
            case in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes):
                $queryData = $this->widgetDemographicGroupRepository->findLeaderCountForDatePeriodByGroupTypes(
                    $from,
                    $to,
                    $group->getId(),
                    $subGroupTypes
                );
                break;
            case in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes):
                $queryData = $this->widgetDemographicGroupRepository->findMemberCountForDatePeriodByGroupTypes(
                    $from,
                    $to,
                    $group->getId(),
                    $subGroupTypes
                );
                break;
            default:
                return $result;
        }

        $lineChartFemaleData = new LineChartDataDTO();
        $lineChartFemaleData->setName($this->translator->trans('gender.female'));
        $lineChartFemaleData->setColor('');
        $lineChartMaleData = new LineChartDataDTO();
        $lineChartMaleData->setName($this->translator->trans('gender.male'));
        $lineChartMaleData->setColor('');
        $lineChartOtherData = new LineChartDataDTO();
        $lineChartOtherData->setName($this->translator->trans('gender.unknown'));
        $lineChartOtherData->setColor('');

        foreach ($queryData as $data) {
            $date = new DateTime($data['data_point_date']);

            $lineChartPointFemale = new LineChartDataPointDTO();
            $lineChartPointFemale->setName($date->format('d.m.Y'));
            $lineChartPointFemale->setValue($data['f']);

            $lineChartPointMale = new LineChartDataPointDTO();
            $lineChartPointMale->setName($date->format('d.m.Y'));
            $lineChartPointMale->setValue($data['m']);

            $lineChartPointOther = new LineChartDataPointDTO();
            $lineChartPointOther->setName($date->format('d.m.Y'));
            $lineChartPointOther->setValue($data['u']);

            $lineChartFemaleData->addSeries($lineChartPointFemale);
            $lineChartOtherData->addSeries($lineChartPointOther);
            $lineChartMaleData->addSeries($lineChartPointMale);
        }

        $result[] = $lineChartFemaleData;
        $result[] = $lineChartOtherData;
        $result[] = $lineChartMaleData;
        return $result;
    }
}
