<?php

namespace App\Service\Apps\Quap;

use App\DTO\Mapper\AnswersMapper;
use App\DTO\Mapper\QuapNodeMapper;
use App\DTO\Model\Apps\Quap\AnswersDTO;
use App\DTO\Model\Apps\Quap\ExtendedAnswersDTO;
use App\DTO\Model\Apps\Quap\NestedExtendedAnswersDTO;
use App\Entity\Aggregated\AggregatedQuap;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Quap\Aspect;
use App\Entity\Quap\Help;
use App\Entity\Quap\Question;
use App\Entity\Quap\Questionnaire;
use App\Exception\ApiException;
use App\Model\QuapNode;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Quap\AspectRepository;
use App\Repository\Quap\HelpRepository;
use App\Repository\Quap\LinkRepository;
use App\Repository\Quap\QuestionnaireRepository;
use App\Repository\Quap\QuestionRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use App\Service\Apps\Quap\Exception\InvalidParentGroupTypeException;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

use function str_contains;

readonly class QuapService extends AccessService
{
    public function __construct(
        private QuestionnaireRepository $questionnaireRepository,
        private AspectRepository $aspectRepository,
        private QuestionRepository $questionRepository,
        private HelpRepository $helpRepository,
        private LinkRepository $linkRepository,
        private AggregatedQuapRepository $quapRepository,
        private StatisticGroupRepository $statisticGroupRepository,
        private EntityManagerInterface $em
    ) {
    }

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

    public function getAnswers(Group $group, ?DateTimeImmutable $dateTime): AnswersDTO
    {
        $widgetQuap = $this->quapRepository->findOneBy([
            "dataPointDate" => $dateTime instanceof DateTimeImmutable ? $dateTime->setTime(0, 0) : null,
            "group" => $group->getId()
        ]);

        return AnswersMapper::mapAnswers($widgetQuap);
    }

    /**
     * @return ExtendedAnswersDTO[]
     * @throws Exception
     * @throws InvalidParentGroupTypeException
     */
    public function getAnswersForSubDepartments(Group $group, ?DateTimeImmutable $date): array
    {
        $ids = $this->getSubordinateGroupIds($group);
        $quaps = $this->quapRepository->findSharedOfGroups($ids, $date);

        return array_map(
            fn(AggregatedQuap $aggregatedQuap): ExtendedAnswersDTO => AnswersMapper::mapExtendedAnswers($aggregatedQuap),
            $quaps
        );
    }

    /**
     * @return NestedExtendedAnswersDTO[]
     * @throws Exception
     * @throws InvalidParentGroupTypeException
     */
    public function getHierarchicalAnswersFromSubDepartments(Group $group, ?DateTimeImmutable $date): array
    {
        $ids = $this->getSubordinateGroupIds($group);
        $quaps = $this->quapRepository->findSharedOfGroups($ids, $date);

        if ($group->getGroupType()->getGroupType() === GroupType::REGION) {
            $dtos = array_map(
                fn(AggregatedQuap $aggregatedQuap): NestedExtendedAnswersDTO => AnswersMapper::mapNestedExtendedAnswers($aggregatedQuap),
                $quaps
            );

            // group all departments
            return array(new NestedExtendedAnswersDTO(null, $dtos));
        }

        $remainingQuaps = $quaps;
        // because not every group shares their answers, the tree has gaps and therefore we need multiple trees
        $trees = [];

        while (count($remainingQuaps) > 0) {
            [$tree, $remaining] = $this->createQuapTree($remainingQuaps);
            $remainingQuaps = $remaining;
            $trees[] = $tree;
        }

        $dtos = array_map(fn(QuapNode $tree): NestedExtendedAnswersDTO => QuapNodeMapper::map($tree), $trees);

        // if only departments are shown it makes more sense to group them
        if ($this->onlyDepartmentAnswers($dtos)) {
            return array(new NestedExtendedAnswersDTO(null, $dtos));
        }

        return $dtos;
    }

    /**
     * @param NestedExtendedAnswersDTO[] $answers
     */
    private function onlyDepartmentAnswers(array $answers): bool
    {
        foreach ($answers as $answer) {
            $value = $answer->getValue();
            if ($value === null || $value->getGroupType() !== GroupType::DEPARTMENT) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return int[]
     * @throws InvalidParentGroupTypeException
     * @throws Exception
     */
    private function getSubordinateGroupIds(Group $group): array
    {
        $subordinateGroupTypes = $this->getSubordinateGroupTypes($group);

        return $this->statisticGroupRepository->findAllRelevantChildGroups(
            $group->getId(),
            $subordinateGroupTypes,
        );
    }

    private function sortByGroupType(AggregatedQuap $a, AggregatedQuap $b): int
    {
        return $a->getGroup()->getGroupType()->getId() - $b->getGroup()->getGroupType()->getId();
    }

    private function findParentQuapNode(QuapNode $root, QuapNode $child): ?QuapNode
    {
        if ($root->hasSameGroupType($child)) {
            return null;
        }

        if ($root->isGroupParent($child)) {
            return $root;
        }

        foreach ($root->getChildren() as $node) {
            $match = $this->findParentQuapNode($node, $child);
            if ($match instanceof QuapNode) {
                return $match;
            }
        }

        return null;
    }

    /**
     * createQuapTree creates a tree where the root is the first element in the quaps array
     * @param AggregatedQuap[] $quaps
     * @return array The array consists of [Tree $tree, AggregatedQuap[] $remaining]
     */
    private function createQuapTree(array $quaps): array
    {
        usort($quaps, fn(AggregatedQuap $a, AggregatedQuap $b): int => $this->sortByGroupType($a, $b));

        /**
         * @var AggregatedQuap $rootQuap
         */
        $rootQuap = array_shift($quaps);
        $root = new QuapNode($rootQuap);

        $length = count($quaps);

        for ($i = 0; $i < $length; $i++) {
            $element = array_shift($quaps);

            $node = new QuapNode($element);

            $parent = $this->findParentQuapNode($root, $node);
            if (!$parent instanceof QuapNode) {
                $quaps[] = $element;
                continue;
            }

            $parent->addChild($node);
        }

        return [$root, $quaps];
    }
}
