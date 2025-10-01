<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Gamification\GoalDTO;
use App\Entity\Gamification\Goal;

class GamificationGoalMapper
{
    public static function createFromEntity(Goal $goal, string $locale, bool $completed, int $progress): GoalDTO
    {
        $dto = new GoalDTO();
        $dto->setRequired($goal->getRequired());
        $dto->setCompleted($completed);
        $dto->setProgress($progress);
        $dto->setKey($goal->getKey());
        if ($locale === 'de') {
            $dto->setTitle($goal->getDeTitle());
            $dto->setInformation($goal->getDeInformation());
            $dto->setHelp($goal->getDeHelp());
        } elseif ($locale === 'it') {
            $dto->setTitle($goal->getItTitle());
            $dto->setInformation($goal->getItInformation());
            $dto->setHelp($goal->getItHelp());
        } else {
            $dto->setTitle($goal->getFrTitle());
            $dto->setInformation($goal->getFrInformation());
            $dto->setHelp($goal->getFrHelp());
        }
        return $dto;
    }
}
