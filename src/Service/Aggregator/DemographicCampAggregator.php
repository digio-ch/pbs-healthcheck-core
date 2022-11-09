<?php

namespace App\Service\Aggregator;

use App\Entity\Aggregated\AggregatedDemographicCamp;
use App\Entity\Aggregated\AggregatedDemographicCampGroup;
use App\Entity\Midata\Camp;
use App\Entity\Midata\EventDate;
use App\Entity\Midata\Group;
use App\Entity\Midata\PersonRole;
use App\Repository\Aggregated\AggregatedDemographicCampGroupRepository;
use App\Repository\Aggregated\AggregatedDemographicCampRepository;
use App\Repository\Midata\EventDateRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\PersonRoleRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DemographicCampAggregator extends WidgetAggregator
{
    private const NAME = 'widget.demographic-camp';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var PersonRoleRepository
     */
    protected $personRoleRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var EventDateRepository
     */
    protected $eventDateRepository;

    /**
     * @var AggregatedDemographicCampRepository
     */
    protected $widgetDemographicCampRepository;

    /**
     * @var AggregatedDemographicCampGroupRepository
     */
    protected $demographicCampGroupRepository;

    /**
     * DemographicCampAggregator constructor.
     * @param EntityManagerInterface $em
     * @param PersonRoleRepository $personRoleRepository
     * @param GroupRepository $groupRepository
     * @param EventDateRepository $eventDateRepository
     * @param AggregatedDemographicCampRepository $widgetDemographicCampRepository
     * @param AggregatedDemographicCampGroupRepository $demographicCampGroupRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        PersonRoleRepository $personRoleRepository,
        GroupRepository $groupRepository,
        EventDateRepository $eventDateRepository,
        AggregatedDemographicCampRepository $widgetDemographicCampRepository,
        AggregatedDemographicCampGroupRepository $demographicCampGroupRepository
    ) {
        $this->em = $em;
        $this->personRoleRepository = $personRoleRepository;
        $this->groupRepository = $groupRepository;
        $this->eventDateRepository = $eventDateRepository;
        $this->widgetDemographicCampRepository = $widgetDemographicCampRepository;
        $this->demographicCampGroupRepository = $demographicCampGroupRepository;
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
     * @throws DBALException
     * @throws Exception
     */
    public function aggregate(DateTime $startDate = null)
    {
        $mainGroups = $this->groupRepository->findAllDepartmentalParentGroups();

        $maxDate = new DateTime();
        $minDate = $startDate !== null ? $startDate : new DateTime(self::AGGREGATION_START_DATE);
        $startPointDate = clone $minDate;

        while ($startPointDate->getTimestamp() < $maxDate->getTimestamp()) {
            $startPointDate->add(new DateInterval("P1M"));
            $startPointDate->modify('first day of this month');

            if ($startPointDate->getTimestamp() > $maxDate->getTimestamp()) {
                $startPointDate = clone $maxDate;
            }

            // find all camps for date period
            $prevMonth = clone $startPointDate;
            $prevMonth->modify('first day of last month');

            /** @var Group $mainGroup */
            foreach ($mainGroups as $mainGroup) {
                $this->deleteLastPeriod($this->widgetDemographicCampRepository, $mainGroup->getId());

                $existingData = $this->getAllDataPointDates(
                    $this->widgetDemographicCampRepository,
                    $mainGroup->getId()
                );
                if ($this->isDataExistsForDate($startPointDate->format('Y-m-d 00:00:00'), $existingData)) {
                    continue;
                }

                $subGroupIds = $this->groupRepository->findAllRelevantSubGroupIdsByParentGroupId($mainGroup->getId());
                $allGroupIds = array_merge($subGroupIds, [$mainGroup->getId()]);
                $eventDates = $this->eventDateRepository->getAllForPeriodAndSubgroups(
                    $prevMonth->format('Y-m-d 00:00:00'),
                    $startPointDate->format(
                        'Y-m-d 00:00:00'
                    ),
                    $allGroupIds
                );

                if (!$eventDates) {
                    continue;
                }

                /** @var EventDate $eventDate */
                foreach ($eventDates as $eventDate) {
                    if (!$eventDate->getEvent() instanceof Camp) {
                        continue;
                    }
                    /** @var Camp $camp */
                    $camp = $eventDate->getEvent();
                    if ($camp->getState() && $camp->getState() === 'canceled') {
                        continue;
                    }

                    $widgetDemographicCamp = $this->widgetDemographicCampRepository->findOneBy([
                        'dataPointDate' => new DateTimeImmutable($startPointDate->format('Y-m-d')),
                        'campName' => $eventDate->getEvent()->getName(),
                        'startDate' => new DateTimeImmutable($eventDate->getStartAt()->format('Y-m-d'))
                    ]);

                    if (!$widgetDemographicCamp) {
                        $widgetDemographicCamp = new AggregatedDemographicCamp();
                        $widgetDemographicCamp->setStartDate($eventDate->getStartAt());
                        $widgetDemographicCamp->setCreatedAt(new DateTimeImmutable());
                        $widgetDemographicCamp->setDataPointDate(
                            new DateTimeImmutable($startPointDate->format('Y-m-d'))
                        );
                        $widgetDemographicCamp->setCampName($eventDate->getEvent()->getName());
                        $this->em->persist($widgetDemographicCamp);
                        $this->em->flush();
                    }

                    $eventStartDate = $eventDate->getStartAt();
                    $memberParticipantIds = $this->personRoleRepository->getMemberParticipants(
                        $allGroupIds,
                        $eventDate->getEvent()->getId(),
                        $eventStartDate->format('Y-m-d')
                    );
                    $leaderParticipantIds = $this->personRoleRepository->getLeaderParticipants(
                        $allGroupIds,
                        $eventDate->getEvent()->getId(),
                        $eventStartDate->format('Y-m-d')
                    );
                    $memberData = $this->processPersonIds($memberParticipantIds, $eventStartDate);
                    $leaderData = $this->processPersonIds($leaderParticipantIds, $eventStartDate);

                    foreach (WidgetAggregator::$typePriority as $groupType) {
                        $membersCounts = array_key_exists($groupType, $memberData) ? $memberData[$groupType] : null;
                        $leadersCounts = array_key_exists($groupType, $leaderData) ? $leaderData[$groupType] : null;

                        $this->demographicCampGroupRepository->deleteAllByCampGroupAndGroupType($widgetDemographicCamp->getId(), $mainGroup->getId(), $groupType);

                        $demographicCampGroup = new AggregatedDemographicCampGroup();
                        $demographicCampGroup->setMCount($membersCounts ? $membersCounts['m'] : 0);
                        $demographicCampGroup->setFCount($membersCounts ? $membersCounts['w'] : 0);
                        $demographicCampGroup->setUCount($membersCounts ? $membersCounts['u'] : 0);
                        $demographicCampGroup->setMCountLeader($leadersCounts ? $leadersCounts['m'] : 0);
                        $demographicCampGroup->setFCountLeader($leadersCounts ? $leadersCounts['w'] : 0);
                        $demographicCampGroup->setUCountLeader($leadersCounts ? $leadersCounts['u'] : 0);
                        $demographicCampGroup->setDemographicCamp($widgetDemographicCamp);
                        $demographicCampGroup->setGroupType($groupType);
                        $demographicCampGroup->setGroup($mainGroup);
                        $this->em->persist($demographicCampGroup);
                    }
                    $this->em->persist($widgetDemographicCamp);
                }
            }
        }
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param array|PersonRole[] $personRoles
     * @param DateTimeImmutable $eventDate
     * @return PersonRole|null
     */
    private function getRelevantPersonRole(array $personRoles, DateTimeImmutable $eventDate): ?PersonRole
    {
        foreach (WidgetAggregator::$typePriority as $groupType) {
            foreach ($personRoles as $personRole) {
                if ($personRole->getDeletedAt() !== null && $personRole->getDeletedAt() < $eventDate) {
                    continue;
                }
                if ($personRole->getCreatedAt() > $eventDate) {
                    continue;
                }
                if (
                    !in_array(
                        $personRole->getRole()->getRoleType(),
                        array_merge(self::$leadersRoleTypes, self::$memberRoleTypes, self::$mainGroupRoleTypes)
                    )
                ) {
                    continue;
                }
                if ($groupType == $personRole->getGroup()->getGroupType()->getGroupType()) {
                    return $personRole;
                }
            }
        }
        return null;
    }

    /**
     * @param array $personIds
     * @param DateTimeImmutable $eventDate
     * @return array
     */
    private function processPersonIds(array $personIds, DateTimeImmutable $eventDate): array
    {
        $countByGroupTypeAndGender = [];
        foreach ($personIds as $personId) {
            /** @var PersonRole[] $personRoles */
            $personRoles = $this->personRoleRepository->findByPersonId($personId);
            if (!$personRoles) {
                continue;
            }
            $relevantRole = $this->getRelevantPersonRole($personRoles, $eventDate);
            if (!$relevantRole) {
                continue;
            }
            $gender = empty($relevantRole->getPerson()->getGender()) ? 'u' : $relevantRole->getPerson()->getGender();
            $groupType = $this->findLeaderGroupTypeForRoleType($relevantRole);
            if (!array_key_exists($groupType, $countByGroupTypeAndGender)) {
                $countByGroupTypeAndGender[$groupType] = ['m' => 0, 'w' => 0, 'u' => 0];
            }
            $countByGroupTypeAndGender[$groupType][$gender] += 1;
        }
        return $countByGroupTypeAndGender;
    }
}
