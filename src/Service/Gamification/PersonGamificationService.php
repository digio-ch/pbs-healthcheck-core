<?php

namespace App\Service\Gamification;

use App\DTO\Model\PbsUserDTO;
use App\Entity\Gamification\PersonGoal;
use App\Entity\Midata\Person;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Gamification\LevelRepository;
use App\Repository\Gamification\PersonGoalRepository;
use App\Repository\Midata\PersonRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class PersonGamificationService
{
    private LevelRepository $levelRepository;

    private PersonRepository $personRepository;

    private PersonGoalRepository $personGoalRepository;

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    public function __construct(
        LevelRepository $levelRepository,
        PersonGoalRepository $personGoalRepository,
        PersonRepository $personRepository,
        EntityManagerInterface $em
    )
    {
        $this->levelRepository = $levelRepository;
        $this->personRepository = $personRepository;
        $this->personGoalRepository = $personGoalRepository;
        $this->em = $em;
    }

    public function getPersonGamification(Person $person): PersonGoal
    {
        $gamificationProfile = $person->getGamification();
        if(is_null($gamificationProfile)) {
            $gamificationProfile = new PersonGoal();
            $gamificationProfile->setLevel($this->levelRepository->findOneBy(['key' => 'U0']));
            $gamificationProfile->setPerson($person);
            $gamificationProfile->setAccessGrantedCount(0);
            $gamificationProfile->setElFilledOut(true);
            $gamificationProfile->setElImproved(false);
            $gamificationProfile->setElIrrelevant(false);
            $gamificationProfile->setElRevised(false);
            $gamificationProfile->setElTwiceYear(false);
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

    public function getPersonGoal(Person $person, String $locale)
    {

    }
}
