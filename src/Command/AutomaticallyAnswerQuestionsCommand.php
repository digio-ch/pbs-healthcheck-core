<?php

namespace App\Command;

use App\Entity\Group;
use App\Entity\Question;
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
    private $groupRepository;

    /** @var WidgetQuapRepository $quapRepository */
    private $quapRepository;

    /** @var QuestionRepository $questionRepository */
    private $questionRepository;

    /** @var QuapComputeAnswersService $quapComputeAnswersService */
    private $quapComputeAnswersService;

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
        $groups = $this->groupRepository->findAllParentGroups();
        $questions = $this->questionRepository->findEvaluable();

        /** @var Group $group */
        foreach ($groups as $group) {
            $answerStack = $this->quapRepository->findCurrentForGroup($group->getId())->getAnswers();
            $helper = new QuapAnswerStackHelper($answerStack);

            /** @var Question $question */
            foreach ($questions as $question) {
                $helper->setAnswer($question->getAspect()->getId(), $question->getId(), $this->quapComputeAnswersService->computeAnswer($question->getEvaluationFunction(), $group));
                $this->evaluateQuestion($group, $question, $helper);
            }
        }

        return 1;
    }
}
