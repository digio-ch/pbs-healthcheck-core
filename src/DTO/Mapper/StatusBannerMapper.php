<?php

namespace App\DTO\Mapper;

use App\DTO\Model\StatusBannerDTO;
use App\Entity\General\StatusMessage;

class StatusBannerMapper
{
    public static function map(StatusMessage $entity, string $lang): StatusBannerDTO
    {
        $raw = $entity->getMessage($lang);
        $decoded = json_decode($raw);

        return new StatusBannerDTO($entity->getSeverity(), $decoded);
    }
}
