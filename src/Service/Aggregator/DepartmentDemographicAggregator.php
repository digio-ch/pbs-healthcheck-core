<?php

namespace App\Service\Aggregator;

use App\Entity\Group;
use App\Entity\WidgetDemographicDepartment;
use App\Repository\GroupRepository;
use App\Repository\PersonRoleRepository;
use App\Repository\WidgetDemographicDepartmentRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DepartmentDemographicAggregator extends WidgetAggregator
{
    private const NAME = 'widget.demographic-department';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var WidgetDemographicDepartmentRepository
     */
    protected $widgetDemographicDepartmentRepository;

    /**
     * @var PersonRoleRepository
     */
    protected $personRoleRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * DepartmentDemographicAggregator constructor.
     * @param EntityManagerInterface $em
     * @param WidgetDemographicDepartmentRepository $widgetDemographicDepartmentRepository
     * @param PersonRoleRepository $personRoleRepository
     * @param GroupRepository $groupRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        WidgetDemographicDepartmentRepository $widgetDemographicDepartmentRepository,
        PersonRoleRepository $personRoleRepository,
        GroupRepository $groupRepository
    ) {
        $this->em = $em;
        $this->widgetDemographicDepartmentRepository = $widgetDemographicDepartmentRepository;
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

            $this->deleteLastPeriod($this->widgetDemographicDepartmentRepository, $mainGroup->getId());

            $existingData = $this->getAllDataPointDates(
                $this->widgetDemographicDepartmentRepository,
                $mainGroup->getId()
            );
            if ($this->isDataExistsForDate($startPointDate->format('Y-m-d 00:00:00'), $existingData)) {
                continue;
            }

            $mainGroup = $this->groupRepository->findOneBy(['id' => $mainGroup->getId()]);
            $subGroupIds = $this->groupRepository->findAllRelevantSubGroupIdsByParentGroupId($mainGroup->getId());
            $allIds = array_merge($subGroupIds, [$mainGroup->getId()]);

            $birthYears = $this->personRoleRepository->findBirthYearsForDepartment(
                $startPointDate->format('Y-m-d'),
                $allIds
            );
            if (!$birthYears) {
                continue;
            }

            foreach ($birthYears as $year) {
                $results = $this->personRoleRepository->findAllByYearWithRoleCountInGroup(
                    $startPointDate->format('Y-m-d'),
                    $year,
                    $allIds
                );
                $data = $this->processPersonIdsAndRoles($results, $allIds, $startPointDate->format('Y-m-d'));
                $this->createWidgetsFromData($data, $year, $mainGroup, $startPointDate);
            }

            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param array $data
     * @param string $year
     * @param Group $mainGroup
     * @param DateTime $startPointDate
     * @throws Exception
     */
    private function createWidgetsFromData(array $data, string $year, Group $mainGroup, DateTime $startPointDate)
    {
        foreach ($data as $groupType => $personTypeAndCountsByGender) {
            $widget = new WidgetDemographicDepartment();
            $widget->setGroup($mainGroup);
            $widget->setGroupType($groupType);
            $widget->setBirthyear(intval($year));

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
