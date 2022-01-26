<?php

namespace App\Service\Aggregator;

use App\Entity\Group;
use App\Entity\PersonRole;
use App\Entity\WidgetDemographicEnteredLeft;
use App\Repository\GroupRepository;
use App\Repository\PersonRepository;
use App\Repository\PersonRoleRepository;
use App\Repository\WidgetDemographicEnteredLeftRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DemographicEnteredLeftAggregator extends WidgetAggregator
{
    public const NAME = 'widget.demographic-entered-left';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var WidgetDemographicEnteredLeftRepository
     */
    protected $widgetDemographicEnteredLeftRepository;

    /*
     * @var PersonRoleRepository
     */
    protected $personRoleRepository;

    /**
     * @var PersonRepository
     */
    protected $personRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * DemographicEnteredLeftAggregator constructor.
     * @param EntityManagerInterface $em
     * @param WidgetDemographicEnteredLeftRepository $widgetDemographicEnteredLeftRepository
     * @param PersonRoleRepository $personRoleRepository
     * @param GroupRepository $groupRepository
     * @param PersonRepository $personRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        WidgetDemographicEnteredLeftRepository $widgetDemographicEnteredLeftRepository,
        PersonRoleRepository $personRoleRepository,
        GroupRepository $groupRepository,
        PersonRepository $personRepository
    ) {
        $this->em = $em;
        $this->widgetDemographicEnteredLeftRepository = $widgetDemographicEnteredLeftRepository;
        $this->personRoleRepository = $personRoleRepository;
        $this->groupRepository = $groupRepository;
        $this->personRepository = $personRepository;
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
     */
    public function aggregate(DateTime $startDate = null)
    {
        $mainGroups = $this->groupRepository->findAllDepartmentalParentGroups();

        $minDate = $startDate !== null ? $startDate : new DateTime(self::AGGREGATION_START_DATE);
        $maxDate = new DateTime();
        $startPointDate = clone $minDate;
        $this->em->getConfiguration()->setSQLLogger(null);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        while ($startPointDate->getTimestamp() < $maxDate->getTimestamp()) {
            $prevPointDate = clone $startPointDate;
            $startPointDate->add(new DateInterval("P1M"));
            $startPointDate->modify('first day of this month');

            if ($startPointDate->getTimestamp() > $maxDate->getTimestamp()) {
                $startPointDate = clone $maxDate;
            }

            /** @var Group $mainGroup */
            foreach ($mainGroups as $mainGroup) {
                $this->deleteLastPeriod($this->widgetDemographicEnteredLeftRepository, $mainGroup->getId());

                $existingData = $this->getAllDataPointDates(
                    $this->widgetDemographicEnteredLeftRepository,
                    $mainGroup->getId()
                );
                if ($this->isDataExistsForDate($startPointDate->format('Y-m-d 00:00:00'), $existingData)) {
                    continue;
                }

                $mainGroup = $this->groupRepository->findOneBy(['id' => $mainGroup->getId()]);
                $allSubGroupIds = $this->groupRepository->findAllRelevantSubGroupIdsByParentGroupId(
                    $mainGroup->getId()
                );
                $allGroupIds = array_merge($allSubGroupIds, [$mainGroup->getId()]);

                $newPeople = $this->personRoleRepository->findAllNewPeopleByIdsAndGroup(
                    $allGroupIds,
                    $prevPointDate->format('Y-m-d'),
                    $startPointDate->format('Y-m-d')
                );
                $processedNewByGroupTypePeopleTypeAndGender = $this->processNewPersonsForGroup(
                    $newPeople,
                    $prevPointDate->format('Y-m-d'),
                    $startPointDate->format('Y-m-d'),
                    $allGroupIds
                );

                $leftPeople = $this->personRoleRepository->findAllLeftPeopleByIdsAndGroup(
                    $allGroupIds,
                    $prevPointDate->format('Y-m-d'),
                    $startPointDate->format('Y-m-d')
                );
                $processedLeftByGroupTypePeopleTypeAndGender = $this->processLeftPersonsForGroup(
                    $leftPeople,
                    $allGroupIds,
                    $prevPointDate,
                    $startPointDate
                );

                $this->processDataForGroup(
                    $processedNewByGroupTypePeopleTypeAndGender,
                    $processedLeftByGroupTypePeopleTypeAndGender,
                    $mainGroup,
                    $startPointDate->format('Y-m-d')
                );
            }
            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param array $newPeople
     * @param array $existPeople
     * @param Group $mainGroup
     * @param string $currentDate
     * @throws Exception
     */
    private function processDataForGroup(
        array $newPeople,
        array $existPeople,
        Group $mainGroup,
        string $currentDate
    ) {
        foreach (WidgetAggregator::$typePriority as $groupType) {
            $widget = new WidgetDemographicEnteredLeft();
            $widget->setGroup($mainGroup);
            $widget->setGroupType($groupType);

            $membersNewCount = array_key_exists($groupType, $newPeople) ? $newPeople[$groupType]['members'] : null;
            $leadersNewCount = array_key_exists($groupType, $newPeople) ? $newPeople[$groupType]['leaders'] : null;
            $membersExitCount = array_key_exists($groupType, $existPeople) ? $existPeople[$groupType]['members'] : null;
            $leadersExitCount = array_key_exists($groupType, $existPeople) ? $existPeople[$groupType]['leaders'] : null;

            $widget->setNewCountM($membersNewCount ? $membersNewCount['m'] : 0);
            $widget->setNewCountLeaderM($leadersNewCount ? $leadersNewCount['m'] : 0);
            $widget->setNewCountF($membersNewCount ? $membersNewCount['w'] : 0);
            $widget->setNewCountLeaderF($leadersNewCount ? $leadersNewCount['w'] : 0);
            $widget->setNewCountU($membersNewCount ? $membersNewCount['u'] : 0);
            $widget->setNewCountLeaderU($leadersNewCount ? $leadersNewCount['u'] : 0);
            $widget->setExitCountM($membersExitCount ? $membersExitCount['m'] : 0);
            $widget->setExitCountLeaderM($leadersExitCount ? $leadersExitCount['m'] : 0);
            $widget->setExitCountF($membersExitCount ? $membersExitCount['w'] : 0);
            $widget->setExitCountLeaderF($leadersExitCount ? $leadersExitCount['w'] : 0);
            $widget->setExitCountU($membersExitCount ? $membersExitCount['u'] : 0);
            $widget->setExitCountLeaderU($leadersExitCount ? $leadersExitCount['u'] : 0);

            $widget->setCreatedAt(new DateTimeImmutable());
            $widget->setDataPointDate(new DateTimeImmutable($currentDate));
            $this->em->persist($widget);
        }
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param array $results
     * @param string $startDate
     * @param string $endDate
     * @param array $groupIds
     * @return array
     */
    private function processNewPersonsForGroup(array $results, string $startDate, string $endDate, array $groupIds)
    {
        $countByGroupTypePersonTypeAndGender = [];
        foreach ($results as $personIdGenderAndPreviousRoleCount) {
            if ($personIdGenderAndPreviousRoleCount['previous_active_role_count'] > 0) {
                continue;
            }
            /** @var PersonRole[]|null $relevantPersonRoles */
            $relevantPersonRoles = $this->personRoleRepository->findPersonRoleInPeriod(
                $startDate,
                $endDate,
                $groupIds,
                $personIdGenderAndPreviousRoleCount['person_id']
            );
            if (!$relevantPersonRoles) {
                continue;
            }
            $groupTypeAndPersonType = $this->findGroupAndPersonTypeByPersonRolesHierarchy($relevantPersonRoles);
            if ($groupTypeAndPersonType === null) {
                continue;
            }
            $this->addCount(
                $countByGroupTypePersonTypeAndGender,
                $groupTypeAndPersonType,
                $personIdGenderAndPreviousRoleCount['gender']
            );
        }
        return $countByGroupTypePersonTypeAndGender;
    }

    /**
     * @param array $results
     * @param array $allGroupIds
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    private function processLeftPersonsForGroup(
        array $results,
        array $allGroupIds,
        DateTime $startDate,
        DateTime $endDate
    ): array {
        $countByGroupTypePersonTypeAndGender = [];
        foreach ($results as $personIdGenderAndActiveRoleCount) {
            if ($personIdGenderAndActiveRoleCount['active_roles_in_group'] > 0) {
                continue;
            }
            /** @var PersonRole[]|null $personRolesDeletedInPeriod */
            $personRolesDeletedInPeriod = $this->personRoleRepository->findAllDeletedForGroupPersonIdAndPeriod(
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
                $allGroupIds,
                $personIdGenderAndActiveRoleCount['person_id']
            );
            if (!$personRolesDeletedInPeriod) {
                continue;
            }
            $groupTypeAndPersonType = $this->findGroupAndPersonTypeByPersonRolesHierarchy($personRolesDeletedInPeriod);
            if ($groupTypeAndPersonType === null) {
                continue;
            }
            $this->addCount(
                $countByGroupTypePersonTypeAndGender,
                $groupTypeAndPersonType,
                $personIdGenderAndActiveRoleCount['gender']
            );
        }
        return $countByGroupTypePersonTypeAndGender;
    }


    /**
     * @param array $countByGroupTypePersonTypeAndGender
     * @param array $groupTypeAndPersonType
     * @param string|null $gender
     */
    private function addCount(
        array &$countByGroupTypePersonTypeAndGender,
        array $groupTypeAndPersonType,
        ?string $gender
    ): void {
        $gender = empty($gender) ? 'u' : $gender;
        $personType = $groupTypeAndPersonType[0];
        $groupType = $groupTypeAndPersonType[1];

        if (!array_key_exists($groupType, $countByGroupTypePersonTypeAndGender)) {
            $countByGroupTypePersonTypeAndGender[$groupType] = [
                'members' => ['m' => 0, 'w' => 0, 'u' => 0],
                'leaders' => ['m' => 0, 'w' => 0, 'u' => 0]
            ];
        }
        $countByGroupTypePersonTypeAndGender[$groupType][$personType][$gender] += 1;
    }
}
