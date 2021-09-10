<?php

namespace App\Service;

use App\DTO\Mapper\QuestionnaireMapper;
use App\Entity\Aspect;
use App\Entity\Group;
use App\Entity\Help;
use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Entity\WidgetQuap;
use App\Repository\AspectRepository;
use App\Repository\HelpRepository;
use App\Repository\LinkRepository;
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
     * @var LinkRepository $linkRepository
     */
    private $linkRepository;

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
     * @param AspectRepository $aspectRepository
     * @param QuestionRepository $questionRepository
     * @param HelpRepository $helpRepository
     * @param LinkRepository $linkRepository
     * @param WidgetQuapRepository $quapRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(
        QuestionnaireRepository $questionnaireRepository,
        AspectRepository $aspectRepository,
        QuestionRepository $questionRepository,
        HelpRepository $helpRepository,
        LinkRepository $linkRepository,
        WidgetQuapRepository $quapRepository,
        EntityManagerInterface $em)
    {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->aspectRepository = $aspectRepository;
        $this->questionRepository = $questionRepository;
        $this->helpRepository = $helpRepository;
        $this->linkRepository = $linkRepository;
        $this->quapRepository = $quapRepository;
        $this->em = $em;
    }

    /**
     * @param string $type
     * @param string $locale
     * @param string $dateTime
     * @return Questionnaire|null
     */
    public function getQuestionnaireByType(string $type, string $locale, string $dateTime): ?Questionnaire
    {
        $questionnaire = $this->questionnaireRepository->findOneBy(["type" => $type]);
        $questionnaire->setAspects(new ArrayCollection($this->aspectRepository->getExisting($questionnaire->getId(), $dateTime)));

        /** @var Aspect $aspect */
        foreach ($questionnaire->getAspects() as $aspect) {
            $aspect->setQuestions(new ArrayCollection($this->questionRepository->getExisting($aspect->getId(), $dateTime)));

            /** @var Question $question */
            foreach ($aspect->getQuestions() as $question) {
                $question->setHelp(new ArrayCollection($this->helpRepository->getExisting($question->getId(), $dateTime)));

                /** @var Help $help */
                foreach ($question->getHelp() as $help) {
                    switch ($locale) {
                        case (str_contains($locale, "it")):
                            $help->setLinksIt(new ArrayCollection($this->linkRepository->findBy([ 'helpIt' => $help])));
                            break;
                        case (str_contains($locale, "fr")):
                            $help->setLinksFr(new ArrayCollection($this->linkRepository->findBy([ 'helpFr' => $help])));
                            break;
                        default:
                            $help->setLinksDe(new ArrayCollection($this->linkRepository->findBy([ 'helpDe' => $help])));
                            break;
                    }
                }
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

    /**
     * @param Group $group
     * @param string|null $dateTime
     * @return WidgetQuap
     */
    public function getAnswers(Group $group, ?\DateTimeImmutable $dateTime): WidgetQuap
    {
        return $this->quapRepository->findOneBy([
            "dataPointDate" => $dateTime !== null ? $dateTime->setTime(0, 0) : null,
            "group" => $group->getId()
        ]);
    }
}