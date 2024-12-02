<?php

namespace App\Service\Apps\Quap;

use App\DTO\Mapper\AnswersMapper;
use App\DTO\Model\Apps\Quap\AnswersDTO;
use App\DTO\Model\Apps\Quap\ExtendedAnswersDTO;
use App\Entity\Aggregated\AggregatedQuap;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Quap\Aspect;
use App\Entity\Quap\Help;
use App\Entity\Quap\Question;
use App\Entity\Quap\Questionnaire;
use App\Exception\ApiException;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Quap\AspectRepository;
use App\Repository\Quap\HelpRepository;
use App\Repository\Quap\LinkRepository;
use App\Repository\Quap\QuestionnaireRepository;
use App\Repository\Quap\QuestionRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

use function str_contains;

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

    /** @var AggregatedQuapRepository $quapRepository */
    private AggregatedQuapRepository $quapRepository;

    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    private StatisticGroupRepository $statisticGroupRepository;

    /**
     * @param QuestionnaireRepository $questionnaireRepository
     * @param AspectRepository $aspectRepository
     * @param QuestionRepository $questionRepository
     * @param HelpRepository $helpRepository
     * @param LinkRepository $linkRepository
     * @param AggregatedQuapRepository $quapRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(
        QuestionnaireRepository $questionnaireRepository,
        AspectRepository $aspectRepository,
        QuestionRepository $questionRepository,
        HelpRepository $helpRepository,
        LinkRepository $linkRepository,
        AggregatedQuapRepository $quapRepository,
        GroupRepository $groupRepository,
        StatisticGroupRepository $statisticGroupRepository,
        EntityManagerInterface $em
    ) {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->aspectRepository = $aspectRepository;
        $this->questionRepository = $questionRepository;
        $this->helpRepository = $helpRepository;
        $this->linkRepository = $linkRepository;
        $this->quapRepository = $quapRepository;
        $this->groupRepository = $groupRepository;
        $this->statisticGroupRepository = $statisticGroupRepository;
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
        $questionnaire->setAspects(
            new ArrayCollection($this->aspectRepository->getExisting($questionnaire->getId(), $dateTime))
        );

        /** @var Aspect $aspect */
        foreach ($questionnaire->getAspects() as $aspect) {
            $aspect->setQuestions(
                new ArrayCollection($this->questionRepository->getExisting($aspect->getId(), $dateTime))
            );

            /** @var Question $question */
            foreach ($aspect->getQuestions() as $question) {
                $question->setHelp(
                    new ArrayCollection($this->helpRepository->getExisting($question->getId(), $dateTime))
                );

                /** @var Help $help */
                foreach ($question->getHelp() as $help) {
                    switch ($locale) {
                        case (str_contains($locale, "it")):
                            $help->setLinksIt(new ArrayCollection($this->linkRepository->findBy(['helpIt' => $help])));
                            break;
                        case (str_contains($locale, "fr")):
                            $help->setLinksFr(new ArrayCollection($this->linkRepository->findBy(['helpFr' => $help])));
                            break;
                        default:
                            $help->setLinksDe(new ArrayCollection($this->linkRepository->findBy(['helpDe' => $help])));
                            break;
                    }
                }
            }
        }

        return $questionnaire;
    }

    public function submitAnswers(Group $group, array $json): AggregatedQuap
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

    public function updateAllowAccess(Group $group, bool $allowAccess): AggregatedQuap
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
        $parentGroupType = $group->getGroupType()->getGroupType();

        $ids = [];
        if ($parentGroupType === GroupType::CANTON) {
            $subdepartments = $this->groupRepository->findAllDepartmentsFromCanton($group->getId());
            foreach ($subdepartments as $group) {
                $ids[] = $group['id'];
            }
        } elseif ($parentGroupType === GroupType::REGION) {
            $ids = $this->statisticGroupRepository->findAllRelevantChildGroups($group->getId(), [GroupType::DEPARTMENT]);
        } else {
            throw new \Exception();
        }

        $answers = $this->quapRepository->findAllAnswers($ids, $date !== null ? $date->format('Y-m-d') : null);

        $dtos = [];
        /** @var AggregatedQuap $answer */
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
