<?php

namespace App\Service\Gamification;

use App\DTO\Mapper\GamificationGoalMapper;
use App\DTO\Mapper\GamificationLevelMapper;
use App\DTO\Model\Gamification\LevelDTO;
use App\DTO\Model\Gamification\PersonGamificationDTO;
use App\DTO\Model\PbsUserDTO;
use App\Entity\Gamification\GamificationPersonProfile;
use App\Entity\Gamification\Level;
use App\Entity\Midata\Person;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Gamification\LevelRepository;
use App\Repository\Gamification\GamificationPersonProfileRepository;
use App\Repository\Gamification\LoginRepository;
use App\Repository\Midata\PersonRepository;
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

    public function __construct(
        LoginRepository $loginRepository,
        LevelRepository $levelRepository,
        GamificationPersonProfileRepository $personGoalRepository,
        PersonRepository $personRepository,
        EntityManagerInterface $em
    )
    {
        $this->loginRepository = $loginRepository;
        $this->levelRepository = $levelRepository;
        $this->personRepository = $personRepository;
        $this->personGoalRepository = $personGoalRepository;
        $this->em = $em;
    }

    public function getPersonGamification(Person $person): GamificationPersonProfile
    {
        $gamificationProfile = $person->getGamification();
        if(is_null($gamificationProfile)) {
            $gamificationProfile = new GamificationPersonProfile();
            $gamificationProfile->setLevel($this->levelRepository->findOneBy(['key' => 'U0']));
            $gamificationProfile->setPerson($person);
            $gamificationProfile->setAccessGrantedCount(0);
            $gamificationProfile->setElFilledOut(true);
            $gamificationProfile->setElImproved(false);
            $gamificationProfile->setElIrrelevant(false);
            $gamificationProfile->setElRevised(false);
            $gamificationProfile->setHasSharedEl(false);
            $gamificationProfile->setHasUsedCardLayer(false); // TODO
            $gamificationProfile->setHasUsedDatafilter(false);
            $gamificationProfile->setHasUsedTimefilter(false);
            $this->personGoalRepository->add($gamificationProfile);
        }
        return $gamificationProfile;
    }

    public function genericGoalProgress(PbsUserDTO $pbsUserDTO, string $type) {
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
                $pgp->setElRevised(true);
                break;
            case 'improvement':
                $pgp->setElImproved(true);
                break;
            case 'irrelevant':
                $pgp->setElIrrelevant(true);
                break;
            default:
                throw new \Exception('typo in type');
                break;
        }

        $this->em->persist($pgp);
        $this->em->flush();
    }

    public function getPersonGamificationDTO(PbsUserDTO $pbsUserDTO, String $locale): PersonGamificationDTO
    {
        $levels = $this->levelRepository->findBy(['type' => Level::USER]);
        /** @var Person $person */
        $person = $this->personRepository->find($pbsUserDTO->getId());
        $personGamification = $this->getPersonGamification($person);

        $personGamificationDTO = new PersonGamificationDTO();
        $personGamificationDTO->setName($person->getNickname());
        $personGamificationDTO->setLevelKey($personGamification->getLevel()->getKey());
        $personGamificationDTO->setLevelUp(false); // TODO

        if (count($levels) === 0) {
            throw new \Exception('no levels found?!');
        }
        $levelDtos = [];
        foreach ($levels as $level) {
            $levelDto = GamificationLevelMapper::createFromEntity($level, $locale);
            $goalDTOs = [];
            $goals = $level->getGoals();
            foreach ($goals as $goal) {
                switch ($goal->getKey()) {
                    case 'FIRST_LOGIN':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, true, 1);
                        break;
                    case 'CARD_LAYERS':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasUsedCardLayer(), 1);
                        break;
                    case 'DATAFILTER':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasUsedDatafilter(), 1);
                        break;
                    case 'TIMEFILTER':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasUsedTimefilter(), 1);
                        break;
                    case 'SHARE_WITH_PARENTS':
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getHasSharedEl(), 1);
                        break;
                    case 'EL_FILL_OUT': // TODO add check
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElFilledOut(), 1);
                        break;
                    case 'SHARE_1':
                        $completed = $personGamification->getAccessGrantedCount() >= 1;
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $completed, $personGamification->getAccessGrantedCount());
                        break;
                    case 'EL_IRRELEVANT': // TODO add check
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElIrrelevant(), 1);
                        break;
                    case 'EL_CHANGE':// TODO add check
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElRevised(), 1);
                        break;
                    case 'EL_IMPROVE':// TODO add check
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $personGamification->getElImproved(), 1);
                        break;
                    case 'LOGIN_FOUR_A_YEAR': // TODO persist this in $pgp
                        $logins = $person->getLogins();
                        $completed = count($logins) >= 4;
                        $goalDTOs[] = GamificationGoalMapper::createFromEntity($goal, $locale, $completed, count($logins));
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
            $levelDto->setGoals($goalDTOs);
            $levelDtos[] = $levelDto;
        }
        $personGamificationDTO->setLevels($levelDtos);
        return $personGamificationDTO;
    }
}
