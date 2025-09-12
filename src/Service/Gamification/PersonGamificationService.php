<?php

namespace App\Service\Gamification;

use App\DTO\Mapper\GamificationGoalMapper;
use App\DTO\Mapper\GamificationLevelMapper;
use App\DTO\Mapper\GamificationPersonProfileMapper;
use App\DTO\Model\Gamification\PersonGamificationDTO;
use App\DTO\Model\PbsUserDTO;
use App\Entity\Aggregated\AggregatedQuap;
use App\Entity\Gamification\GamificationPersonProfile;
use App\Entity\Gamification\GamificationQuapEvent;
use App\Entity\Gamification\Goal;
use App\Entity\Gamification\Level;
use App\Entity\Gamification\LevelUpLog;
use App\Entity\Midata\Person;
use App\Entity\Quap\Questionnaire;
use App\Repository\Gamification\GamificationQuapEventRepository;
use App\Repository\Gamification\LevelRepository;
use App\Repository\Gamification\GamificationPersonProfileRepository;
use App\Repository\Gamification\LevelUpLogRepository;
use App\Repository\Midata\PersonRepository;
use App\Repository\Quap\QuestionnaireRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;

class PersonGamificationService
{
    private LevelRepository $levelRepository;

    private PersonRepository $personRepository;

    private QuestionnaireRepository $questionnaireRepository;

    private GamificationPersonProfileRepository $personGoalRepository;

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    private LevelUpLogRepository $levelUpLogRepository;

    private GamificationQuapEventRepository $gamificationQuapEventRepository;

    private MailService $mailService;

    public function __construct(
        LevelRepository $levelRepository,
        PersonRepository $personRepository,
        QuestionnaireRepository $questionnaireRepository,
        GamificationPersonProfileRepository $personGoalRepository,
        EntityManagerInterface $em,
        LevelUpLogRepository $levelUpLogRepository,
        GamificationQuapEventRepository $gamificationQuapEventRepository,
        MailService $mailService
    ) {
        $this->levelRepository = $levelRepository;
        $this->personRepository = $personRepository;
        $this->questionnaireRepository = $questionnaireRepository;
        $this->personGoalRepository = $personGoalRepository;
        $this->em = $em;
        $this->levelUpLogRepository = $levelUpLogRepository;
        $this->gamificationQuapEventRepository = $gamificationQuapEventRepository;
        $this->mailService = $mailService;
    }

    public function reset(PbsUserDTO $pbsUserDTO)
    {
        $person = $this->personRepository->find($pbsUserDTO->getId());
        $pgp = $this->getPersonGamification($person);
        $this->personGoalRepository->remove($pgp);
        $events = $this->gamificationQuapEventRepository->findBy(['person' => $person]);
        foreach ($events as $event) {
            $this->gamificationQuapEventRepository->remove($event);
        }
    }

    public function getPersonGamification(Person $person): GamificationPersonProfile
    {
        $gamificationProfile = $person->getGamification();
        if (is_null($gamificationProfile)) {
            $gamificationProfile = new GamificationPersonProfile();
            $gamificationProfile->setLevel($this->levelRepository->findOneBy(['key' => 0]));
            $gamificationProfile->setPerson($person);
            $gamificationProfile->setAccessGrantedCount(0);
            $gamificationProfile->setElFilledOut(false);
            $gamificationProfile->setElImproved(false);
            $gamificationProfile->setElIrrelevant(false);
            $gamificationProfile->setElRevised(false);
            $gamificationProfile->setHasSharedEl(false);
            $gamificationProfile->setHasUsedCardLayer(false);
            $gamificationProfile->setHasUsedDatafilter(false);
            $gamificationProfile->setHasUsedTimefilter(false);
            $gamificationProfile->setBetaStatus(false);
            $this->personGoalRepository->add($gamificationProfile);
        }
        return $gamificationProfile;
    }

