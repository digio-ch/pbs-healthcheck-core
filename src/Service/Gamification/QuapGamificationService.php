<?php

namespace App\Service\Gamification;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Aggregated\AggregatedQuap;
use App\Entity\Gamification\GamificationQuapEvent;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Gamification\GamificationQuapEventRepository;
use App\Repository\Midata\PersonRepository;

class QuapGamificationService
{
    private AggregatedQuapRepository $aggregatedQuapRepository;
    private PersonGamificationService $personGamificationService;
    private PersonRepository $personRepository;
    private GamificationQuapEventRepository $gamificationQuapEventRepository;
    public function __construct(
        PersonGamificationService $personGamificationService,
        AggregatedQuapRepository $aggregatedQuapRepository,
        PersonRepository $personRepository,
        GamificationQuapEventRepository $gamificationQuapEventRepository
    ) {
        $this->personGamificationService = $personGamificationService;
        $this->aggregatedQuapRepository = $aggregatedQuapRepository;
        $this->personRepository = $personRepository;
        $this->gamificationQuapEventRepository = $gamificationQuapEventRepository;
    }

    /**
     * Answered State: Erfüllt = 1, Meistens Erfüllt = 2, ... , Nicht Relevant = 5
     * @param array $newAnswers
     * @param Group $group
     * @return string
     */
    public function processQuapEvent(array $newAnswers, Group $group, PbsUserDTO $pbsUserDTO)
    {
        $aggregatedQuap = $this->aggregatedQuapRepository->findCurrentForGroup($group->getId());
        $oldAnswers = $aggregatedQuap->getAnswers();

        $changedQuestionnaires = [];
        $irrelevant = false;
        $improvement = false;

        for ($questionnaireIndex = 0; $questionnaireIndex < count($oldAnswers); $questionnaireIndex++) {
            for ($i = 0; $i < count($oldAnswers[$questionnaireIndex]); $i++) {
                if ($oldAnswers[$questionnaireIndex][$i] !== $newAnswers[$questionnaireIndex][$i]) {
                    $changedQuestionnaires[] = $questionnaireIndex;
                    if ($newAnswers[$questionnaireIndex][$i] === 5) {
                        $irrelevant = true;
                    }
                    if ($newAnswers[$questionnaireIndex][$i] !== 4 && $newAnswers[$questionnaireIndex][$i] < $oldAnswers[$questionnaireIndex][$i]) {
                        $improvement = true;
                    }
                    //$textchanges .= 'Question ' . $questionnaireIndex . '.' . $i . ': ' . $oldAnswers[$questionnaireIndex][$i] . '/' . $newAnswers[$questionnaireIndex][$i] . '; ';
                }
            }
        }

        foreach ($changedQuestionnaires as $index) {
            if ($this->isQuestionnaireFullyAnswered($newAnswers[$index])) {
                $this->personGamificationService->genericGoalProgress($pbsUserDTO, 'revised');
            }
        }
        if ($irrelevant) {
            $this->personGamificationService->genericGoalProgress($pbsUserDTO, 'irrelevant');
        }
        if ($improvement) {
            $this->personGamificationService->genericGoalProgress($pbsUserDTO, 'improvement');
        }

        $this->personGamificationService->logEvent($changedQuestionnaires, $aggregatedQuap, $pbsUserDTO);
    }

    private function isQuestionnaireFullyAnswered($questionnaire)
    {
        foreach ($questionnaire as $answer) {
            if ($answer === 4 || $answer === 0) {
                return false;
            }
        }
        return true;
    }
}
