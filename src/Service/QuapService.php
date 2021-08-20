<?php

namespace App\Service;

use App\DTO\Mapper\QuestionnaireMapper;
use App\Entity\Group;
use App\Entity\Questionnaire;
use App\Entity\WidgetQuap;
use App\Repository\AspectRepository;
use App\Repository\HelpRepository;
use App\Repository\QuestionnaireRepository;
use App\Repository\QuestionRepository;
use App\Repository\WidgetQuapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\BadMessageException;

class QuapService
{
    /**
     * @var QuestionnaireRepository $questionnaireRepository
     */
    private $questionnaireRepository;

    /**
     * @var AspectRepository $aspectRepository
     */
    private $aspectRepository;

    /**
     * @var QuestionRepository $questionRepository
     */
    private $questionRepository;

    /**
     * @var HelpRepository $helpRepository
     */
    private $helpRepository;

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
    public function __construct(
        QuestionnaireRepository $questionnaireRepository,
        AspectRepository        $aspectRepository,
        QuestionRepository      $questionRepository,
        HelpRepository          $helpRepository,
        WidgetQuapRepository    $quapRepository,
        EntityManagerInterface  $em)
    {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->aspectRepository = $aspectRepository;
        $this->questionRepository = $questionRepository;
        $this->helpRepository = $helpRepository;
        $this->quapRepository = $quapRepository;
        $this->em = $em;
    }


    public function getQuestionnaireByType(string $type, string $locale, \DateTime $dateTime): ?Questionnaire
    {
        $questionnaire = $this->questionnaireRepository->findOneBy(["type" => $type]);
        $questionnaire->setAspects(new ArrayCollection($this->aspectRepository->getExisting($questionnaire->getId(), $dateTime)));
        foreach ($questionnaire->getAspects() as $aspect) {
            $aspect->setQuestions(new ArrayCollection($this->questionRepository->getExisting($aspect->getId(), $dateTime)));

            foreach ($aspect->getQuestions() as $question) {
                $question->setHelp(new ArrayCollection($this->helpRepository->getExisting($question->getId(), $dateTime)));
            }
        }

        return $questionnaire;
    }

    public function submitAnswers(Group $group, array $json): WidgetQuap
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

    public function getAnswers(Group $group, \DateTime $dateTime): WidgetQuap
    {
        return $this->quapRepository->findOneBy([
            "dataPointDate" => null,
            "group" => $group->getId()
        ]);
    }
}