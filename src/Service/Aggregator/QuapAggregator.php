<?php

namespace App\Service\Aggregator;

use App\Entity\Group;
use App\Entity\WidgetQuap;
use App\Repository\GroupRepository;
use App\Repository\QuestionnaireRepository;
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

    /** @var QuestionnaireRepository $questionnaireRepository */
    private $questionnaireRepository;

    public function __construct(
        EntityManagerInterface $em,
        GroupRepository $groupRepository,
        WidgetQuapRepository $quapRepository,
        QuestionnaireRepository $questionnaireRepository
    ) {
        parent::__construct($groupRepository);

        $this->em = $em;
        $this->groupRepository = $groupRepository;
        $this->quapRepository = $quapRepository;
        $this->questionnaireRepository = $questionnaireRepository;
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
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
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
                    // TODO EVALUATE THE CORRET QUESTIONNAIRE FOR THIS GROUP
                    $questionnaire = $this->questionnaireRepository->find(1);

                    $currentQuap = new WidgetQuap();
                    $currentQuap->setGroup($mainGroup);
                    $currentQuap->setQuestionnaire($questionnaire);
                    $currentQuap->setAnswers(json_decode('{}'));
                    $currentQuap->setCreatedAt(new \DateTimeImmutable());
                }

                $currentQuap->setDataPointDate(new \DateTimeImmutable($startPointDate->format('Y-m-d')));

                $this->em->persist($currentQuap);

                $newQuap = new WidgetQuap();
                $newQuap->setGroup($currentQuap->getGroup());
                $newQuap->setQuestionnaire($currentQuap->getQuestionnaire());
                $newQuap->setAnswers($currentQuap->getAnswers());
                $newQuap->setCreatedAt(new \DateTimeImmutable());

                $this->em->persist($newQuap);
            }

            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
        $this->em->clear();
    }
}
