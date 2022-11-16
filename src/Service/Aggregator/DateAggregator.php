<?php

namespace App\Service\Aggregator;

use App\Entity\Aggregated\AggregatedDate;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedDateRepository;
use App\Repository\Midata\GroupRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DateAggregator extends WidgetAggregator
{
    private const NAME = 'general.date';

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var AggregatedDateRepository $widgetDateRepository */
    private AggregatedDateRepository $widgetDateRepository;

    public function __construct(
        EntityManagerInterface $em,
        GroupRepository $groupRepository,
        AggregatedDateRepository $widgetDateRepository
    ) {
        parent::__construct($groupRepository);

        $this->em = $em;
        $this->groupRepository = $groupRepository;
        $this->widgetDateRepository = $widgetDateRepository;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function aggregate(DateTime $startDate = null)
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
                $this->deleteLastPeriod($this->widgetDateRepository, $mainGroup->getId());

                $existingData = $this->getAllDataPointDates(
                    $this->widgetDateRepository,
                    $mainGroup->getId()
                );
                if ($this->isDataExistsForDate($startPointDate->format('Y-m-d 00:00:00'), $existingData)) {
                    continue;
                }

                $widget = new AggregatedDate();
                $widget->setGroup($mainGroup);
                $widget->setCreatedAt(new \DateTimeImmutable());
                $widget->setDataPointDate(new \DateTimeImmutable($startPointDate->format('Y-m-d')));

                $this->em->persist($widget);
            }

            $this->em->flush();
        }

        $this->em->flush();
        $this->em->clear();
    }
}