    /**
     * Processes the goal progress of the given type.
     * The progress is sometimes ignored if the goal is in a level that is more than 1 level ahead.
     * @param PbsUserDTO $pbsUserDTO
     * @param string $type of the goal
     * @throws \Exception
     */
    public function genericGoalProgress(PbsUserDTO $pbsUserDTO, string $type)
    {
        $person = $this->personRepository->find($pbsUserDTO->getId());
        $pgp = $this->getPersonGamification($person);

        switch ($type) {
            case Goal::TYPE_CARD_LAYERS:
                $pgp->setHasUsedCardLayer(true);
                break;
            case Goal::TYPE_TIME_FILTER:
                $pgp->setHasUsedTimefilter(true);
                break;
            case Goal::TYPE_DATA_FILTER:
                $pgp->setHasUsedDatafilter(true);
                break;
            case Goal::TYPE_SHARE_EL:
                $pgp->setHasSharedEl(true);
                break;
            case Goal::TYPE_SHARE_THREE:
            case Goal::TYPE_SHARE_ONE:
                $newCount = $pgp->getAccessGrantedCount() + 1;
                $pgp->setAccessGrantedCount($newCount);
                break;
            case Goal::TYPE_EL_REVISION:
                if ($pgp->getLevel()->getKey() >= 1) {
                    $pgp->setElRevised(true);
                }
                break;
            case Goal::TYPE_EL_IMPROVEMENT:
                if ($pgp->getLevel()->getKey() >= 2) {
                    $pgp->setElImproved(true);
                }
                break;
            case Goal::TYPE_EL_IRRELEVANT:
                if ($pgp->getLevel()->getKey() >= 1) {
                    $pgp->setElIrrelevant(true);
                }
                break;
            case Goal::TYPE_EL_FILL_OUT:
                if ($this->isElFilledOut($person)) {
                    $pgp->setElFilledOut(true);
                }
                break;
            default:
                throw new \Exception('typo in type');
        }

        $this->checkLevelUp($pgp);

        $this->em->persist($pgp);
        $this->em->flush();
    }

    public function checkLevelUp(GamificationPersonProfile $person)
    {
        $currentLevel = $person->getLevel();
        $nextLevel = $this->levelRepository->findNextLevel($currentLevel);

        if (count($nextLevel) === 0) {
            return $person;
        }
        $nextLevel = $nextLevel[0];
        $levelUp = false;
        if ($currentLevel->getKey() === 0) {
            if ($person->getHasUsedDatafilter() && ($person->getHasUsedCardLayer() || $person->getHasUsedTimefilter() || $person->getHasSharedEl())) {
                $levelUp = true;
            }
        }
        if ($currentLevel->getKey() === 1) {
            $completedCounter = 0;
            if ($person->getElFilledOut()) {
                if ($person->getAccessGrantedCount() >= 1) {
                    $completedCounter++;
                }
                if ($person->getElIrrelevant()) {
                    $completedCounter++;
                }
                if ($person->getElRevised()) {
                    $completedCounter++;
                }
                if ($completedCounter >= 2) {
                    $levelUp = true;
                }
            }
        }
        if ($currentLevel->getKey() === 2) {
            if ($person->getElImproved() && ($this->checkLoginGoal($person) || $person->getAccessGrantedCount() >= 3)) {
                $levelUp = true;
            }
        }

        if ($levelUp) {
            $person->setLevel($nextLevel);
            $log = new LevelUpLog();
            $log->setPerson($person->getPerson());
            $log->setLevel($nextLevel);
            $log->setDate(new \DateTimeImmutable());
            $log->setDisplayed(false);
            $this->levelUpLogRepository->add($log);
        }
        $this->em->persist($person);
        $this->em->flush();

        return $person;
    }

    public function checkLoginGoal(GamificationPersonProfile $profile): bool
    {
        return count($profile->getPerson()->getLogins()) >= 4;
    }

