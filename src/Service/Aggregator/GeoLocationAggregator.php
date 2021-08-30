<?php

namespace App\Service\Aggregator;

use App\Entity\Group;
use App\Entity\Person;
use App\Entity\Role;
use App\Entity\WidgetGeoLocation;
use App\Repository\GroupRepository;
use App\Repository\PersonRepository;
use App\Repository\PersonRoleRepository;
use App\Repository\RoleRepository;
use App\Repository\WidgetGeoLocationRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Parent_;

class GeoLocationAggregator extends WidgetAggregator
{
    private const NAME = 'widget.geo-location';

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var GroupRepository $groupRepository */
    private $groupRepository;

    /** @var PersonRepository $personRepository */
    private $personRepository;

    /** @var PersonRoleRepository $personRoleRepository */
    private $personRoleRepository;

    /** @var RoleRepository $roleRepository */
    private $roleRepository;

    /** @var WidgetGeoLocationRepository $geoLocationRepository */
    private $geoLocationRepository;

    public function __construct(
        EntityManagerInterface $em,
        GroupRepository $groupRepository,
        PersonRepository $personRepository,
        PersonRoleRepository $personRoleRepository,
        RoleRepository $roleRepository,
        WidgetGeoLocationRepository $geoLocationRepository
    ) {
        parent::__construct($groupRepository);

        $this->em = $em;
        $this->groupRepository = $groupRepository;
        $this->personRepository = $personRepository;
        $this->personRoleRepository = $personRoleRepository;
        $this->roleRepository = $roleRepository;
        $this->geoLocationRepository = $geoLocationRepository;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param DateTime|null $startDate
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function aggregate(DateTime $startDate = null): void
    {
        $mainGroups = $this->groupRepository->findAllParentGroups();

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
                $this->deleteLastPeriod($this->geoLocationRepository, $mainGroup->getId());

                $existingData = $this->getAllDataPointDates(
                    $this->geoLocationRepository,
                    $mainGroup->getId()
                );
                if ($this->isDataExistsForDate($startPointDate->format('Y-m-d 00:00:00'), $existingData)) {
                    continue;
                }

                $mainGroup = $this->groupRepository->findOneBy(['id' => $mainGroup->getId()]);
                $subGroupIds = $this->groupRepository->findAllRelevantSubGroupIdsByParentGroupId($mainGroup->getId());
                $groupIds = array_merge($subGroupIds, [$mainGroup->getId()]);

                $personGroups = $this->personRoleRepository->findAllByDate(
                    $startPointDate->format('Y-m-d'),
                    $groupIds,
                    array_merge(parent::$memberRoleTypes, parent::$leadersRoleTypes),
                    parent::$leadersRoleTypes,
                    parent::$roleTypePriority
                );

                $this->createWidgetsFromData($personGroups, $mainGroup, $startPointDate);
            }

            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param array $data
     * @param Group $group
     * @param DateTime $dateTime
     * @throws \Exception
     */
    private function createWidgetsFromData(array $data, Group $group, DateTime $dateTime)
    {
        foreach ($data as $singleData) {
            if (is_null($singleData['group_type']) || is_null($singleData['role_type'])) {
                continue;
            }

            $widget = new WidgetGeoLocation();
            $widget->setGroup($group);
            $widget->setLabel($singleData['nickname']);
            $widget->setGroupType(parent::$groupTypeByLeaderRoleType[$singleData['group_type']]);
            $widget->setPersonType($singleData['role_type']);
            $widget->setCreatedAt(new \DateTimeImmutable());
            $widget->setDataPointDate(new \DateTimeImmutable($dateTime->format('Y-m-d')));
            if (!is_null($singleData['longitude']) && !is_null($singleData['latitude'])) {
                $widget->setLongitude($singleData['longitude']);
                $widget->setLatitude($singleData['latitude']);
            }

            $this->em->persist($widget);
        }
    }

    /**
     * @param Role $role
     * @return string|null
     */
    private function filterPersonType(Role $role): ?string
    {
        foreach (self::$leadersRoleTypes as $leaderRole) {
            if (trim($role->getRoleType()) !== $leaderRole) {
                continue;
            }
            return 'leaders';
        }
        foreach (self::$memberRoleTypes as $memberRole) {
            if (trim($role->getRoleType()) !== $memberRole) {
                continue;
            }
            return 'members';
        }
        return null;
    }
}
