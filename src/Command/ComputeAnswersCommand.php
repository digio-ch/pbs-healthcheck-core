<?php

namespace App\Command;

use App\Entity\Midata\Group;
use App\Entity\Quap\Question;
use App\Helper\QuapAnswerStackHelper;
use App\Model\CommandStatistics;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\QuestionnaireRepository;
use App\Repository\Quap\QuestionRepository;
use App\Service\Apps\Quap\QuapComputeAnswersService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComputeAnswersCommand extends StatisticsCommand
{
    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var AggregatedQuapRepository $quapRepository */
    private AggregatedQuapRepository $quapRepository;

    /** @var QuestionRepository $questionRepository */
    private QuestionRepository $questionRepository;

    /** @var QuapComputeAnswersService $quapComputeAnswersService */
    private QuapComputeAnswersService $quapComputeAnswersService;

    /** @var QuestionnaireRepository $questionnaireRepository */
    private QuestionnaireRepository $questionnaireRepository;

    private float $totalDuration = 0;

    public function __construct(
        GroupRepository $groupRepository,
        AggregatedQuapRepository $quapRepository,
        QuestionRepository $questionRepository,
        QuapComputeAnswersService $quapComputeAnswersService,
        QuestionnaireRepository $questionnaireRepository
    ) {
        parent::__construct();

        $this->groupRepository = $groupRepository;
        $this->quapRepository = $quapRepository;
        $this->questionRepository = $questionRepository;
        $this->quapComputeAnswersService = $quapComputeAnswersService;
        $this->questionnaireRepository = $questionnaireRepository;
    }

    protected function configure()
    {
        $this
            ->setName("app:quap:compute-answers");
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $output->writeln('Computing automated questions...');

        $groups = $this->groupRepository->findAllDepartmentalAndRegionalAndCantonalGroups();

        /** @var Group $group */
        foreach ($groups as $group) {
            try {
                $questionnaire = $this->questionnaireRepository->getQuestionnaireByGroup($group);
                $questions = $this->questionRepository->findEvaluableByQuestionnaire($questionnaire);
                $widgetQuap = $this->quapRepository->findCurrentForGroup($group->getId());
                $helper = new QuapAnswerStackHelper([]);

                /** @var Question $question */
                foreach ($questions as $question) {
                    $result = $this->quapComputeAnswersService->computeAnswer(
                        $question->getEvaluationFunction(),
                        $group
                    );
                    $helper->setAnswer($question->getAspect()->getLocalId(), $question->getLocalId(), $result);
                }
                $widgetQuap->setComputedAnswers($helper->getAnswerStack());
                $widgetQuap->setQuestionnaire($questionnaire);
                $this->quapRepository->save($widgetQuap);
            } catch (\Exception $e) {
                $output->writeln(['An Error occurred', $group, $e]);
            }
        }

        $output->writeln('finished computing all automated questions.');
        $this->totalDuration = microtime(true) - $start;
        return 0;
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics($this->totalDuration, '');
    }
}
