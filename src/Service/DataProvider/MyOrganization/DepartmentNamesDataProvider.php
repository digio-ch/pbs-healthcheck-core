<?php

namespace App\Service\DataProvider\MyOrganization;

use App\Entity\Midata\Group;
use App\Repository\Statistics\StatisticGroupRepository;
use DateTimeInterface;
use Doctrine\DBAL\Exception;

class DepartmentNamesDataProvider
{
    private StatisticGroupRepository $statisticGroupRepository;

    public function __construct(StatisticGroupRepository $statisticGroupRepository)
    {
        $this->statisticGroupRepository = $statisticGroupRepository;
    }


    /**
     * @return string[]
     * @throws Exception
     */
    public function getDepartmentNames(Group $association, DateTimeInterface $date): array
    {
        return $this->statisticGroupRepository->findDepartmentNames(
            $association->getId(),
            $date->format('Y-m-d')
        );
    }
}
