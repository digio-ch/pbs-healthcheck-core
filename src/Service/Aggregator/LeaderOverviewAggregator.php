<?php

namespace App\Service\Aggregator;

use App\Entity\Group;
use App\Entity\LeaderOverviewLeader;
use App\Entity\LeaderOverviewQualification;
use App\Entity\PersonQualification;
use App\Entity\QualificationType;
use App\Entity\WidgetLeaderOverview;
use App\Repository\GroupRepository;
use App\Repository\PersonQualificationRepository;
use App\Repository\PersonRoleRepository;
use App\Repository\WidgetLeaderOverviewRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class LeaderOverviewAggregator extends WidgetAggregator
{
    private const NAME = 'widget.leader-overview';

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
     * @var PersonQualificationRepository
     */
    protected $personQualificationRepository;

    /**
     * @var WidgetLeaderOverviewRepository
     */
    protected $widgetLeaderOverviewRepository;

    /**
     * LeaderOverviewAggregator constructor.
     * @param EntityManagerInterface $em
     * @param PersonRoleRepository $personRoleRepository
     * @param GroupRepository $groupRepository
     * @param WidgetLeaderOverviewRepository $widgetLeaderOverviewRepository
     * @param PersonQualificationRepository $personQualificationRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        PersonRoleRepository $personRoleRepository,
        GroupRepository $groupRepository,
        WidgetLeaderOverviewRepository $widgetLeaderOverviewRepository,
        PersonQualificationRepository $personQualificationRepository
    ) {
        $this->em = $em;
        $this->personRoleRepository = $personRoleRepository;
        $this->groupRepository = $groupRepository;
        $this->widgetLeaderOverviewRepository = $widgetLeaderOverviewRepository;
        $this->personQualificationRepository = $personQualificationRepository;
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
    public function aggregate(string $groupId, DateTime $startDate = null)
    {
        /** @var Group $mainGroup */
        $mainGroup = $this->groupRepository->find($groupId);

        $minDate = $startDate !== null ? $startDate : new DateTime(self::AGGREGATION_START_DATE);
        $maxDate = new DateTime();
        $startPointDate = clone $minDate;

        while ($startPointDate->getTimestamp() < $maxDate->getTimestamp()) {
            $startPointDate->add(new DateInterval("P1M"));
            $startPointDate->modify('first day of this month');

            if ($startPointDate->getTimestamp() > $maxDate->getTimestamp()) {
                $startPointDate = clone $maxDate;
            }

            $this->deleteLastPeriod($this->widgetLeaderOverviewRepository, $mainGroup->getId());

            $existingData = $this->getAllDataPointDates(
                $this->widgetLeaderOverviewRepository,
                $mainGroup->getId()
            );
            if ($this->isDataExistsForDate($startPointDate->format('Y-m-d 00:00:00'), $existingData)) {
                continue;
            }

            $mainGroup = $this->groupRepository->findOneBy(['id' => $mainGroup->getId()]);
            $subGroups = $this->groupRepository->findAllRelevantSubGroupsByParentGroupId($mainGroup->getId());
            $subGroupsByType = $this->groupSubGroupsByGroupType($subGroups);
            foreach ($subGroupsByType as $groupsAndType) {
                $groupType = $groupsAndType['group_type'];
                $groupIds = $groupsAndType['groups'];
                $mCount = $this->personRoleRepository->findMemberCountForPeriodByGenderGroupTypeAndGroupIds(
                    $startPointDate->format('Y-m-d'),
                    $groupIds,
                    'm'
                );
                $fCount = $this->personRoleRepository->findMemberCountForPeriodByGenderGroupTypeAndGroupIds(
                    $startPointDate->format('Y-m-d'),
                    $groupIds,
                    'w'
                );
                $uCount = $this->personRoleRepository->findMemberCountForPeriodByGenderGroupTypeAndGroupIds(
                    $startPointDate->format('Y-m-d'),
                    $groupIds
                );
                $widget = new WidgetLeaderOverview();
                $widget->setGroup($mainGroup);
                $widget->setGroupType($groupType);
                $widget->setMCount($mCount[0]);
                $widget->setFCount($fCount[0]);
                $widget->setUCount($uCount[0]);
                $widget->setCreatedAt(new DateTimeImmutable());
                $widget->setDataPointDate(new DateTimeImmutable($startPointDate->format('Y-m-d')));

                $this->aggregateLeadersData($mainGroup, $startPointDate, $groupType, $groupIds, $widget);
                $this->em->persist($widget);
            }

            $allSubGroupIds = [];
            foreach ($subGroups as $group) {
                $allSubGroupIds[] = $group['id'];
            }
            $this->aggregateDataForMainGroup($mainGroup, $startPointDate, $allSubGroupIds);
            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param Group $mainGroup
     * @param DateTime $date
     * @param string $groupType
     * @param array $subGroupIds
     * @param WidgetLeaderOverview $widget
     * @throws DBALException
     * @throws Exception
     */
    private function aggregateLeadersData(
        Group $mainGroup,
        DateTime $date,
        string $groupType,
        array $subGroupIds,
        WidgetLeaderOverview $widget
    ) {
        $leaders = $this->personRoleRepository->findAllLeadersByDateAndGroupType(
            $date->format('Y-m-d'),
            array_merge([$mainGroup->getId()], $subGroupIds),
            $groupType
        );
        if ($leaders === null || count($leaders) === 0) {
            return;
        }
        foreach ($leaders as $leaderData) {
            $leaderOverviewLeader = new LeaderOverviewLeader();
            $leaderOverviewLeader->setName($leaderData['nickname']);
            $leaderOverviewLeader->setGender(empty($leaderData['gender']) ? 'u' : $leaderData['gender']);
            $leaderOverviewLeader->setBirthday(new DateTimeImmutable($leaderData['birthday']));
            $leaderOverviewLeader->setLeaderOverview($widget);
            $this->aggregateQualificationData($leaderData['id'], $date, $leaderOverviewLeader);
            $this->em->persist($leaderOverviewLeader);
        }
    }

    /**
     * @param int $personId
     * @param DateTime $date
     * @param LeaderOverviewLeader $leader
     * @throws Exception
     */
    private function aggregateQualificationData(int $personId, DateTime $date, LeaderOverviewLeader $leader)
    {
        $qualifications = $this->personQualificationRepository->findQualificationsForPersonByDate(
            $personId,
            $date->format('Y-m-d')
        );
        if ($qualifications === null || count($qualifications) === 0) {
            return;
        }
        /** @var PersonQualification $qualification */
        foreach ($qualifications as $qualification) {
            $leaderOverviewQualification = new LeaderOverviewQualification();
            $leaderOverviewQualification->setLeaderOverviewLeader($leader);
            $leaderOverviewQualification->setQualificationType(
                $this->em->getReference(QualificationType::class, $qualification['id'])
            );
            $leaderOverviewQualification->setEventOrigin($qualification['event_origin']);
            $endAt = $qualification['end_at'] ? new DateTimeImmutable($qualification['end_at']) : null;
            $leaderOverviewQualification->setExpiresAt($endAt);

            if ($qualification['validity'] === null || $endAt == null) {
                $leaderOverviewQualification->setState('valid');
                $this->em->persist($leaderOverviewQualification);
                continue;
            }

            if ($endAt->format('Y') == $date->format('Y')) {
                $leaderOverviewQualification->setState('expiring_soon');
                $this->em->persist($leaderOverviewQualification);
                continue;
            }

            if ($endAt->getTimestamp() <= $date->getTimestamp()) {
                $leaderOverviewQualification->setState('expired');
                $this->em->persist($leaderOverviewQualification);
                continue;
            }

            $leaderOverviewQualification->setState('valid');
            $this->em->persist($leaderOverviewQualification);
        }
    }

    /**
     * @param Group $mainGroup
     * @param DateTime $date
     * @param array $subGroupIds
     * @throws Exception
     */
    private function aggregateDataForMainGroup(Group $mainGroup, DateTime $date, array $subGroupIds)
    {
        $widget = new WidgetLeaderOverview();
        $widget->setGroup($mainGroup);
        $widget->setGroupType($mainGroup->getGroupType()->getGroupType());
        $widget->setCreatedAt(new DateTimeImmutable());
        $widget->setDataPointDate(new DateTimeImmutable($date->format('Y-m-d')));
        $allGroupIds = array_merge([$mainGroup->getId()], $subGroupIds);
        $mCount = $this->personRoleRepository->findTotalLeaderCountForGenderAllSubGroupTypesAndDate(
            $allGroupIds,
            $date->format('Y-m-d'),
            'm'
        );
        $fCount = $this->personRoleRepository->findTotalLeaderCountForGenderAllSubGroupTypesAndDate(
            $allGroupIds,
            $date->format('Y-m-d'),
            'w'
        );
        $uCount = $this->personRoleRepository->findTotalLeaderCountForGenderAllSubGroupTypesAndDate(
            $allGroupIds,
            $date->format('Y-m-d')
        );
        $widget->setMCount($mCount[0]);
        $widget->setFCount($fCount[0]);
        $widget->setUCount($uCount[0]);
        $this->aggregateLeadersData($mainGroup, $date, $mainGroup->getGroupType()->getGroupType(), [], $widget);
        $this->em->persist($widget);
    }
}
