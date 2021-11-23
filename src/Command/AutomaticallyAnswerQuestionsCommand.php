<?php

namespace App\Command;

use App\Entity\Group;
use App\Entity\Question;
use App\Helper\QuapAnswerStackHelper;
use App\Repository\GroupRepository;
use App\Repository\QuestionRepository;
use App\Repository\WidgetQuapRepository;
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

    public function __construct(
        GroupRepository $groupRepository,
        WidgetQuapRepository $quapRepository,
        QuestionRepository $questionRepository
    ) {
        parent::__construct();

        $this->groupRepository = $groupRepository;
        $this->quapRepository = $quapRepository;
        $this->questionRepository = $questionRepository;
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

            foreach ($questions as $question) {
                $this->evaluateQuestion($group, $question, $helper);
            }
        }

        return 1;
    }

    private function evaluateQuestion(Group $group, Question $question, QuapAnswerStackHelper $helper): void {
        switch ($question->getEvaluationFunction()) {
            default;
        }
    }
}
