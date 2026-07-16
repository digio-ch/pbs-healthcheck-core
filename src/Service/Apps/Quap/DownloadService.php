<?php

declare(strict_types=1);

namespace App\Service\Apps\Quap;

use App\DTO\Model\Apps\CsvFile;
use App\Entity\Aggregated\AggregatedQuap;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Quap\Aspect;
use App\Entity\Quap\Question;
use App\Entity\Quap\Questionnaire;
use App\Repository\Aggregated\AggregatedDateRepository;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Quap\AspectRepository;
use App\Repository\Quap\QuestionnaireRepository;
use App\Repository\Quap\QuestionRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use App\Service\Apps\Quap\Exception\InvalidGroupTypeException;
use App\Service\Apps\Quap\Exception\InvalidParentGroupTypeException;
use App\Service\Apps\Quap\Exception\InvalidSubordinateGroupTypeException;
use App\Service\Apps\Quap\Exception\NoDataException;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Generator;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class DownloadService extends AccessService
{
    public function __construct(
        private QuestionnaireRepository $questionnaireRepository,
        private AspectRepository $aspectRepository,
        private QuestionRepository $questionRepository,
        private AggregatedQuapRepository $quapRepository,
        private AggregatedDateRepository $dateRepository,
        private StatisticGroupRepository $statisticGroupRepository,
        private GroupRepository $groupRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws NoDataException
     * @throws InvalidGroupTypeException
     */
    public function download(Group $group, ?DateTimeInterface $date): CsvFile
    {
        $this->validateQuapAccess($group);

        $widgetQuap = $this->quapRepository->findOneBy([
            "group" => $group->getId(),
            "dataPointDate" => $date->format('Y-m-d')
        ]);

        if (is_null($widgetQuap)) {
            throw new NoDataException(
                sprintf(
                    "No answers exist for the given date %s",
                    $date?->format('Y-m-d') ?? 'null'
                )
            );
        }

        return $this->createCsvFile($group, $widgetQuap, $date);
    }

    /**
     * @throws NoDataException
     * @throws InvalidParentGroupTypeException
     * @throws InvalidSubordinateGroupTypeException
     */
    public function downloadShared(Group $group, Group $subordinateGroup, ?DateTimeInterface $date): CsvFile
    {
        if (!$this->isValidGroupAccess($group, $subordinateGroup)) {
            throw new InvalidSubordinateGroupTypeException(
                sprintf(
                    "subordinate group type %s (%s) cannot be accessed by group type %s (%s)",
                    $subordinateGroup->getGroupType()->getGroupType(),
                    $subordinateGroup->getId(),
                    $group->getGroupType()->getGroupType(),
                    $group->getId(),
                )
            );
        }

        $quap = $this->quapRepository->findSharedOfGroup($subordinateGroup->getId(), $date);

        if ($quap === null) {
            throw new NoDataException(
                sprintf(
                    "No shared answers of group %s exist for the given date %s",
                    $subordinateGroup->getId(),
                    $date?->format('Y-m-d') ?? 'null'
                )
            );
        }

        return $this->createCsvFile($subordinateGroup, $quap, $date);
    }

    /**
     * @throws InvalidParentGroupTypeException
     * @throws NoDataException
     * @throws Exception
     */
    public function downloadAllShared(Group $group, ?DateTimeInterface $date): CsvFile
    {
        $subordinateGroupTypes =  $this->getSubordinateGroupTypes($group);

        $subordinateIds = $this->statisticGroupRepository->findAllRelevantChildGroups(
            $group->getId(),
            $subordinateGroupTypes,
        );

        $quaps = $this->quapRepository->findSharedOfGroups($subordinateIds, $date);

        if (empty($quaps)) {
            throw new NoDataException(
                sprintf(
                    "No answers exist for the given date %s",
                    $date?->format('Y-m-d') ?? 'null'
                )
            );
        }
        // only keep the groups that have shared answers on the given date
        $subordinateIds = array_map(fn(AggregatedQuap $quap) => $quap->getGroup()->getId(), $quaps);

        $subordinateGroups = $this->groupRepository->findByGroupIds($subordinateIds);

        return $this->createCsvFileOfShared($group, $subordinateGroups, $quaps, $date);
    }

    /**
     * @throws NoDataException
     */
    private function createCsvFile(Group $group, AggregatedQuap $quap, ?DateTimeInterface $date): CsvFile
    {
        $aspects = $this->getAspects($group, $date);

        if (empty($aspects)) {
            throw new NoDataException(
                sprintf(
                    "No questions exist for the given date %s",
                    $date?->format('Y-m-d') ?? 'null'
                )
            );
        }

        if (is_null($date)) {
            // we cannot use today's date because the aggregation might not run every day
            $date = $this->getLatestAggregationDate($group);
        }

        [$fileName, $fallback] = $this->generateFilenameWithFallback($group, $date);

        return new CsvFile(
            name: $fileName,
            fallbackName: $fallback,
            rows: $this->generateRowsOfGroup($group, $aspects, $quap),
        );
    }

    /**
     * @throws NoDataException
     */
    private function createCsvFileOfShared(
        Group $group,
        array $subordinateGroups,
        array $quaps,
        ?DateTimeInterface $date
    ): CsvFile {
        $aspectsPerGroupType = $this->getAspectsPerGroupType($subordinateGroups, $date);

        if (is_null($date)) {
            // we cannot use today's date because the aggregation might not run every day
            $date = $this->getLatestAggregationDate($group);
        }

        [$fileName, $fallback] = $this->generateFilenameWithFallback($group, $date, shared: true);

        return new CsvFile(
            name: $fileName,
            fallbackName: $fallback,
            rows: $this->generateRowsOfGroups($subordinateGroups, $aspectsPerGroupType, $quaps),
        );
    }

    /**
     * Queries the latest aggregation date that exists
     *
     * @throws NoDataException
     */
    private function getLatestAggregationDate(Group $group): DateTimeInterface
    {
        $date = $this->dateRepository->findLatestDataPointDateByGroupId($group->getId());

        if (is_null($date)) {
            throw new NoDataException(
                sprintf("no aggregation date for group %s", $group->getId())
            );
        }

        return $date;
    }

    /**
     * @return bool whether the group has access to the subordinate group
     * @throws InvalidParentGroupTypeException
     */
    private function isValidGroupAccess(Group $group, Group $subordinateGroup): bool
    {
        $allowedSubordinateGroupTypes = $this->getSubordinateGroupTypes($group);

        $subordinateGroupType = $subordinateGroup->getGroupType()->getGroupType();

        return in_array($subordinateGroupType, $allowedSubordinateGroupTypes, true);
    }

    /**
     * Generates a UTF8 filename. Additionally, it generates an ASCII fallback
     * @param bool $shared whether the prefix should be something like 'quap' or 'shared_quaps'
     * @return array<string, string> [$filename, $fallback]
     */
    private function generateFilenameWithFallback(Group $group, DateTimeInterface $date, bool $shared = false): array
    {
        /** @noinspection PhpComposerExtensionStubsInspection mb_strcut already provided in symfony/polyfill-mbstring */
        $groupName = mb_strcut($group->getName(), 0, 150, 'UTF-8');

        $key = $shared ? 'quap.export.shared_name' : 'quap.export.name';

        $filename = sprintf(
            "%s-%s-%s.csv",
            $this->translator->trans($key),
            str_replace(" ", "_", $groupName),
            $date->format('dmY')
        );

        // convert ü => u, è => e, etc.
        $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
        $fallback = $transliterator->transliterate($filename);

        // keep only safe ASCII filename characters
        $fallback = preg_replace('/[^A-Za-z0-9._-]+/', '', $fallback);

        return [$filename, $fallback];
    }

    /**
     * @param Group $group
     * @param Aspect[] $aspects
     * @param AggregatedQuap $quap
     * @return Generator
     */
    private function generateRowsOfGroup(Group $group, array $aspects, AggregatedQuap $quap): Generator
    {
        yield $this->generateHeaderRow();

        yield from $this->generateBodyRows($group, $aspects, $quap);
    }

    /**
     * @param Group[] $groups
     * @param array<string, Aspect[]> $aspectsPerGroupType
     * @param AggregatedQuap[] $quaps
     * @return Generator
     */
    private function generateRowsOfGroups(array $groups, array $aspectsPerGroupType, array $quaps): Generator
    {
        yield $this->generateHeaderRow();

        $quapByGroupId = [];

        foreach ($quaps as $quap) {
            $quapByGroupId[$quap->getGroup()->getId()] = $quap;
        }

        foreach ($groups as $group) {
            $quap = $quapByGroupId[$group->getId()];
            $aspects = $aspectsPerGroupType[$group->getGroupType()->getGroupType()];

            yield from $this->generateBodyRows($group, $aspects, $quap);
        }
    }

    /**
     * @return string[]
     */
    private function generateHeaderRow(): array
    {
        return [
            'group',
            'group_id',
            'group_type',
            'aspect_de',
            'aspect_fr',
            'aspect_it',
            'aspect_id',
            'question_de',
            'question_fr',
            'question_it',
            'question_id',
            'answer_option',
            'answer',
            'not_relevant'
        ];
    }

    /**
     * @param Group $group
     * @param Aspect[] $aspects
     * @param AggregatedQuap $quap
     * @return Generator
     */
    private function generateBodyRows(Group $group, array $aspects, AggregatedQuap $quap): Generator
    {
        $answers = $quap->getAnswers();
        $computedAnswers = $quap->getComputedAnswers();

        foreach ($aspects as $aspect) {
            foreach ($aspect->getQuestions() as $question) {
                $answer = $answers[$aspect->getLocalId()][$question->getLocalId()] ?? AggregatedQuap::NO_ANSWER;
                $computedAnswer = $computedAnswers[$aspect->getLocalId()][$question->getLocalId()] ?? null;

                yield [
                    $group->getName(),
                    strval($group->getId()),
                    $group->getGroupType()->getGroupType(),

                    $aspect->getNameDe(),
                    $aspect->getNameFr(),
                    $aspect->getNameIt(),
                    $aspect->getId(),

                    $question->getQuestionDe(),
                    $question->getQuestionFr(),
                    $question->getQuestionIt(),
                    $question->getId(),

                    $question->getAnswerOptions(),
                    $this->getHumanReadableAnswer($answer, $computedAnswer, $question->getAnswerOptions()),
                    $answer === AggregatedQuap::ANSWER_IRRELEVANT ? 'true' : 'false',
                ];
            }
        }
    }

    /**
     * Returns a human-readable version of the answer.
     *
     * If the answer is not relevant {@see null} is returned expect the answer is automatically evaluated.
     */
    private function getHumanReadableAnswer(int $answer, ?int $computedAnswer, string $answerOption): ?string
    {
        if (in_array($answerOption, [ Question::ANSWER_OPTION_BINARY, Question::ANSWER_OPTION_RANGE ], true)) {
            return $answer === AggregatedQuap::ANSWER_IRRELEVANT || $answer === AggregatedQuap::NO_ANSWER
                ? null
                : $this->answerToLabel($answerOption, $answer);
        }

        if ($computedAnswer === null) {
            throw new \LogicException("computed answer cannot be null for answer option: " . $answerOption);
        }

        return $this->answerToLabel($answerOption, $computedAnswer);
    }

    private function answerToLabel(string $answerOption, int $answer): string
    {
        return match ($answerOption) {
            Question::ANSWER_OPTION_RANGE, Question::ANSWER_OPTION_MIDATA_RANGE => match ($answer) {
                AggregatedQuap::ANSWER_NOT_IMPLEMENTED => "not-implemented",
                AggregatedQuap::ANSWER_PARTIALLY_FULFILLED => "partially-fulfilled",
                AggregatedQuap::ANSWER_MOSTLY_FULFILLED => "mostly-fulfilled",
                AggregatedQuap::ANSWER_FULFILLED => "fulfilled",
            },
            Question::ANSWER_OPTION_BINARY, Question::ANSWER_OPTION_MIDATA_BINARY => match ($answer) {
                AggregatedQuap::ANSWER_NOT_IMPLEMENTED => "no",
                AggregatedQuap::ANSWER_FULFILLED => "yes",
            }
        };
    }

    /**
     * @param Group $group
     * @param DateTimeInterface|null $date
     * @return Aspect[]
     */
    private function getAspects(Group $group, ?DateTimeInterface $date): array
    {
        $type = $this->getQuestionnaireType($group);

        $dateStr = ($date ?? new DateTimeImmutable())
            ->format('Y-m-d');

        $questionnaireId = $this->questionnaireRepository->findOneBy(["type" => $type])->getId();

        /**
         * @var Aspect[] $aspects
         */
        $aspects = $this->aspectRepository->getExisting($questionnaireId, $dateStr);

        foreach ($aspects as $aspect) {
            $aspect->setQuestions(
                new ArrayCollection($this->questionRepository->getExisting($aspect->getId(), $dateStr))
            );
        }

        return $aspects;
    }

    private function getQuestionnaireType(Group $group): string
    {
        $groupType = $group->getGroupType()->getGroupType();

        return match ($groupType) {
            GroupType::DEPARTMENT => Questionnaire::TYPE_DEPARTMENT,
            GroupType::REGION, GroupType::CANTON => Questionnaire::TYPE_CANTON,
        };
    }

    /**
     * @param Group[] $groups
     * @param DateTimeInterface|null $date
     * @return array<string, Aspect[]>
     * @throws NoDataException
     */
    public function getAspectsPerGroupType(array $groups, ?DateTimeInterface $date): array
    {
        $aspectsByGroupType = [];

        foreach ($groups as $group) {
            $groupType = $group->getGroupType()->getGroupType();

            if (array_key_exists($groupType, $aspectsByGroupType)) {
                continue;
            }

            $aspects = $this->getAspects($group, $date);

            if (empty($aspects)) {
                throw new NoDataException(
                    sprintf(
                        "No questions exist for the given date %s and group type %s",
                        $date?->format('Y-m-d') ?? 'null',
                        $groupType,
                    )
                );
            }

            $aspectsByGroupType[$groupType] = $aspects;
        }

        return $aspectsByGroupType;
    }
}
