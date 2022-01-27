<?php

namespace App\Service;

use App\DTO\Mapper\AnswersMapper;
use App\DTO\Mapper\QuestionnaireMapper;
use App\DTO\Model\AnswersDTO;
use App\DTO\Model\ExtendedAnswersDTO;
use App\Entity\Aspect;
use App\Entity\Group;
use App\Entity\GroupType;
use App\Entity\Help;
use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Entity\WidgetQuap;
use App\Exception\ApiException;
use App\Repository\AspectRepository;
use App\Repository\GroupRepository;
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
    /** @var QuestionnaireRepository $questionnaireRepository */
    private QuestionnaireRepository $questionnaireRepository;

    /** @var AspectRepository $aspectRepository */
    private AspectRepository $aspectRepository;

    /** @var QuestionRepository $questionRepository */
    private QuestionRepository $questionRepository;

    /** @var HelpRepository $helpRepository */
    private HelpRepository $helpRepository;

    /** @var LinkRepository $linkRepository */
    private LinkRepository $linkRepository;

    /** @var WidgetQuapRepository $quapRepository */
    private WidgetQuapRepository $quapRepository;

    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

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
        EntityManagerInterface $em
    ) {
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
            throw new ApiException(400, "Something went wrong please try again later");
        }

        $widgetQuap->setAnswers($json);

        $this->em->persist($widgetQuap);
        $this->em->flush();

        return $widgetQuap;
    }

    public function updateAllowAccess(Group $group, bool $allowAccess): WidgetQuap
    {
        $widgetQuap = $this->quapRepository->findOneBy([
            "dataPointDate" => null,
            "group" => $group->getId()
        ]);

        if (!$widgetQuap) {
            throw new ApiException(400, "Something went wrong please try again later");
        }

        $widgetQuap->setAllowAccess($allowAccess);

        $this->em->persist($widgetQuap);
        $this->em->flush();

        return $widgetQuap;
    }

    /**
     * @param Group $group
     * @param \DateTimeImmutable|null $dateTime
     * @return AnswersDTO
     */
    public function getAnswers(Group $group, ?\DateTimeImmutable $dateTime): AnswersDTO
    {
        $widgetQuap = $this->quapRepository->findOneBy([
            "dataPointDate" => $dateTime !== null ? $dateTime->setTime(0, 0) : null,
            "group" => $group->getId()
        ]);

        return AnswersMapper::mapAnswers($widgetQuap);
    }

    public function getAnswersForSubdepartments(Group $group, ?\DateTimeImmutable $date): array
    {
        $parentGroupType = $group->getGroupType()->getId();
        $groupTypes = null;
        if ($parentGroupType === GroupType::CANTON) {
            $groupTypes = [
                'Group::Abteilung',
            ];
        } elseif ($parentGroupType === GroupType::FEDERATION) {
            $groupTypes = [
                'Group::Kantonalverband',
            ];
        }

        $subdepartments = $this->groupRepository->findAllRelevantSubGroupsByParentGroupId($group->getId(), $groupTypes);
        if (!$subdepartments) {
            $subdepartments = [];
        }

        $ids = [];
        foreach ($subdepartments as $group) {
            $ids[] = $group['id'];
        }

        $answers = $this->quapRepository->findAllAnswers($ids, $date !== null ? $date->setTime(0, 0) : null);

        $dtos = [];
        /** @var WidgetQuap $answer */
        foreach ($answers as $answer) {
            $answerGroup = $answer->getGroup();

            $dto = new ExtendedAnswersDTO();
            $dto->setAnswers($answer->getAnswers());
            $dto->setComputedAnswers($answer->getComputedAnswers());
            $dto->setGroupId($answerGroup->getId());
            $dto->setGroupName($answerGroup->getName());
            $dto->setGroupTypeId($answerGroup->getGroupType()->getId());
            $dto->setGroupType($answerGroup->getGroupType()->getGroupType());

            $dtos[] = $dto;
        }

        return $dtos;
    }
}
