<?php

namespace App\Service\Aggregator;

use App\Entity\Group;
use App\Entity\GroupType;
use App\Entity\Questionnaire;
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
    private EntityManagerInterface $em;

    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var WidgetQuapRepository $quapRepository */
    private WidgetQuapRepository $quapRepository;

    /** @var QuestionnaireRepository $questionnaireRepository */
    private QuestionnaireRepository $questionnaireRepository;

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
        $mainGroups = $this->groupRepository->findParentGroups([
            'Group::Abteilung',
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
                        case GroupType::CANTON:
                            $questionnaireType = Questionnaire::TYPE_CANTON;
                            break;
                        case GroupType::DEPARTMENT:
                        default:
                            $questionnaireType = Questionnaire::TYPE_DEPARTMENT;
                            break;
                    }
                    $questionnaire = $this->questionnaireRepository->findBy(['type' => $questionnaireType]);
                    if (is_array($questionnaire)) {
                        $questionnaire = $questionnaire[0];
                    }

                    $currentQuap = new WidgetQuap();
                    $currentQuap->setGroup($mainGroup);
                    $currentQuap->setQuestionnaire($questionnaire);
                    $currentQuap->setAnswers(json_decode('{}'));
                    $currentQuap->setComputedAnswers(json_decode('{}'));
                    $currentQuap->setCreatedAt(new \DateTimeImmutable());
                }

                $currentQuap->setDataPointDate(new \DateTimeImmutable($startPointDate->format('Y-m-d')));

                $this->em->persist($currentQuap);

                $newQuap = new WidgetQuap();
                $newQuap->setGroup($currentQuap->getGroup());
                $newQuap->setQuestionnaire($currentQuap->getQuestionnaire());
                $newQuap->setAnswers($currentQuap->getAnswers());
                $newQuap->setComputedAnswers($currentQuap->getComputedAnswers());
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
