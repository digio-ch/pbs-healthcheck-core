<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Gamification\GoalDTO;
use App\DTO\Model\Gamification\LevelDTO;
use App\Entity\Gamification\Goal;
use App\Entity\Gamification\Level;

class GamificationLevelMapper
{
    public static function createFromEntity(Level $level, string $locale): LevelDTO
    {
        $dto = new LevelDTO();
        $dto->setActive(false);
        $dto->setKey($level->getKey());
        $dto->setRequired($level->getRequired());

        if ($locale === 'de') {
            $dto->setTitle($level->getDeTitle());
        } elseif ($locale === 'it') {
            $dto->setTitle($level->getItTitle());
        } else {
            $dto->setTitle($level->getFrTitle());
        }
        return $dto;
    }
}
