<?php

namespace App\Command;

use App\Entity\Group;
use App\Entity\Question;
use App\Entity\WidgetQuap;
use App\Helper\QuapAnswerStackHelper;
use App\Repository\GroupRepository;
use App\Repository\QuestionRepository;
use App\Repository\WidgetQuapRepository;
use App\Service\QuapComputeAnswersService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AutomaticallyAnswerQuestionsCommand extends Command
{
    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var WidgetQuapRepository $quapRepository */
    private WidgetQuapRepository $quapRepository;

    /** @var QuestionRepository $questionRepository */
    private QuestionRepository $questionRepository;

    /** @var QuapComputeAnswersService $quapComputeAnswersService */
    private QuapComputeAnswersService $quapComputeAnswersService;

    public function __construct(
        GroupRepository $groupRepository,
        WidgetQuapRepository $quapRepository,
        QuestionRepository $questionRepository,
        QuapComputeAnswersService $quapComputeAnswersService
    ) {
        parent::__construct();

        $this->groupRepository = $groupRepository;
        $this->quapRepository = $quapRepository;
        $this->questionRepository = $questionRepository;
        $this->quapComputeAnswersService = $quapComputeAnswersService;
    }

    protected function configure()
    {
        $this
            ->setName("app:quap:automatically-answer-questions");
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Computing automated questions...');

        $groups = $this->groupRepository->findAllParentGroups();
        $questions = $this->questionRepository->findEvaluable();

        /** @var Group $group */
        foreach ($groups as $group) {
            $widgetQuap = $this->quapRepository->findCurrentForGroup($group->getId());
            $helper = new QuapAnswerStackHelper([]);

            /** @var Question $question */
            foreach ($questions as $question) {
                $result = $this->quapComputeAnswersService->computeAnswer($question->getEvaluationFunction(), $group);
                $helper->setAnswer($question->getAspect()->getLocalId(), $question->getLocalId(), $result);
            }

            $widgetQuap->setComputedAnswers($helper->getAnswerStack());
            $this->quapRepository->save($widgetQuap);
        }

        $output->writeln('finished computing all automated questions.');
        return 0;
    }
}
