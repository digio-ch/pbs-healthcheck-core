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

    public function __construct(
        LevelRepository $levelRepository,
        GamificationPersonProfileRepository $personGoalRepository,
        PersonRepository $personRepository,
        EntityManagerInterface $em
    )
    {
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
                        break;
                    case 'DATAFILTER':
                        break;
                    case 'TIMEFILTER':
                        break;
                    case 'SHARE_WITH_PARENTS':
                        break;
                    case 'EL_FILL_OUT':
                        break;
                    case 'SHARE_1':
                        break;
                    case 'EL_IRRELEVANT':
                        break;
                    case 'EL_CHANGE':
                        break;
                    case 'EL_IMPROVE':
                        break;
                    case 'EL_TWICE_A_YEAR':
                        break;
                    case 'LOGIN_FOUR_A_YEAR':
                        break;
                    case 'SHARE_THREE':
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
