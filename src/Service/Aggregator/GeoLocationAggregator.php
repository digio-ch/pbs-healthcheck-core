<?php


namespace App\Service\Aggregator;


use App\Entity\Group;
use App\Entity\PersonRole;
use App\Entity\WidgetGeoLocation;
use App\Repository\GroupRepository;
use App\Repository\PersonRoleRepository;
use App\Repository\WidgetGeoLocationRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;

class GeoLocationAggregator extends WidgetAggregator
{
    private const NAME = 'widget.geo-location';

    /** @var EntityManager $em */
    private $em;

    /** @var GroupRepository $groupRepository */
    private $groupRepository;

    /** @var PersonRoleRepository $personRoleRepository */
    private $personRoleRepository;

    /** @var WidgetGeoLocationRepository $geoLocationRepository */
    private $geoLocationRepository;

    public function __construct(
        EntityManager $em,
        GroupRepository $groupRepository,
        PersonRoleRepository $personRoleRepository,
        WidgetGeoLocationRepository $geoLocationRepository
    ) {
        parent::__construct($groupRepository);

        $this->em = $em;
        $this->groupRepository = $groupRepository;
        $this->personRoleRepository = $personRoleRepository;
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
     * @throws \Doctrine\Persistence\Mapping\MappingException
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
                    $groupIds
                );

                $this->createWidgetsFromData($personGroups, $mainGroup, $startPointDate);

                $this->em->flush();
                $this->em->clear();
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
     * @throws \Doctrine\ORM\ORMException
     */
    private function createWidgetsFromData(array $data, Group $group, DateTime $dateTime)
    {
        /** @var PersonRole $personRole */
        foreach ($data as $personRole) {
            $widget = new WidgetGeoLocation();
            $widget->setGroup($group);
            $widget->setLabel($personRole->getPerson()->getAddress());
            $widget->setCreatedAt(new \DateTimeImmutable());
            $widget->setDataPointDate(new \DateTimeImmutable($dateTime->format('Y-m-d')));

            $location = $personRole->getPerson()->getGeoLocation();
            if (!is_null($location)) {
                $widget->setLongitude($location->getLongitude());
                $widget->setLatitude($location->getLatitude());
            }

            $this->em->persist($widget);
        }
    }
}
