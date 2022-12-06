<?php

namespace App\Service\Aggregator;

use App\Repository\Aggregated\AggregatedQuapRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Quap\QuestionnaireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class RoleAggregator extends WidgetAggregator
{
    private const NAME = 'widget.roles';

    public function __construct(
        EntityManagerInterface $em,
        GroupRepository $groupRepository
    ) {
        parent::__construct($groupRepository);

        $this->em = $em;
    }

    public function getName()
    {
        return self::NAME;
    }

    public function aggregate(DateTime $startDate = null)
    {
        return 0;
    }
}
