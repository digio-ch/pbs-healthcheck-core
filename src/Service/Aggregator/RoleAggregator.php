<?php

namespace App\Service\Aggregator;

use App\Repository\Aggregated\AggregatedPersonRoleRepository;
use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\PersonRoleRepository;
use App\Repository\Midata\RoleRepository;
use App\Repository\Quap\QuestionnaireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RoleAggregator extends WidgetAggregator
{
    private const NAME = 'widget.roles';

    private AggregatedPersonRoleRepository $personRoleRepository;

    private PersonRoleRepository $midataPersonRoleRepository;

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

    public function getName()
    {
        return self::NAME;
    }

    public function aggregate(DateTime $startDate = null)
    {
        return 0;
    }

    public function aggregateWithOutput(OutputInterface $output)
    {
        $listOfUnfinished = $this->personRoleRepository->getUnfinished();

        foreach ($listOfUnfinished as $unfinished) {
            $midataObject = $this->midataPersonRoleRepository->find($unfinished->getMidata());
            $deletedAt = $midataObject->getDeletedAt();
            if ($deletedAt !== null) {
                // Update $unfinished end_at to the deleted at
                $output->writeln('somethings wrong');
            }
        }
        //flush

        //implement aggregating the new stuff
        //implement the deletion stuff
        return 0;
    }
}
