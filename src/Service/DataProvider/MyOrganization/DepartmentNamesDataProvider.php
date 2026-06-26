<?php

namespace App\Service\DataProvider\MyOrganization;

use App\Entity\Midata\Group;
use App\Repository\Statistics\StatisticGroupRepository;
use DateTimeInterface;
use Doctrine\DBAL\Exception;

class DepartmentNamesDataProvider
{
    /**
     * @var StatisticGroupRepository $statisticGroupRepository
     */
    private StatisticGroupRepository $statisticGroupRepository;

    /**
     * @param StatisticGroupRepository $statisticGroupRepository
     */
    public function __construct(StatisticGroupRepository $statisticGroupRepository)
    {
        $this->statisticGroupRepository = $statisticGroupRepository;
    }


    /**
     * @param Group $association
     * @param DateTimeInterface $date
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
