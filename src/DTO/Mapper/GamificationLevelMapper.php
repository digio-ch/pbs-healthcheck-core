<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Gamification\LevelDTO;
use App\Entity\Gamification\Level;
use App\Entity\Gamification\LevelAccess;

class GamificationLevelMapper
{
    public static function createFromEntity(Level $level, string $locale): LevelDTO
    {
        $dto = new LevelDTO();
        $dto->setActive(false);
        $dto->setKey($level->getKey());
        $dto->setRequired($level->getRequired());

        $access = self::mapAccess($locale, $level->getAccess());
        $dto->setAccess($access);

        if ($locale === 'de') {
            $dto->setTitle($level->getDeTitle());
        } elseif ($locale === 'it') {
            $dto->setTitle($level->getItTitle());
        } else {
            $dto->setTitle($level->getFrTitle());
        }
        return $dto;
    }

    private static function mapAccess(string $locale, ?LevelAccess $access): ?string
    {
        if (is_null($access)) {
            return null;
        }

        switch ($locale) {
            case 'de':
                return $access->getDeDescription();
            case 'it':
                return $access->getItDescription();
            case 'fr':
                return $access->getFrDescription();
        }

        return "";
    }
}
