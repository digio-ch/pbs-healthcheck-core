<?php

namespace App\Service\Aggregator;

use App\Entity\Aggregated\AggregatedGeoLocation;
use App\Entity\Midata\Group;
use App\Entity\Midata\Role;
use App\Entity\Statistics\GroupGeoLocation;
use App\Repository\Aggregated\AggregatedGeoLocationRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\PersonRepository;
use App\Repository\Midata\PersonRoleRepository;
use App\Repository\Midata\RoleRepository;
use App\Repository\Statistics\GroupGeoLocationRepository;
use DateInterval;
use DateTime;
use Digio\Logging\GelfLogger;
use Doctrine\ORM\EntityManagerInterface;

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

    /** @var AggregatedGeoLocationRepository $geoLocationRepository */
    private $geoLocationRepository;

    private GroupGeoLocationRepository $groupGeoLocationRepository;

    protected GelfLogger $gelfLogger;


    public function __construct(
        EntityManagerInterface $em,
        GroupRepository $groupRepository,
        PersonRepository $personRepository,
        PersonRoleRepository $personRoleRepository,
        RoleRepository $roleRepository,
        AggregatedGeoLocationRepository $geoLocationRepository,
        GroupGeoLocationRepository $groupGeoLocationRepository,
        GelfLogger $gelfLogger
    ) {
        parent::__construct($groupRepository);

        $this->em = $em;
        $this->groupRepository = $groupRepository;
        $this->personRepository = $personRepository;
        $this->personRoleRepository = $personRoleRepository;
        $this->roleRepository = $roleRepository;
        $this->geoLocationRepository = $geoLocationRepository;
        $this->groupGeoLocationRepository = $groupGeoLocationRepository;
        $this->gelfLogger = $gelfLogger;
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
        $mainGroups = $this->groupRepository->findAllDepartmentalParentGroups();

        $minDate = $startDate !== null ? $startDate : new DateTime(self::AGGREGATION_START_DATE);
        $maxDate = new DateTime();
        $startPointDate = clone $minDate;

        // von 2014 - heute
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
                // Why are we checking if data for this date exists here and not at the start?!
                // Can we even reconstruct passed data?!
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
                    parent::$memberRoleTypes,
                    parent::$roleTypePriority
                );

                $this->aggregateGroupMeetingPoints($mainGroup, $startPointDate);
                $this->createWidgetsFromData($personGroups, $mainGroup, $startPointDate);
                $this->em->flush();
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

            $widget = new AggregatedGeoLocation();
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

    private function aggregateGroupMeetingPoints(Group $group, DateTime $dateTime)
    {
        $geoLocations = $this->groupGeoLocationRepository->findBy(['group' => $group->getId()]);
        foreach ($geoLocations as $geoLocation) {
            if (!is_numeric($geoLocation->getLong()) || !is_numeric($geoLocation->getLat())) {
                $this->gelfLogger->warning('Geolocation ' . $geoLocation->getId() . ' from group ' . $group->getId() . ' could not be aggregated because lat or long is not numeric. (lat: ' . $geoLocation->getLat() . ' ,long: ' . $geoLocation->getLong() . ')');
                continue;
            }
            $widget = new AggregatedGeoLocation();
            $widget->setGroup($group);
            $widget->setLabel(''); // no label
            $widget->setGroupType($group->getGroupType()->getGroupType()); // no group type
            $widget->setPersonType('group_meeting_point'); // isn't a person
            $widget->setShape('group_meeting_point');
            $widget->setCreatedAt(new \DateTimeImmutable());
            $widget->setDataPointDate(new \DateTimeImmutable($dateTime->format('Y-m-d')));
            $widget->setLongitude($geoLocation->getLong());
            $widget->setLatitude($geoLocation->getLat());

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
