<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Gamification\LevelDTO;
use App\DTO\Model\Gamification\PersonGamificationDTO;
use App\Entity\Gamification\GamificationPersonProfile;
use App\Entity\Gamification\Level;

class GamificationPersonProfileMapper
{
    public static function createFromEntity(GamificationPersonProfile $profile, string $locale): PersonGamificationDTO
    {
        $dto = new PersonGamificationDTO();
        $dto->setName($profile->getPerson()->getNickname());
        $dto->setLevelKey($profile->getLevel()->getKey());
        $dto->setLevelUp(self::checkLevelUp($profile));

        if ($locale === 'de') {
            $dto->setTitle($profile->getLevel()->getDeTitle());
        } elseif ($locale === 'it') {
            $dto->setTitle($profile->getLevel()->getItTitle());
        } else {
            $dto->setTitle($profile->getLevel()->getFrTitle());
        }
        return $dto;
    }

    public static function checkLevelUp(GamificationPersonProfile $profile)
    {
        $levelUps = $profile->getPerson()->getLevelUps();
        if (count($levelUps) > 0) {
            return true;
        }
        return false;
    }
}
