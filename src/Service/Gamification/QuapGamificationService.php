<?php

namespace App\Service\Gamification;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Aggregated\AggregatedQuap;
use App\Entity\Gamification\Goal;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedQuapRepository;

class QuapGamificationService
{
    private AggregatedQuapRepository $aggregatedQuapRepository;
    private PersonGamificationService $personGamificationService;
    public function __construct(
        PersonGamificationService $personGamificationService,
        AggregatedQuapRepository $aggregatedQuapRepository
    ) {
        $this->personGamificationService = $personGamificationService;
        $this->aggregatedQuapRepository = $aggregatedQuapRepository;
    }

    /**
     * processQuapEvent evaluates the changes made to the questionnaire and updates the gamification goal process.
     * @param array $newAnswers
     * @param Group $group
     * @param PbsUserDTO $pbsUserDTO
     */
    public function processQuapEvent(array $newAnswers, Group $group, PbsUserDTO $pbsUserDTO)
    {
        // we can assume that there already exists an aggregatedQuap because the aggregator creates one every day
        $aggregatedQuap = $this->aggregatedQuapRepository->findCurrentForGroup($group->getId());
        $oldAnswers = $aggregatedQuap->getAnswers();

        $changedAspectLocalIds = [];
        $irrelevant = false;
        $improvement = false;
        $revision = false;

        foreach ($newAnswers as $aspectId => $newAspect) {
            // aspect didn't exist before
            if (!array_key_exists($aspectId, $oldAnswers)) {
                if (!$this->isAspectUnanswered($newAspect)) {
                    // mark the aspect as changed
                    $changedAspectLocalIds[$aspectId] = true;
                }

                continue;
            }

            $oldAspect = $oldAnswers[$aspectId];

            foreach ($newAspect as $questionId => $newAnswer) {
                // aspect didn't exist on the day before
                if (!array_key_exists($questionId, $oldAspect)) {
                    if ($newAnswer !== AggregatedQuap::NO_ANSWER) {
                        // mark the aspect as changed
                        $changedAspectLocalIds[$aspectId] = true;
                    }

                    continue;
                }

                $oldAnswer = $oldAspect[$questionId];

                // continue because the answer didn't change
                if ($oldAnswer === $newAnswer) {
                    continue;
                }

                // mark the aspect as changed
                $changedAspectLocalIds[$aspectId] = true;

                if ($newAnswer === AggregatedQuap::ANSWER_IRRELEVANT) {
                    $irrelevant = true;
                }

                // there can be no improvement or revision if there is no answer previously or now
                if ($oldAnswer === AggregatedQuap::NO_ANSWER || $newAnswer === AggregatedQuap::NO_ANSWER) {
                    continue;
                }

                $revision = true;

                // the change IRRELEVANT -> NOT_IMPLEMENTED is not considered an improvement
                if ($newAnswer !== AggregatedQuap::ANSWER_NOT_IMPLEMENTED && $newAnswer < $oldAnswer) {
                    $improvement = true;
                }
            }
        }

        if ($revision) {
            $this->personGamificationService->genericGoalProgress($pbsUserDTO, Goal::TYPE_EL_REVISION);
        }
        if ($irrelevant) {
            $this->personGamificationService->genericGoalProgress($pbsUserDTO, Goal::TYPE_EL_IRRELEVANT);
        }
        if ($improvement) {
            $this->personGamificationService->genericGoalProgress($pbsUserDTO, Goal::TYPE_EL_IMPROVEMENT);
        }

        $this->personGamificationService->logEvent(
            array_keys($changedAspectLocalIds),
            $aggregatedQuap,
            $pbsUserDTO,
        );
    }

    /**
     * Returns true if all the questions are NO_ANSWER
     *
     * @param int[] $aspect
     * @return bool
     */
    private function isAspectUnanswered(array $aspect): bool
    {
        foreach ($aspect as $answer) {
            if ($answer !== AggregatedQuap::NO_ANSWER) {
                return false;
            }
        }

        return true;
    }
}