    public function getPersonGamificationDTO(PbsUserDTO $pbsUserDTO, string $locale): PersonGamificationDTO
    {
        $levels = $this->levelRepository->findBy(['type' => Level::USER]);
        /** @var Person $person */
        $person = $this->personRepository->find($pbsUserDTO->getId());
        $personGamification = $this->getPersonGamification($person);
        $personGamification = $this->checkLevelUp($personGamification);

        $personGamificationDTO = GamificationPersonProfileMapper::createFromEntity($personGamification, $locale);

        $levelUp = $this->levelUpLogRepository->findOneBy(['person' => $person, 'displayed' => false]);
        if (!is_null($levelUp)) {
            $levelUp->setDisplayed(true);
            $this->levelUpLogRepository->add($levelUp);
            $personGamificationDTO->setLevelUp(true);
        }

        if (count($levels) === 0) {
            throw new \Exception('no levels found?!');
        }
        $levelDtos = [];
        foreach ($levels as $level) {
            $levelDto = GamificationLevelMapper::createFromEntity($level, $locale);
            if ($personGamification->getLevel()->getNextKey() === $level->getKey()) {
                $levelDto->setActive(true);
            }
            $goalDTOs = [];
            $goals = $level->getGoals();
            foreach ($goals as $goal) {
                switch ($goal->getKey()) {
                    case Goal::TYPE_FIRST_LOGIN:
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, true, 0);
                        break;
                    case Goal::TYPE_CARD_LAYERS:
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasUsedCardLayer(), 0);
                        break;
                    case Goal::TYPE_DATA_FILTER:
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasUsedDatafilter(), 0);
                        break;
                    case Goal::TYPE_TIME_FILTER:
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasUsedTimefilter(), 0);
                        break;
                    case Goal::TYPE_SHARE_EL:
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasSharedEl(), 0);
                        break;
                    case Goal::TYPE_EL_FILL_OUT:
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElFilledOut(), $this->getElFilledOutProgress($person));
                        break;
                    case Goal::TYPE_SHARE_ONE:
                        $completed = $personGamification->getAccessGrantedCount() >= 1;
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $completed, $personGamification->getAccessGrantedCount());
                        break;
                    case Goal::TYPE_EL_IRRELEVANT:
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElIrrelevant(), 0);
                        break;
                    case Goal::TYPE_EL_REVISION:
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElRevised(), 0);
                        break;
                    case Goal::TYPE_EL_IMPROVEMENT:
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElImproved(), 0);
                        break;
                    case Goal::TYPE_LOGIN_FOUR_A_YEAR:
                        $logins = $person->getLogins();
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $this->checkLoginGoal($personGamification), count($logins));
                        break;
                    case Goal::TYPE_SHARE_THREE:
                        $completed = $personGamification->getAccessGrantedCount() >= 3;
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $completed, $personGamification->getAccessGrantedCount());
                        break;
                    default:
                        throw new \Exception('Couldnt find goal');
                        break;
                }
            }
            if (count($goalDTOs) !== 0) {
                $levelDto->setGoals(array_reverse($goalDTOs));
                $levelDtos[] = $levelDto;
            }
        }

        $personGamificationDTO->setLevels($levelDtos);
        return $personGamificationDTO;
    }

    /**
     * Maps the questionnaires to the amount of filled out aspects
     * @param Person $person
     * @return array<string,int>
     */
    private function getElFilledOutAspectsCount(Person $person): array
    {
        $counters = [Questionnaire::TYPE_DEPARTMENT => 0, Questionnaire::TYPE_CANTON => 0];

        $filledAspects = $this->gamificationQuapEventRepository->getUniquieIds($person);

        foreach ($filledAspects as $item) {
            $counters[$item['type']]++;
        }

        return $counters;
    }

    /**
     * Returns the amount of filled out aspects of the questionnaire with more filled out aspects.
     * @return int amount of filled out aspects
     */
    private function getElFilledOutProgress(Person $person): int
    {
        $filledOutAspects = $this->getElFilledOutAspectsCount($person);

        return max($filledOutAspects[Questionnaire::TYPE_CANTON], $filledOutAspects[Questionnaire::TYPE_DEPARTMENT]);
    }

    /**
     * Checks whether one of the questionnaires was filled out.
     *
     * This check is done by comparing the gamification_quap_event table to the amount of aspects
     * that exist in the hc_aggregated_quap table.
     * @param Person $person
     * @return bool
     * @throws \Exception
     */
    private function isElFilledOut(Person $person): bool
    {
        $filledOutAspects = $this->getElFilledOutAspectsCount($person);

        $amountOfAspects = $this->questionnaireRepository->getAmountOfAnswerableAspects();

        foreach ($filledOutAspects as $type => $count) {
            if ($amountOfAspects[$type] <= $count) {
                return true;
            }
        }

        return false;
    }

    public function logEvent(array $changedIds, AggregatedQuap $aggregatedQuap, PbsUserDTO $pbsUserDTO)
    {
        $person = $this->personRepository->find($pbsUserDTO->getId());
        if ($this->getPersonGamification($person)->getLevel()->getKey() >= 1) {
            foreach ($changedIds as $id) {
                $eventLog = new GamificationQuapEvent();
                $eventLog->setQuestionnaire($aggregatedQuap->getQuestionnaire());
                $eventLog->setDate(new \DateTimeImmutable());
                $eventLog->setGroup($aggregatedQuap->getGroup());
                $eventLog->setPerson($this->personRepository->find($pbsUserDTO->getId()));
                $eventLog->setLocalChangeIndex($id);
                $this->gamificationQuapEventRepository->add($eventLog);
            }
        }
        $this->genericGoalProgress($pbsUserDTO, Goal::TYPE_EL_FILL_OUT);
    }

    public function getBetaAccess(PbsUserDTO $pbsUserDTO): bool
    {
        /** @var Person $person */
        $person = $this->personRepository->find($pbsUserDTO->getId());
        $profile = $person->getGamification();
        if ($profile->getLevel()->getKey() === 3) {
            /** request logic */
            $profile->setBetaStatus(true);
            $this->personGoalRepository->add($profile);
            $this->mailService->sendBetaAccessMail($person);
            return true;
        }
        return false;
    }
}
