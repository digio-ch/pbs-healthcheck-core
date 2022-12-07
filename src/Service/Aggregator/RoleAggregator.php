<?php

namespace App\Service\Aggregator;

use App\Entity\Aggregated\AggregatedPersonRole;
use App\Repository\Aggregated\AggregatedPersonRoleRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\PersonRoleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RoleAggregator extends WidgetAggregator
{
    private const NAME = 'widget.roles';

    private AggregatedPersonRoleRepository $personRoleRepository;

    private PersonRoleRepository $midataPersonRoleRepository;

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        GroupRepository $groupRepository,
        AggregatedPersonRoleRepository $personRoleRepository,
        PersonRoleRepository $midataPersonRoleRepository
    ) {
        parent::__construct($groupRepository);

        $this->em = $em;
        $this->personRoleRepository = $personRoleRepository;
        $this->midataPersonRoleRepository = $midataPersonRoleRepository;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function aggregate(DateTime $startDate = null)
    {
        // check if People have quit their jobs, and if so aggregate it
        $listOfUnfinished = $this->personRoleRepository->getUnfinished();
        foreach ($listOfUnfinished as $unfinished) {
            $midataObject = $this->midataPersonRoleRepository->find($unfinished->getMidata());
            $deletedAt = $midataObject->getDeletedAt();
            if ($unfinished->getId() == 217) {
                $unfinished->setEndAt($deletedAt);
                $this->em->persist($unfinished);
            }
        }
        $this->em->flush();

        // check if people have been employed and aggregate it
        $highestAggregatedMidataIndex = $this->personRoleRepository->getHighestAggregatedMidataIndex();
        $newPersonRoles = $this->midataPersonRoleRepository->findAllWithHigherIndex($highestAggregatedMidataIndex);
        foreach ($newPersonRoles as $newPersonRole) {
            $aggregatedPersonRole = new AggregatedPersonRole();
            $aggregatedPersonRole->setMidata($newPersonRole);
            $aggregatedPersonRole->setEndAt($newPersonRole->getDeletedAt());
            $aggregatedPersonRole->setGroup($newPersonRole->getGroup());
            $aggregatedPersonRole->setNickname($newPersonRole->getPerson()->getNickname());
            $aggregatedPersonRole->setPerson($newPersonRole->getPerson());
            $aggregatedPersonRole->setRole($newPersonRole->getRole());
            $aggregatedPersonRole->setStartAt($newPersonRole->getCreatedAt());

            $this->em->persist($aggregatedPersonRole);
        }
        $this->em->flush();
    }
}
