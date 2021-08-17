<?php

namespace App\Service;

use App\DTO\Mapper\QuapQuestionnaireMapper;
use App\Entity\Group;
use App\Entity\WidgetQuap;
use App\Repository\QuestionnaireRepository;
use App\Repository\WidgetQuapRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\BadMessageException;

class QuapService
{
    /**
     * @var QuestionnaireRepository $questionnaireRepository
     */
    private $questionnaireRepository;

    /**
     * @var WidgetQuapRepository $quapRepository
     */
    private $quapRepository;

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @param QuestionnaireRepository $questionnaireRepository
     * @param WidgetQuapRepository $quapRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(QuestionnaireRepository $questionnaireRepository, WidgetQuapRepository $quapRepository, EntityManagerInterface $em)
    {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->quapRepository = $quapRepository;
        $this->em = $em;
    }


    public function getQuestionnaireDataByType(string $type, string $locale, \DateTime $dateTime): array
    {

        $questionnaires = $this->questionnaireRepository->findBy([
            "type" => $type
        ]);

        $aspects = QuapQuestionnaireMapper::createFromEntities($questionnaires, $locale, $dateTime);

        return $aspects;
    }

    public function submitWidgetQuapAnswers(Group $group, array $json): WidgetQuap
    {
        $widgetQuap = $this->quapRepository->findOneBy([
            "dataPointDate" => null,
            "group" => $group->getId()
        ]);

        if (!$widgetQuap) {

            // TODO get correct questionnaire
            $questionnaire = $this->questionnaireRepository->find(1);


            $widgetQuap = new WidgetQuap();
            $widgetQuap->setQuestionnaire($questionnaire);
            $widgetQuap->setGroup($group);
            $widgetQuap->setCreatedAt(new \DateTimeImmutable("now"));
        }

        $widgetQuap->setAnswers($json);

        $this->em->persist($widgetQuap);
        $this->em->flush();

        return $widgetQuap;
    }
}