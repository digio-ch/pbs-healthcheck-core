<?php

namespace App\DTO\Mapper;

use App\DTO\Model\StatusBannerDTO;
use App\DTO\Model\StatusMessageDTO;
use App\Entity\General\StatusMessage;

class StatusBannerMapper
{
    public static function map(StatusMessage $entity, string $lang): StatusBannerDTO
    {
        $raw = $entity->getMessage($lang);
        $decoded = json_decode($raw);

        $message = new StatusMessageDTO();

        $message->setTitle($decoded->title);
        $message->setBody($decoded->body);

        return new StatusBannerDTO($entity->getSeverity(), $message);
    }
}
