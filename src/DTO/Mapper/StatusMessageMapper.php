<?php

namespace App\DTO\Mapper;

use App\DTO\Model\StatusMessageDTO;
use App\Entity\General\StatusMessage;

class StatusMessageMapper
{
    public static function mapStatusBanner(StatusMessage $entity, string $lang): StatusMessageDTO
    {
        $message = $entity->getMessage($lang);
        return new StatusMessageDTO($entity->getSeverity(), $message);
    }
}