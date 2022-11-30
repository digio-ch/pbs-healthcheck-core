<?php

namespace App\Service\Aggregator;

use App\Entity\Aggregated\AggregatedQuap;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Quap\Questionnaire;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Quap\QuestionnaireRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class QuapAggregator extends WidgetAggregator
{
    private const NAME = 'widget.quap';

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var AggregatedQuapRepository $quapRepository */
    private AggregatedQuapRepository $quapRepository;

    /** @var QuestionnaireRepository $questionnaireRepository */
    private QuestionnaireRepository $questionnaireRepository;

    public function __construct(
        EntityManagerInterface $em,
        GroupRepository $groupRepository,
        AggregatedQuapRepository $quapRepository,
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
        $mainGroups = $this->groupRepository->findParentGroups([
            'Group::Abteilung',
            'Group::Region',
            'Group::Kantonalverband',
        ]);

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
                    switch ($mainGroup->getGroupType()->getId()) {
                        case GroupType::REGION:
                        case GroupType::CANTON:
                            $questionnaireType = Questionnaire::TYPE_CANTON;
                            break;
                        case GroupType::DEPARTMENT:
                        default:
                            $questionnaireType = Questionnaire::TYPE_DEPARTMENT;
                            break;
                    }
                    $questionnaire = $this->questionnaireRepository->findOneBy(['type' => $questionnaireType]);

                    $currentQuap = new AggregatedQuap();
                    $currentQuap->setGroup($mainGroup);
                    $currentQuap->setQuestionnaire($questionnaire);
                    $currentQuap->setAnswers(json_decode('{}'));
                    $currentQuap->setComputedAnswers(json_decode('{}'));
                    $currentQuap->setCreatedAt(new \DateTimeImmutable());
                }

                $currentQuap->setDataPointDate(new \DateTimeImmutable($startPointDate->format('Y-m-d')));

                $this->em->persist($currentQuap);

                $newQuap = new AggregatedQuap();
                $newQuap->setGroup($currentQuap->getGroup());
                $newQuap->setQuestionnaire($currentQuap->getQuestionnaire());
                $newQuap->setAnswers($currentQuap->getAnswers());
                $newQuap->setComputedAnswers($currentQuap->getComputedAnswers());
                $newQuap->setAllowAccess($currentQuap->getAllowAccess());
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
