<?php

namespace App\Service\Aggregator;

use App\Entity\Aggregated\AggregatedDemographicGroup;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedDemographicGroupRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\PersonRoleRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DemographicGroupAggregator extends WidgetAggregator
{
    private const NAME = 'widget.demographic-group';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var AggregatedDemographicGroupRepository
     */
    protected $widgetDemographicGroupRepository;

    /**
     * @var PersonRoleRepository
     */
    protected $personRoleRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    public function __construct(
        EntityManagerInterface               $em,
        AggregatedDemographicGroupRepository $widgetDemographicGroupRepository,
        PersonRoleRepository                 $personRoleRepository,
        GroupRepository                      $groupRepository
    ) {
        $this->em = $em;
        $this->widgetDemographicGroupRepository = $widgetDemographicGroupRepository;
        $this->personRoleRepository = $personRoleRepository;
        $this->groupRepository = $groupRepository;
        parent::__construct($groupRepository);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param DateTime|null $startDate
     * @throws Exception
     */
    public function aggregate(DateTime $startDate = null)
    {
        $mainGroups = $this->groupRepository->findAllDepartmentalParentGroups();

        $minDate = $startDate !== null ? $startDate : new DateTime(self::AGGREGATION_START_DATE);
        $maxDate = new DateTime();
        $startPointDate = clone $minDate;

        while ($startPointDate->getTimestamp() < $maxDate->getTimestamp()) {
            $startPointDate->add(new DateInterval("P1M"));
            $startPointDate->modify('first day of this month');

            if ($startPointDate->getTimestamp() > $maxDate->getTimestamp()) {
                $startPointDate = clone $maxDate;
            }

            /** @var Group $mainGroup */
            foreach ($mainGroups as $mainGroup) {
                $this->deleteLastPeriod($this->widgetDemographicGroupRepository, $mainGroup->getId());

                $existingData = $this->getAllDataPointDates(
                    $this->widgetDemographicGroupRepository,
                    $mainGroup->getId()
                );
                if ($this->isDataExistsForDate($startPointDate->format('Y-m-d 00:00:00'), $existingData)) {
                    continue;
                }

                $mainGroup = $this->groupRepository->findOneBy(['id' => $mainGroup->getId()]);
                $subGroupIds = $this->groupRepository->findAllRelevantSubGroupIdsByParentGroupId($mainGroup->getId());
                $ids = array_merge($subGroupIds, [$mainGroup->getId()]);

                $results = $this->personRoleRepository->findAllWithRoleCountInGroup(
                    $startPointDate->format('Y-m-d'),
                    $ids
                );

                $data = $this->processPersonIdsAndRoles($results, $ids, $startPointDate->format('Y-m-d'));

                $this->createWidgetsFromData($data, $mainGroup, $startPointDate);
            }
            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param array $data
     * @param Group $mainGroup
     * @param DateTime $startPointDate
     * @throws Exception
     */
    private function createWidgetsFromData(array $data, Group $mainGroup, DateTime $startPointDate)
    {
        foreach ($data as $groupType => $personTypeAndCountsByGender) {
            $widget = new AggregatedDemographicGroup();
            $widget->setGroup($mainGroup);
            $widget->setGroupType($groupType);
            $widget->setMCount($personTypeAndCountsByGender['members']['m']);
            $widget->setFCount($personTypeAndCountsByGender['members']['w']);
            $widget->setUCount($personTypeAndCountsByGender['members']['u']);
            $widget->setMCountLeader($personTypeAndCountsByGender['leaders']['m']);
            $widget->setFCountLeader($personTypeAndCountsByGender['leaders']['w']);
            $widget->setUCountLeader($personTypeAndCountsByGender['leaders']['u']);
            $widget->setCreatedAt(new DateTimeImmutable());
            $widget->setDataPointDate(new DateTimeImmutable($startPointDate->format('Y-m-d')));
            $this->em->persist($widget);
        }
    }
}
