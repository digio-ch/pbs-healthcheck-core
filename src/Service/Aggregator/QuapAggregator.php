<?php


namespace App\Service\Aggregator;


use App\Entity\Group;
use App\Entity\WidgetQuap;
use App\Repository\GroupRepository;
use App\Repository\WidgetQuapRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class QuapAggregator extends WidgetAggregator
{
    private const NAME = 'feature.quap';

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var GroupRepository $groupRepository */
    private $groupRepository;

    /** @var WidgetQuapRepository $quapRepository */
    private $quapRepository;

    public function __construct(
        EntityManagerInterface $em,
        GroupRepository $groupRepository,
        WidgetQuapRepository $quapRepository
    ) {
        parent::__construct($groupRepository);

        $this->em = $em;
        $this->groupRepository = $groupRepository;
        $this->quapRepository = $quapRepository;
    }

    /**
     * @return string
     */
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
                $this->deleteLastPeriod($this->quapRepository, $mainGroup->getId());

                $existingData = $this->getAllDataPointDates(
                    $this->quapRepository,
                    $mainGroup->getId()
                );
                if ($this->isDataExistsForDate($startPointDate->format('Y-m-d 00:00:00'), $existingData)) {
                    continue;
                }

                $currentQuap = $this->quapRepository->findCurrentForGroup($mainGroup->getId());

                if (is_null($currentQuap)) {
                    // $newQuap = new WidgetQuap();
                    // $newQuap->setGroup($mainGroup);
                    // TODO GET QUESTIONNAIRE
                    // TODO EVALUATE THE CORRET QUESTIONNAIRE FOR THIS GROUP
                    // $newQuap->setQuestionnaire(null);
                    // $newQuap->setAnswers('{}');

                    // $this->em->persist($newQuap);
                    continue;
                }

                // TODO IF NULL CREATE EMPTY ONE

                $newQuap = new WidgetQuap();
                $newQuap->setGroup($currentQuap->getGroup());
                $newQuap->setQuestionnaire($currentQuap->getQuestionnaire());
                $newQuap->setAnswers($currentQuap->getAnswers());

                $this->em->persist($newQuap);
            }

            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
        $this->em->clear();
    }
}
