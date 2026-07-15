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
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Quap\AspectRepository;
use App\Repository\Quap\QuestionnaireRepository;
use App\Repository\Quap\QuestionRepository;
use App\Service\Apps\Quap\Exception\GroupTypeHasNoQuestionnaireException;
use App\Service\Apps\Quap\Exception\NoAnswersException;
use App\Service\Apps\Quap\Exception\NoQuestionsException;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Generator;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class DownloadService
{
    public function __construct(
        private QuestionnaireRepository $questionnaireRepository,
        private AspectRepository $aspectRepository,
        private QuestionRepository $questionRepository,
        private AggregatedQuapRepository $quapRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws GroupTypeHasNoQuestionnaireException
     * @throws NoQuestionsException
     * @throws NoAnswersException
     */
    public function download(Group $group, ?DateTimeInterface $date): CsvFile
    {
        $aspects = $this->getAspects($group, $date);

        if (empty($aspects)) {
            throw new NoQuestionsException("No questions exist for the given date: " . $date?->format('Y-m-d') ?? 'null');
        }

        $widgetQuap = $this->quapRepository->findOneBy([
            "group" => $group->getId(),
            "dataPointDate" => $date
        ]);

        if (is_null($widgetQuap)) {
            throw new NoAnswersException("No answers exist for the given date: " . $date?->format('Y-m-d') ?? 'null');
        }

        [$fileName, $fallback] = $this->generateFilenameWithFallback($group, $date);

        return new CsvFile(
            name: $fileName,
            fallbackName: $fallback,
            rows: $this->generateRows($group, $aspects, $widgetQuap),
        );
    }

    /**
     * Generates a UTF8 filename. Additionally, it generates an ASCII fallback
     * @return array<string, string> [$filename, $fallback]
     */
    private function generateFilenameWithFallback(Group $group, ?DateTimeInterface $date): array
    {
        /** @noinspection PhpComposerExtensionStubsInspection mb_strcut already provided in symfony/polyfill-mbstring */
        $groupName = mb_strcut($group->getName(), 0, 150, 'UTF-8');

        $filename = sprintf(
            "%s-%s-%s.csv",
            $this->translator->trans('quap.export.name'),
            str_replace(" ", "_", $groupName),
            ($date ?? new DateTimeImmutable())->format('dmY')
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
    private function generateRows(Group $group, array $aspects, AggregatedQuap $quap): Generator
    {
        yield [
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
                    strval($aspect->getLocalId()),

                    $question->getQuestionDe(),
                    $question->getQuestionFr(),
                    $question->getQuestionIt(),
                    strval($question->getLocalId()),

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
                AggregatedQuap::NO_ANSWER => "no-answer",
                AggregatedQuap::ANSWER_NOT_IMPLEMENTED => "not-implemented",
                AggregatedQuap::ANSWER_PARTIALLY_FULFILLED => "partially-fulfilled",
                AggregatedQuap::ANSWER_MOSTLY_FULFILLED => "mostly-fulfilled",
                AggregatedQuap::ANSWER_FULFILLED => "fulfilled",
            },
            Question::ANSWER_OPTION_BINARY, Question::ANSWER_OPTION_MIDATA_BINARY => match ($answer) {
                AggregatedQuap::NO_ANSWER => "no-answer",
                AggregatedQuap::ANSWER_NOT_IMPLEMENTED => "no",
                AggregatedQuap::ANSWER_FULFILLED => "yes",
            }
        };
    }

    /**
     * @param Group $group
     * @param DateTimeInterface|null $date
     * @return Aspect[]
     * @throws GroupTypeHasNoQuestionnaireException
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

    /**
     * @throws GroupTypeHasNoQuestionnaireException
     */
    private function getQuestionnaireType(Group $group): string
    {
        $groupType = $group->getGroupType()->getGroupType();

        return match ($groupType) {
            GroupType::DEPARTMENT => Questionnaire::TYPE_DEPARTMENT,
            GroupType::REGION, GroupType::CANTON => Questionnaire::TYPE_CANTON,
            default => throw new GroupTypeHasNoQuestionnaireException('invalid group type: ' . $groupType)
        };
    }
}
