<?php

namespace App\Service\Apps\Quap;

use App\DTO\Mapper\AnswersMapper;
use App\DTO\Mapper\HierarchyMapper;
use App\DTO\Model\Apps\Quap\AnswersDTO;
use App\DTO\Model\Apps\Quap\ExtendedAnswersDTO;
use App\DTO\Model\HierarchyDTO;
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
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
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
     * @param DateTimeImmutable|null $dateTime
     * @return AnswersDTO
     */
    public function getAnswers(Group $group, ?DateTimeImmutable $dateTime): AnswersDTO
    {
        $widgetQuap = $this->quapRepository->findOneBy([
            "dataPointDate" => $dateTime !== null ? $dateTime->setTime(0, 0) : null,
            "group" => $group->getId()
        ]);

        return AnswersMapper::mapAnswers($widgetQuap);
    }

    /**
     * @param Group $group
     * @param DateTimeImmutable|null $date
     * @return ExtendedAnswersDTO[]
     * @throws Exception
     */
    public function getAnswersForSubDepartments(Group $group, ?DateTimeImmutable $date): array
    {
        $ids = $this->getDepartmentIdsFromGroup($group);
        $quaps = $this->quapRepository->findAllAnswers($ids, $date !== null ? $date->format('Y-m-d') : null);

        return array_map(
            fn($aggregatedQuap) => AnswersMapper::mapExtendedAnswers($aggregatedQuap),
            $quaps
        );
    }


    /**
     * @param Group $group
     * @param DateTimeImmutable|null $date
     * @return HierarchyDTO<ExtendedAnswersDTO>[]
     * @throws Exception
     */
    public function getHierarchicalAnswersFromSubDepartments(Group $group, ?DateTimeImmutable $date): array
    {
        $groupType = $group->getGroupType()->getGroupType();

        $ids = $this->getDepartmentIdsFromGroup($group);
        $quaps = $this->quapRepository->findAllAnswers($ids, $date !== null ? $date->format('Y-m-d') : null);

        if ($groupType === GroupType::REGION) {
            return array_map(
                fn($aggregatedQuap) => new HierarchyDTO(AnswersMapper::mapExtendedAnswers($aggregatedQuap)),
                $quaps
            );
        }

        if ($groupType === GroupType::CANTON) {
            $nestedQuaps = $this->mapToRegionalHierarchyAggregated($quaps);
            return array_map(
                fn($node) => HierarchyMapper::mapNode(
                    $node,
                    fn($a) => AnswersMapper::mapExtendedAnswers($a),
                ),
                $nestedQuaps,
            );
        }

        if ($groupType === GroupType::FEDERATION) {
            $nestedQuaps = $this->mapToCantonalHierarchyAggregatedQuad($quaps);
            return array_map(
                fn($node) => HierarchyMapper::mapNode(
                    $node,
                    fn($a) => AnswersMapper::mapExtendedAnswers($a),
                ),
                $nestedQuaps,
            );
        }

        throw new Exception("Invalid group type");
    }

    /**
     * @param Group $group
     * @return int[]
     * @throws Exception
     */
    private function getDepartmentIdsFromGroup(Group $group): array
    {
        switch ($group->getGroupType()->getGroupType())
        {
            case GroupType::FEDERATION:
                return $this->statisticGroupRepository->findAllRelevantChildGroups(
                    $group->getId(),
                    [GroupType::CANTON, GroupType::REGION, GroupType::DEPARTMENT]
                );
            case GroupType::CANTON:
                $groups = $this->groupRepository->findAllDepartmentsFromCanton($group->getId());
                return array_map(fn($group) => $group['id'], $groups);
            case GroupType::REGION:
                return $this->statisticGroupRepository->findAllRelevantChildGroups($group->getId(), [GroupType::DEPARTMENT]);
            default:
                throw new Exception("can't get departments of a department");
        }
    }

    /**
     * @param AggregatedQuap $a
     * @param AggregatedQuap $b
     * @return int
     */
    private function sortByGroupType(AggregatedQuap $a, AggregatedQuap $b): int
    {
        return $a->getGroup()->getGroupType()->getId() - $b->getGroup()->getGroupType()->getId();
    }

    /**
     * @param AggregatedQuap[] $quaps
     * @return HierarchyDTO<AggregatedQuap>[]
     */
    private function mapToCantonalHierarchyAggregatedQuad(array $quaps): array
    {
        usort($quaps, fn($a, $b) => $this->sortByGroupType($a, $b));

        /**
         * maps a group_id of the canton to a HierarchyDTO of AggregatedQuap
         * @var array<int, HierarchyDTO<AggregatedQuap>> $nestedDepartmentsMap
         */
        $nestedDepartmentsMap = array();
        /**
         * @var AggregatedQuap[] $parentlessDepartments
         */
        $parentlessDepartments = array();

        foreach ($quaps as $quap) {
            $group = $quap->getGroup();
            $groupType = $group->getGroupType()->getGroupType();

            if ($groupType === GroupType::CANTON) {
                    $nestedDepartmentsMap[$group->getId()] = new HierarchyDTO($quap);
                    continue;
            }

            if ($groupType === GroupType::REGION) {
                $parentGroup = $group->getParentGroup();
                if ($parentGroup == null) {
                    $parentlessDepartments[] = $quap;
                    continue;
                }
                $parentGroupId = $parentGroup->getId();

                $nestedAnswer = $nestedDepartmentsMap[$parentGroupId] ?? null;
                if ($nestedAnswer === null) {
                    $nestedDepartmentsMap[$parentGroupId] = new HierarchyDTO(null, array(new HierarchyDTO($quap)));
                    continue;
                }

                $nestedAnswer->addChild(new HierarchyDTO($quap));
                continue;
            }

            $parentGroup = $group->getParentGroup();
            if ($parentGroup == null) {
                $parentlessDepartments[] = $quap;
                continue;
            }
            $parentGroupId = $parentGroup->getId();

            $cantonId = $group->getCantonId();
            if ($cantonId === null) {
                $parentlessDepartments[] = $quap;
            }

            $cantonalHierarchy = $nestedDepartmentsMap[$cantonId] ?? null;
            if ($cantonalHierarchy === null) {
                $parentlessDepartments[] = $quap;
                continue;
            }

            $found = false;
            foreach ($cantonalHierarchy->getChildren() as $regionalHierarchy) {
                $regionalQuap = $regionalHierarchy->getParent();
                if ($regionalQuap->getGroup()->getId() === $parentGroupId) {
                    $regionalHierarchy->addChild(new HierarchyDTO($quap));
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $parentlessDepartments[$cantonId] = $quap;
            }
        }

        $nestedQuaps = array_values($nestedDepartmentsMap);
        if (count($parentlessDepartments) > 0) {
            $nestedQuaps[] = new HierarchyDTO(null, $this->mapToRegionalHierarchyAggregated($parentlessDepartments));
        }

        return $nestedQuaps;
    }


    /**
     * @param AggregatedQuap[] $quaps
     * @return HierarchyDTO<AggregatedQuap>[]
     */
    private function mapToRegionalHierarchyAggregated(array $quaps): array
    {
        /**
         * maps a group_id of a region to a HierarchyDTO of AggregatedQuap
         * @var array<int, HierarchyDTO<AggregatedQuap>> $nestedDepartmentsMap
         */
        $nestedDepartmentsMap = array();
        /**
         * @var AggregatedQuap[] $parentlessDepartments
         */
        $parentlessDepartments = array();

        foreach ($quaps as $quap) {
            $group = $quap->getGroup();
            if ($group->getGroupType()->getGroupType() === GroupType::REGION) {
                $nested = $nestedDepartmentsMap[$group->getId()] ?? null;
                if ($nested === null) {
                    $nestedDepartmentsMap[$group->getId()] = new HierarchyDTO($quap);
                    continue;
                }
                $nested->setParent($quap);
                continue;
            }

            $parentGroup = $group->getParentGroup();
            if ($parentGroup == null) {
                $parentlessDepartments[] = $quap;
                continue;
            }
            $parentGroupId = $parentGroup->getId();

            $nestedAnswer = $nestedDepartmentsMap[$parentGroupId] ?? null;
            if ($nestedAnswer === null) {
                $nestedDepartmentsMap[$parentGroupId] = new HierarchyDTO(null, array(new HierarchyDTO($quap)));
                continue;
            }

            $nestedAnswer->addChild(new HierarchyDTO($quap));
        }

        $nestedQuaps = array_values($nestedDepartmentsMap);
        if (count($parentlessDepartments) > 0) {
            $nestedQuaps[] = new HierarchyDTO(null, array_map(
                fn($parentlessQuap) => new HierarchyDTO($parentlessQuap),
                $parentlessDepartments)
            );
        }

        return $nestedQuaps;
    }
}