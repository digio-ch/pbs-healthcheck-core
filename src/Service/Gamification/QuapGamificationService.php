<?php

namespace App\Service\Gamification;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Aggregated\AggregatedQuap;
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
        $aggregatedQuap = $this->aggregatedQuapRepository->findCurrentForGroup($group->getId());
        $oldAnswers = $aggregatedQuap->getAnswers();

        $changedQuestionnaires = [];
        $irrelevant = false;
        $improvement = false;
        $revision = false;

        for ($questionnaireIndex = 0; $questionnaireIndex < count($oldAnswers); $questionnaireIndex++) {
            for ($i = 0; $i < count($oldAnswers[$questionnaireIndex]); $i++) {
                $oldAnswer = $oldAnswers[$questionnaireIndex][$i];
                $newAnswer = $newAnswers[$questionnaireIndex][$i];

                // continue if the answer hasn't changed
                if ($oldAnswer === $newAnswer) {
                    continue;
                }

                $changedQuestionnaires[] = $questionnaireIndex;

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
            $this->personGamificationService->genericGoalProgress($pbsUserDTO, 'revised');
        }
        if ($irrelevant) {
            $this->personGamificationService->genericGoalProgress($pbsUserDTO, 'irrelevant');
        }
        if ($improvement) {
            $this->personGamificationService->genericGoalProgress($pbsUserDTO, 'improvement');
        }

        $this->personGamificationService->logEvent($changedQuestionnaires, $aggregatedQuap, $pbsUserDTO);
    }
}
