<?php

namespace App\Service\Gamification;

use App\DTO\Mapper\GamificationGoalMapper;
use App\DTO\Mapper\GamificationLevelMapper;
use App\DTO\Mapper\GamificationPersonProfileMapper;
use App\DTO\Model\Gamification\LevelDTO;
use App\DTO\Model\Gamification\PersonGamificationDTO;
use App\DTO\Model\PbsUserDTO;
use App\Entity\Aggregated\AggregatedQuap;
use App\Entity\Gamification\GamificationPersonProfile;
use App\Entity\Gamification\GamificationQuapEvent;
use App\Entity\Gamification\Level;
use App\Entity\Gamification\LevelUpLog;
use App\Entity\Midata\Person;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Gamification\GamificationQuapEventRepository;
use App\Repository\Gamification\LevelRepository;
use App\Repository\Gamification\GamificationPersonProfileRepository;
use App\Repository\Gamification\LevelUpLogRepository;
use App\Repository\Gamification\LoginRepository;
use App\Repository\Midata\PersonRepository;
use App\Repository\Quap\QuestionnaireRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class PersonGamificationService
{
    private LevelRepository $levelRepository;

    private PersonRepository $personRepository;

    private GamificationPersonProfileRepository $personGoalRepository;

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    private LoginRepository $loginRepository;

    private LevelUpLogRepository $levelUpLogRepository;

    private GamificationQuapEventRepository $gamificationQuapEventRepository;

    public function __construct(
        LoginRepository $loginRepository,
        LevelRepository $levelRepository,
        GamificationPersonProfileRepository $personGoalRepository,
        PersonRepository $personRepository,
        EntityManagerInterface $em,
        LevelUpLogRepository $levelUpLogRepository,
        GamificationQuapEventRepository $gamificationQuapEventRepository
    ) {
        $this->loginRepository = $loginRepository;
        $this->levelRepository = $levelRepository;
        $this->personRepository = $personRepository;
        $this->personGoalRepository = $personGoalRepository;
        $this->em = $em;
        $this->levelUpLogRepository = $levelUpLogRepository;
        $this->gamificationQuapEventRepository = $gamificationQuapEventRepository;
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

    public function genericGoalProgress(PbsUserDTO $pbsUserDTO, string $type)
    {
        $person = $this->personRepository->find($pbsUserDTO->getId());
        $pgp = $this->getPersonGamification($person);

        switch ($type) {
            case 'card':
                $pgp->setHasUsedCardLayer(true);
                break;
            case 'time':
                $pgp->setHasUsedTimefilter(true);
                break;
            case 'data':
                $pgp->setHasUsedDatafilter(true);
                break;
            case 'shareEL':
                $pgp->setHasSharedEl(true);
                break;
            case 'invite':
                $newCount = $pgp->getAccessGrantedCount() + 1;
                $pgp->setAccessGrantedCount($newCount);
                break;
            case 'revised':
                if ($pgp->getLevel()->getKey() >= 1) {
                    $pgp->setElRevised(true);
                }
                break;
            case 'improvement':
                if ($pgp->getLevel()->getKey() >= 2) {
                    $pgp->setElImproved(true);
                }
                break;
            case 'irrelevant':
                if ($pgp->getLevel()->getKey() >= 1) {
                    $pgp->setElIrrelevant(true);
                }
                break;
            case 'filledOut':
                if ($this->getElFilledOut($person) === 7) {
                    $pgp->setElFilledOut(true);
                }
                break;
            default:
                throw new \Exception('typo in type');
                break;
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
            if ($person->getHasUsedCardLayer() && ($person->getHasUsedDatafilter() || $person->getHasUsedTimefilter() || $person->getHasSharedEl())) {
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
                    case 'FIRST_LOGIN':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, true, 0);
                        break;
                    case 'CARD_LAYERS':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasUsedCardLayer(), 0);
                        break;
                    case 'DATAFILTER':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasUsedDatafilter(), 0);
                        break;
                    case 'TIMEFILTER':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasUsedTimefilter(), 0);
                        break;
                    case 'SHARE_WITH_PARENTS':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasSharedEl(), 0);
                        break;
                    case 'EL_FILL_OUT': // TODO add check
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElFilledOut(), $this->getElFilledOut($person));
                        break;
                    case 'SHARE_1':
                        $completed = $personGamification->getAccessGrantedCount() >= 1;
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $completed, $personGamification->getAccessGrantedCount());
                        break;
                    case 'EL_IRRELEVANT': // TODO add check
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElIrrelevant(), 0);
                        break;
                    case 'EL_CHANGE':// TODO add check
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElRevised(), 0);
                        break;
                    case 'EL_IMPROVE':// TODO add check
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElImproved(), 0);
                        break;
                    case 'LOGIN_FOUR_A_YEAR': // TODO persist this in $pgp
                        $logins = $person->getLogins();
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $this->checkLoginGoal($personGamification), count($logins));
                        break;
                    case 'SHARE_THREE':
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
     * Every questionnaire has 7 aspects which can be answered, if all 7 of them have been answered the goal
     * is completed
     */
    private function getElFilledOut(Person $person): int
    {
        $counters = ['Questionnaire::Group::Default' => 0, 'Questionnaire::Group::Canton' => 0];
        $localIdAndQuestionnaireId = $this->gamificationQuapEventRepository->getUniquieIds($person);
        foreach ($localIdAndQuestionnaireId as $item) {
            $counters[$item['type']]++;
        }
        return max($counters['Questionnaire::Group::Canton'], $counters['Questionnaire::Group::Default']);
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
        $this->genericGoalProgress($pbsUserDTO, 'filledOut');
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
            return true;
        }
        return false;
    }
}
